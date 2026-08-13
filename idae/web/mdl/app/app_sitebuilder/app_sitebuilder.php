<?php
	include_once($_SERVER['CONF_INC']);
	$APP = new App();

	$APP->init_scheme('sitebase_base', 'appsitebuilder', ['fields' => ['nom', 'code', 'url']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_skin', ['fields' => ['nom', 'code']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page', ['fields' => ['nom', 'code', 'url', 'ordre'], 'has' => ['type']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_module', ['fields' => ['nom', 'code', 'url', 'ordre'], 'has' => ['type'], 'grilleFK' => ['appsitebuilder_page']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_menu', ['fields' => ['nom', 'code', 'html'], 'has' => ['type'], 'grilleFK' => ['appsitebuilder_page']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_template', ['fields' => ['nom', 'code', 'html']]);

?>
<div style="height:100%;overflow:hidden;" class="flex_v blanc" >
	<div class="titre_entete">
		<?= idioma('..') ?>
	</div>
	<div class="flex_h flex_main">
		<div class="frmCol1 flex_v">
			<div class="titre_entete">
				<a onclick="<?= fonctionsJs::app_create('appsitebuilder_page'); ?>"><?= idioma('Ajouter une page') ?></a>
			</div>
			<div class="flex_main ededed" id="zone_agent_tuile" data-dsp_liste="dsp_lists" data-vars="table=agent_tuile&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini"
			     style=" resize: horizontal;"></div>
		</div>
		<div class="frmCol2" app_gui_explorer>
			<div class="flex_h" style="height:100%;" expl_left_zone>
				<div class="frmCol2" id="dropzone_site">
					<div class="applink applinkblock  " dropzone data-vars="vars[dsds]=fdsfds" style="height:100%;">
					</div>
				</div>
				<div class="frmCol1 ededed applink applinkblock">
					<a draggable="true" data-vars="table=&table_value=" data-type="element" data-className="border4  flex_main" >element</a>
					<a draggable="true" data-vars="table=&table_value=" data-className="flex_h flex_main margin_more">container ligne</a>
					<a draggable="true" data-vars="table=&table_value=" data-className="flex_v flex_main margin_more">container colonne</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .select, .first, .addClassName, .insert, .readAttribute,
	 * Event.stop) to native DOM, with file-local `asb_` helpers. The two
	 * `$(node)` wrappers were no-ops — the shim patches HTMLElement.prototype,
	 * so wrapping an element hands back the same element.
	 */
	function asb_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function asb_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	asb_on (asb_el ('dropzone_site'), 'dom:act_drop', function (event, node) {
		var drop_node = event.memo.drop_node;
		// if ( !node.getAttribute ('dropzone') ) node = asb_up (node, '[dropzone]')
		event.preventDefault ();
		event.stopPropagation ();
		var className = drop_node.getAttribute ('data-className') || '';
		var type      = drop_node.getAttribute ('data-type') || 'normal';
		switch (type) {
			case 'normal' :
				var  inserted_node = create_element_of('<div class="padding ededed"><div class="padding borderb">'+className+'</div> <div dropzone="dropzone" class="blanc padding_more   border4"></div></div>');
				inserted_node.querySelector('[dropzone]').classList.add(className);
				// Element#insert with a bare node appends it at the bottom.
				node.appendChild (inserted_node);
				break;
			case 'element' :
				var  inserted_node = create_element_of('<div class="padding flex_main"><div act_defer mdl="app/app_sitebuilder/app_sitebuilder_element"></div></div>');

				// inserted_node.querySelector('[dropzone]').classList.add(className);
				node.appendChild (inserted_node);
				break;
		}
		register_site_module (node);
	})
	function register_site_module(node) {
		// on post id , et au retour on inscrit id
		// ajaxValidation();
	}
</script>
<style>
	#dropzone_site .flex_v { height : auto; }
</style>