<?
	include_once($_SERVER['CONF_INC']);
	$APP     = new App('appscheme');
	$arr_tbl = ['client', 'prospect', 'contact', 'tache', 'affaire', 'financement', 'contrat', 'opportunite', 'intervention', 'materiel'];

	$RSSCHEME = $APP->find([])->sort(['nomAppscheme' => 1]); // 'codeAppscheme_base'=>'sitebase_base'
	//
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');
	$APP_GD     = new App('agent_groupe_droit'); // verification des droits utilisateur // code =  $code.'_'.$table

	$arr_table_R = $APP_GD->distinct_all('codeAppscheme', ['R' => true, 'idagent_groupe' => (int)$_SESSION['idagent_groupe']]);

	$ARR_TYPE = $APP_SCH->distinct_all('idappscheme_type', ['codeAppscheme' => ['$in' => $arr_table_R]]);
	$ARR_TYPE = array_values(array_filter($ARR_TYPE));

	$RSNOSCHEME = $APP_SCH->find(['idappscheme_type' => ['$in' => [null, 0, '']]])->sort(['nomAppscheme' => 1]);

	$arr_pref = $APP->plug('sitebase_pref', 'agent_pref')->distinct('codeAgent_pref', ['idagent' => (int)$_SESSION['idagent'], 'valeurAgent_pref' => ['$in' => [true, 'true']], 'codeAgent_pref' => new MongoRegex('/app_menu_start_/')]);

	$pattern       = "/app_menu_start_/i";
	$DIST_TBL_PREF = preg_replace($pattern, '', $arr_pref);

	$arr_sch      = $APP_GD->distinct_all('idappscheme', ['idagent_groupe' => (int)$_SESSION['idagent_groupe'], 'codeAppscheme' => ['$in' => $DIST_TBL_PREF], 'R' => true]);
	$arr_sch_type = $APP_SCH->distinct_all('idappscheme_type', ['idappscheme' => ['$in' => $arr_sch]]);
	$RS_TY        = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $arr_sch_type]])->sort(['nomAppscheme_type' => 1]);

?>
<style>
	.minibox {
		display       : table;
		width         : 100%;
		margin-bottom : 1em;
	}
	.minibox {
		vertical-align : top;
	}
	.minibox .first {
		width       : 40px;
		text-align  : center;
		padding-top : 5px;
	}
	.tiersbox {
		display        : inline-table;
		width          : 32%;
		margin-bottom  : 1em;
		vertical-align : top;
	}
	.tiersbox .first {
		width       : 40px;
		text-align  : center;
		padding-top : 5px;
	}
	.box {
		display        : table;
		width          : 100%;
		margin-bottom  : 1em;
		vertical-align : top;
	}
	.box .first {
		width       : 40px;
		text-align  : center;
		padding-top : 5px;
	}
</style>
<div class="applink flex_h fond_noir color_fond_noir" style="height: 100%;width:100%;">
	<div class="toggler flex_v " style="height:100%;width: 100%;">
		<div class="applinkblock applink panel_entete flex_main flex_v  " style="overflow:auto;">
			<? while ($ARR_TY = $RS_TY->getNext()) {
				$idappscheme_type = (int)$ARR_TY['idappscheme_type'];
				$RS_SCH           = $APP_SCH->find(['idappscheme_type' => $idappscheme_type, 'idappscheme' => ['$in' => $arr_sch]])->sort(['nomAppscheme' => 1]);
				?>
				<div class="flex_h">
					<div class="transpnoir" title="<?= $ARR_TY['nomAppscheme_type'] ?>">
						<a vars="idappscheme_type=<?= $ARR_TY['idappscheme_type'] ?>" act_target="loader_gui_pane" mdl="app/app_gui/app_gui_start_menu_launch_all"><i class="fa fa-<?= $ARR_TY['iconAppscheme_type'] ?>"></i></a>
					</div>
					<div class="flex_main">
						<?
							while ($ARR_SCH = $RS_SCH->getNext()) {
								$table       = $ARR_SCH['codeAppscheme'];
								$nom_table   = $ARR_SCH['nomAppscheme'];
								$icon_table  = $ARR_SCH['iconAppscheme'];
								$color_table = $ARR_SCH['colorAppscheme'];
								?>
								<a class="flex_h flex autoToggle" vars="table=<?= $table ?>" mdl="app/app_gui/app_gui_start_menu_launch"
								   act_target="loader_gui_pane">
									<span style="width:25px;" class="aligncenter textbold"><i class="fa fa-<?= $icon_table ?>"></i></span>
									<span class="flex_main"><?= ucfirst(idioma($nom_table)) ?></span>
									<span style="width:30px;" class="aligncenter"><i class="fa fa-caret-right"></i></span>
								</a>
								<?
							} ?>
					</div>
				</div>
			<? } ?>
			<div class="flex_h">
				<div class="flex_main" style="order:1">
					<? $has = 0;
						foreach ($RSNOSCHEME as $sch):
							$table   = $sch['codeAppscheme'];
							$table_name = $sch['nomAppscheme'];
							if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;
							if ($APP->get_settings($_SESSION['idagent'], 'app_menu_start_' . $table) != 'true') continue;
							$APP_TMP = new App($table);
							$has     = 1;
							?>
							<a class="flex_h flex autoToggle" vars="table=<?= $table ?>" mdl="app/app_gui/app_gui_start_menu_launch"
							   act_target="loader_gui_pane">
								<span style="width:30px;" class="aligncenter textbold"><i class="fa fa-<?= $APP_TMP->iconAppscheme ?>"></i></span>
								<span class="flex_main"><?= ucfirst(idioma($table_name)) ?></span>
								<span style="width:30px;" class="aligncenter"><i class="fa fa-caret-right"></i></span>
							</a>
						<? endforeach; ?>
				</div>
				<? if (!empty($has)) { ?>
					<div class="transpnoir" style="order:0;">
						<a><i class="fa fa-random"></i> <? //= $arr['nomAppscheme_type'] ?></a>
					</div>
				<? } ?>
			</div>
			<div class="flex_h flex flex_main ">
				<div class="transpnoir">
					<a><i class="fa fa-empty"></i></a>
				</div>
				<div class="flex_main">
				</div>
			</div>
			<? if (BUSINESS == 'cruise') { ?>
				<div class="hide_gui_pane  ">
					<a onclick="ajaxMdl('app/app_custom/mail/mail_send','Nouveau Mail')"> Nouveau mail</a>
				</div>
				<? if (droit('DEV')) { ?>
					<div class="hide_gui_pane  ">
						<a  onclick="<?=fonctionsJs::app_mdl('app/app_mail/app_mail')?>">app_mail</a>
					</div>

				<? } ?>
			<? } ?>
		</div>
		<div class="flex_h flex_align_middle">
			<a class="autoToggle flex_main" mdl="app/app_gui/app_gui_production" act_target="loader_gui_pane">
				<i class="fa fa-cube"></i> &nbsp;<?= idioma('Production') ?> </a>
			<? if (droit('ADMIN')) { ?>
				<a class="autoToggle flex_main" act_target="loader_gui_pane" mdl="app/app_admin/app_admin">
					<i class="fa fa-cogs"></i>
					&nbsp;Administration&nbsp;
				</a>
			<? } ?>
			<div class="alignright">
				<?= skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['moduleTag' => 'span', 'code' => 'app_menu_start']) ?>
			</div>
		</div>
	</div>
</div>
