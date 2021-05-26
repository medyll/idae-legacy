<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$table = $_POST['table'];

	$APP   = new App($table);
	//
	$table_main = empty($_POST['table_main']) ? $table : $_POST['table_main'];
	$table_from = empty($_POST['table_from']) ? $table : $_POST['table_from'];

	$vars      = empty($_POST['vars']) ? array() : $_POST['vars'];
	$vars      = fonctionsProduction::cleanPostMongo($vars, true);

	$APP_MAIN  = new App($table_main);

	$vars         = array_filter($vars);
	$filter       = ['id' . $table => ['$exists' => true]];
	$fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter + $vars); // seulement ceux étant dans item_main

	if ($table_from == $table):

	else:
		// $fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter+$vars); // seulement ceux étant dans item_main

	endif;

	///
	$rs = $APP->find(array('id' . $table => array('$in' => $fitered_data)));


	if(!empty($_POST['search_type'])):
		echo skelMdl::cf_module('app/app_search/search_item_'.$_POST['search_type'],$_POST);
		return;
		endif;
	if($rs->count()==0):
		 // return;
		endif;
	if($rs->count()>50):
		echo skelMdl::cf_module('app/app_search/search_item_text',$_POST);
		return;
	endif;
	echo skelMdl::cf_module('app/app_search/search_item_select',$_POST);

