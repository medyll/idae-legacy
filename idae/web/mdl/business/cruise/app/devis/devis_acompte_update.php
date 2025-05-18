<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 25/02/2016
	 * Time: 17:46
	 */

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($_POST['table']);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];

	//
	$APP             = new App($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	//
	$_table = 'devis_acompte_type';
	$_Table = ucfirst($_table);
	$_id    = 'id' . $_table;
	$_nom   = 'nom' . $_Table;
	//
	$APP_TYPE = new App($_table);
	$rsA = $APP_TYPE->find()->sort(array('ordre'.$_Table => 1));
	$selectA = fonctionsProduction::getSelectMongo('vars['.$_id.']', $rsA, $_id, $_nom, $ARR[$_id]);
?>
<div class="padding borderb">
	<form id="formacco<?=$table_value?>" class="Form" action="<?= ACTIONMDL ?>app/actions.php" onchange="ajaxFormValidation(this);return false;">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<table class="table table-bordered tabletop" style="width:100%;vertical-align: top;line-height:2">
			<tr>
				<td style="width:120px;" class="borderr">
					<input name="vars[dateDevis_acompte]" value="<?=date_fr( $ARR['dateDevis_acompte']) ?>" type="text" class="validate-date-au inputSmall noborder">
				</td>
				<td style="width:120px;">
					<input name="vars[prixDevis_acompte]" value="<?= maskNbre($ARR['prixDevis_acompte'],2) ?>" type="text" class="inputSmall noborder">
				</td>
				<td  style="width:120px;">
					<?=$selectA?>
				</td>
				<td class="alignright" >
					<a onclick="act_chrome_gui('app/app/app_delete','table=<?= $table ?>&table_value=<?= $table_value ?>')"><i class="fa fa-times textrouge"></i></a>
				</td>
			</tr>
		</table>
	</form>
</div>
<script>
	$('formacco<?=$table_value?>').on('dom:act_change',function(){ajaxFormValidation($('formacco<?=$table_value?>'))})
</script>