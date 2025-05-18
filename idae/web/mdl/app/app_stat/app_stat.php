<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);
	$uniqid            = uniqid();
	$APP_SCH           = new APP('appscheme');
	$APP_SCH_TY        = new APP('appscheme_type');
	$APP_SCH_FIELD     = new APP('appscheme_field');
	$APP_SCH_HAS_FIELD = new APP('appscheme_has_field');

	$APP_GD = new App('agent_groupe_droit'); // verification des droits utilisateur // code =  $code.'_'.$table
	//
	$ARR_FIELD_DATE = $APP_SCH_FIELD->distinct_all('idappscheme_field', ['codeAppscheme_field_type' => 'date']);
	$ARR_HAS        = $APP_SCH_HAS_FIELD->distinct_all('idappscheme', ['idappscheme_field' => ['$in' => $ARR_FIELD_DATE]]);

	$arr_sch      = $APP_GD->distinct_all('idappscheme', ['idappscheme' => ['$in' => $ARR_HAS], 'idagent_groupe' => (int)$_SESSION['idagent_groupe'], 'L' => true]);
	$arr_sch_type = $APP_SCH->distinct_all('idappscheme_type', ['idappscheme' => ['$in' => $arr_sch]]);
	$RS_TY        = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $arr_sch_type]])->sort(['nomAppscheme_type' => 1]);

?>
<style>

</style>
<div class="flex_h blanc" style="height:100%;overflow:hidden;">
	<div class="frmCol1 flex_v flex_align_stretch" style="overflow:auto;">
		<div class="titre_entete blanc alignright borderb">
			<div class="carre inline fond_noir color_fond_noir "><i class="fa fa-bar-chart "></i></div>
		</div>
		<div class="applink applinkblock padding" main_auto_tree>
			<div class="toggler">
				<? while ($ARR_TY = $RS_TY->getNext()) {
					$idappscheme_type = (int)$ARR_TY['idappscheme_type'];
					$RS_SCH           = $APP_SCH->find(['idappscheme_type' => $idappscheme_type, 'idappscheme' => ['$in' => $arr_sch]])->sort(['nomAppscheme' => 1]);
					?>
					<div auto_tree right="right" class="borderb">
						<div class="flex_h flex_align_middle ededed">
							<div class="carre">
								<i class="fa fa-<?= $ARR_TY['iconAppscheme_type'] ?> textbold"></i>
							</div>
							<div class="padding_more bold blanc borderl flex_main"><?= $ARR_TY['nomAppscheme_type'] ?></div>
						</div>
					</div>
					<div class="borderb">
						<?
							while ($ARR_SCH = $RS_SCH->getNext()) {
								$table       = $ARR_SCH['codeAppscheme'];
								$nom_table   = $ARR_SCH['nomAppscheme'];
								$icon_table  = $ARR_SCH['iconAppscheme'];
								$color_table = $ARR_SCH['colorAppscheme'];
								?>
								<div class="flex_h flex_align_middle ededed">
									<div class="carre textgrisfonce" style="height:100%;vertical-align:middle;">
										<i class="fa fa-<?= $icon_table ?> " style="color: <?= $color_table ?>;"></i>
									</div>
									<div class="applinkblock flex_main blanc borderl">
										<a class="autoToggle" onclick="$('<?= $uniqid ?>').loadModule('app/app_stat/app_stat_dispatch','table=<?= $table ?>');"><?= $nom_table ?></a>
									</div>
								</div>
								<?
							} ?>
					</div>
				<? } ?>
			</div>
		</div>
		<div class="flex_main ">
			<div class=" " style=" height:100%;">
				<i class="fa   textbold"></i>
			</div>
		</div>
	</div>
	<div class="flex_main" id="<?= $uniqid ?>" style="height:100%;overflow:hidden;">
	</div>
</div>