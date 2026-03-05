<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 29/10/2016
	 * Time: 16:18
	 */

	include_once($_SERVER['CONF_INC']);

	$APP = new App('appscheme');

	$appschemeCount = 0;
	try{
		// Count existing entity definitions; if zero, schema was never installed
		$appschemeCount = method_exists($APP->app_conn, 'countDocuments')
			? $APP->app_conn->countDocuments()
			: $APP->app_conn->count();
	}catch(Exception $e){
		$appschemeCount = 0;
		if (!empty(getenv('DEBUG_DB'))){
			error_log('[conf_init] appscheme count failed: '.$e->getMessage());
		}
	}

	if($appschemeCount === 0){
		if (!empty(getenv('DEBUG_DB'))) {
			header('Content-Type: text/plain; charset=UTF-8');
			$mongoHost = getenv('MONGO_HOST') ?: (defined('MDB_HOST') ? MDB_HOST : 'undefined');
			$mongoPrefix = getenv('MDB_PREFIX') ?: (defined('MDB_PREFIX') ? MDB_PREFIX : 'undefined');
			$mongoUser = getenv('MDB_USER') ?: (defined('MDB_USER') ? MDB_USER : '');
			$mongoPass = (getenv('MDB_PASSWORD') ?: (defined('MDB_PASSWORD') ? MDB_PASSWORD : '')) ? '***' : '';
			$mongoEnvHost = getenv('MONGO_HOST') ?: '';
			$sitebaseApp = $mongoPrefix . 'sitebase_app';
			$sitebaseSockets = $mongoPrefix . 'sitebase_sockets';
			$databaseName = method_exists($APP, 'get_database_name') ? $APP->get_database_name() : 'unknown';
			$appschemeCount = 0;
			try {
				$appschemeCount = $APP->app_conn->countDocuments();
			} catch (Exception $e) {
				$appschemeCount = 'error: ' . $e->getMessage();
			}
			echo "DB DEBUG\n";
			echo "MDB_HOST: {$mongoHost}\n";
			echo "MONGO_HOST env: {$mongoEnvHost}\n";
			echo "MDB_USER: {$mongoUser}\n";
			echo "MDB_PASSWORD: {$mongoPass}\n";
			echo "MDB_PREFIX: {$mongoPrefix}\n";
			echo "Expected DB (app): {$sitebaseApp}\n";
			echo "Expected DB (sockets): {$sitebaseSockets}\n";
			echo "Selected DB: {$databaseName}\n";
			echo "appscheme count: {$appschemeCount}\n";
			exit;
		}
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