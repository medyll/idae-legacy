<?php
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
				<?php while ($arrDev = $rsDev->getNext()) { ?>
					<label class = "autoToggle"
					       value = "zebre block"
					       onclick = "amore('vars[iddevis]=<?= $arrDev['iddevis'] ?>')">
						<input type = "radio"
						       name = "vars[iddevis]"
						       value = "<?= $arrDev['iddevis'] ?>">
						Devis <?= $arrDev['iddevis'] . ' ' . $arrDev['nomClient'] ?></label>
				<?php } ?>
				<?php while ($arrCli = $rsCli->getNext()) {
					?>
					<label class = "autoToggle"
					       value = "zebre block"
					       onclick = "amore('vars[idclient]=<?= $arrCli['idclient'] ?>')">
						<input type = "radio"
						       name = "vars[idclient]"
						       value = "<?= $arrCli['idclient'] ?>">
						<?= '<strong>' . strtoupper($arrCli['nomClient']) . '</strong> ' . strtolower($arrCli['prenomClient']) . ' ' . $arrCli['idclient'] ?></label>
				<?php } ?>
				<div class = "bordert"
				     id = "moredst"></div>
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS `$` shim, and fixed a
	 * syntax error that made this block unparseable: the module path read
	 * 'app_document/app_document'_data_select_more — a string literal followed
	 * by an identifier. amore() was therefore never defined and every caller
	 * threw ReferenceError. Same misplaced quote as document_liste_drop.php and
	 * document_liste_spy.php; predates the idae-be migration.
	 */
	amore = function (vars) {
		//document.getElementById('moredst').toggleContent();
		document.getElementById('moredst').loadModule ('app_document/app_document_data_select_more', vars);
	}
</script>