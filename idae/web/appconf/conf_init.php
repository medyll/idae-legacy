<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 29/10/2016
	 * Time: 16:18
	 */

	include_once($_SERVER['CONF_INC']);

	$APP = new App('appscheme');

	if(empty($APP->app_table_one) || $APP->app_table_one == 'null'){
		echo HTTPAPP."conf_install.php";
		header("Location: ".HTTPAPP."appconf/conf_install.php");
		exit;
	}
	//return;
	//vardump($APP);
	$APP->init_scheme('sitebase_app', 'app_conf');
	$APP->init_scheme('sitebase_app', 'app_version', ['fields' => ['code', 'nom', 'date', 'dateCreation']]);
	$APP->init_scheme('sitebase_app', 'app_version_file', ['fields' => ['code', 'nom', 'date', 'heure', 'dateCreation', 'heureCreation']]);
	$APP->init_scheme('sitebase_app', 'app_version_file_preprod', ['fields' => ['code', 'nom', 'date', 'heure', 'dateCreation', 'heureCreation']]);
	$APP->init_scheme('sitebase_app', 'appscheme_view',['fields'=>['nom','code'],'grilleFK'=>['appscheme','appscheme_field']]);

	$APP->init_scheme('sitebase_pref', 'agent', ['fields' => ['nom', 'code', 'password', 'login', 'mailPassword']]);
	$APP->init_scheme('sitebase_base', 'agent_groupe_droit', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_base', 'agent_type', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_base', 'agent_note', ['fields' => ['nom', 'description'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_pref', 'agent_history', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_pref', 'agent_liste', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_pref', 'agent_recherche', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_pref', 'agent_table', ['fields' => ['nom', 'code'], 'grilleFK' => ['agent']]);
	$APP->init_scheme('sitebase_app',  'app_daemon', ['fields' => ['nom', 'code']]);

	$conf_module_dir   = 'conf_modules/';
	$conf_module       = APPCONFDIR . $conf_module_dir;
	$scanned_directory = array_diff(scandir($conf_module), ['..', '.']);

	foreach ($scanned_directory as $file) {
		$mdl = str_replace('.php', '', 'appconf/' . $conf_module_dir . $file);
		// skelMdl::run($mdl);
		include_once(($conf_module . $file));
	}