<!DOCTYPE html><?
	include_once($_SERVER['CONF_INC']);
	clearstatcache(true);
	$mtime = filemtime(DOCUMENTROOT . "javascript/");
	// var_dump(stat(DOCUMENTROOT."javascript/app/."));
	//  if (empty($_SESSION['reindex'])) header("Location: reindex.php"); ?>
<html >
<head>
	<title>APP <?= APPNAME ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
	<link rel="icon" type="image/png" href="favicon.png"/> 
</head>
<body id="body" style="background-size:cover;background-repeat: no-repeat;background-color:#333;height:100%;width:100%;overflow:hidden;max-height:100%"> 
 <iframe src="<?=HTTPCUSTOMERSITE?>?<?=http_build_query($_GET)?>" style="width:100%;height:100%;overflow:hidden;margin:0;padding:0;" border="0" marginheight="0" marginwidth="0"  ></iframe>
</body>
</html>
<style> 
	html{height:100%;width:100%;margin:0;padding:0;}
	body{height:100%;width:100%;margin:0;padding:0;}
</style>