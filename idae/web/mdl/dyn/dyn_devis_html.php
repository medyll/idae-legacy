<?
	include_once($_SERVER['CONF_INC']);

	$tpl_path = 'customer/' . CUSTOMERNAME . '/devis/devis_agent';;
	$APP = new App('devis');

	$APP_TARIF         = new App('produit_tarif');
	$APP_CLIENT        = new App('client');
	$APP_PRODUIT       = new App('produit');
	$APP_PRODUIT_ETAPE = new App('produit_etape');
	$APP_AGENT         = new App('agent');

	$time = time();

	$iddevis = (int)$_POST['iddevis'];

	ini_set('display_errors', 55);

	$time = time();
	$arrF = [];

	$arrDevis = $APP->query_one(['iddevis' => $iddevis]);
	$iddevis  = $arrF['iddevis'] = $_POST['iddevis'] = (int)$arrDevis['iddevis'];
	//
	$idclient              = (int)$arrDevis['idclient'];
	$idagent               = (int)$arrDevis['idagent'];
	$idproduit             = (int)$arrDevis['idproduit'];
	$idfournisseur         = (int)$arrDevis['produit']['idfournisseur'];
	$idproduit_tarif       = (int)$arrDevis['idproduit_tarif'];
	$idproduit_tarif_gamme = (int)$arrDevis['idproduit_tarif_gamme'];
	// 
	$arrClient      = $APP_CLIENT->findOne(['idclient' => (int)$idclient]);
	$arrProduit     = $APP_PRODUIT->findOne(['idproduit' => $idproduit]);
	$rsProduitEtape = $APP_PRODUIT_ETAPE->find(['idproduit' => $idproduit]);
	$arrAgent       = $APP_AGENT->findOne(['idagent' => $idagent]);
	$arrTarif       = $APP_TARIF->findOne(['idproduit_tarif' => $idproduit_tarif]);

	/** @var  $rsPrestation */
	$rsPrestation = $APP->plug('sitebase_devis', 'devis_prestation')->find(['iddevis' => $iddevis])->sort(['ordreDevis_prestation' => 1]);
	/** @var  $rsPassager */
	$rsPassager = $APP->plug('sitebase_devis', 'devis_passager')->find(['iddevis' => $iddevis]);
	/** @var  $rsEcheancier */
	$rsEcheancier = $APP->plug('sitebase_devis', 'devis_acompte')->find(['iddevis' => $iddevis])->sort(['dateDevis_acompte' => 1]);

	// $arrFour = $APP->plug('fournisseur')->findOne(array( 'idfournisseur' => (int)$idfournisseur ));
	$CGV = $arrFour['clauseFournisseur']['CGV']['descriptionFournisseur_clause'];
	//
	if (!empty($arrProduit['idtransport'])):
		$arrTransp                    = $APP->plug('sitebase_production', 'transport')->findOne(['idtransport' => $arrProduit['idtransport']]);
		$arrF['nomTransport']         = stripslashes($arrTransp['nomTransport']);
		$arrF['descriptionTransport'] = stripslashes(nl2br($arrTransp['descriptionTransport']));
		$arrF['nomTransport_type']    = stripslashes($arrTransp['nomTransport_type']);
		$arrF['imageTransport']       = Act::imgSrc('transport-small-' . $arrProduit['idtransport']);
	endif;

	if (!empty($arrDevis['idtransport_gamme'])) {
		$arrTRGamme        = $APP->plug('sitebase_production', 'transport_gamme')->findOne(['idtransport_gamme' => (int)$arrDevis['idtransport_gamme']]);
		$idtransport_gamme = (int)$arrDevis['idtransport_gamme'];
		$descriptionGamme  = $arrTRGamme['descriptionTransport_gamme'];
		$nomGamme          = $arrTRGamme['nomTransport_gamme'] . ' ' . $arrTRGamme['nomGamme'];
		//$arrF['imageCabine'] = Act::imgApp('transport_gamme' , $arrTRGamme['idtransport_gamme'] , 'small');
	} else {
		$descriptionGamme = '';
		$nomGamme         = $arrTarif['nomGamme'];
	}

	$arrLigneDevis = [];
	//
	//
	//
	$arrF['nomClient']         = $arrClient['nomClient'];
	$arrF['prenomClient']      = $arrClient['prenomClient'];
	$arrF['numeroDevis']       = $numeroDevis = $arrDevis['numeroDevis'];
	$arrF['dateCreationDevis'] = date_fr($arrDevis['dateCreationDevis']);
	$arrF['sexeClient']        = $arrClient['sexeClient'];
	$arrF['telephoneClient']   = maskTel($arrClient['telephoneClient']);
	$arrF['emailClient']       = $arrClient['emailClient'];
	$arrF['adresseClient']     = $arrClient['adresseClient'] . ' ' . $arrClient['adresse2Client'] . '<br>' . $arrClient['codePostalClient'] . ' ' . $arrClient['villeClient'];
	$arrF['emailClient']       = $arrClient['emailClient'];
	$arrF['texteDevis']        = nl2br($arrDevis['texteDevis']);
	$arrF['imageProduit']      = '';//fonctionsSite::imageProduit($idproduit , 'tiny');
	//
	$arrF['prenomAgent'] = $arrAgent['prenomAgent'];
	$arrF['emailAgent']  = $arrAgent['emailAgent'];
	//
	$arrF['inclusDevis']    = nl2br($arrDevis['inclusDevis']);
	$arrF['nonInclusDevis'] = nl2br($arrDevis['nonInclusDevis']);
	//
	$arrF['dateAcompte']  = date_fr($arrDevis['dateAcompte']);
	$arrF['sommeAcompte'] = $arrDevis['sommeAcompte'];
	$arrF['dateSolde']    = date_fr($arrDevis['dateSolde']);
	$arrF['sommeFinale']  = $arrDevis['sommeFinale'];
	//
	$arrF['nomProduit']          = stripslashes($arrProduit['nomProduit']);
	$arrF['nomVille']            = stripslashes($arrProduit['nomVille']);
	$arrF['nomVilleArrivee']     = stripslashes($arrProduit['nomVilleArrivee']);
	$arrF['dureeProduit']        = stripslashes($arrProduit['dureeProduit']);
	$arrF['dureeNuitProduit']    = stripslashes($arrProduit['dureeProduit'] - 1);
	$arrF['nomProduit_type']     = stripslashes($arrProduit['nomProduit_type']);
	$arrF['nomFournisseur_type'] = stripslashes($arrProduit['nomFournisseur_type']);
	$arrF['nomGammeDevis']       = $arrDevis['nomGammeDevis'];
	//
