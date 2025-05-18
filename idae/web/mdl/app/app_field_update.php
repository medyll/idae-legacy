<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 05/01/15
	 * Time: 01:19
	 */

	include_once($_SERVER['CONF_INC']);


	$field_name     = $_POST['field_name'];
	$field_name_raw = $_POST['field_name_raw'];
	$table          = $_POST['table'];
	$Table          = ucfirst($table);
	$table_value    = (int)$_POST['table_value'];
	$vars           = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$field_name_raw = empty($_POST['field_name_raw']) ? '' : $_POST['field_name_raw'];
	$field_name     = empty($_POST['field_name']) ? $field_name_raw . $Table : $_POST['field_name'];

	//
	$APP = new App($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$field_value     = empty($_POST['field_value']) ? $ARR[$field_name] : $_POST['field_value'];


?>
<form action="<?= ACTIONMDL ?>app/actions.php" auto_close="true"
      onsubmit="ajaxFormValidation(this);return false;" style="max-width:100%;">
	<input type="hidden"
	       name="F_action"
	       value="app_update"/>
	<input type="hidden"
	       name="table"
	       value="<?= $table ?>"/>
	<input type="hidden"
	       name="table_value"
	       value="<?= $table_value ?>"/>
	<input type="hidden"
	       name="scope"
	       value="<?= $id ?>"/>
	<input type="hidden"
	       name="<?= $id ?>"
	       value="<?= $table_value ?>"/>
	<div class="flex_h flex_align_middle">
		<div class="flex_main">
			<?= $APP->draw_field_input(['table'=>$table,'field_name_raw' => $field_name_raw,'field_value'=>$field_value]); ?>
		</div>
		<div class="flex_h flex_align_middle">
			<button type="submit" style="width:20px;border:none;" class="noborder blanc"><i class="fa fa-check textvert"></i></button>
			<button style="width:20px;" class="cancelHide cancelClose blanc noborder textrouge" ><i class="fa fa-ban"></i></button>
		</div>
	</div>
</form>