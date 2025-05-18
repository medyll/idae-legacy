<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	$APP_DEV = new App('devis');
	$APP_CLI = new App('client');
	$APP_FAC = new App('facture');

	$time       = time();
	$iddevis    = (int)$_POST['iddevis'];
	$arr        = $APP_DEV->query_one(array('iddevis' => $iddevis));
	$idclient   = (int)$arr['idclient'];
	$arrClient  = $APP_CLI->query_one(array('idclient' => (int)$arr['idclient']));
	$arrFacture = $APP_FAC->query_one(array('iddevis' => $iddevis));

	$link =  "ajaxInMdl('business/".BUSINESS."/app/devis/devis_make','div_dev_upd_".$iddevis."','iddevis=$iddevis',{value: $iddevis ,ident:'dev_upd_$iddevis',onglet:'Devis ".$iddevis.' '.niceUrl($arrClient['prenomClient'].' '.$arrClient['nomClient'])."'});";

?>