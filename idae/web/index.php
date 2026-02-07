<!DOCTYPE html>
<?php
	// phpinfo();
	// session_start();
	/*foreach (new DirectoryIterator('/') as $fileInfo) {
		if($fileInfo->isDot()) continue;
		echo $fileInfo->getFilename() . "<br>\n";
	}*/
 
	include_once($_SERVER['CONF_INC']);
	// die("Configuration loaded. CONF_INC included. You can now start the application.");

	// Redirect loop protection
	if (empty($_SESSION['reindex'])) {
		// die('Session reindex is empty - stopped to prevent infinite loop');
		// Check if we're already in redirect loop (max 3 redirects)
		if (isset($_GET['retry']) && (int)$_GET['retry'] >= 3) {
			die('<h1>Session Error</h1><p>Cannot initialize session. Check MongoDB connection and logs.</p>');
		}
		$retry = isset($_GET['retry']) ? (int)$_GET['retry'] + 1 : 1;
		header("Location: reindex.php?retry=$retry");
		exit;
	}
	// die("Before app_confdir include. Session reindex: " . $_SESSION['reindex']);
	// APPCONF_DIR
	include_once(APPCONFDIR . "conf_init.php");
	// die("Session initialized successfully. You can now access the application.");
?>
<html>
	<head>
		<title><?= APPNAME ?></title>
		<!--<meta http-equiv="Cache-control" content="public"/>-->
		<meta name="expires" content="tue, 01 Jun 2017 19:45:00 GMT"/>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
		<link rel="icon" type="image/png" href="favicon.ico"/>
		<!--<script type="riot/tag" src="appcomponents/datatable.tag"></script>-->
		<?  include_once('lessc.inc.php');  ?>
		<script src="javascript/vendor/bag.js" type="text/javascript"></script>
	</head>
	<body id="body" style="background-size:cover;background-repeat: no-repeat;background-color:#333;height:100%;width:100%;overflow:hidden;max-height:100%" class="skin_default">
		<div id="inBody" style="width:100%;height:100%;position:relative;z-index:0;overflow:hidden;"></div>
		<div id="log" class="blanc absolute" style="bottom:0"></div>
		<div id="msg_log" class="blanc absolute" style="display:none;top:20px;right:20px;"></div>
	</body>
	<script src="javascript/main_bag.js?v=<?=time()?>" type="text/javascript"></script>
	<link type='text/css' rel='stylesheet' href='css/fontawesome/css/font-awesome.css' >
</html>