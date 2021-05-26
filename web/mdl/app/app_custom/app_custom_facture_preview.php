<?
	include_once($_SERVER['CONF_INC']);

	$idfacture = (int)$_POST['idfacture'];
	$arr       = skelMongo::connect('facture', 'sitebase_devis')->findOne(array('idfacture' => (int)$idfacture));
	echo $arr['documentFacture'];
	return;;
