<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];
	$APP = new App($table);
	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];


	$dad_foradzone = uniqid($table);
	$for_patolon_bismuth= uniqid($table);
	$patolon_bismuth= uniqid($table);
	$forward_zone= uniqid($table);
	$forward_zone_entete= uniqid($table);
?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;"  >
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="titre_entete" syle="z-index:10">
			<i class="fa fa-<?= $APP->iconAppscheme?>"></i> <?=idioma('Comparer')?> <?=ucfirst($table)?>s
		</div>
		<div class="flex_main " style="position:relative;overflow:hidden;">
			<div style="position: absolute;height: 100%;width: 100%;">
				<div class="flex_h" style="height:100%;overflow: hidden;">
					<div class="frmCol1 ededed">
						<div class="padding ededed borderb aligncenter">
							<script>
								// main_item_search_finder = new BuildSearch('<?=$patolon_bismuth?>');
							</script>
							<form onsubmit="load_table_in_zone(serializeFields(this),'<?=$patolon_bismuth?>');clf_show(clf_el('<?=$patolon_bismuth?>'));return false;">
								<input type="hidden" name="table" value="<?=$table?>">
								<button type="submit" style="position:absolute;right: 0.5em; z-index: 10;border: none;background-color: transparent;"><i class="fa fa-search"></i></button>
								<input placeholder="Recherche" name="search" style="position: relative;margin-right:0px;z-index:1;width:100%;line-height:2" value="" type="text" class="border4"/>
								<br>
							</form>
						</div>
						<div class="flex_h toggler applink applinkblock">
							<div class="flex_main aligncenter"><a class="autoToggle active" onclick="load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-desktop"></i><br> tuiles</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle" onclick="load_table_in_zone('sortBy=quantiteAgent_history&sortDir=-1&table=agent_history&vars[codeAgent_history]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-history"></i><br> historique</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle"><i class="fa fa-folder-o"></i><br> tout</a></div>
						</div>
						<div id="<?=$for_patolon_bismuth?>" class="flex_v frmCol1 blanc shadowbox " style="overflow:hidden;">
							<div class="flex_main ededed applink applinkblock" id="<?=$patolon_bismuth?>"  data-dsp="line" ></div>
						</div>
					</div>
					<div id="<?=$dad_foradzone?>" class="flex_main " style="overflow:hidden;">
						<div id="<?=$forward_zone?>" class="flex_h" style="height:100%;overflow-y: hidden;overflow-x: auto;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, $$, .on, .up, .select, .first, .show, .readAttribute, .each,
	 * new Element) to native DOM, with file-local `clf_` helpers.
	 *
	 * loadModule() is NOT a shim call and is left alone: engine/methods.js
	 * installs it directly on HTMLElement.prototype, so native nodes carry it.
	 */
	function clf_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function clf_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function clf_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function clf_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	/**
	 * The interpolated attribute values below are quoted. Unquoted, a table
	 * name starting with a digit makes the selector invalid CSS and
	 * querySelectorAll throws SyntaxError — the shim's $$ papered over this by
	 * retrying with quotes, native qSA does not.
	 */
	function clf_qsa(selector) {
		return Array.prototype.slice.call(document.querySelectorAll(selector));
	}

	load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');
	clf_show(clf_el('<?=$patolon_bismuth?>'));

	clf_on(clf_el('<?=$for_patolon_bismuth?>'),'click','[data-table][data-table_value]',function(event,node){
		var table =node.getAttribute('data-table');
		var table_value =node.getAttribute('data-table_value');
		var div = document.createElement('div');
		clf_el('<?=$forward_zone?>').appendChild(div);
		div.loadModule('app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	clf_on(clf_el('<?=$dad_foradzone?>'),'click','[data-link][data-table][data-table_value]',function(event,node){

		var table =node.getAttribute('data-table');
		var table_value =node.getAttribute('data-table_value');

		clf_qsa('#<?=$dad_foradzone?> [data-link][data-table="'+table+'"]').forEach(function(renode){
			var value = renode.getAttribute('data-table_value')
			var zone = clf_up(renode,'.forwarder').querySelector('.forwarder_zone_fiche');
			clf_show(zone);
			zone.loadModule('app/app/app_fiche_mini','table='+table+'&table_value='+value);
		})

	//	nav_forward(node,'app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	clf_on(clf_el('<?=$dad_foradzone?>'),'click','[data-link][data-table][data-vars]',function(event,node){

		var table =node.getAttribute('data-table');
		var vars =node.getAttribute('data-vars');
		var value = clf_up(node,'[data-table_value]').getAttribute('data-table_value')

		clf_qsa('#<?=$dad_foradzone?> [data-link][data-table="'+table+'"][data-vars]').forEach(function(renode){
			var f_node = renode;
			var retable =renode.getAttribute('data-table');
			var revars =renode.getAttribute('data-vars');

			var zone = clf_up(f_node,'.forwarder').querySelector('.forwarder_zone_fiche');
			clf_show(zone);
			zone.loadModule('app/app/app_fiche_forward_liste','table='+retable+'&'+revars);
			console.log(retable,revars);
		})

	})
</script>
