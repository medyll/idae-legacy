<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 17/11/2015
	 * Time: 11:19
	 */
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP = new App();



	$APP          = new App('appscheme_icon');
	$test_install = $APP->find()->count();
	$vars         = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$table        = $_POST['table'];
	$field_name   = $_POST['field_name_raw'] . ucfirst($_POST['table']);


  if (empty($_POST['run'])) { ?>
	<input id="icon_ch_<?= $table ?>" type="hidden" value="<?= $_POST['field_value'] ?>" name="vars[<?= $field_name ?>]">
	<div class="flex_h flex_align_middle">
		<div class="borderr ellipsis"><i id="fa_for_fa" class="fa fa-fw fa-<?= $_POST['field_value'] ?>"></i></div>
		<input datalist_input_name="vars[<?= $_id ?>]"
		       id="fa_icon_choose"
		       datalist="app/app_select"
		       datalist_input_value=""
		       populate
		       name="vars[<?= $_nom ?>]"
		       paramName="search"
		       value="<?= $_POST['field_value'] ?>"
		       vars="run=1&table=appscheme_icon"
		       class="inputMedium"/>
	</div>
	<progress id="progress_fa" style="display:none;"></progress>
	<?
} ?>
<script>
	$('fa_icon_choose').observe('dom:act_change', function (event, node) {
		$('fa_for_fa').className = 'fa fa-' + event.memo.code
		$('icon_ch_<?= $table  ?>').value = event.memo.code
	})
</script>


