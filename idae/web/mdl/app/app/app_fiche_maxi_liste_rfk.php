<?php
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	//
	// ESPACE
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	/*if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_espace.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_espace', $_POST);
		return;
	}*/
	$APP = new App($table);
	//
	$R_FK = $APP->get_reverse_grille_fk($table, $table_value);

	//
	$zouzou = uniqid($table);
	$zou    = uniqid($table);
?>
<div class="flex_v blanc" style="height: 100%;overflow:hidden;">
	<div id="<?= $zouzou ?>" class="flex_main flex_h" style="width: 100%;overflow-y:hidden;overflow-x: auto;">
		<?php if (sizeof($R_FK) != 0): ?>
			<div class="flex_h  ">
				<?php foreach ($R_FK as $arr_fk):
					$value_rfk               = $arr_fk['table_value'];
					$table_rfk               = $arr_fk['table'];
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$vars_rfk['table']       = $table_rfk;
					$vars_rfk['table_value'] = $value_rfk;
					$count                   = $arr_fk['count'];
					?>
					<div style="order:-<?= $count ?>" act_defer mdl="app/app/app_fiche_forward_liste" vars="table=<?= $table_rfk ?>&vars[<?= 'id' . $table ?>]=<?= $table_value ?>"></div>                <?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .readAttribute, .show) to native DOM, with file-local `rfk_`
	 * helpers. loadModule is not a shim call: engine/methods.js installs it on
	 * HTMLElement.prototype.
	 */
	function rfk_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function rfk_show(node) { if (node) node.style.display = ''; return node; }

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function rfk_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	rfk_on (rfk_el ('<?=$zouzou?>'), 'click', '[data-table][data-table_value][data-link]', function (event, node) {
		var table       = node.getAttribute ('data-table');
		var table_value = node.getAttribute ('data-table_value');
		rfk_show (rfk_el ('<?=$zou?>'));
		rfk_el ('for_<?=$zou?>').loadModule ('app/app/app_fiche_preview', 'table=' + table + '&table_value=' + table_value);
	});
	//
	rfk_on (rfk_el ('<?=$zouzou?>'), 'click', '[data-link][data-table][data-vars]', function (event, node) {
		if ( node.getAttribute ('data-table_value') ) return;
		var table = node.getAttribute ('data-table');
		var vars  = node.getAttribute ('data-vars');
		rfk_show (rfk_el ('<?=$zou?>'));
		rfk_el ('for_<?=$zou?>').loadModule ('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// act_chrome_gui('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// alert('dre')
	})
</script>