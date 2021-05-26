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
	var timer_prest
	$ ('form<?=$table.$table_value?>').on ('keyup', function () {
		if ( timer_prest ) clearTimeout (timer_prest);
		timer_prest = setTimeout (function () {
			var totDevis                   = 0;
			a                              = $ ('quantite<?=$uniqkey?>').value.replace (' ', '', 'gi');
			b                              = $ ('prix<?=$uniqkey?>').value.replace (' ', '', 'gi');
			tot                            = (eval (a) || 0) * (eval (b) || 0 )
			$ ('total<?=$uniqkey?>').value = tot;
			$ ('total<?=$uniqkey?>').writeAttribute ({ 'value' : tot })

			$$ ('[total][iddevis=<?=$iddevis?>]').each (function (node) {
				//	console.log(node,node.value)
				totDevis += eval (node.value) || 0;
				//	console.log(totDevis,node,node.value)
			}.bind (this))

			ajaxValidation ('app_update', 'mdl/app/', 'table=devis&table_value=<?=$iddevis?>&vars[prixDevis]=' + totDevis)
		}.bind (this), 1000)

	});
	$ ('content_edit_prest<?=$table_value?>').on ('click', function () {
		$ ('content_edit_prest<?=$table_value?>').removeClassName ('cursor');
		$ ('content_edit_prest<?=$table_value?>').up ('[draggable]').setAttribute ('draggable', 'false');
		;
		if ( !this.readAttribute ('contenteditable') ) $ ('content_edit_prest<?=$table_value?>').setAttribute ('contenteditable', 'true')
	})
	$ ('content_edit_prest<?=$table_value?>').on ('blur', function () {
		$ ('content_edit_prest<?=$table_value?>').addClassName ('cursor');
		var desc = $ ('content_edit_prest<?=$table_value?>').innerHTML;
		// desc = desc.escapeHTML();
		$ ('description_<?=$table.$table_value?>').value = desc
		ajaxValidation ('app_update', 'mdl/app/', 'table=<?=$table?>&table_value=<?=$table_value?>&vars[descriptionDevis_prestation]=' + desc)
		$ ('content_edit_prest<?=$table_value?>').removeAttribute ('contenteditable');
		$ ('content_edit_prest<?=$table_value?>').up ('[draggable]').setAttribute ('draggable', 'true');
		;
	})
	$ ('form<?= $table . $table_value ?>').on ('click', function (event) {
		console.log (event);

	})
	$ ('form<?= $table . $table_value ?>').on ('blur', function (event) {
		console.log (event);
	})
</script>