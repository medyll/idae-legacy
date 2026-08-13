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
	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];

	$dad_foradzone       = uniqid($table);
	$for_patolon_bismuth = uniqid($table);
	$patolon_bismuth     = uniqid($table);
	$forward_zone        = uniqid($table);
	$forward_zone_entete = uniqid($table);

	$TEST_AGENT = $APP->has_agent();

?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;">
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="titre_entete flex_h">
			<div class="flex_main"><i class="fa fa-<?= $APP->iconAppscheme ?>"></i> <?= idioma('tri') ?> <?= ucfirst($table) ?>s</div>
			<div class="applink">
				<a onclick="reloadModule('<?= $_POST['mdl'] ?>','*')"><i class="fa fa-refresh"></i></a>
			</div>
		</div>
		<div class="flex_main " style=" overflow:hidden;">
			<div class="flex_v" style=" overflow:hidden;width:100%;">
				<div app_gui_explorer id="<?= $dad_foradzone ?>" class="flex_main flex_v" style="overflow:hidden;">
								<div class="flex_h flex_align_bottom">
						<?php if (!empty($TEST_AGENT)): ?>
							<div class="app_onglet toggler  applink flex_h">
								<a class="autoToggle textvert" app_button="app_button" vars="<?= $HTTP_VARS ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>"><i class="fa fa-user"></i></a>
								<a class="autoToggle textorange" app_button="app_button" vars="<?= $HTTP_VARS_NOAGENT ?>"><i class="fa fa-globe"></i></a>
							</div>
						<?php endif; ?>
						<div class="app_onglet toggler flex_main">
							<?php
								$arr_has = ['statut', 'type', 'categorie', 'group'];
								foreach ($arr_has as $key => $value):
									$Value = ucfirst($value);
									if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
										<a class="autoToggle" act_target="<?= $forward_zone ?>" mdl="app/app/app_dispatch_inner" vars="table=<?= $table ?>&type=<?= $value ?>">
											<?= ucfirst(idioma($Value)) ?>
										</a>
									<?php endif; ?>
								<?php endforeach; ?>
						</div>
					</div>
					<div expl_left_zone id="<?= $forward_zone ?>" class="flex_h flex_margin" style="height:100%;overflow-y: hidden;overflow-x: auto;">
					</div>
				</div>
			</div>
		</div>
		<div class="padding ededed bordert">
			<br>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, $$, .on, .up, .select, .first, .show, .readAttribute, .each,
	 * new Element) to native DOM, with file-local `dsp_` helpers.
	 *
	 * loadModule() is NOT a shim call and is left alone: engine/methods.js
	 * installs it directly on HTMLElement.prototype, so native nodes carry it.
	 */
	function dsp_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function dsp_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function dsp_up(node, selector) {
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
	function dsp_on(root, eventName, selectorOrHandler, maybeHandler) {
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
	function dsp_qsa(selector) {
		return Array.prototype.slice.call(document.querySelectorAll(selector));
	}

	load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');
	dsp_show(dsp_el('<?=$patolon_bismuth?>'));

	dsp_on(dsp_el('<?=$for_patolon_bismuth?>'),'click','[data-table][data-table_value]',function(event,node){
		var table =node.getAttribute('data-table');
		var table_value =node.getAttribute('data-table_value');
		var div = document.createElement('div');
		dsp_el('<?=$forward_zone?>').appendChild(div);
		div.loadModule('app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	dsp_on(dsp_el('<?=$dad_foradzone?>'),'click','[data-link][data-table][data-table_value]',function(event,node){

		var table =node.getAttribute('data-table');
		var table_value =node.getAttribute('data-table_value');

		dsp_qsa('#<?=$dad_foradzone?> [data-link][data-table="'+table+'"]').forEach(function(renode){
			var value = renode.getAttribute('data-table_value')
			var zone = dsp_up(renode,'.forwarder').querySelector('.forwarder_zone_fiche');
			dsp_show(zone);
			zone.loadModule('app/app/app_fiche_mini','table='+table+'&table_value='+value);
		})

	//	nav_forward(node,'app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	dsp_on(dsp_el('<?=$dad_foradzone?>'),'click','[data-link][data-table][data-vars]',function(event,node){

		var table =node.getAttribute('data-table');
		var vars =node.getAttribute('data-vars');
		var value = dsp_up(node,'[data-table_value]').getAttribute('data-table_value')

		dsp_qsa('#<?=$dad_foradzone?> [data-link][data-table="'+table+'"][data-vars]').forEach(function(renode){
			var f_node = renode;
			var retable =renode.getAttribute('data-table');
			var revars =renode.getAttribute('data-vars');

			var zone = dsp_up(f_node,'.forwarder').querySelector('.forwarder_zone_fiche');
			dsp_show(zone);
			zone.loadModule('app/app/app_fiche_forward_liste','table='+retable+'&'+revars);
			console.log(retable,revars);
		})

	})
</script>