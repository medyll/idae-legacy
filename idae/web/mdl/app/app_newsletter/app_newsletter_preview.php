<?php
/**
 * Created by PhpStorm.
 * User: lebru_000
 * Date: 17/02/15
 * Time: 00:33
 */
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);
	$APP = new App('newsletter');
	$APP_ITEM = new App('newsletter_item');

	$uniqid     =   uniqid();
	$_POST      =   fonctionsProduction::cleanPostMongo($_POST,1);
	$idnewsletter   =   $_POST['idnewsletter'];


	$arr        =   $APP->query_one(array('idnewsletter'=>(int)$idnewsletter));

	echo $arr['htmlNewsletter'];