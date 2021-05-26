<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 30/12/14
	 * Time: 16:42
	 */
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', '55');
	set_time_limit(0);
	ini_set('max_execution_time', 0);
	ini_set('memory_limit', '3G');

	$session_id = $_POST['session_id'];

	if (empty($_POST['vars']["idxml_cruise"]) || empty($_POST['vars']["CruiseLine"]) || empty($_POST['vars']['idproduit']) || empty($_POST['vars']['ShipCode']) || empty($_POST['vars']['PackageId'])) {
		echo " du vide ";
		skelMdl::send_cmd('act_notify', array('msg' => 'vide'), $session_id);

		return;
	}

	//
	/** @var  $CruiseLine */
	/** @var  $idproduit */
	/** @var  $ShipCode */
	/** @var  $PackageId */
	/** @var  $idxml_cruise */

	$CruiseLine   = $_POST['vars']["CruiseLine"];
	$ShipCode     = $_POST['vars']['ShipCode'];
	$PackageId    = $_POST['vars']['PackageId'];
	$idproduit    = (int)$_POST['vars']['idproduit'];
	$idxml_cruise = (int)$_POST['vars']['idxml_cruise'];

	$APP = new App('feed_header');

	$APP_PRODUIT            = $APP->plug('sitebase_production', 'produit');
	$APP_ETAPE              = $APP->plug('sitebase_production', 'produit_etape');
	$APP_TARIF              = $APP->plug('sitebase_production', 'produit_tarif');
	$col_P_tarif_gamme      = $APP->plug('sitebase_production', 'produit_tarif_gamme');
	$APP_TRANSPORT_GAMME    = $APP->plug('sitebase_production', 'transport_gamme');
	$APP_TRANSPORT_CABINE   = $APP->plug('sitebase_production', 'transport_cabine');
	$APP_GAMME              = $APP->plug('sitebase_production', 'gamme');

	$APP_FOURNISSEUR = $APP->plug('sitebase_production', 'fournisseur');
	$APP_TRANSPORT   = $APP->plug('sitebase_production', 'transport');

	$APP_XML_DEST  = $APP->plug('sitebase_xml', 'xml_destination');
	$APP_XML_VILLE = $APP->plug('sitebase_xml', 'xml_ville');
	$col_I         = $APP->plug('sitebase_xml', 'xml_itinerary');
	$APP_XML_PRICE = $APP->plug('sitebase_xml', 'xml_price');
	$col_CRUISE    = $APP->plug('sitebase_xml', 'xml_cruise');

	$col_D = $APP->plug('sitebase_production', 'destination');
	$col_V = $APP->plug('sitebase_production', 'ville');

	/** @var  $ARR_P */
	$ARR_P         = $APP_PRODUIT->findOne(array('idproduit' => $idproduit));
	$idfournisseur = (int)$ARR_P['idfournisseur'];
	$idtransport   = (int)$ARR_P['idtransport'];

	// estActifFeed_header
	$arrApp = $APP->query_one(array('codeFeed_header' => $CruiseLine, 'estActifFeed_header' => 1)); // $query_vars
	if (empty($arrApp['idfournisseur'])) {
		skelMdl::send_cmd('act_notify', array('sticky' => true, 'msg' => 'Pas de fournisseur'), $session_id);

		return;
	}

	/** @var  $rs_price_vars */
	$rs_price_vars = array('idxml_cruise' => $idxml_cruise);// $idxml_cruise
	/** @var  $rs_price */
	$rs_price = $APP_XML_PRICE->find($rs_price_vars);

	/** @var  $ct_date */
	$ct_date = 0;

	$PROGRESS_NAME = $CruiseLine;

	if ($rs_price->count() != 0) {
		// skelMdl::send_cmd('act_notify', array('sticky' => true, 'msg' => 'Prices  ' . $idxml_cruise . ' <b>' . $rs_price->count() . '</b>'), session_id());
	}

	while ($arr_price = $rs_price->getNext()):
		if (empty($arr_price['PrecioCabina'])) {
			continue;
		}
		if (empty($arr_price['DeckLoc'])) {
			continue;
		}
		// recupere idtransport / -> lign xml_cruise
		$idxml_cruise = (int)$arr_price['idxml_cruise'];
		//
		$codeF = $arr_price['CruiseLine'];
		$codeT = $arr_price['ShipCode'];

		if ($arr_price['Flight'] == "True"):
			$arr_price['Flight'] = 1;
		else:
			$arr_price['Flight'] = 0;
		endif;

		$ARR_CRUISE = $col_CRUISE->findOne(array('idxml_cruise' => $idxml_cruise));

		// Cabine
		// echo "\r\n recherche gamme ".$arr_price['DeckLoc'];
		$test_gamme = $APP_GAMME->find(array('codeGamme' => $arr_price['DeckLoc']));

		if ($test_gamme->count() == 0):
			$idgamme  = (int)$APP->getNext('idgamme');
			$nomGamme = $arr_price['DeckLoc'];
			$APP_GAMME->insert(array('idgamme'   => $idgamme,
			                         'codeGamme' => $arr_price['DeckLoc'],
			                         'nomGamme'  => $arr_price['DeckLoc']));
		else :
			$arr_gm   = $test_gamme->getNext();
			$idgamme  = (int)$arr_gm['idgamme'];
			$nomGamme = $arr_gm['nomGamme'];
		endif;
		// echo "\r\n gamme ok".$arr_price['DeckLoc'];
		$test_tg = $APP_TRANSPORT_GAMME->find(array('idtransport'          => $idtransport,
		                                             'codeTransport_gamme' => $arr_price['Category']));
		// echo "\r\n test transport gamme   idtransport  codeTransport_gamme ".$idtransport.' '.$arr_price['Category'];
		if ($test_tg->count() == 0):
			// ne pas créer !!!!
			// skelMdl::send_cmd('act_notify', array('msg' => 'Cabine manquante ' . $CruiseLine . ' ' . $codeT . ' ' . $arr_price['DeckLoc'] . ' ' . $arr_price['Category']), session_id());
			// continue;
			$idtransport_gamme  = (int)$APP->getNext('idtransport_gamme');
			$nomTransport_gamme = ucfirst(strtolower($arr_price['Category']));
			$APP_TRANSPORT_GAMME->insert(array('idtransport_gamme'   => $idtransport_gamme,
			                                    'codeTransport_gamme' => $arr_price['Category'],
			                                    'nomTransport_gamme'  => ucfirst(strtolower($arr_price['Category'])),
			                                    'idgamme'              => $idgamme,
			                                    'nomGamme'             => $nomGamme,
			                                    'idtransport'          => $idtransport,
			                                    'nomTransport'         => $arr_price['ShipCode']));

		// skelMdl::send_cmd('act_notify', array('msg' => 'no TG  ' . $idxml_cruise . '-TG ' . $nomGamme . '- SH ' . $arr_price['ShipCode'] . '- CAT ' . $arr_price['Category']),  session_id());

		else :
			$arr_t               = $test_tg->getNext();
			$idtransport_gamme  = (int)$arr_t['idtransport_gamme'];
			$nomTransport_gamme = $arr_t['nomTransport_gamme'];
			// skelMdl::send_cmd('act_notify', array('msg' => ' TG OK ' . $idxml_cruise . '-TG ' . $nomGamme . '- SH ' . $arr_price['ShipCode'] . '- CAT ' . $arr_price['Category']),session_id());

		endif;
		//

		// echo "\r\n test transport gamme  ok  idtransport  idtransport_gamme ".$idtransport.' '.$idtransport_gamme.' '.$nomTransport_gamme;
		//
		$ct_date++;
		// produit_tarif, pour les dates // $APP_TARIF
		$test_date = $APP_TARIF->find(array('idproduit'              => $idproduit,
		                                    'dateDebutProduit_tarif' => $arr_price['DepartureDate'],
		                                    'volProduit_tarif'       => (int)$arr_price['Flight']));

		$ct_date = $test_date->count();
		// echo "\r\n test date idproduit ".$idproduit.' date '.$arr_price['DepartureDate'].' Flight  '.$arr_price['Flight'];


		if ($ct_date == 0):
			$idproduit_tarif = (int)$APP->getNext('idproduit_tarif');
			$APP_TARIF->insert(array('dateDebutProduit_tarif' => $arr_price['DepartureDate'],
			                         'dateProduit_tarif'      => $arr_price['DepartureDate'],
			                         'volProduit_tarif'       => (int)$arr_price['Flight'],
			                         'idproduit_tarif'        => $idproduit_tarif,
			                         'idproduit'              => $idproduit,
			                         'nomProduit'             => $ARR_CRUISE['nomXml_cruise'],
			                         'nomProduit_tarif'       => date_fr($arr_price['DepartureDate']) . ' ' . $ARR_CRUISE['CruiseLine'] . ' ' . $ARR_CRUISE['ShipCode']));
		/*skelMdl::send_cmd('act_notify' , array( 'msg' => 'NO DATE !!!  ' . $idxml_cruise.'- date '.$arr_price['DepartureDate'] ) , session_id());*/
		else :
			$arr_date        = $test_date->getNext();
			$idproduit_tarif = (int)$arr_date['idproduit_tarif'];
		endif;
		// skelMdl::send_cmd('act_notify', array('msg' => ' =>' . $idproduit . '- date ' . $arr_price['DepartureDate'] . ' ' . $idproduit_tarif), session_id());

		// produit_tarif_gamme, les prix / Category
		// verfication liaison code cabine + code gamme (I/O/S/B)
		$arrTG = $APP_TRANSPORT_GAMME->findOne(array('idtransport'          => (int)$idtransport,
		                                              'codeTransport_gamme' => $arr_price['Category']));
		//
		$TARIF['idproduit']          = (int)$idproduit;
		$TARIF['idproduit_tarif']    = (int)$idproduit_tarif;
		$TARIF['idtransport_gamme'] = (int)$arrTG['idtransport_gamme'];
		//
		$TARIF['idgamme']             = (int)$arrTG['idgamme'];
		$TARIF['codeGamme']           = $arrTG['codeTransport_gamme'];
		$TARIF['nomGamme']            = $arrTG['nomGamme'];
		$TARIF['nomTransport_gamme'] = $arrTG['nomTransport_gamme'];
		//
		$TARIF['volProduit_tarif'] = (int)$arr_price['Flight'];
		//
		$TARIF['dateProduit_tarif']      = $arr_price['DepartureDate'];
		$TARIF['dateDebutProduit_tarif'] = $arr_price['DepartureDate'];
		$TARIF['timeProduit_tarif']      = strtotime($arr_price['DepartureDate']);
		$TARIF['timeDebutProduit_tarif'] = strtotime($arr_price['DepartureDate']);
		//
		$TARIF['prixProduit_tarif_gamme'] = (float)(str_replace(',', '.', $arr_price['PrecioCabina']) / 2);
		if ($arr_price['CruiseLine'] == 'CDF') {
			$TARIF['prixProduit_tarif_gamme'] = (float)(str_replace(',', '.', $arr_price['PrecioTotal']) / 2);
		}
		// pour externe et selon compagnie
		$test_prix = $col_P_tarif_gamme->find(array('idproduit'          => $idproduit,
		                                            'idproduit_tarif'    => $idproduit_tarif,
		                                            'idgamme'            => $TARIF['idgamme'],
		                                            'idtransport_gamme' => $TARIF['idtransport_gamme']));
		$ct_prix   = $test_prix->count();

		//
		if ($ct_prix == 0):
			//

			$idproduit_tarif_gamme = (int)$APP->getNext('idproduit_tarif_gamme');
			$col_P_tarif_gamme->insert(array(/*'dateDebutProduit_tarif_gamme' => $arr_price['DepartureDate'],*/
			                                 'dateDebutProduit_tarif'       => $arr_price['DepartureDate'],
			                                 'dateProduit_tarif'            => $arr_price['DepartureDate'],
			                                 'prixProduit_tarif_gamme'      => $TARIF['prixProduit_tarif_gamme'],
			                                 'volProduit_tarif_gamme'       => $arr_price['Flight'],
			                                 'idproduit_tarif'              => $idproduit_tarif,
			                                 'idproduit_tarif_gamme'        => $idproduit_tarif_gamme,
			                                 'idtransport_gamme'           => $TARIF['idtransport_gamme'],
			                                 'idgamme'                      => $TARIF['idgamme'],
			                                 'idproduit'                    => (int)$idproduit,
			                                 'nomProduit'                   => $ARR_CRUISE['nomXml_cruise'],
			                                 'nomTransport_gamme'          => $TARIF['nomTransport_gamme'],
			                                 'nomProduit_tarif_gamme'       => $ARR_CRUISE['CruiseLine'] . ' ' . $ARR_CRUISE['ShipCode'] . ' ' . $arr_price['Category']));
		//
		else :
			$arr_date = $test_prix->getNext();
			if (empty($arr_date['m_mode'])):
				$idproduit_tarif_gamme = (int)$arr_date['idproduit_tarif_gamme'];
				if ($TARIF['prixProduit_tarif_gamme'] != $arr_date['prixProduit_tarif_gamme']):
					$int_prix  = (int)$arr_date['prixProduit_tarif_gamme'];
					$int_tarif = (int)$TARIF['prixProduit_tarif_gamme'];
					if ($int_tarif > $int_prix):
						$TARIF['oldPrixProduit_tarif_gamme'] = $arr_date['prixProduit_tarif_gamme'];
					else:
						$TARIF['oldPrixProduit_tarif_gamme'] = '';
					endif;
				endif;
				$col_P_tarif_gamme->update(array('idproduit_tarif_gamme' => $idproduit_tarif_gamme), array('$set' => $TARIF), array('upsert' => true));
			endif;
		endif;
		//
		// pour prix Produit
		$test_prix->reset();
		$arr_date = $test_prix->getNext();
		$test_prix_2 = $col_P_tarif_gamme->find(array('idproduit'=> $idproduit))->sort(['prixProduit_tarif_gamme'=>1])->getNext();
		if($test_prix_2['prixProduit_tarif_gamme']!=$arr_date['prixProduit_tarif_gamme'] ){
			$APP_PRODUIT->update(array('idproduit' => $idproduit), array('$set' => array('prixProduit' => $test_prix_2['prixProduit_tarif_gamme'],'oldprixProduit' => $arr_date['prixProduit_tarif_gamme'])));
		}
		// inscription du vol
		if (!empty($arr_price['Flight'])):
			$APP_PRODUIT->update(array('idproduit' => $idproduit), array('$set' => array('volProduit' => (int)$arr_price['Flight'])));
		endif;

		if ($_POST['count'] == $_POST['maxcount']):
			// skelMdl::runModule('app/app_admin/app_build_prod');
		endif;

	endwhile;

	$vars = array('progress_name' => $PROGRESS_NAME, 'progress_message' => $CruiseLine . ' ' . $ShipCode . ' ' . $PackageId . ' End Price ', 'progress_value' => 100, 'progress_max' => 100);
	skelMdl::send_cmd('act_progress', $vars, $session_id);