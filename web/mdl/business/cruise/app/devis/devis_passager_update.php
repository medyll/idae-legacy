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
	$APP             = new App($table);

	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
?>
<div class="padding borderb">
	<form class="Form" action="<?= ACTIONMDL ?>app/actions.php" onchange="ajaxFormValidation(this);return false;">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<input type="hidden" name="reloadModule[<?= $path_to_devis ?>/devis_make_nav]" value="<?= $ARR['iddevis'] ?>"/>
		<table class="table table-bordered tabletop cursor" style="width:100%;vertical-align: top;line-height:2">
			<tr>
				<td style="width:120px;" class="borderr">
					<input name="vars[nomDevis_passager]" value="<?= $ARR['nomDevis_passager'] ?>" type="text" class="inputMedium noborder">
				</td>
				<td style="width:120px;" class="borderr">
					<input name="vars[prenomDevis_passager]" value="<?= $ARR['prenomDevis_passager'] ?>" type="text" class="inputSmall noborder">
				</td>
				<td style="width:120px;">
					<input name="vars[emailDevis_passager]" value="<?= $ARR['emailDevis_passager'] ?>" type="text" class="inputSmall noborder">
				</td>
				<td class="alignright">
					<a onclick="act_chrome_gui('app/app/app_delete','table=<?= $table ?>&table_value=<?= $table_value ?>')"><i class="fa fa-times textrouge"></i></a>
				</td>
			</tr>
		</table>
	</form>
</div>