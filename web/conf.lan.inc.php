<?php

	if (!ini_get('date.timezone')) {
		date_default_timezone_set('GMT');
	}
	ini_set('short_open_tag', 'On');
	ini_set('scream.enabled', false);

	!defined('SOCKET_EMSGSIZE') && DEFINE('SOCKET_EMSGSIZE', 4000000);

	$HTTP_PREFIX = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	//
	// ini_set('session.save_path','/var/www/idaertys.mydde.fr/tmp/') ;
	//
	setlocale(LC_CTYPE, 'fr_FR.UTF-8');
	//
	global $IMG_SIZE_ARR, $buildArr;
	//
	$host      = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	$host = explode(':', $host)[0];

	$host_name = explode('.',$_SERVER['HTTP_HOST'])[0];
	//
	$host_parts = explode('.', $_SERVER['HTTP_HOST']);
	if ('lan' === end($host_parts)) {
		//ini_set('display_errors', 55);
		DEFINE('ENVIRONEMENT', 'PREPROD');
		DEFINE('SITEPATH', 'D:\boulot\wamp64\www\idae.preprod.lan\web\\');
		DEFINE('APPPATH', 'D:\\boulot\\wamp64\\www\\idae.preprod.lan\\');
	} else {
		die(".lan include only");
	}
	DEFINE('CUSTOMERPATH', 'D:\boulot\wamp64\www\idae.preprod.lan\web\\');
	// SESSION
	DEFINE("SESSION_PATH", APPPATH . 'sessions/');
	DEFINE("COOKIE_PATH", APPPATH . '/cookies/');
	set_include_path(get_include_path() . ':' . APPPATH . '/web');
	//
	DEFINE('DOCUMENTROOT', APPPATH . '/web/');
	DEFINE('APPCONFDIR', APPPATH . '/web/appconf/');

	DEFINE('CONFINC', APPPATH . '/web/conf.inc.php');
	DEFINE('ACTIVEMODULEFILE', APPPATH . '/web/services/json_data_event.txt');
	//
	DEFINE('APPNAME', 'idae-'.$host_name.'-lan');
	DEFINE('CUSTOMERNAME', $host_name);

	DEFINE('APPBIN', APPPATH . 'web/bin/');
	DEFINE('APP_CONFIG_DIR', APPBIN . 'config/'); // auto creation of metier
	DEFINE('APPMDL', APPPATH . 'web/mdl/');
	DEFINE('APPLESS', 'appcss/');
	DEFINE('APPTPL', APPPATH . 'web\\tpl\\app\\');
	DEFINE('APPBINTPL', APPPATH . 'web/bin/templates/app/');
	DEFINE('PATHTMP', APPPATH . '/tmp/');
	DEFINE('ADODBDIR', APPPATH . '/web/adodb/');
	DEFINE('REPFONCTIONS_APP', APPPATH . '/web/appfunc/');
	DEFINE('XMLDIR', APPPATH . 'web/xmlfiles/');
	//
	DEFINE("APPCLASSES", APPPATH . "web/appclasses/");
	DEFINE("APPBINCLASSES", APPPATH . "web/bin/classes/");
	DEFINE("APPCLASSES_APP", APPPATH . "web/bin/classes_app/");
	DEFINE("OLDAPPCLASSES", APPPATH . "web/classes/");
	DEFINE('REPFONCTIONS', APPPATH . 'appfunc/');

	DEFINE('DOCUMENTDOMAIN', $host);
	DEFINE('DOCUMENTDOMAINNOPORT', $host);
	DEFINE('DOCUMENTDOMAINPORT', '');
	DEFINE('HTTPCUSTOMERSITE', $HTTP_PREFIX . $host . '/');
	DEFINE('HTTPAPP', $HTTP_PREFIX . $host . '/');
	DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
	DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
	DEFINE('SOCKETIO_PORT', 3005);
	//
	//
	DEFINE('HTTPHOST', $HTTP_PREFIX . DOCUMENTDOMAIN);
	DEFINE('HTTPHOSTNOPORT', $HTTP_PREFIX . DOCUMENTDOMAINNOPORT);
	DEFINE('NAMESITE', DOCUMENTDOMAIN);
	DEFINE('MAINSITEHOST', HTTPHOST);
	DEFINE('ACTIONMDL', $HTTP_PREFIX . DOCUMENTDOMAIN . '/mdl/');
	DEFINE('HTTPCSS', $HTTP_PREFIX . DOCUMENTDOMAIN . '/css/');
	DEFINE('HTTPMDL', $HTTP_PREFIX . DOCUMENTDOMAIN . '/mdl/');
	DEFINE('HTTPJAVASCRIPT', $HTTP_PREFIX . DOCUMENTDOMAIN . '/javascript/');
	DEFINE('HTTPIMAGES', $HTTP_PREFIX . DOCUMENTDOMAIN . '/images/');
	DEFINE('ICONPATH', 'images/icones/');

	DEFINE('PATH', DOCUMENTROOT);
	DEFINE('PATHICC', DOCUMENTROOT . 'icc/');
	DEFINE('APPROOT', DOCUMENTROOT);
	//
	switch ($host) {
		case "tactac.idae.preprod.lan":

			DEFINE('BUSINESS', 'foodlivery');
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "tactac_"); // crfr_ // maw_ // idaenext_
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			//
			// TAILLE DES IMAGES !!!
			$IMG_SIZE_ARR = ['square' => ['150', '150'], 'tiny' => ['150', '70'], 'small' => ['300', '200'], 'long' => ['1100', '100'], 'large' => ['1100', '350'], 'wallpaper' => ['1920', '1080']];
			$buildArr     = ['tinyy'      => [50, 25],
			                 'tiny'       => [150, 70],
			                 'smally'     => [68, 68],
			                 'squary'     => [70, 70],
			                 'largy'      => [325, 215],
			                 'largey'     => [325, 215],
			                 'wallpapery' => [100, 25]
			];

			break;
		case "maw.idae.preprod.lan":

			DEFINE('BUSINESS', 'cruise');
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "maw_");
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			DEFINE('HTTPEXTERNALCUSTOMERSITE', 'https://maw.lan');
			// TAILLE DES IMAGES !!!
			$IMG_SIZE_ARR = ['square' => ['150', '150'], 'tiny' => ['150', '70'], 'small' => ['300', '200'], 'long' => ['1100', '100'], 'large' => ['1100', '350'], 'wallpaper' => ['1920', '1080']];
			$buildArr     = ['tinyy'      => [50, 25],
			                 'tiny'       => [150, 70],
			                 'smally'     => [68, 68],
			                 'squary'     => [70, 70],
			                 'largy'      => [325, 215],
			                 'largey'     => [325, 215],
			                 'wallpapery' => [100, 25]
			];

			break;
		case "leasys.idae.preprod.lan":

			DEFINE('BUSINESS', 'commercial');
			//
			DEFINE("SQL_HOST", "localhost");
			DEFINE("SQL_BDD", "crm_general_new");
			DEFINE("SQL_USER", "root");
			DEFINE("SQL_PASSWORD", "redPoi654pied");
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "idaenext_"); // crfr_ // maw_ // idaenext_
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			//
			break;
					case "crfr.idae.preprod.lan":

			DEFINE('BUSINESS', 'commercial');
			//
			DEFINE("SQL_HOST", "localhost");
			DEFINE("SQL_BDD", "crm_general_new");
			DEFINE("SQL_USER", "root");
			DEFINE("SQL_PASSWORD", "redPoi654pied");
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "crfr_"); // crfr_ // maw_ // idaenext_
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			//
			break;
		case "idae.io.idae.preprod.lan":

			DEFINE('BUSINESS', 'communicationdata');
			//
			DEFINE("SQL_HOST", "localhost");
			DEFINE("SQL_BDD", "crm_general_new");
			DEFINE("SQL_USER", "root");
			DEFINE("SQL_PASSWORD", "redPoi654pied");
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "idae_io_");
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			//
			break;
		case "blog.idae.preprod.lan":

			DEFINE('BUSINESS', 'blog');
			//
			DEFINE("SQL_HOST", "localhost");
			DEFINE("SQL_BDD", "crm_general_new");
			DEFINE("SQL_USER", "root");
			DEFINE("SQL_PASSWORD", "redPoi654pied");
			//
			DEFINE("MDB_HOST", "127.0.0.1");
			DEFINE("MDB_USER", "admin");
			DEFINE("MDB_PASSWORD", "gwetme2011");
			DEFINE("MDB_PREFIX", "appblog_");
			//
			DEFINE('SMTPHOST', 'mail.mydde.fr');
			DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASS', 'malaterre654');

			DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
			DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
			DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
			DEFINE('SMTPPASSGED', 'malaterre654');

			//
			break;
		default :
			die('please define host');
			break;
	}

	DEFINE('APPSITE', $host);

	require_once(REPFONCTIONS_APP . "function_prod.php");
	require_once(REPFONCTIONS_APP . "function.php");
	require_once(REPFONCTIONS_APP . "function_site.php");

	require_once(REPFONCTIONS_APP . "fonctionsDevis.php");
	require_once(REPFONCTIONS_APP . "fonctionsJs.php");
	include_once(REPFONCTIONS_APP . 'phpthumb/ThumbLib.inc.php');

	//
	if (file_exists(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php')) {
		include_once(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php');
	}
	// skel
	include_once('vendor/autoload.php');

	include_once(APPPATH . "web/appskel/skelStrap.php");
	/*include_once(APPCLASSES . "ClassAct.php");
	include_once(APPCLASSES . "ClassApp.php");*/

	//
	$IMG_SIZE_ARR = !empty($IMG_SIZE_ARR) ? $IMG_SIZE_ARR : ['square' => ['120', '120'], 'small' => ['210', '140'], 'large' => ['650', '430'], 'wallpaper' => ['1920', '1080']];
	$buildArr     = !empty($buildArr) ? $buildArr : ['tiny' => ['100', '25'], 'squary' => ['68', '68'], 'largy' => ['325', '215'], 'wallpapery' => ['100', '25']];
	//

	if (!function_exists("my_autoloader")) {
		function my_autoloader($class_name) {
			// echo APPCLASSES . '/appcommon/Class' . $class_name . '.php';
			$dirs = array(
				APPCLASSES,
				APPCLASSES . '/appcommon/',
				APPBIN . '/classes/shared/',
				OLDAPPCLASSES,
				APPBINCLASSES
			);
			foreach($dirs as $directory){
				//see if the file exsists
				if(file_exists($directory.'Class' . $class_name . '.php')){
					require($directory.'Class' . $class_name . '.php');
					return true;
				}
			}
			$folder     = APPCLASSES;
			$class_name = ltrim($class_name, '\\');
			$fileName   = '';

			if ($lastNsPos = strripos($class_name, '\\')) {
				$namespace  = substr($class_name, 0, $lastNsPos);
				$class_name = substr($class_name, $lastNsPos + 1);
				$fileName   = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
			}
			$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
			if (file_exists($folder . $fileName)) require ($folder . $fileName);
			else{
				$name_class =  "\\$class_name";
				return new $name_class ;
			}
		}

		spl_autoload_register('my_autoloader');
	}

	// SESSIONS
	include_once('appclasses/ClassSession.php');

	//
/* 	if (!function_exists(myddeDebug)) {
		function myddeDebug($vars) {
			echo "<pre>";
			var_dump($vars);
			echo "</pre>";
		}
	} */
	/*$APP->init_scheme('sitebase_devis', 'client_type');*/

