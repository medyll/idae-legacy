<?php
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];

	$Idae        = new Idae($table);
	$ARR_COLLECT = $Idae->get_table_fields($table_value, $_POST);
	if ($Idae->has_field('adresse')) {
		$html_map_link = 'table=' . $table . '&table_value=' . $table_value;
	}
	if (!empty($_POST['hide_empty'])) {
		$ARR_COLLECT = array_filter($ARR_COLLECT,function($atr){return sizeof($atr['appscheme_fields']) !=0;});
	}
	echo AppTemplate::cf_template('app/app_fiche_fields/app_fiche_fields',array_merge($_POST,['ARR_FIELDS'=>$ARR_COLLECT]));
