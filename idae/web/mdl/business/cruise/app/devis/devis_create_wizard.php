<?
	include_once($_SERVER['CONF_INC']);

	$table = 'devis';
	$path_to_devis = 'business/'.BUSINESS.'/app/' . $table . '/' ;

	$APP_DV = new App('devis');
	$APP_PR = new App('produit');

	ini_set('display_errors' , 55);
	if ( ! empty($_POST['idclient']) ) {
		$idclient           = (int)$_POST['idclient'];
		$rs = $APP_DV->query(array('idclient' => $idclient), 0, 1);
		$arrP = $rs->getNext();
		$_POST['idproduit'] = $arrP['idproduit'];
	}

	if ( ! empty($_POST['idproduit']) ) {
		$arrProduit                        = $APP_PR->query_one(array( 'idproduit' => (int)$_POST['idproduit'] ));
		$nomProduit                        = $arrProduit['nomProduit'];
		$nomFournisseur                    = $arrProduit['nomFournisseur'];
		$nomTransport                      = $arrProduit['nomTransport'];
		$nomDestination                    = $arrProduit['nomDestination'];
		$nomProduit_type                   = $arrProduit['nomProduit_type'];
		$dureeProduit                  = $arrProduit['dureeProduit'];
		$nomVille                = $arrProduit['nomVille'];
		$extravars['idproduit_type']       = $arrProduit['idproduit_type'];
		$extravars['idfournisseur']        = $arrProduit['idfournisseur'];
		$extravars['idtransport']          = $arrProduit['idtransport'];
		$extravars['iddestination']        = $arrProduit['iddestination'];
		$extravars['dureeProduit']     = $arrProduit['dureeProduit'];
		$extravars['idville'] = $arrProduit['idville'];
	}
	if ( empty($_POST['idproduit']) ) {
		return;
	}
?>
<div class = "autoNext avoid" ><?= idioma('Suggestions') ?></div >
<div class="retrait">
<div class = "padding alignright" id = "uniqu_cw"   style = "display:none" ><a onclick = "$('div_create_devis_wizard').update()" ><i class="fa fa-times"></i>Annuler</a ></div >
<div onclick = "$('uniqu_cw').show();$('div_produit_liste_devis').toggleContent();load_table_in_zone(Form.serialize(this),'table_produit_devis_make');" >
	<input type="hidden" name="table" value="produit">
	<div class = "padding applink applinkblock" >
		<label class = "nolabel" >
			<input type = "checkbox" checked = "checked" name = "vars[idfournisseur]" value = "<?= $extravars['idfournisseur'] ?>" />
			<?= $nomFournisseur ?>
		</label >
		<label class = "nolabel" >
			<input type = "checkbox" checked = "checked" name = "vars[idtransport]" value = "<?= $extravars['idtransport'] ?>" />
			<?= $nomTransport ?>
		</label >
		<label class = "nolabel" >
			<input type = "checkbox" checked = "checked" name = "vars[iddestination]" value = "<?= $extravars['iddestination'] ?>" />
			<?= $nomDestination ?>
		</label >
		<label class = "nolabel" >
			<input type = "checkbox" checked = "checked" name = "vars[dureeProduit]" value = "<?= $extravars['dureeProduit'] ?>" />
			<?= $dureeProduit ?>
			jour</label >
		<label class = "nolabel" >
			<input type = "checkbox" checked = "checked" name = "vars[idville]" value = "<?= $extravars['idville'] ?>" />
			<?= $nomVille ?>
		</label >
	</div >
</div ></div>