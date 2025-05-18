<?
	include_once($_SERVER['CONF_INC']);

	$uniqid = uniqid();
	$table  = $_POST['table'];

	$APP_SCH           = new APP('appscheme');
	$APP_SCH_TY        = new APP('appscheme_type');
	$APP_SCH_FIELD     = new APP('appscheme_field');
	$APP_SCH_HAS_FIELD = new APP('appscheme_has_field');
	// Dates
	$ARR_FIELD_DATE = $APP_SCH_FIELD->distinct_all('idappscheme_field', ['codeAppscheme_field_type' => 'date']);
	$ARR_HAS        = $APP_SCH_HAS_FIELD->distinct_all('idappscheme_field', ['codeAppscheme' => $table, 'idappscheme_field' => ['$in' => $ARR_FIELD_DATE]]);
	$RS_FIELD       = $APP_SCH_FIELD->find(['idappscheme_field' => ['$in' => $ARR_HAS]])->sort(['ordreAppscheme_field' => 1]);
	// periodicité
	$ARR_PERIODE = ['day' => 'jour', 'week' => 'semaine','quarter' => 'trim', 'month' => 'mois', 'year' => 'année'];
	// scope
	$app_stat_scope = 'app_stat_scope';
?>
<div class="flex_h"  id="main_<?= $uniqid ?>" style="height:100%;">
	<div class="frmCol1">
		<div id="date_<?= $uniqid ?>">
			<?= skelMdl::cf_module('app/app_stat/app_stat_periode', ['table' => $table]) ?>
		</div>
	</div>
	<div class="flex_main">
		<div class="flex_v">
			<div class="titre_entete">
				<i class="fa fa-chevron-right"></i>&nbsp;<?= idioma('Statistiques') . ' ' . $table ?>
			</div>
			<div class="titre_entete_menu ">
				<div class="in_menu">
					<span class="padding inline">Choix du type de date &nbsp;</span>
				</div>
				<div class="  applink toggler">
					<? while ($ARR_FIELD = $RS_FIELD->getNext()) {
						$css                 = (empty($i)) ? 'active' : '';
						$i                   = true;
						$idappscheme_field   = $ARR_FIELD['idappscheme_field'];
						$iconAppscheme_field = $ARR_FIELD['iconAppscheme_field'];
						$codeAppscheme_field = $ARR_FIELD['codeAppscheme_field'];
						$nomAppscheme_field  = $ARR_FIELD['nomAppscheme_field'];
						?>
						<a class="autoToggle <?= $css ?>" app_button data-vars="type_date=<?=$codeAppscheme_field?>" ><i class="fa fa-<?= $iconAppscheme_field ?>"></i> <?= $nomAppscheme_field ?></a>
					<? } ?>
				</div>
				<div class="flex_main"></div>
				<div class="in_menu" style="width:25%;">
					<div class="flex_main   bold alignright"><?=idioma('Périodicité')?> <i class="fa fa-caret-right"></i> </div>
				</div>
				<div class="in_menu" style="width:25%;">
					<div class="flex_h toggler">
						<? foreach ($ARR_PERIODE as $key => $value) {
							?>
							<div class="flex_main aligncenter ">
								<a class="autoToggle   link" app_button data-vars="type_periodicite=<?=$key?>" ><?= ucfirst($value) ?></a>
							</div>
							<? } ?>
					</div>
				</div>
			</div>
			<div class="flex_main" id="chart_<?= $uniqid ?>" style="overflow:hidden;">
			</div>
		</div>
	</div>
</div>
<script>
	$('main_<?=$uniqid?>').on('click','[app_button]', function (event,node) {
		if(node.readAttribute('data-vars')){
			var vars = node.readAttribute('data-vars');
			reloadScope('<?=$app_stat_scope?>','*',vars)
		}
	})

	$('date_<?=$uniqid?>').observe('dom:act_click', function (event) {
		var varsDate = Form.serialize($('date_<?=$uniqid?>'));
		$('chart_<?=$uniqid?>').loadModule('app/app_stat/app_stat_dispatch_inner', 'table=<?=$table?>&' + varsDate).show();
	}.bind(this));

	$('chart_<?=$uniqid?>').loadModule('app/app_stat/app_stat_dispatch_inner', 'app_stat_scope=<?=$app_stat_scope?>&table=<?=$table?>&' + Form.serialize($('date_<?=$uniqid?>'))).show();
</script> 