//	$arrF['imageDestination'] = Act::imgApp('destination' , $arrProduit['iddestination'] , 'small');
	//
	$arrF['nomFournisseur'] = stripslashes($arrProduit['nomFournisseur']);
	//$arrF['imageFournisseur'] = Act::imgApp('fournisseur' , $arrProduit['idfournisseur'] , 'square');
	$arrF['imageFournisseur'] = Act::imgSrc('fournisseur-small-' . $arrProduit['idfournisseur']);
	//
	$arrF['dateDebutProduit_tarif'] = date_fr($arrTarif['dateDebutProduit_tarif']); //date_fr($arrDepart['dateProduit_tarif']);
	//
	$arrF['prixProduit_tarif_gamme'] = $arrTarif['prixProduit_tarif_gamme'];
	$arrF['prixTotal']               = 0;
	$arrF['prixDevis']               = maskNbre($arrDevis['totalDevis']);
	//
	$arrClauseP = $arrProduit['grilleClauseProduit'];
	//
	$iti               = '';
	$arrSort           = [];
	$arrListeEtape     = [];
	$arrListeClause    = [];
	$arrF['has_hotel'] = '';

	//
	$arrF['inclusItineraireDevis'] = fonctionsSite::itineraireProduit($idproduit); //implode(', ',$arrListeEtape) ;
	//
	$arrF['texteProduit_clause'] = (sizeof($arrProduit['grilleClauseProduit']['ASAVOIR']) == 0) ? $arrASAVOIR['descriptionFournisseur_clause'] : $arrProduit['grilleClauseProduit']['ASAVOIR']['descriptionProduit_clause'];
	//
	$arrF['ligne_devis'] = '';
	$i_pass              = 0;
	while ($arrPrestation = $rsPrestation->getNext()) {
		$i_pass ++;
		unset($arrPrestation{"_id"});
		if (!empty($arrPrestation['prixDevis_prestation'])) {
			$arrPrestation['totalDevis_prestation'] = $arrPrestation['prixDevis_prestation'] * $arrPrestation['quantiteDevis_prestation']; //. ' &euro;';
			$arrF['prixDevis'] += $arrPrestation['totalDevis_prestation'];
			$arrPrestation['prixDevis_prestation'] = $arrPrestation['prixDevis_prestation']; //. ' &euro;';
		} else {
			$arrPrestation['prixDevis_prestation']  = '';
			$arrPrestation['totalDevis_prestation'] = '';
		}
		//
		/*if (!empty($arrPrestation['descriptionDevis_prestation'])) {
			$arrPrestation['nomDevis_prestation'] = $arrPrestation['descriptionDevis_prestation'];
			unset($arrPrestation['descriptionDevis_prestation']);
		}*/
		if (!empty($arrPrestation['prixDevis_prestation'])) {
			$arrPrestation['prixDevis_prestation'] = maskNbre($arrPrestation['prixDevis_prestation'], 2);
		}
		if (!empty($arrPrestation['totalDevis_prestation'])) {
			$arrPrestation['totalDevis_prestation'] = maskNbre($arrPrestation['totalDevis_prestation'], 2);
		}

		$arrF['ligne_devis'] .= skelTpl::cf_template($tpl_path, $arrPrestation, 'ligne_devis');
	}
	$i_pass = 0;
	$rsPrestation->reset();
	while ($arrPrestation = $rsPrestation->getNext()) {
		unset($arrPrestation{"_id"});
		/*if ( ! empty($arrPrestation['descriptionDevis_prestation']) ) {
			$i_pass ++;
			$outPresta['descriptionDevis_prestation'] = '(' . $i_pass . ') ' . $arrPrestation['descriptionDevis_prestation'];
			$arrF['ligne_devis'] .= skelTpl::cf_template('devis_agent' , $outPresta , 'ligne_devis');
		}*/
	}

	$arrF['ligne_passager'] = '';
	while ($arrPassager = $rsPassager->getNext()) {
		$arrLignePassager['nomPassager']       = $value['nomPassager'];
		$arrLignePassager['prenomPassager']    = $value['prenomPassager'];
		$arrLignePassager['naissancePassager'] = date_fr($value['naissancePassager']);
		$arrLignePassager['numeroDevis']       = $arrF['numeroDevis'];
		$arrF['ligne_passager'] .= skelTpl::cf_template($tpl_path, $arrLignePassager, 'ligne_passager');
	}

	// ACOMPTE
	$totalAcompte = 0;
	while ($arrAc = $rsEcheancier->getNext()):
		unset($arrAc['_id']);
		$arrAc['dateDebutDevis_acompte'] = date_fr($arrAc['dateDevis_acompte'], 2);
		$arrAc['prixDevis_acompte']      = maskNbre($arrAc['prixDevis_acompte'], 2);
		$arrF['ligne_acompte'] .= skelTpl::cf_template($tpl_path, $arrAc, 'ligne_acompte');
	endwhile;

	//
	/*$arrF['prixAnnul']    = pourcentage($arrF['prixTotal'] , 3.5) * $arrF['prixTotal'];
	$arrF['prixMulti']    = pourcentage($arrF['prixTotal'] , 5) * $arrF['prixTotal'];
	$arrF['prixTotalAss'] = $arrF['prixTotal'] + $arrF['prixAnnul'] + $arrF['prixMulti'];*/
	//
	$arrF['cgv_text'] = nl2br($CGV);
	//

	if (!empty($arrDevis['inclusCabineDevis'])) {
		$arrF['nomTransport_gamme']         = $nomGamme;
		$arrF['descriptionTransport_gamme'] = $descriptionGamme;
	}

	$content = skelTpl::cf_template($tpl_path, $arrF);
	$instyle = new InStyle();
	$content = $instyle->convert($content, true);
	$APP->update(['iddevis' => (int)$iddevis], ['htmlDevis' => $content]);

	skelMdl::reloadModule('business/cruise/app/devis/devis_preview_inner', $iddevis);


 
