<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 01/01/2016
	 * Time: 22:17
	 */
	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	$APP->init_scheme('sitebase_app','app_conf');
	$APP = new App('app_conf');

	/** @var  $arr_keys */
	$arr_keys = ['SITEPATH',
				'APPPATH',
				'APPNAME',
				'CUSTOMERNAME',
				'DOCUMENTDOMAIN',
				'DOCUMENTDOMAINNOPORT',
				'DOCUMENTDOMAINPORT',
				'HTTPCUSTOMERSITE',
				'CUSTOMERPATH',
				"SESSION_PATH",
				"COOKIE_PATH",
				'SOCKETIO_PORT',
				"SQL_HOST",
				"SQL_BDD",
				"SQL_USER",
				"SQL_PASSWORD",
				"MDB_HOST",
				"MDB_USER",
				"MDB_PASSWORD",
				"MDB_PREFIX",
				'SMTPHOST',
				'SMTPUSER',
				'SMTPEMAIL',
				'SMTPPASS'];

	foreach($arr_keys as $index=>$key):
		$value = defined($key) ? constant($key) : '';
		$APP->create_update(['codeApp_conf'=>$key],['nomApp_conf'=>strtolower($key),'valeurApp_conf'=>$value]);
	endforeach;