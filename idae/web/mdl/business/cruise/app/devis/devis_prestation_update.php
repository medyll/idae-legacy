<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 25/02/2016
	 * Time: 17:46
	 */

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$path_to_devis = 'business/cruise/app/devis/';
	// POST
	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];

	//
	$APP = new App($table);

	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//

	//
	$uniqkey = $table . $table_value;

	$iddevis = (int)$ARR['iddevis'];

?>
<div class="padding borderb">
	<form id="form<?= $table . $table_value ?>" class="Form" action="<?= ACTIONMDL ?>app/actions.php" onchange="ajaxFormValidation(this);return false;">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<input type="hidden" id="description_<?= $table . $table_value ?>" name="vars[descriptionDevis_prestation]" value="<?= empty($ARR['descriptionDevis_prestation']) ? '' : $ARR['descriptionDevis_prestation'] ?>">
		<input type="hidden" name="reloadModule[<?= $path_to_devis ?>/devis_make_nav]" value="<?= $ARR['iddevis'] ?>"/>
		<table class="table table-bordered tabletop cursor" style="width:100%;vertical-align: top;line-height:2">
			<tr>
				<td id="content_edit_prest<?= $table_value ?>" class="inputFull noborder" style="min-height:24px;">
					<?= empty($ARR['descriptionDevis_prestation']) ? '' : $ARR['descriptionDevis_prestation'] ?>
				</td>
				<td style="width:40px;" class="borderr">
					<input id="quantite<?= $uniqkey ?>" name="vars[quantiteDevis_prestation]" value="<?= $ARR['quantiteDevis_prestation'] ?>" type="text" class="inputTiny noborder">
				</td>
				<td style="width:80px;">
					<input id="prix<?= $uniqkey ?>" name="vars[prixDevis_prestation]" value="<?= $ARR['prixDevis_prestation'] ?>" type="text" class="inputTiny noborder">
				</td>
				<td style="width:80px;">
					<input total="total" iddevis="<?= $iddevis ?>" id="total<?= $uniqkey ?>" name="vars[totalDevis_prestation]" value="<?= $ARR['totalDevis_prestation'] ?>" type="text" class="inputTiny noborder">
				</td>
				<td style="width:40px;">
					<a onclick="act_chrome_gui('app/app/app_delete','table=<?= $table ?>&table_value=<?= $table_value ?>')"><i class="fa fa-times textrouge"></i></a>
				</td>
			</tr>
		</table>
	</form>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, $$, .on, .up, .each, .readAttribute, .writeAttribute,
	 * .add/removeClassName) to native DOM, with file-local `dpu_` helpers.
	 *
	 * Every .on() here is two-argument with no selector, so none of them was
	 * ever delegation — the shim fell through to Event.observe. Plain listeners
	 * keep `this` on the element, which the contenteditable handler reads.
	 */
	function dpu_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function dpu_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	var timer_prest
	dpu_el ('form<?=$table.$table_value?>').addEventListener ('keyup', function () {
		if ( timer_prest ) clearTimeout (timer_prest);
		timer_prest = setTimeout (function () {
			var totDevis                   = 0;
			a                              = dpu_el ('quantite<?=$uniqkey?>').value.replace (' ', '', 'gi');
			b                              = dpu_el ('prix<?=$uniqkey?>').value.replace (' ', '', 'gi');
			tot                            = (eval (a) || 0) * (eval (b) || 0 )
			dpu_el ('total<?=$uniqkey?>').value = tot;
			// writeAttribute({value: tot}) — the attribute, not the property;
			// the line above already set the property.
			dpu_el ('total<?=$uniqkey?>').setAttribute ('value', tot)

			// The iddevis value is numeric, so [iddevis=123] is invalid CSS —
			// an attribute value must be an identifier or a quoted string, and
			// identifiers cannot start with a digit. The shim's $$ retried with
			// quotes added; native querySelectorAll throws. Quoted at source.
			document.querySelectorAll ('[total][iddevis="<?=$iddevis?>"]').forEach (function (node) {
				//	console.log(node,node.value)
				totDevis += eval (node.value) || 0;
				//	console.log(totDevis,node,node.value)
			})

			ajaxValidation ('app_update', 'mdl/app/', 'table=devis&table_value=<?=$iddevis?>&vars[prixDevis]=' + totDevis)
		}, 1000)

	});
	dpu_el ('content_edit_prest<?=$table_value?>').addEventListener ('click', function () {
		dpu_el ('content_edit_prest<?=$table_value?>').classList.remove ('cursor');
		dpu_up (dpu_el ('content_edit_prest<?=$table_value?>'), '[draggable]').setAttribute ('draggable', 'false');
		;
		if ( !this.getAttribute ('contenteditable') ) dpu_el ('content_edit_prest<?=$table_value?>').setAttribute ('contenteditable', 'true')
	})
	dpu_el ('content_edit_prest<?=$table_value?>').addEventListener ('blur', function () {
		dpu_el ('content_edit_prest<?=$table_value?>').classList.add ('cursor');
		var desc = dpu_el ('content_edit_prest<?=$table_value?>').innerHTML;
		// desc = desc.escapeHTML();
		dpu_el ('description_<?=$table.$table_value?>').value = desc
		ajaxValidation ('app_update', 'mdl/app/', 'table=<?=$table?>&table_value=<?=$table_value?>&vars[descriptionDevis_prestation]=' + desc)
		dpu_el ('content_edit_prest<?=$table_value?>').removeAttribute ('contenteditable');
		dpu_up (dpu_el ('content_edit_prest<?=$table_value?>'), '[draggable]').setAttribute ('draggable', 'true');
		;
	})
	dpu_el ('form<?= $table . $table_value ?>').addEventListener ('click', function (event) {
		console.log (event);

	})
	dpu_el ('form<?= $table . $table_value ?>').addEventListener ('blur', function (event) {
		console.log (event);
	})
</script>