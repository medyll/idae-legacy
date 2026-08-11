<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);
	$formSearch = 'ct_se';
	$list = 'ct_ls';

?>
<form name = "<?= $formSearch ?>" id = "<?= $formSearch ?>" method = "post" action = "actions.php" >
	<input type = "hidden" name = "F_action" value = "act_search_maw" />

	<div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item_date' , array( 'item' => 'date' ) , '' , 'item="date"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'destination' ) , '' , 'item="destination"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'fournisseur' ) , '' , 'item="fournisseur"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'ville' ) , '' , 'item="ville"') ?>
		</div >
	</div >
</form >
<script >
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .up, .next, .previousSiblings, .select, .first, .each,
	 * .readAttribute, .fire) to native DOM, with file-local `sch_` helpers.
	 *
	 * Form.serialize / Form.serializeElements are deliberately kept: shim-form.js
	 * is the shim that stays. serializeElements in particular did not exist as a
	 * static until 2026-08-11, so the line using it below had been throwing --
	 * see form-serialize.spec.ts. loadModule() is not a shim call either;
	 * engine/methods.js installs it on HTMLElement.prototype.
	 */
	function sch_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function sch_up(node, selector) {
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
	function sch_next(node, selector) {
		var sib = node ? node.nextElementSibling : null;
		while (sib) {
			if (sib.matches(selector)) return sib;
			sib = sib.nextElementSibling;
		}
		return null;
	}

	/** Prototype's Element#previousSiblings: all of them, nearest first. */
	function sch_previousSiblings(node) {
		var out = [], sib = node ? node.previousElementSibling : null;
		while (sib) { out.push(sib); sib = sib.previousElementSibling; }
		return out;
	}

	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	function sch_fire(node, eventName, memo) {
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
	function sch_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	sch_on(sch_el('<?=$formSearch?>'), 'change', 'select', function (event, node) {

		var vars = 'n=p';
		var ac_elem = sch_up(node, '.cellsearch');
		var next_elem = ac_elem;
		vars = '&' + Form.serialize(ac_elem);
		vars += '&' + Form.serializeElements(sch_el('<?=$formSearch?>').querySelectorAll('.act_int'));

		sch_previousSiblings(ac_elem).forEach(function (danode) {
			if (Form.serialize(danode) != '')vars += '&' + Form.serialize(danode);

		})


		if (!sch_next(next_elem, '.cellsearch')) {
			vars = Form.serialize(sch_el('<?=$formSearch?>'));

		} else {
			var wrkon, mdl, vars_item;
			while (sch_next(next_elem, '.cellsearch')) {
				wrkon = sch_next(next_elem, '.cellsearch');
				mdl = wrkon.querySelector('[mdl]').getAttribute('mdl');
				vars_item = wrkon.querySelector('[mdl]').getAttribute('item');

				next_elem = sch_next(next_elem, '.cellsearch');
				wrkon.querySelector('[mdl]').loadModule(mdl, 'item=' + vars_item + vars);
			}
		}
		sch_fire(sch_el('<?=$formSearch?>'), 'dom:act_change')
	})
</script >