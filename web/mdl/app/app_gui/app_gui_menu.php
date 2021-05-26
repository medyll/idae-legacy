<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	$APP      = new App();
	$POST     = empty($MDLPOST) ? $_POST : $MDLPOST;
	$time     = time();
	$arr      = $APP->plug('sitebase_base', 'agent')->findOne(['idagent' => (int)$_SESSION['idagent']]);
	$RSSCHEME = $APP->get_schemes([], '', 370); // 'codeAppscheme_base' => 'sitebase_base'

	$arr_tbl = ['client', 'prospect', 'contact', 'tache', 'affaire', 'financement', 'contrat', 'opportunite', 'intervention', 'materiel'];

	//
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');
	$APP_GD     = new App('agent_groupe_droit');

	$arr_pref = $APP->plug('sitebase_pref', 'agent_pref')->distinct('codeAgent_pref', ['idagent' => (int)$_SESSION['idagent'], 'valeurAgent_pref' => ['$in' => [true, 'true']], 'codeAgent_pref' => new MongoRegex('/app_menu_/')]);

	$pattern       = "/app_menu_/i";
	$DIST_TBL_PREF = preg_replace($pattern, '', $arr_pref);

	$arr_sch      = $APP_GD->distinct_all('idappscheme', ['idagent_groupe' => (int)$_SESSION['idagent_groupe'], 'codeAppscheme' => ['$in' => $DIST_TBL_PREF], 'R' => true]);
	$arr_sch_type = $APP_SCH->distinct_all('idappscheme_type', ['idappscheme' => ['$in' => $arr_sch]]);
	$RS_TY        = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $arr_sch_type]])->sort(['nomAppscheme_type' => 1]);

?>
<div id="app_menu_dyn" main_auto_tree class="flex_v dark_1">
	<div class="flex_main" style="overflow:auto;">
		<? while ($ARR_TY = $RS_TY->getNext()) {
			$idappscheme_type = (int)$ARR_TY['idappscheme_type'];
			$RS_SCH           = $APP_SCH->find(['idappscheme_type' => $idappscheme_type, 'idappscheme' => ['$in' => $arr_sch]])->sort(['nomAppscheme' => 1]);
			?>
			<div class="dark_1">
				<div class="titre" auto_tree right="right" title="<?= $ARR_TY['nomAppscheme_type'] ?>">
					<div class="padding_more cursor" auto_tree_click="true">
					<a ><i class="fa fa-<?= $ARR_TY['iconAppscheme_type'] ?>"></i> <?= $ARR_TY['nomAppscheme_type'] ?></a></div>
				</div>
				<div class=" boxshadow  animated fadeIn" style="display: none;">
					<div class=" ">
						<?
							while ($ARR_SCH = $RS_SCH->getNext()) {
								$table       = $ARR_SCH['codeAppscheme'];
								$nom_table   = $ARR_SCH['nomAppscheme'];
								$icon_table  = $ARR_SCH['iconAppscheme'];
								$color  = $ARR_SCH['colorAppscheme'];
								$APP_TMP     = new App($table);
								?>
								<div auto_tree  right="right" class="dark_2" style="border-color: <?=$color?>">
									<div  auto_tree_click="true" class="padding_more retrait">
										<a class="flex_h flex autoToggle"  >
											<span style="width:25px;color:<?=$color?>;" class="aligncenter"><i class="fa fa-<?= $icon_table ?>"></i></span>
											<span class="flex_main titre"><?= ucfirst(idioma($nom_table)) ?></span>
										</a>
									</div>
								</div>
								<div class="dark_3 retrait animated fadeIn" style="display: none;">
									<div class="retrait applink applinkblock">
										<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
											<a class="flex_main" onclick="<?= fonctionsJs::app_explorer($table) ?>;"><span>Espace <?= $APP_TMP->nomAppscheme ?></span><i class="fa fa-home "></i></a>
										<? else: ?>
										<? endif; ?>
										<? if (droit_table($_SESSION['idagent'], 'C', $table)) : ?>
											<a onclick="<?= fonctionsJs::app_create($table) ?>">Créer <?= $APP_TMP->nomAppscheme ?><i class="fa fa-save "></i></a>
										<? endif; ?>
										<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
											<a class="none" onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_prod','vars[collection]=<?= $table ?>',{onglet:'Production <?= $table ?>'});">Explorer</a>
										<? endif; ?>
									</div>
								</div>
								<?
							} ?>
					</div>
				</div>
			</div>
		<? } ?>
	</div>
	<div class="alignright margin">
		<div class="" style="overflow:hidden;"><?= skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['code' => 'app_menu']) ?></div>
	</div>

	<div act_defer mdl="app/app_gui/app_gui_today_create" class="ededed"></div>
</div>
<style>
	#app_menu_dyn a .fa {
		width : 20px;
	}
	#app_menu_dyn .padding_more{
		padding:0.8rem;
	  }
	/*.auto_tree.opened {
		border-left-width: 5px;
		border-left-style: solid;
	  }*/
</style>