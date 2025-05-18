<?
	include_once($_SERVER['CONF_INC']);

	$type_cron = $_GET['type_cron'];

	$options = [];

	switch ($type_cron) {
		case "minute":

			break;
		default:
			//$options['sticky'] = false;
			break;
	}

	$file_name_native   = __DIR__ . '/cron_' . $type_cron . '.php';
	$file_name_business = APPPATH . 'web/mdl/business/' . BUSINESS . '/cron/cron_' . $type_cron . '.php';// APPPATH
	$file_name_customer = APPPATH . 'web/mdl/customer/' . CUSTOMERNAME . '/cron/cron_' . $type_cron . '.php';// APPPATH

	session_id('cron_runner');


	if (file_exists($file_name_native)) {
		include($file_name_native);
	}else{
		write_cron_file($file_name_native);
	};
	if (file_exists($file_name_business)) {
		include($file_name_business);
	}else{
		write_cron_file($file_name_business);
	};
	if (file_exists($file_name_customer)) {
		include($file_name_customer);
	}else{
		write_cron_file($file_name_customer);
	};

	function write_cron_file($path){
		$arr_path = explode('/',$path);
		array_pop($arr_path);
		$str_path = implode('/',$arr_path);

		@mkdir( $str_path, 0777, true );
		$fp = fopen($path,'w');
		@fwrite($fp,"<?php // file for cron ");
		@fclose($fp);
	}