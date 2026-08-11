<?php
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	// if ($_SESSION['idagent'] != 1) return;
	$uniqid = uniqid();
	$formSearch = 'form' . $uniqid;
	$table = $_POST['table'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP = new App($table);
	$APP_TABLE = $APP->app_table_one;
	$GRILLE_FK = $APP->get_grille_fk();

?>
<div style="height:100%;overflow:hidden;">
	<form style="height:100%;overflow:hidden;" id="<?= $formSearch ?>" expl_form="expl_form" onsubmit="return false;">
		<input type="hidden" name="table_main" value="<?=$table?>" class="act_int">
		<div style="height:100%;width:100%" class="flex_v">
			<div class="titre_entete color_fond_noir">
				<i class="fa fa-search"></i> Recherche <?= $table ?>
			</div>
			<div style="overflow-x:hidden;height:100%;">
				<div><input type="hidden" name="table" value="<?= $table ?>"></div>			
				<?php if (!empty($APP_TABLE['hasTypeScheme'])): ?>
					<div class="cellsearch">
						<?= skelMdl::cf_module('app/app_search/search_item_check', array('table_main'=>$table,'table' => $table . '_type'), '', 'item="' . $table . '_type"') ?>
					</div>
				<?php endif; ?>
				<?php foreach ($GRILLE_FK as $fk):
					$table_fk = $fk['table_fk'];
					$id_fk    = 'id' . $fk['table_fk'];
					$rs_dist  = $APP->distinct($table_fk, $vars);
					$arr_fk   = $APP->get_fk_id_tables($table_fk);

					$arr_inter = array_intersect_key($vars, $arr_fk);
					?>
					<div class="cellsearch">
						<?= skelMdl::cf_module('app/app_search/search_item_check', array('table_main'=>$table,'item' => $table_fk), '', 'item="' . $table_fk . '"') ?>
					</div>
				<?php endforeach; ?>
			</div>
			
			<div class="buttonZone">
				<div class="alignright">
					<button type="submit" value="Ok"><i class="fa fa-search-plus"></i> <?= idioma('Rechercher') ?></button>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .up, .next, .previousSiblings, .select, .first, .each,
	 * .readAttribute, .fire) to native DOM, with file-local `pms_` helpers.
	 *
	 * Form.serialize / Form.serializeElements are deliberately kept: shim-form.js
	 * is the shim that stays. serializeElements in particular did not exist as a
	 * static until 2026-08-11, so the line using it below had been throwing --
	 * see form-serialize.spec.ts. loadModule() is not a shim call either;
	 * engine/methods.js installs it on HTMLElement.prototype.
	 */
	function pms_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function pms_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	/**
	 * Prototype's Element#next(selector): scans forward through the following
	 * siblings and returns the first that matches, not merely the immediate
	 * one. nextElementSibling alone would stop at the first non-matching node
	 * and end the while-loop below early.
	 */
	function pms_next(node, selector) {
		var sib = node ? node.nextElementSibling : null;
		while (sib) {
			if (sib.matches(selector)) return sib;
			sib = sib.nextElementSibling;
		}
		return null;
	}

	/** Prototype's Element#previousSiblings: all of them, nearest first. */
	function pms_previousSiblings(node) {
		var out = [], sib = node ? node.previousElementSibling : null;
		while (sib) { out.push(sib); sib = sib.previousElementSibling; }
		return out;
	}

	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	function pms_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function pms_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	pms_on(pms_el('<?=$formSearch?>'), 'change', 'input', function (event, node) {

		var vars = 'n=p';
		var ac_elem = pms_up(node, '.cellsearch');
		var next_elem = ac_elem;
		vars = '&' + Form.serialize(ac_elem);
		vars += '&' + Form.serializeElements(pms_el('<?=$formSearch?>').querySelectorAll('.act_int'));

		pms_previousSiblings(ac_elem).forEach(function (danode) {
			if (Form.serialize(danode) != '')vars += '&' + Form.serialize(danode);

		})


		if (!pms_next(next_elem, '.cellsearch')) {
			vars = Form.serialize(pms_el('<?=$formSearch?>'));
			alert('red')

		} else {
			var wrkon, mdl, vars_item;
			while (pms_next(next_elem, '.cellsearch')) {
				wrkon = pms_next(next_elem, '.cellsearch');
				mdl = wrkon.querySelector('[mdl]').getAttribute('mdl');
				vars_item = wrkon.querySelector('[mdl]').getAttribute('item');

				next_elem = pms_next(next_elem, '.cellsearch');
				wrkon.querySelector('[mdl]').loadModule(mdl, 'item=' + vars_item + vars);
			}
		}
		pms_fire(pms_el('<?=$formSearch?>'), 'dom:act_change')
	})
</script>