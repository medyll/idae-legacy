<?
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	$_POST = fonctionsProduction::cleanPostMongo($_POST);
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];

	$arrSearchClient = explode(' ', trim($_POST['searchDataClient']));
	foreach ($arrSearchClient as $key => $value) {
		$outClient[] = MongoCompat::toRegex("/.*" . preg_quote((string)$arrSearchClient[$key], '/') . ".*/i");
	}
	$arrSearchDevis = explode(' ', trim($_POST['searchDataDevis']));
	foreach ($arrSearchDevis as $key => $value) {
		$outDevis[] = MongoCompat::toRegex("/.*" . preg_quote((string)$arrSearchDevis[$key], '/') . ".*/i");
	}
	// vardump($outDevis);
	$varsCli    = array('estClientClient' => 1, '$or' => array(array('idclient' => (int)$_POST['searchDataClient']), array('nomClient' => array('$in' => $outClient)), array('prenomClient' => array('$in' => $outClient))));
	$varsDevis  = array('$or' => array(array('iddevis' => (int)$_POST['searchDataDevis']), array('numeroDossierDevis' => (int)$_POST['searchDataDevis']), array('numeroFactureDevis' => (int)$_POST['searchDataDevis'])));
	// de varsdevis on prend distinct : fournisseur,devis,dossier,facture$rs
	$rsCli = skelMongo::connect('client', 'sitebase_devis')->find($varsCli, ['limit' => 10]);
	$rsDev = skelMongo::connect('devis', 'sitebase_devis')->find($varsDevis + ['est_signe' => 1], ['limit' => 10]);
?>
<div class = "padding">
	<div class = "">
		<div class = "">
			<div class = "applink applinkbig applinkblock toggler">
				<? while ($arrDev = $rsDev->getNext()) { ?>
					<label class = "autoToggle"
					       value = "zebre block"
					       onclick = "amore('vars[iddevis]=<?= $arrDev['iddevis'] ?>')">
						<input type = "radio"
						       name = "vars[iddevis]"
						       value = "<?= $arrDev['iddevis'] ?>">
						Devis <?= $arrDev['iddevis'] . ' ' . $arrDev['nomClient'] ?></label>
				<? } ?>
				<? while ($arrCli = $rsCli->getNext()) {
					?>
					<label class = "autoToggle"
					       value = "zebre block"
					       onclick = "amore('vars[idclient]=<?= $arrCli['idclient'] ?>')">
						<input type = "radio"
						       name = "vars[idclient]"
						       value = "<?= $arrCli['idclient'] ?>">
						<?= '<strong>' . strtoupper($arrCli['nomClient']) . '</strong> ' . strtolower($arrCli['prenomClient']) . ' ' . $arrCli['idclient'] ?></label>
				<? } ?>
				<div class = "bordert"
				     id = "moredst"></div>
			</div>
		</div>
	</div>
</div>
<script>
	amore = function (vars) {
		//$('moredst').toggleContent();
		$ ('moredst').loadModule ('app_document/app_document'_data_select_more', vars);
	}
</script>