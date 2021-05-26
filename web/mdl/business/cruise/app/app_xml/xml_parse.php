<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 30/12/14
	 * Time: 16:42
	 * 1 PRODUIT = 1 ITINERAIRE
	 */
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', '55');

	ini_set("default_socket_timeout", 3460);
	set_time_limit(0);
	ini_set('max_execution_time', 0);
	ini_set('memory_limit', '3G');
	//
	$APP_CONF = $APP->init_scheme('sitebase_xml', 'xml_conf');

	$idxml_conf = $APP_CONF->create_update(['codeXmlConf'=>'dateXml_conf']);
	$arr_conf = $APP_CONF->findOne(['codeXmlConf'=>'dateXml_conf']);
	if(empty($arr_conf['dateDebutXml_conf']) && empty($arr_conf['dateFinXml_conf']) ){
		skelMdl::send_cmd('act_notify', array('msg' => 'pas de date configurée' ), session_id());
		return;
	}
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$APP  = new App('feed_header');

	$APP_PRODUIT          = new App('produit');
	$col_P_etape          = $APP->plug('sitebase_production', 'produit_etape');
	$col_P_tarif          = $APP->plug('sitebase_production', 'produit_tarif');
	$col_P_tarif_gamme    = $APP->plug('sitebase_production', 'produit_tarif_gamme');
	$col_transport_gamme  = $APP->plug('sitebase_production', 'transport_gamme');
	$col_transport_cabine = $APP->plug('sitebase_production', 'transport_cabine');
	$col_Gamme            = $APP->plug('sitebase_production', 'gamme');
	$COL_FEED             = new App('feed_header');

	$COL_XML = $APP->plug('sitebase_xml', 'xml_cruise');

	$col_F = $APP->plug('sitebase_production', 'fournisseur');
	$col_T = $APP->plug('sitebase_production', 'transport');

	$col_XD      = $APP->plug('sitebase_xml', 'xml_destination');
	$COL_XML_VILLE      = $APP->plug('sitebase_xml', 'xml_ville');
	$COL_XML_ITI = $APP->plug('sitebase_xml', 'xml_itinerary');
	$col_Price   = $APP->plug('sitebase_xml', 'xml_price');

	$col_D         = $APP->plug('sitebase_production', 'destination');
	$APP_VILLE = $APP->plug('sitebase_production', 'ville');

	//
	$COL_XML->ensureIndex(array('DepartureDate' => 1));
	$COL_XML->ensureIndex(array('CruiseLine' => 1));
	$COL_XML->ensureIndex(array('ShipCode' => 1));
	$COL_XML->ensureIndex(array('PackageId' => 1));

	$COL_XML_ITI->ensureIndex(array('DepartureDate' => 1));
	$COL_XML_ITI->ensureIndex(array('CruiseLine' => 1));
	$COL_XML_ITI->ensureIndex(array('ShipCode' => 1));
	$COL_XML_ITI->ensureIndex(array('PackageId' => 1));
	$COL_XML_ITI->ensureIndex(array('idxml_cruise' => 1));

	$col_Price->ensureIndex(array('DepartureDate' => 1));
	$col_Price->ensureIndex(array('CruiseLine' => 1));
	$col_Price->ensureIndex(array('ShipCode' => 1));
	$col_Price->ensureIndex(array('PackageId' => 1));

	$COL_XML_VILLE->ensureIndex(array('codeFournisseur' => 1));
	$COL_XML_VILLE->ensureIndex(array('codeXml_ville' => 1));

	$col_XD->ensureIndex(array('codeXml_destination' => 1));
	$col_XD->ensureIndex(array('idfournisseur' => 1));

	$col_Gamme->ensureIndex(array('codeGamme' => 1));
	$col_transport_gamme->ensureIndex(array('codeTransport_gamme' => 1));
	$col_P_tarif->ensureIndex(array('dateDebutProduit_tarif' => 1));
	$col_P_tarif->ensureIndex(array('volProduit_tarif' => 1));
	$col_P_tarif_gamme->ensureIndex(array('prixProduit_tarif_gamme' => 1));

	$APP_VILLE->ensureIndex(array('nomVille' => 1));

	$col_P_etape->ensureIndex(array('ordreProduit_etape' => 1));


	//
	$dist_actif = $COL_FEED->distinct_all('codeFeed_header', $vars + array('estActifFeed_header' => 1)); // $query_vars

	$rs_xml = $COL_XML->find(['CruiseLine' => ['$in' => $dist_actif],'DepartureDate'=>['$gte'=>$arr_conf['dateDebutXml_conf'],'$lte'=>$arr_conf['dateFinXml_conf']]])->sort(array('idxml_cruise' => 1)); // 1000

	skelMdl::send_cmd('act_notify', array('msg' => 'total ' . $rs_xml->count()));

	// raccord cruise itinerary
	while ($arr_xml = $rs_xml->getNext()):
		if(empty( $arr_xml['PackageId'])) continue;
		$rsdbl = $COL_XML->find(['idxml_cruise'=>['$ne'=>(int)$arr_xml['idxml_cruise']],'CruiseLine' =>$arr_xml['CruiseLine'],'ShipCode'=>$arr_xml['ShipCode'],'DepartureDate'=>$arr_xml['DepartureDate']]);
		if($rsdbl->count()>1){
			skelMdl::send_cmd('act_notify', array('msg' => 'total dbl for '.$arr_xml['idxml_cruise'].' => ' . $rsdbl->count()));
			$COL_XML->remove(['idxml_cruise'=>['$ne'=>(int)$arr_xml['idxml_cruise']],'CruiseLine' =>$arr_xml['CruiseLine'],'ShipCode'=>$arr_xml['ShipCode'],'DepartureDate'=>$arr_xml['DepartureDate']]);
		}
	endwhile;

	$rs_xml = $COL_XML->find(['CruiseLine' => ['$in' => $dist_actif],'DepartureDate'=>['$gte'=>$arr_conf['dateDebutXml_conf'],'$lte'=>$arr_conf['dateFinXml_conf']]])->sort(array('idxml_cruise' => 1)); // 1000
	/*$rsct_xml = $COL_XML->distinct('CruiseLine',['CruiseLine' => ['$in' => $dist_actif],'DepartureDate'=>['$gte'=>$arr_conf['dateDebutXml_conf'],'$lte'=>$arr_conf['dateFinXml_conf']]]); // 1000
	$ct_xml = $rsct_xml->count();*/

	$i = 0;
	// raccord cruise itinerary
	while ($arr_xml = $rs_xml->getNext()):
		if ($i % 1000 == 0):
			// skelMdl::send_cmd('act_notify' , array( 'msg' => 'Sleep '.$i.' / ' . $rs_xml->count() ) , session_id());
			// sleep(1);
		endif;
		$i++;

		$codeF              = $arr_xml['CruiseLine'];
		$codeT              = $arr_xml['ShipCode'];
		$codeD              = $arr_xml['GeoCode'];
		$codeV              = empty($arr_xml['PortCode'])? preg_replace('/\s/', '', $arr_xml['PortName']) : $arr_xml['PortCode'] ;
		$codeIATA           = $arr_xml['PortCode'];
		$dateDebutXml_price = $arr_xml['DepartureDate'];
		$XML_CODE           = $arr_xml['XML_CODE'];

		$arrApp = $APP->findOne(array('codeFeed_header' => $codeF, 'estActifFeed_header' => 1)); // $query_vars
		if (empty($arrApp['idfournisseur'])) {
			continue;
		}

		// VERIFICATIONS
		$testF         = $col_F->findOne(array('codeFournisseur' => $codeF));
		$idfournisseur = (int)$testF['idfournisseur'];
		$testT         = $col_T->findOne(array('codeTransport' => $codeT, 'idfournisseur' => $idfournisseur));
		$testXD        = $col_XD->findOne(array('codeXml_destination' => $codeD));
		$testXV        = $COL_XML_VILLE->findOne(array('codeXml_ville' => $codeV));

		$idfournisseur = (int)$testF['idfournisseur'];
		$idtransport   = (int)$testT['idtransport'];

		// Recherche itineraire dans xml_iti d'apres CruiseLine ShipCode DepartureDate // ItineraryCode
		$iti_vars = array('CruiseLine'    => $arr_xml['CruiseLine'],
		                  'ShipCode'      => $arr_xml['ShipCode'],
		                  'PortCode'      => $codeV,
		                  'DepartureDate' => $arr_xml['DepartureDate']);
		//
		if (empty($arr_xml['PackageId'])):

			skelMdl::send_cmd('act_notify', array('msg' => 'pas de PackageId !!! ' . date('H:i:s')), session_id());
		else:
			// ITINERAY TROUVE
			/** @var  $PackageId */
			$PackageId = $arr_xml['PackageId'];
			// skelMdl::send_cmd('act_notify' , array( 'msg' => $codeF.'/'.$idfournisseur.' / ' . $idtransport.'/'.$PackageId.'/'.$XML_CODE ) , session_id());

			if (!empty($idfournisseur) && !empty($idtransport) && !empty($PackageId) && !empty($XML_CODE)):

				// 1 PRODUIT = 1 ITINERAIRE
				// DEBUT CREATION PRODUIT
				$test_produit_exist = array('idfournisseur' => $idfournisseur,
				                            'idtransport'   => $idtransport,
				                            'XML_CODE'      => $XML_CODE
					/* 'codeProduit'   => $PackageId*/);
				/** GENERATION idproduit  */
				$idproduit = $APP_PRODUIT->create_update($test_produit_exist, $test_produit_exist);

				// $test_p_exist = $APP_PRODUIT->find($test_produit_exist);

				// SI LE PRODUIT EST DEJA CREE
				/*if ( $test_p_exist->count() != 0 ):
					$arr_prod  = $test_p_exist->getNext();
					$idproduit = (int)$arr_prod['idproduit'];
					skelMdl::send_cmd('act_notify', array('msg' => 'existes !  ' . $itineraryCode . ' ' . $test_p_exist->count()), session_id());*/

				//  SI LE PRODUIT N'EST PAS ENCORE CREE
				//	else:
				/** @var  $test_XV
				 * Pour test ville */
				// VILLE DEPART
				$test_XV = $COL_XML_VILLE->findOne(array('codeFournisseur' => $codeF, 'codeXml_ville' => $codeV));
				if (!(empty($test_XV['idville']))):
					$idville  = (int)$test_XV['idville'];
					$nomVille = ucfirst(strtolower($test_XV['nomVille']));
				else:
					$idville  = '';
					$nomVille = '';
					skelMdl::send_cmd('act_notify', array('msg' => 'existes pas du tout ' . $codeV.' ; '.$arr_xml['PortName']  ), session_id());

				endif;
				/** @var  $test_XD
				 * Pour test destination  */
				// DESTINATION
				$test_XD = $col_XD->findOne(array('idfournisseur' => $idfournisseur, 'codeXml_destination' => $codeD));
				if (!(empty($test_XD['iddestination']))):
					$iddestination  = (int)$test_XD['iddestination'];
					$nomDestination = $test_XD['nomDestination'];
				else:
					$iddestination  = '';
					$nomDestination = '';
				endif;
				//
				/** @var  $test_p_vars
				Test produit*/
				$test_p_vars = array('idfournisseur' => $idfournisseur,
				                     'idtransport'   => $idtransport,
				                     'codeProduit'   => $PackageId);

				/** GENERATION idproduit  */
				/*$idproduit = (int)$APP->getNext('idproduit');*/

				$test_p_vars['idproduit'] = $idproduit;
				// CODE
				$test_p_vars['codeProduit'] = $PackageId;
				//
				$test_p_vars['estActifProduit'] = 1;

				$test_p_vars['nomProduit']          = ucfirst(strtolower($nomDestination)) . ' ' . ucfirst(strtolower($nomVille)) . ' ' . $arr_xml['CruiseLength'] . ' jours ' . $testT['nomTransport'];
				$test_p_vars['idproduit_type']      = 2;
				$test_p_vars['dureeProduit']        = (int)$arr_xml['CruiseLength'];
				$test_p_vars['nomFournisseur']      = $testF['nomFournisseur'];
				$test_p_vars['nomTransport']        = $testT['nomTransport'];
				$test_p_vars['idville']             = $idville;
				$test_p_vars['nomVille']            = ucfirst(strtolower($nomVille));
				$test_p_vars['iddestination']       = $iddestination;
				$test_p_vars['nomDestination']      = $nomDestination;
				$test_p_vars['ItineraryCode']       = $ItineraryCode;
				$test_p_vars['XML_CODE']            = $XML_CODE;
				$test_p_vars['dateCreationProduit'] = date('Y-m-d');

				// INSERTION PRODUIT
				$APP_PRODUIT->update(['idproduit' => (int)$idproduit], $test_p_vars); //
				///skelMdl::send_cmd('act_notify' , array( 'msg' => 'Sleep '.$i.' / ' . $rs_xml->count() ) );

				// produit ok !!!
				// skelMdl::send_cmd('act_notify' , array( 'msg' => '!!! nouveau produit  ' . $idproduit . ' ' . date('H:i:s') ) , session_id());

				// endif;
				$TOT[$codeF]++;
				/*
				skelMdl::send_cmd('act_progress', array('progress_name'  => 'xml_job_' . $codeF.'_test',
				                                        'progress_text'  => ' XML JOB ' . $codeF,
				                                        'progress_value' => $TOT[$codeF],
				                                        'progress_max'   => $ct_xml), session_id());*/

				if (!empty($_POST['vars']['idfournisseur'])) {
					skelMdl::send_cmd('act_progress', array('progress_name'  => 'xml_job_' . $codeF,
					                                        'progress_text'  => ' XML . ' . $codeF,
					                                        'progress_message'  =>   $i.' / '.$rs_xml->count(),
					                                        'progress_value' => $i,
					                                        'progress_max'   => ($rs_xml->count())), session_id());

				} else {

					skelMdl::send_cmd('act_progress', array('progress_name'  =>  'xml_job_' . $codeF,
					                                        'progress_text'  => ' XML JOB ..  ',
					                                        'progress_message'  =>   $i.' / '.$rs_xml->count(),
					                                        'progress_value' => $i,
					                                        'progress_max'   => ($rs_xml->count())), session_id());

					skelMdl::send_cmd('act_progress', array('progress_name'  => 'xml_job',
					                                        'progress_text'  => ' XML JOB ',
					                                        'progress_message'  =>   $TOT[$codeF].' / '.$i.' / '.$rs_xml->count(),
					                                        'progress_value' => $i,
					                                        'progress_max'   => ($rs_xml->count())), session_id());
				}

				if (!empty($idproduit)):
					// skelMdl::send_cmd('act_notify' , array( 'msg' => 'Produit ok ' . $idproduit ) , session_id());
					//
					/** @var  $CruiseLine */
					/** @var  $idproduit */
					/** @var  $ShipCode */
					/** @var  $PackageId */
					/** @var  $idxml_cruise */

					skelMdl::runModule('business/' . BUSINESS . '/app/app_xml/xml_parse_itinerary_cruise', array('vars' => array('CruiseLine'   => $codeF,
					                                                                                                             'idxml_cruise' => $arr_xml['idxml_cruise'],
					                                                                                                             'idproduit'    => $idproduit,
					                                                                                                             'ShipCode'     => $codeT,
					                                                                                                             'session_id'   => session_id(),
					                                                                                                             'PackageId'    => $PackageId)));

					// LES prix pour cette date de depart
					skelMdl::runModule('business/' . BUSINESS . '/app/app_xml/xml_parse_price', array('vars' => array('CruiseLine'   => $codeF,
					                                                                                                  'idxml_cruise' => $arr_xml['idxml_cruise'],
					                                                                                                  'idproduit'    => $idproduit,
					                                                                                                  'ShipCode'     => $codeT,
					                                                                                                  'session_id'   => session_id(),
					                                                                                                  'PackageId'    => $PackageId)));
				endif;

			else:
				$nb_err++;
				/*skelMdl::send_cmd('act_progress' , array( 'progress_name' => 'xml_job' ,
														  'progress_log'  => 'error $ItineraryCode ' . $ItineraryCode . ' * ' . $codeF . ' * ' . $codeT . ' * ' . $codeV . ' $idfournisseur ' . $idfournisseur . '  $idtransport' . $idtransport . ' ' . $i ) , session_id());*/

			endif;

		endif;

	endwhile;
	/*skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'xml_price' ,
	                                          'progress_text'    => ' XML JOB ' ,
	                                          'progress_message' => ' TerminÃ© ' ,
	                                          'progress_value'   => $i ,
	                                          'progress_max'     => ($rs_xml->count()) ) , session_id());*/
	echo "Done";