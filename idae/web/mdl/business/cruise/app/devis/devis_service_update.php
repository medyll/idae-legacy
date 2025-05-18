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
	$field       = empty($_POST['field']) ? 'inclus' : $_POST['field'];

	if (file_exists(APPMDL . 'customer/' . CUSTOMERNAME . '/app/app_update.php')) {
		// echo skelMdl::cf_module('customer/'.CUSTOMERNAME.'/app/app_update', $_POST);

		// return;
	}

	//
	$APP = new App($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	//
	$uniqkey = $field . $Table . $table_value;

	if ($field == 'inclus' && empty($ARR[$field . $Table])) {

	}
?>
<div class="padding borderu">
	<form id="form<?= $field . $table . $table_value ?>" class="Form" action="<?= ACTIONMDL ?>app/actions.php" onchange="ajaxFormValidation(this);return false;">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<input type="hidden" id="input_<?= $uniqkey ?>" name="vars[<?= $field . $Table ?>]" value="<?= $ARR[$field . $Table] ?>"/>
		<div id="<?= $uniqkey ?>" class="inputFull cursor" style="min-height:100px;">
			<?= empty($ARR[$field . $Table]) ? '&nbsp;' : nl2br($ARR[$field . $Table]) . '&nbsp;' ?>
		</div>
	</form>
	<br>
</div>
<script>
	$ ('<?=$uniqkey?>').on ('click', function () {
		$ ('<?=$uniqkey?>').removeClassName ('cursor');
		if ( !this.readAttribute ('contenteditable') ) $ ('<?=$uniqkey?>').setAttribute ('contenteditable', 'true')
	})
	$ ('<?=$uniqkey?>').on ('blur', function () {
		$ ('<?=$uniqkey?>').addClassName ('cursor');
		$ ('input_<?=$uniqkey?>').value = $ ('<?=$uniqkey?>').innerHTML;
		ajaxFormValidation ($ ('form<?=$field. $table . $table_value ?>'));
		//ajaxValidation('app_update', 'mdl/app/', 'table=<?=$table?>&table_value=<?=$table_value?>&vars[<?=$field.$Table?>]=' + $('<?=$uniqkey?>').innerHTML)
		$ ('<?=$uniqkey?>').removeAttribute ('contenteditable');
	})
</script>