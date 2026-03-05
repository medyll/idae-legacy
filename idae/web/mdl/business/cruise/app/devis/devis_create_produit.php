<?
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/'.BUSINESS.'/app/devis/' ;

	$APP_DV = new App('devis');
	$APP_PR = new App('produit');
	// var_dump($_POST);
	$time = time();
	ini_set('display_errors' , 55);
	if ( ! empty($_POST['idclient']) ) {
		$idclient           = (int)$_POST['idclient'];
		$rs = $APP_DV->query(array('idclient' => $idclient), 0, 1);
		$arrP = $rs->getNext();
		$_POST['idproduit'] = [ 'idproduit' ];
	}

	if ( ! empty($_POST['idproduit']) ) {
		$idproduit = $_POST['idproduit'];
	}
	$arrProduit = $APP_PR->query_one(array( 'idproduit' => (int)$idproduit ));
?>

<div class = " fond_bleu color_fond_bleu" >
	<button onclick="$('div_produit_liste_devis').unToggleContent();$('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', 'idproduit=' + idproduit)"><?=idioma('Choisir ce produit')?></button>
	<?= $idproduit ?>
	&nbsp;|&nbsp;
	<?= $arrProduit['nomProduit'] ?>
	<?= $arrProduit['nomFournisseur'] ?>
</div >
