<?
	include_once($_SERVER['CONF_INC']);

	$APP           = new App('feed_header');
	$APP_XML_JOB   = new App('xml_job');
	$TABLE_XML_JOB = $APP_XML_JOB->app_table_one;

	ini_set('display_errors', 55);
	ini_set("default_socket_timeout", 1200);

	$PROGRESS_NAME = $_POST['PROGRESS_NAME'];
	$APP_XML_JOB->plug($APP_XML_JOB->codeAppscheme_base, $APP_XML_JOB->codeAppscheme)->update(array('codeXml_job' => $PROGRESS_NAME), array('valeurXml_job' => array('$inc' => 1)), array('upsert' => true));

	if (empty($_POST['vars']["idxml_cruise"]) || empty($_POST['vars']["CruiseLine"]) || empty($_POST['vars']['ShipCode']) || empty($_POST['vars']['PackageId'])) {
		echo " du vide ";

		return;
	}
	//
	$CruiseLine    = $_POST['vars']["CruiseLine"];
	$ShipCode      = $_POST['vars']['ShipCode'];
	$PackageId     = $_POST['vars']['PackageId'];
	$DepartureDate = $_POST['vars']['DepartureDate'];
	$idxml_cruise  = (int)$_POST['vars']['idxml_cruise'];
	//
	$col                 = $APP->plug('sitebase_xml', 'xml_itinerary');
	$col_cruise          = $APP->plug('sitebase_xml', 'xml_cruise');
	$col_XD              = $APP->plug('sitebase_xml', 'xml_destination');
	$col_XV              = $APP->plug('sitebase_xml', 'xml_ville');
	$col_debug_itinerary = $APP->plug('sitebase_xml', 'xml_itinerary_debug');
	$col_debug_error     = $APP->plug('sitebase_xml', 'xml_itinerary_debug_error');
	//
	$col_F       = $APP->plug('sitebase_production', 'fournisseur');
	$col_T       = $APP->plug('sitebase_production', 'transport');
	$col_etape   = $APP->plug('sitebase_production', 'produit_etape');
	$col_V       = $APP->plug('sitebase_production', 'ville');
	$col_Pays    = $APP->plug('sitebase_production', 'pays');
	$col_produit = $APP->plug('sitebase_production', 'produit');
	//
	$WSDL = "http://idf.istinfor.com/Service1.asmx?WSDL";//  "http://multitinerary.istinfor.com:85/Itinerarios/Service1.asmx?WSDL";
	//
	$arrApp = $APP->query_one(array('codeFeed_header' => $CruiseLine, 'estActifFeed_header' => 1)); // $query_vars
	$header = $arrApp['descriptionFeed_header'];

	if (empty($arrApp['idfournisseur'])) {
		// skelMdl::send_cmd('act_notify', array('msg' => 'pas de fournisseur :  ' . $CruiseLine), session_id());
		exit;
		return;
	}
	//

	if (!empty($ShipCode)):
		//
		try {
			$soapClient = @new SoapClient($WSDL, array('trace' => 1, 'exceptions' => 0, 'connection_timeout' => 1200));
		} catch (Exception $e) {
			echo $e->getMessage();

			return;
		}

		$cdata_string = '<RequestItinerary>' . $header . '<Itinerary><ShipCode>' . $ShipCode . '</ShipCode><DepartureDate >' . (empty($_POST['vars']['DepartureDate']) ? '' : date_fr($_POST['vars']['DepartureDate'])) . '</DepartureDate><PackageId>' . $PackageId . '</PackageId></Itinerary></RequestItinerary>';
		$searchBySea     = $soapClient->Itinerario(array('MessageXML' => $cdata_string));
		$simple          = $searchBySea->ItinerarioResult;
		$xml_load_string = simplexml_load_string($simple);
		/** SEARCHBYSEA  */
		$col_debug_itinerary->update(array('CruiseLine' => $CruiseLine, 'ShipCode' => $ShipCode, 'PackageId' => $PackageId), array('$set' => array('date'  => date('d/m/Y H:m'),
		                                                                                                                                           'debug' => $cdata_string)), array('upsert' => true));


		//
		if (!$xml_load_string) {
			$col_debug_error->insert(array('error' => $xml_load_string, 'response' => $simple, 'cdata' => $cdata_string, 'debug' => soapDebug($soapClient)));
		} else {
			//
			$APP = new App();
			//
			$col_debug_itinerary->update(array('CruiseLine' => $CruiseLine, 'ShipCode' => $ShipCode, 'PackageId' => $PackageId), array('$set' => array('soapDebug'        => soapDebug($soapClient),
		                                                                                                                                           'sizeItinerayList' => sizeof($xml_load_string->Itinerary->List))), array('upsert' => true));
			$HowMuch = $xml_load_string->Itinerary->GeneralInfo->HowMuch;

			if ($HowMuch == 0):
				$col_debug_itinerary->update(array('CruiseLine' => $CruiseLine,
				                                   'ShipCode'   => $ShipCode,
				                                   'PackageId'  => $PackageId), array('$set' => array('HowMuch' => $HowMuch)), array('upsert' => true));
			else:
				foreach ($xml_load_string->Itinerary->List as $List):
					//
					$i++;
					$a = 0;

					$col_debug_itinerary->update(array('CruiseLine' => $CruiseLine,
					                                   'ShipCode'   => $ShipCode,
					                                   'PackageId'  => $PackageId), array('$set' => array('List' => $List)), array('upsert' => true));

					//
					if ($List->attributes()->ItineraryCode):
						$iti['ItineraryCode'] = (string)$List->attributes()->ItineraryCode;
					endif;
					//
					$FINAL_CODE = '';
					foreach ($List->ListElement as $ListElement):
						$ordre++;
						$a++;
						$iti = array();
						if ($List->attributes()->ItineraryCode):
							$iti['ItineraryCode'] = (string)$List->attributes()->ItineraryCode;
						endif;
						//
						$iti['idxml_cruise'] = $idxml_cruise;
						$iti['PackageId']    = $PackageId;
						//
						$iti['DWeek'] = (string)$ListElement->DWeek;
						/**   */
						$iti['DepartureDate'] = date_mysql((string)$ListElement->DepartureDate);
						$iti['PortName']      = (string)$ListElement->PortName;

						$iti['ArrivalTime']   = (string)$ListElement->ArrivalTime;
						$iti['DepartureTime'] = (string)$ListElement->DepartureTime;

						if ($ListElement->PortName->attributes()->Code):
							$iti['PortCode'] = $iti['Code'] = (string)$ListElement->PortName->attributes()->Code;
						else:
							$iti['PortCode'] = $iti['Code'] = preg_replace('/\s/', '', $iti['PortName']);
						endif;
						// if (empty($iti['PortCode'])) $iti['PortCode'] = preg_replace('/\s/', '', $iti['PortName']);
						// if ($iti['PortCode'] ==$iti['PortName'] ) $iti['PortCode']  = $iti['Code'] = preg_replace('/\s/', '', $iti['PortName']);
						//
						$iti['nomXml_itinerary']       = $CruiseLine . ' ' . $ShipCode . ' ' . $iti['DepartureDate'] . ' ' . $iti['PortName'];
						$iti['dateXml_itinerary']      = $iti['DepartureDate'];
						$iti['timeDepartureDate']      = strtotime($iti['DepartureDate']);
						$iti['timeDebutXml_itinerary'] = strtotime($iti['DepartureDate']);
						$iti['dateDebutXml_itinerary'] = $iti['DepartureDate'];

						if (empty($iti['ArrivalTime']) || $iti['ArrivalTime'] == 100 || $iti['ArrivalTime'] == '000100' || $iti['ArrivalTime'] == '000000'):
							$iti['heureDebutXml_itinerary'] = '';
							$iti['ArrivalTime']             = '';
						else:
							$date                           = DateTime::createFromFormat('His', $iti['ArrivalTime']);
							$iti['heureDebutXml_itinerary'] = !empty($date) ? $date->format('H:i') : '';
							$iti['ArrivalTime']             = !empty($date) ? $date->format('H:i') : '';
						endif;

						if (empty($iti['DepartureTime']) || $iti['DepartureTime'] == 100 || $iti['DepartureTime'] == '000100' || $iti['DepartureTime'] == '000000'):
							$iti['heureFinXml_itinerary'] = '';
							$iti['DepartureTime']         = '';
						else:
							$date                         = DateTime::createFromFormat('His', $iti['DepartureTime']);
							$iti['heureFinXml_itinerary'] = !empty($date) ? $date->format('H:i') : '';
							$iti['DepartureTime']         = !empty($date) ? $date->format('H:i') : '';

						endif;

						$iti['heureDebutXml_itinerary'] = $iti['ArrivalTime'];
						$iti['heureFinXml_itinerary']   = $iti['DepartureTime'];

						$iti['codeXml_itinerary'] = $iti['ItineraryCode'];
						$iti['ShipCode']          = $ShipCode;
						$iti['CruiseLine']        = $CruiseLine; // (string)$ListElement->CruiseLine;//  $CruiseLine;

						/*if($CruiseLine != (string)$ListElement->CruiseLine):
						 	skelMdl::send_cmd('act_notify' , array( 'msg' => ' !!!! :  ' . $CruiseLine.' '.(string)$ListElement->CruiseLine ) , session_id());
							continue;
						endif;*/
						//
						if ($a == 1):
							$first_DepartureDate = new DateTime($iti['DepartureDate']);
						endif;
						//
						$datetime2 = new DateTime($iti['DepartureDate']);
						$interval  = $first_DepartureDate->diff($datetime2);
						$ordre     = (int)$interval->days + 1;

						// INSERT XML ITI
						$test = $col->find(array('codeXml_itinerary'      => $iti['ItineraryCode'],
						                         'idxml_cruise'           => (int)$idxml_cruise,
						                         'ShipCode'               => $ShipCode,
						                         'PortName'               => $iti['PortName'],
						                         'CruiseLine'             => $CruiseLine,
						                         'dateDebutXml_itinerary' => $iti['DepartureDate']));
						if ($test->count() == 0):
							$iti['idxml_itinerary'] = (int)$APP->getNext('idxml_itinerary');
							//
							$col->insert($iti);
						endif;

						$FINAL_CODE .= (empty($iti['PortCode'])) ? trim($iti['PortName']) : $iti['PortCode'];

					endforeach;
					// write back : iti_uniq
					$col_cruise->update(array('idxml_cruise' => (int)$idxml_cruise), array('$set' => array('XML_CODE' => $FINAL_CODE)), array('upsert' => true, 'multi' => 1));
					break;
				endforeach;
			endif;

		}

	endif;

	$ARR_XML_JOB = $APP_XML_JOB->findOne(array('codeXml_job' => $PROGRESS_NAME));
	$vars        = array('progress_name' => $PROGRESS_NAME, 'progress_value' => $ARR_XML_JOB['valeurXml_job']);
	skelMdl::send_cmd('act_progress', $vars, session_id());