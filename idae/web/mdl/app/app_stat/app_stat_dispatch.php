<?php
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
					<?php while ($ARR_FIELD = $RS_FIELD->getNext()) {
						$css                 = (empty($i)) ? 'active' : '';
						$i                   = true;
						$idappscheme_field   = $ARR_FIELD['idappscheme_field'];
						$iconAppscheme_field = $ARR_FIELD['iconAppscheme_field'];
						$codeAppscheme_field = $ARR_FIELD['codeAppscheme_field'];
						$nomAppscheme_field  = $ARR_FIELD['nomAppscheme_field'];
						?>
						<a class="autoToggle <?= $css ?>" app_button data-vars="type_date=<?=$codeAppscheme_field?>" ><i class="fa fa-<?= $iconAppscheme_field ?>"></i> <?= $nomAppscheme_field ?></a>
					<?php } ?>
				</div>
				<div class="flex_main"></div>
				<div class="in_menu" style="width:25%;">
					<div class="flex_main   bold alignright"><?=idioma('Périodicité')?> <i class="fa fa-caret-right"></i> </div>
				</div>
				<div class="in_menu" style="width:25%;">
					<div class="flex_h toggler">
						<?php foreach ($ARR_PERIODE as $key => $value) {
							?>
							<div class="flex_main aligncenter ">
								<a class="autoToggle   link" app_button data-vars="type_periodicite=<?=$key?>" ><?= ucfirst($value) ?></a>
							</div>
							<?php } ?>
					</div>
				</div>
			</div>
			<div class="flex_main" id="chart_<?= $uniqid ?>" style="overflow:hidden;">
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .observe, .readAttribute, .show) to native DOM, with file-local
	 * `asd_` helpers. Form.serialize stays; loadModule is not a shim call.
	 */
	function asd_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function asd_show(node) { if (node) node.style.display = ''; return node; }

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function asd_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (!root) return;
		if (maybeHandler === undefined) {
			root.addEventListener(eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	asd_on(asd_el('main_<?=$uniqid?>'), 'click', '[app_button]', function (event, node) {
		if (node.getAttribute('data-vars')) {
			var vars = node.getAttribute('data-vars');
			reloadScope('<?=$app_stat_scope?>','*',vars)
		}
	})

	asd_el('date_<?=$uniqid?>').addEventListener('dom:act_click', function (event) {
		var varsDate = Form.serialize(asd_el('date_<?=$uniqid?>'));
		// loadModule returns the element, which is what let Prototype chain .show().
		asd_show(asd_el('chart_<?=$uniqid?>').loadModule('app/app_stat/app_stat_dispatch_inner', 'table=<?=$table?>&' + varsDate));
	});

	asd_show(asd_el('chart_<?=$uniqid?>').loadModule('app/app_stat/app_stat_dispatch_inner', 'app_stat_scope=<?=$app_stat_scope?>&table=<?=$table?>&' + Form.serialize(asd_el('date_<?=$uniqid?>'))));
</script> 
