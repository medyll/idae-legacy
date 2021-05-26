<?
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
// onrecoit id client ou idautre chose
	$arr_iddevis = skelMongo::connect('devis', 'sitebase_devis')->distinct('iddevis', $vars + array('est_signe' => 1));
	$arr_iddevis = array_unique($arr_iddevis);
	$rs_devis = skelMongo::connect('devis_marge', 'sitebase_devis')->find(array('iddevis' => array('$in' => $arr_iddevis)));
	$arr_client = skelMongo::connect('devis', 'sitebase_devis')->distinct('idclient',array('iddevis' => array('$in' => $arr_iddevis)));
	$rs_client = skelMongo::connect('client', 'sitebase_devis')->find(array('idclient' => array('$in' => $arr_client)));
	$rsPrestataire = skelMongo::connect('devis_marge', 'sitebase_devis')->find(array('iddevis' => array('$in' => $arr_iddevis)))->sort(array('ordreMarge' => 1));
	$colPrest = skelMongo::connect('devis_marge', 'sitebase_devis')->distinct('iddevis', array('iddevis' => array('$in' => $arr_iddevis)));
//
?>
	<div class = "padding aligncenter">
		<li class = "fa fa-caret-down"></li>
	</div>
<? if (empty($vars['iddevis'])): ?>
	<? while ($arr_devis = $rs_devis->getNext()) { ?>
		<label class = "autoToggle block"
		       style = "display: block;">
			<input type = "radio"
			       name = "vars[iddevis]"
			       value = "<?= $arr_devis['iddevis'] ?>">
			<?= ' devis ' . $arr_devis['iddevis'] ?>
		</label>
		</label>
	<? } ?>
<? endif; ?>
<? if (empty($vars['idclient'])): ?>
	<? while ($arr_client = $rs_client->getNext()) { ?>
		<label class = "autoToggle block"
		       style = "display: block;">
			<input type = "radio"
			       name = "vars[idclient]"
			       value = "<?= $arr_client['idclient'] ?>">
			<?= ' Client ' . $arr_client['idclient'].' '.$arr_client['nomClient'] ?>
		</label>
	<? } ?>
<? endif; ?>
	<div class = "padding aligncenter bordert">
		<li class = "fa fa-caret-down"></li>
	</div>
<? while ($arrPrest = $rsPrestataire->getNext()) { ?>
	<label class = "autoToggle block"
	       style = "display: block;">
		<input type = "radio"
		       name = "vars[idprestataire]"
		       value = "<?= $arrPrest['idprestataire'] ?>">
		<?= 'prestataire <strong>' . strtoupper($arrPrest['nomPrestataire']) . '</strong> Dossier ' . strtolower($arrPrest['numeroDossierDevis']) . ' devis ' . $arrPrest['iddevis'] ?>
	</label>
<? } ?>