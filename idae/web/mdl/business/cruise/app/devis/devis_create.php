<?php
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);

	$uniqid = uniqid();
	$table = 'devis';
	$path_to_devis = 'business/'.BUSINESS.'/app/' . $table . '/' ;
	$APP = new App();
	$APP->init_scheme('sitebase_devis', 'devis_prestation');
	$APP->init_scheme('sitebase_devis', 'devis_acompte');
	$APP->init_scheme('sitebase_devis', 'devis_passager');
	$APP->init_scheme('sitebase_devis', 'devis_annotation');
	//

if(empty($_POST['BIG_SCREEN'])){
	?>
<div id="dev<?=$uniqid?>"  ></div>
	<script>
		 dv_fire(dv_el('dev<?=$uniqid?>'), 'dom:close')
		 ajaxInMdl('<?=$path_to_devis?>devis_create','nouveau_devis','BIG_SCREEN=1&<?=http_build_query($_POST)?>',{onglet:'Nouveau devis'});
	</script>
	<?php
	return;
}
?>
<div id="dev<?=$uniqid?>" style="width:100%;position:relative;height:100%;" class="blanc">
	<div class="flex_v" style="height:100%;">
		<div class="titre_entete">
			<img src="<?= ICONPATH ?>tarif16.png"/> <?= idioma('Nouveau devis') ?>
		</div>
		<div class="flex_h flex_main">
			<div style="overflow:hidden;right:0;top:0;bottom:0;width:50%;z-index:300;display:none;" id="div_devis_create_wait" class="blanc absolute borderl boxshadow"></div>
			<div class="frmCol1">
				<div class="autoNext avoid"><?= idioma('Client') ?></div>
				<div class="retrait">
					<input id="cho_cli<?= $uniqid ?>" datalist_input_name="vars[idclient]" datalist_input_value="" datalist="app/app_select" populate name="vars[nomClient]" paramName="search" vars="table=client" value=""/>
				</div>
				<div class="autoNext avoid"><?= idioma('Voyage') ?></div>
				<div class="retrait">
					<input id="devis_create_zone" datalist_input_name="vars[idproduit]" datalist_input_value="" datalist="app/app_select" populate name="vars[nomProduit]" paramName="search" vars="table=produit" value=""/>
				</div>
				<div id="div_create_devis_wizard">
					<?= skelMdl::cf_module($path_to_devis.'devis_create_wizard', $_POST + array('uniqid' => $uniqid), 'wizard_' . $uniqid) ?>
				</div>
			</div>
			<div style="overflow:auto;width:100%;z-index:2000;height:150px;display:none" id="div_devis_app_select" class="blanc absolute applink applinkblock toggler boxshadow"></div>
			<div class="flex_v flex_main" style="overflow:auto;">
				<div>
					<form id="devis_form" name="devis_form" onsubmit="dv_show(dv_el('div_devis_create_wait')).loadModule('<?=$path_to_devis?>devis_create_wait',serializeFields(this));return false" action="">
						<input type="hidden" name="vars[idclient]" id="tmp_idclient">
						<input type="hidden" name="vars[iddevis_type]" value="2">
						<input type="hidden" name="vars[idagent]" value="<?= $_SESSION['idagent'] ?>">
						<div id="div_devis_create_make"></div>
					</form>
				</div>
				<div id="div_produit_liste_devis"  class="borderb flex_main flex_v relative" app_gui_explorer>
					<div class="titre_entete" id="div_devis_create_produit">

					</div>
					<div expl_view_button="true" ></div>
					<div><?= skelMdl::cf_module('app/app_prod/app_prod_liste_menu_search',   array('table'=>'produit','uniqid' => $uniqid), 'wizard_' . $uniqid) ?></div>
					<div class="flex_main" id="table_produit_devis_make"   expl_file_zone  expl_file_list   data-data_model="defaultModel" >
					</div>
					<div class="absolute" expl_preview_zone="true" style="display:none;z-index:100;height:100%;width:30%;right:0;top:0;"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .observe, .on, .fire, .show, .readAttribute, Event.stop) to native
	 * DOM, with file-local `dv_` helpers.
	 *
	 * loadModule / unToggleContent are NOT shim calls and stay: engine/methods.js
	 * installs both on HTMLElement.prototype. Form values use serializeFields.
	 */
	function dv_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Returns the node so `dv_show(x).loadModule(...)` keeps Prototype's chaining. */
	function dv_show(node) { if (node) node.style.display = ''; return node; }

	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	function dv_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	/** Prototype's Event.stop: cancel the default and stop the bubble. */
	function dv_stop(event) {
		if (!event) return;
		event.preventDefault();
		event.stopPropagation();
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function dv_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	<?php
	if(!empty($_POST['idclient']) || !empty($_POST['idproduit']) ){?>
	dv_el('div_produit_liste_devis').unToggleContent();
	dv_el('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', '<?=http_build_query($_POST)?>');
	<?php }?>

	dv_el('devis_create_zone').addEventListener('dom:act_change', function (event) {
		var idproduit = event.memo.id
		dv_el('div_produit_liste_devis').unToggleContent();
		dv_el('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', 'idproduit=' + idproduit)
		dv_stop(event)
		reloadModule('<?=$path_to_devis?>devis_create_wizard', 'wizard_<?=$uniqid?>', 'idproduit=' + idproduit)
	})

	/*dv_el('div_devis_search').addEventListener('dom:act_change', function (event) {
		var form = event.target;
		vars = serializeFields();

		dv_show(dv_el('div_devis_app_select')).loadModule('app/app_liste/app_liste', 'table=produit&' + vars)

	})*/

	  dv_el('cho_cli<?=$uniqid?>').addEventListener('dom:act_change', function (event) {
		var idclient = event.memo.id;
		dv_el('tmp_idclient').value = idclient;
		reloadModule('<?=$path_to_devis?>devis_create_wizard', 'wizard_<?=$uniqid?>', 'idclient=' + idclient);

	})

	dv_on(dv_el('div_produit_liste_devis'), 'click', 'tr', function (event, node) {
		console.log('click')
		var idproduit = node.getAttribute('data-table_value');
		dv_show(dv_el('div_devis_create_produit')).loadModule('<?=$path_to_devis?>devis_create_produit', 'idproduit=' + idproduit)

	})

	/* dv_el('div_devis_app_select').addEventListener('dom:act_click', function (event) {
		idproduit = event.memo.id;
		dv_el('div_produit_liste_devis').unToggleContent();
		dv_el('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', 'idproduit=' + idproduit)
		dv_stop(event)
	})*/
</script>
