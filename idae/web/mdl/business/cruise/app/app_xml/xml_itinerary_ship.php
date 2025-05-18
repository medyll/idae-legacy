<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('feed_header');

	ini_set('display_errors' , 55);
	ini_set("default_socket_timeout",10);
	// on reçoit :
	/*	array('CruiseLine'    => $codeF,
			  'idtransport'   =>	$idtransport,
			  'idproduit'   	=>	$idproduit,
			  'idfournisseur' => 	$idfournisseur,
			  'ShipCode'      => 	$codeT,
			  'PackageId'     =>   $PackageId);*/


	if ( empty($_POST['vars']["CruiseLine"]) || empty($_POST['vars']['idproduit']) || empty($_POST['vars']['idtransport']) || empty($_POST['vars']['idfournisseur']) || empty($_POST['vars']['ShipCode']) || empty($_POST['vars']['PackageId']) ) {
echo " du vide ";
		return;
	}


	//
	/** @var  $CruiseLine */
	/** @var  $ShipCode */
	/** @var  $PackageId */
	/** @var  $idtransport */
	/** @var  $idfournisseur */
	/** @var  $idproduit */


	$CruiseLine    = $_POST['vars']["CruiseLine"];
	$ShipCode      = $_POST['vars']['ShipCode'];
	$PackageId     = $_POST['vars']['PackageId'];
	$idtransport   = (int)$_POST['vars']['idtransport'];
	$idfournisseur = (int)$_POST['vars']['idfournisseur'];
	$idproduit     = (int)$_POST['vars']['idproduit'];
	//
	$PROGRESS_NAME = $CruiseLine . $ShipCode;
	//
	$col                 = $APP->plug('sitebase_xml' , 'xml_itinerary');
	$col_cruise          = $APP->plug('sitebase_xml' , 'xml_cruise');
	$col_XD              = $APP->plug('sitebase_xml' , 'xml_destination');
	$col_XV              = $APP->plug('sitebase_xml' , 'xml_ville');
	$col_debug_itinerary = $APP->plug('sitebase_xml' , 'xml_itinerary_debug');
	$col_debug_error     = $APP->plug('sitebase_xml' , 'xml_itinerary_debug_error');
	//
	$col_F       = $APP->plug('sitebase_production' , 'fournisseur');
	$col_T       = $APP->plug('sitebase_production' , 'transport');
	$col_etape   = $APP->plug('sitebase_production' , 'produit_etape');
	$col_V       = $APP->plug('sitebase_production' , 'ville');
	$col_Pays    = $APP->plug('sitebase_production' , 'pays');
	$col_produit = $APP->plug('sitebase_production' , 'produit');
	/** @var  $arr_produit */
	$arr_produit = $col_produit->findOne(array( 'idproduit' => $idproduit ));
	$nomProduit  = $arr_produit['nomProduit'];
	//
	$WSDL = "http://idf.istinfor.com/Service1.asmx?WSDL";// "http://multitinerary.istinfor.com:85/Itinerarios/Service1.asmx?WSDL";
	//
	$soapClient = new SoapClient($WSDL , array( 'trace' => 1 , 'exceptions' => 0 ));
	//
	$arrApp = $APP->query_one(array( 'idfournisseur' => (int)$idfournisseur , 'estActifFeed_header' => 1 )); // $query_vars

	if ( empty($arrApp['idfournisseur']) ) {
		return;
	}
	//skelMdl::send_cmd('act_notify' , array( 'msg' => '  itineraire    ' . $idfournisseur . ' =>  '  ) , session_id());

	$CODE_FOURNISSEUR = $arrApp['codeFeed_header'];
	$header           = $arrApp['descriptionFeed_header'];

	// le bateaux
	$arrT = $col_T->findOne(array( 'idfournisseur' => $idfournisseur , 'idtransport' => $idtransport ));

	// usleep(1000000);
	$CODE_BATEAU = $arrT['codeTransport'];
	echo "running ship " . $PackageId . '-' . $ShipCode . '-' . $CruiseLine;
	//
	if ( ! empty($CODE_BATEAU) ):
		//
		// $soapClient = new SoapClient($WSDL , array( 'trace' => 1 , 'exceptions' => 0 ));
		try {
			$soapClient = @new SoapClient($WSDL);
		} catch (Exception $e) {
			echo $e->getMessage();

			return;
		}


		$cdata_string = '<RequestItinerary>' . $header . '<Itinerary><ShipCode>' . $ShipCode . '</ShipCode><DepartureDate>' . (empty($_POST['vars']['DepartureDate']) ? '' : $_POST['vars']['DepartureDate']) . '</DepartureDate><PackageId>' . $PackageId . '</PackageId></Itinerary></RequestItinerary>';

		// PROGRESS
		$prog_vars = array( 'progress_name'    => $PROGRESS_NAME ,
		                    'progress_max'     => 3 ,
		                    'progress_value'   => 0 ,
		                    'progress_text'    => $CruiseLine . ' ' . $PackageId . ' ' . $ShipCode ,
		                    'progress_message' => ' Requesting Itinerary ... ' . $CruiseLine . ' ' . $ShipCode );

		skelMdl::send_cmd('act_progress' , $prog_vars , session_id());

		/** SEARCHBYSEA  */
		$searchBySea = $soapClient->Itinerario(array( 'MessageXML' => $cdata_string ));

		// PROGRESS
		$prog_vars = array( 'progress_name'    => $PROGRESS_NAME ,
		                    'progress_message' => 'Itinerary Response ' . $CruiseLine . ' ' . $PackageId . ' ' . $ShipCode ,
		                    'progress_value'   => 1 ,
		                    'progress_max'     => 3 );

		skelMdl::send_cmd('act_progress' , $prog_vars , session_id());

		/** @var  $simple */
		$simple          = $searchBySea->ItinerarioResult;
		$xml_load_string = simplexml_load_string($simple);
		// PROGRESS
		$prog_vars = array( 'progress_name'    => $PROGRESS_NAME ,
		                    'progress_message' => 'XML parse ...' ,
		                    'progress_value'   => 2 ,
		                    'progress_max'     => 3 );

		skelMdl::send_cmd('act_progress' , $prog_vars , session_id());
		//
		// $col_debug_itinerary ->update(array('id'=>$PROGRESS_NAME),array( '$set' =>array( 'simple' => $simple,'SearchBySea'=>$searchBySea,'debug'=>soapDebug($soapClient))   ),array('upsert'=>true)); // SailingsList

		//
		if ( ! $xml_load_string ) {
			$col_debug_error->insert(array( 'error' => $xml_load_string , 'response' => $simple , 'cdata' => $cdata_string , 'debug' => soapDebug($soapClient) ));
			$vars = array( 'progress_name' => 'progress_' . $PackageId , 'progress_log' => 'Error Itinerary ...' . $PackageId , 'progress_value' => 3 , 'progress_max' => 3 );
			skelMdl::send_cmd('act_progress' , $vars , session_id());
		} else {
			//
			$vars = array( 'progress_name' => $PROGRESS_NAME , 'progress_log' => 'Itinerary OK ' . $PackageId , 'progress_value' => 3 );
			skelMdl::send_cmd('act_progress' , $vars , session_id());
			//
			$APP = new App();
			//
			foreach ($xml_load_string->Itinerary->List as $List):
				//
				$i ++;
				$a = 0;
				// PROGRESS
				/*$prog_vars = array( 'progress_name'    => $PROGRESS_NAME ,
				                    'progress_message' => '- cours ...' . $PackageId . ' ' . $i . '/' . (sizeof($xml_load_string->Itinerary->List)) ,
				                    'progress_value'   => $i ,
				                    'progress_max'     => sizeof($xml_load_string->Itinerary->List) );
				//
				skelMdl::send_cmd('act_progress' , $prog_vars , session_id());*/
				//
				if ( $List->attributes()->ItineraryCode ):
					$iti['ItineraryCode'] = (string)$List->attributes()->ItineraryCode;
				endif;
				//
				foreach ($List->ListElement as $ListElement):
					//$ListElement = $List->ListElement[0];
					$ordre ++;
					$a ++;
					$iti = array();
					if ( $List->attributes()->ItineraryCode ):
						$iti['ItineraryCode'] = (string)$List->attributes()->ItineraryCode;
					endif;
					//
					$iti['idproduit'] = $idproduit;
					//
					$iti['DWeek']         = (string)$ListElement->DWeek;
					$iti['DepartureDate'] = date_mysql((string)$ListElement->DepartureDate);
					$iti['PortName']      = (string)$ListElement->PortName;
					$iti['ArrivalTime']   = (string)$ListElement->ArrivalTime;
					$iti['DepartureTime'] = (string)$ListElement->DepartureTime;

					if ( $ListElement->PortName->attributes()->Code ):
						$iti['PortCode'] = $iti['Code'] = (string)$ListElement->PortName->attributes()->Code;
					endif;
					//
					$iti['nomXml_itinerary']        = $CODE_FOURNISSEUR . ' ' . $CODE_BATEAU . ' ' . $iti['DepartureDate'] . ' ' . $iti['PortName'];
					$iti['dateXml_itinerary']       = $iti['DepartureDate'];
					$iti['timeDepartureDate']       = strtotime($iti['DepartureDate']);
					$iti['timeDebutXml_itinerary']  = strtotime($iti['DepartureDate']);
					$iti['dateDebutXml_itinerary']  = $iti['DepartureDate'];
					$iti['heureDebutXml_itinerary'] = $iti['ArrivalTime'];
					$iti['heureFinXml_itinerary']   = $iti['DepartureTime'];
					$iti['codeXml_itinerary']       = $iti['ItineraryCode'];
					$iti['ShipCode']                = $CODE_BATEAU;
					$iti['CruiseLine']              = $CODE_FOURNISSEUR;

					$heureDebutProduit_etape = $iti['ArrivalTime'];
					$heureFinProduit_etape   = $iti['DepartureTime'];

					// INSERT XML ITI
					$test = $col->find(array( 'codeXml_itinerary' => $iti['ItineraryCode'] ,
					                          'ShipCode' => $CODE_BATEAU ,
					                          'CruiseLine' => $CODE_FOURNISSEUR ,
					                          'dateDebutXml_itinerary' => $iti['DepartureDate'] ));
					if ( $test->count() == 0 ):
						$iti['idxml_itinerary'] = (int)$APP->getNext('idxml_itinerary');
						//
						$col->insert($iti);
					endif;
					// VILLE
					$test_XV = $col_XV->find(array( 'codeFournisseur' => $CODE_FOURNISSEUR , 'codeXml_ville' => $iti['PortCode'] ));
					if ( $test_XV->count() == 0 ):

						if ( $CODE_FOURNISSEUR == 'MSC' && ! empty($iti['PortCode']) ):
							$test_V = $col_V->findOne(array( 'codeVille' => $iti['PortCode'] ));
							if ( ! (empty($test_V['idville'])) ):
								$idville  = (int)$test_V['idville'];
								$nomVille = $test_V['nomVille'];
							else: // on cree la ville dans app
								$idville = (int)$APP->getNext('idville');
								// le pays, aprés la virgule
								$nomVille = $iti['PortName'];
								$arr_tmp  = explode(',' , $nomVille);
								$nomVille = $arr_tmp[0];
								$nomPays  = $arr_tmp[1];
								//
								$test_Pays = $col_Pays->findOne(array( 'codePays' => $nomPays ));
								if ( $test_Pays['idpays'] ):

								endif;
								$col_V->insert(array( 'idville' => $idville , 'nomVille' => $nomVille , 'codeVille' => $iti['PortCode'] ));
							endif;
						else:
							$idville  = '';
							$nomVille = '';
						endif;
						if ( ! empty($iti['PortCode']) ):
							$id = (int)$APP->getNext('idxml_ville');
							$col_XV->insert(array( 'idxml_ville'     => $id ,
							                       'idfournisseur'   => (int)$idfournisseur ,
							                       'idville'         => (int)$idville ,
							                       'codeFournisseur' => $CruiseLine ,
							                       'nomVille'        => $nomVille ,
							                       'codeXml_ville'   => $iti['PortCode'] ,
							                       'nomXml_ville'    => $iti['PortName'] ));
						endif;
					else:
						$arr_idville = $test_XV->getNext();
						$idville     = (int)$arr_idville['idville'];
						$nomVille    = $arr_idville['nomVille'];

					endif;
					// Etape
					$test_etape = $col_etape->find(array( 'idproduit'          => $idproduit ,
					                                      'ordreProduit_etape' => $ordre ));
					//
					if ( $test_etape->count() == 0 ):
						// vast hreure
						if ( empty($iti['ArrivalTime']) || $iti['ArrivalTime']=='000000' ):
							$heureDebutProduit_etape = '';
						else:
							$date                    = DateTime::createFromFormat('His' , $iti['ArrivalTime']);
							$heureDebutProduit_etape = ! empty($date) ? $date->format('H:m:s') : '';
						endif;

						if ( empty($iti['DepartureTime']) || $iti['DepartureTime']=='000000' ):
							$heureFinProduit_etape = '';
						else:
							$date                  = DateTime::createFromFormat('His' , $iti['DepartureTime']);
							$heureFinProduit_etape = ! empty($date) ? $date->format('H:m:s') : '';

						endif;
						//

						$arr_XV          = $col_XV->findOne(array( 'codeFournisseur' => $CruiseLine , 'codeXml_ville' => $iti['PortCode'] ));
						$idproduit_etape = (int)$APP->getNext('idproduit_etape');
						$col_etape->insert(array( 'ordreProduit_etape'      => $ordre ,
						                          'dateDebutProduit'        => $idproduit_etape ,
						                          'heureDebutProduit_etape' => $heureDebutProduit_etape ,
						                          'heureFinProduit_etape'   => $heureFinProduit_etape ,
						                          'idproduit_etape'         => $idproduit_etape ,
						                          'idproduit'               => $idproduit ,
						                          'codeXml_ville'           => $iti['PortCode'] ,
						                          'idville'                 => $idville ,
						                          'nomVille'                => ucfirst(strtolower($nomVille)) ,
						                          'nomProduit_etape'        => ucfirst(strtolower($nomVille)) ,
						                          'nomXml_ville'            => $iti['PortName'] ));
					endif;

				endforeach;
			//
			endforeach;

			$vars = array( 'progress_name'    => $PROGRESS_NAME ,
			               'progress_value'   => 100 ,
			               'progress_max'     => 100 ,
			               'progress_message' => 'fin iti ' . $CruiseLine . ' ' . $ShipCode );
			skelMdl::send_cmd('act_progress' , $vars , session_id());
		}


	endif;




