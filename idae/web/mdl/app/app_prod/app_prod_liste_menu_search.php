<?php
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	// if ($_SESSION['idagent'] != 1) return;
	$uniqid          = uniqid();
	$table           = $_POST['table'];
	$vars            = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP             = new App($table);
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	$formSearch = 'ct_se' . $uniqid;
?>
<div class="flex_h flex_wrap flex_align_middle flex_inline ">
	<div>
		<form id="<?= $formSearch ?>" expl_form="expl_form" onsubmit="return false;">
			<input type="hidden" name="table" value="<?= $table ?>">
			<div class="flex_h flex_inline flex_align_top">
				<div class="retrait borderr padding">
					<?php
						$arr_has = ['statut', 'type', 'group'];
						foreach ($arr_has as $key => $value):
							$Value  = ucfirst($value);
							$_table = $table . '_' . $value;
							$_Table = ucfirst($_table);
							$_id    = 'id' . $_table;
							$_nom   = 'nom' . $_Table;
							if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
								<div class="flex_h flex_align_middle padding ">
									<label><i class="fa fa-caret-right textgrisfonce"></i></label>
									<div class="borderb">
										<input datalist_input_name="vars[<?= $_id ?>]"
										       datalist_input_value=""
										       datalist="app/app_select"
										       populate
										       paramName="search"
										       vars="table=<?= $_table ?>"
										       value="<?= ucfirst(idioma($Value)) ?>"
										       class="noborder borderb inline textgrisfonce"/>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
				</div>
				<div class="flex_h flex_align_middle flex_wrap flex_inline" style="max-width: <?php //= ceil(sizeof($GRILLE_FK) / 2) * 205 ?>px;">
					<?php foreach ($GRILLE_FK as $fk):
						$table_fk  = $fk['table_fk'];
						$id_fk   = 'id' . $fk['table_fk'];
						$rs_dist = $APP->distinct($table_fk, $vars);
						$arr_fk  = $APP->get_fk_id_tables($table_fk);

						$arr_inter = array_intersect_key($vars, $arr_fk);
						?>
						<div style="max-width:50%;" class="cellsearch relative flex_h flex_align_middle" sort_zone="sort_zone">
							<div><?= skelMdl::cf_module('app/app_search/search_item', ['table_main' => $table, 'table' => $table_fk], '', 'table="' . $table_fk . '"') ?></div>
							<div style="width:40px;"><i class="fa fa-caret-left sortprevious"></i><i class="fa fa-caret-right sortnext"></i></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php unset($_POST['MODULE'], $_POST['uniqid']); ?>
				<div class="ededed padding">
					<div class="border4 blanc"  act_defer vars="<?= http_build_query($_POST) ?>"
					     mdl="app/app_prod/app_prod_liste_menu_date">
					</div>
				</div>
				<div class="blanc">
					<button type="submit" value="Ok" style="line-height:2;height:4em;"><i class="fa fa-search"></i> <?= idioma('Rechercher') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .observe, .up, .next, .previousSiblings, .select, .first, .each,
	 * .readAttribute) to native DOM, with file-local `pms2_` helpers.
	 *
	 * Form.serialize / Form.serializeElements stay: shim-form.js is the shim
	 * that keeps living. loadModule is not a shim call either — engine/methods.js
	 * installs it on HTMLElement.prototype.
	 */
	function pms2_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function pms2_up(node, selector) {
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
	function pms2_next(node, selector) {
		var sib = node ? node.nextElementSibling : null;
		while (sib) {
			if (sib.matches(selector)) return sib;
			sib = sib.nextElementSibling;
		}
		return null;
	}

	/** Prototype's Element#previousSiblings: all of them, nearest first. */
	function pms2_previousSiblings(node) {
		var out = [], sib = node ? node.previousElementSibling : null;
		while (sib) { out.push(sib); sib = sib.previousElementSibling; }
		return out;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function pms2_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	do_reload = function (node) {

		var vars      = 'n=p';
		var ac_elem   = pms2_up (node, '.cellsearch');
		var next_elem = ac_elem;
		vars          = '&' + Form.serialize (ac_elem);
		vars += '&' + Form.serializeElements (pms2_el ('<?=$formSearch?>').querySelectorAll ('.act_int'));

		pms2_previousSiblings (ac_elem).forEach (function (danode) {
			if ( Form.serialize (danode) != '' )vars += '&' + Form.serialize (danode);

		})

		if ( !pms2_next (next_elem, '.cellsearch') ) {
			vars = Form.serialize (pms2_el ('<?=$formSearch?>'));

		} else {
			var wrkon, mdl, vars_item, table_from;
			while (pms2_next (next_elem, '.cellsearch')) {
				wrkon     = pms2_next (next_elem, '.cellsearch');
				mdl       = wrkon.querySelector ('[mdl]').getAttribute ('mdl');
				vars_item = wrkon.querySelector ('[mdl]').getAttribute ('table');

				console.log (pms2_next (next_elem, '.cellsearch'))

				if ( next_elem.querySelector ('[table]') ) {
					table_from = next_elem.querySelector ('[table]').getAttribute ('table');
				}
				next_elem = pms2_next (next_elem, '.cellsearch');
				wrkon.querySelector ('[mdl]').loadModule (mdl, 'table_from=' + table_from + '&table_main=<?=$table?>&table=' + vars_item + '&' + vars);

			}
		}
		// pms2_fire(pms2_el('<?=$formSearch?>'), 'dom:act_change')
	}

	if ( pms2_el ('<?= $formSearch ?>') != null ) {
		pms2_on (pms2_el ('<?= $formSearch ?>'), 'change', 'select', function (event, node) {
			do_reload (node);

		})
		// Two arguments, no selector: never delegation — the shim fell through
		// to Event.observe.
		pms2_el ('<?= $formSearch ?>').addEventListener ('dom:act_change', function (event) {
			do_reload (event.target);

		})

	}
</script>

