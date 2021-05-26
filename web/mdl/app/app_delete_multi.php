<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 31/05/14
	 * Time: 16:18
	 */
	include_once($_SERVER['CONF_INC']);

	$time = time();
	$arr_id = $_POST['id'];
	$F_action = $_POST['F_action'];
	//
	$table = $_POST['table'];
	//
	//
?>

<div style="width:450px;">
	<div class="titre_entete fond_noir color_fond_noir">
		<i class="fa fa-question-circle"></i> <?= idioma('Supression de plusieurs éléments ') ?><?= $table ?>
	</div>


	<form action="<?= ACTIONMDL ?>app/actions.php"
		  onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">

		<input type="hidden"
			   name="F_action"
			   value="app_multi_delete"/><input type="hidden"
												name="table"
												value="<?= $table ?>"/>
		<? foreach ($arr_id as $value) { ?>
			<input name="arr_id[]"
				   type="hidden"
				   value="<?= $value ?>">
		<? } ?>
		<table>
			<tr>
				<td style = "width:90px;text-align:center"><br>
					<i class="fa fa-trash-o fa-5x fa-aligncenter" ></i> </td>
				<td class = "texterouge"><br>
					<?= idioma('Voulez vous supprimer') ?> <br>
					<?= sizeof($arr_id) ?> <?= $table ?> ?<br>
					<br></td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_progress_multi_delete" style="display:none;"></progress></div>

		<div class="buttonZone">
			<input type="submit"
				   value="Supprimer">
			<input type="reset"
				   value="Annuler"
				   class="cancelClose">
		</div>
	</form>
</div>