<?
	include_once($_SERVER['CONF_INC']);
	$App = new App('client');
	$time = time();
	$idclient = (int)$_POST['table_value'];
	$arr = $App->findOne(array('idclient' => (int)$idclient));
?>
<div class = "applink applinkblock ededed">
	<a onClick = "<?= fonctionsJs::client_big_fiche($idclient); ?>"><i class="fa fa-expand textorange"></i> <?= idioma('Vue globale').' '. $arr['nomClient'] ?></a>
</div>