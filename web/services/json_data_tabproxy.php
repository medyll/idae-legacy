<?
	include_once($_SERVER['CONF_INC']);
	$_POST = array_merge($_GET, $_POST);

	skelMdl::runModule('services/json_data_table',$_POST);