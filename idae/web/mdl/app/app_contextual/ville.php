<?
	if (file_exists('../conf.inc.php'))
		include_once('../conf.inc.php');
	if (file_exists('../../conf.inc.php'))
		include_once('../../conf.inc.php');
	$time = time();
	$idville = (int)$_POST['idville'];
	//$arr = skelMongo::connect('ville')->findOne(array('idville' => $idville));
?>
<div class = "applink applinkblock">
	<a act_chrome_gui="app/app_custom/app_custom_ville_map" vars="idville=<?=$idville?>"><img src = "<?= ICONPATH ?>thumb16.png"/> Voir
	                                             carte</a>

</div>