<?php
include_once($_SERVER['CONF_INC']);
$uniqid =  uniqid(); 
echo $mdl = $_POST['mdl_stat'].'/'.$_POST['mdl_stat'];
?>

<div class="fond_noir color_fond_noir titre_entete">
  <li class="fa fa-chevron-right"></li>&nbsp;<?=idioma('Statistiques').' '.$_POST['mdl_stat']?>
</div>
<div id="liste_<?=$uniqid?>" class="flowDown borderb" style="overflow:auto;">
  <?=skelMdl::cf_module('app/app_stat/'.$mdl.'_liste');?>
</div>
<div class="stayDown">
  <div class="bordert borderb flowDown" id="date_<?=$uniqid?>">
    <?=skelMdl::cf_module('app/app_stat/statistique_periode');?> 
  </div>
  <div class="stayDown" id="chart_<?=$uniqid?>" style="height: 400px; width: 100%;">
    <?=skelMdl::cf_module('app/app_stat/'.$mdl.'_stat',array('emptyModule'=>true));?>
  </div>
</div>
<script>
/*
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * ($, .on, .observe, .show) to native DOM, with file-local `sd_` helpers.
 * Form.serialize stays (shim-form.js is the shim that keeps living) and
 * loadModule is not a shim call: engine/methods.js puts it on the prototype.
 */
function sd_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function sd_show(node) { if (node) node.style.display = ''; return node; }

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function sd_on(root, eventName, selectorOrHandler, maybeHandler) {
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

// Both handlers ran the same three lines; factored out.
function sd_refresh() {
	var vars     = Form.serialize(sd_el('liste_<?=$uniqid?>'));
	var varsDate = Form.serialize(sd_el('date_<?=$uniqid?>'));
	// loadModule returns the element, which is what let Prototype chain .show().
	sd_show(sd_el('chart_<?=$uniqid?>').loadModule('statistique/<?=$mdl?>_stat', vars + '&' + varsDate));
}

sd_on(sd_el('liste_<?=$uniqid?>'), 'click', 'input[type=checkbox]', function (event, node) {
	sd_refresh();
})
sd_el('date_<?=$uniqid?>').addEventListener('dom:datechoosen', function (event) {
	sd_refresh();
})
</script> 
