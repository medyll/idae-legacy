<?
	include_once($_SERVER['CONF_INC']);

	if (empty($_POST['vars']['GeoCode']) || empty($_POST['vars']['CruiseLine'])) {
		return;
	}

	$PROGRESS_NAME = $_POST['PROGRESS_NAME'];
	$DOWNLOAD_TIME = time();

	$CruiseLine = $_POST['vars']['CruiseLine'];
	$GeoCode    = $_POST['vars']['GeoCode'];

	skelMdl::send_cmd('act_progress', array('progress_name'  => $PROGRESS_NAME,
	                                        'progress_value' => 1000, 'progress_message' => '<i class="fa fa-circle-o-notch fa-spin"></i> En attente ... ' . $CruiseLine . ' ' . $GeoCode,
	                                        'progress_max'   => 1000), session_id());

	ini_set('display_errors', 55);
	ini_set("default_socket_timeout", 3600);
	//
	$APP           = new App('feed_header');
	$APP_XML_JOB   = new App('xml_job');
	$APP_TRANSPORT = new App('transport');
	$APP_CONF      = $APP->init_scheme('sitebase_xml', 'xml_conf');

	$idxml_conf = $APP_CONF->create_update(['codeXmlConf' => 'dateXml_conf']);
	$arr_conf   = $APP_CONF->findOne(['codeXmlConf' => 'dateXml_conf']);
	if (empty($arr_conf['dateDebutXml_conf']) && empty($arr_conf['dateFinXml_conf'])) {
		skelMdl::send_cmd('act_notify', array('msg' => 'pas de date configurée '), session_id());

		return;
	}

	$APP_XML_JOB->create_update(array('codeXml_job' => $PROGRESS_NAME), array('valeurXml_job' => 0, 'maxXml_job' => 0));

	$col_price       = $APP->plug('sitebase_xml', 'xml_price');
	$APP_XML_CRUISE  = $APP->plug('sitebase_xml', 'xml_cruise');
	$col_itinerary   = $APP->plug('sitebase_xml', 'xml_itinerary');
	$col_F           = $APP->plug('sitebase_production', 'fournisseur');
	$col_T           = $APP->plug('sitebase_production', 'transport');
	$col_debug       = $APP->plug('sitebase_xml', 'xml_cruise_debug');
	$col_price_debug = $APP->plug('sitebase_xml', 'xml_price_debug');
	$col_debug_error = $APP->plug('sitebase_xml', 'xml_price_debug_error');
	$col_XD          = $APP->plug('sitebase_xml', 'xml_destination');
	//
	$ARR_FEED_HEADER = $APP->query_one(array('codeFeed_header' => $CruiseLine, 'estActifFeed_header' => 1));
	$APP->update(array('codeFeed_header' => $CruiseLine), array('dateRunFeed_header' => date('Y-m-d'), 'heureRunFeed_header' => date('H:i:s')));
	$header = $ARR_FEED_HEADER['descriptionFeed_header'];
	$WSDL   = $ARR_FEED_HEADER['urlFeed_header'] . '?WSDL';
	//
	$ARR_FOURNISSEUR = $col_F->findOne(array('codeFournisseur' => $CruiseLine));
	$nomFournisseur  = $ARR_FOURNISSEUR['nomFournisseur'];
	$idfournisseur   = (int)$ARR_FOURNISSEUR['idfournisseur'];

	if (empty($idfournisseur)):
		// skelMdl::send_cmd('act_notify', array('msg' => 'pas de fournisseur :  ' . $CruiseLine . ' ' . $nomFournisseur), session_id());
exit;
		return;
	endif;

	/** @var  $arr_XD */
	$arr_XD = $col_XD->findOne(array('codeXml_destination' => $GeoCode, 'idfournisseur' => $idfournisseur));

	try {
		$soapClient = @new SoapClient($WSDL, array('trace' => 1, 'exceptions' => 0, 'connection_timeout' => 3600));
	} catch (Exception $e) {
		skelMdl::send_cmd('act_notify', array('msg' => 'Error !! ' . $CruiseLine . ' ' . $GeoCode), session_id());
		$a = $e->getMessage();
		$col_debug_error->update(array('CruiseLine' => $CruiseLine, 'GeoCode' => $GeoCode), array('$set' => array('error' => $a)), array('upsert' => true));

		return;
	}

	skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME.'_panel',
	                                        'progress_message' =>   ' en cours ' . date('i:s', $DOWNLOAD_TIME),
	                                        'progress_max'     => empty(sizeof($BATCH)) ? 100 : sizeof($BATCH),
	                                        'progress_value'   => 100), session_id());

	$cdata_string = '<RequestSearchBySeaPricing>' . trim($header) . '<SearchBySea><Sea>' . trim($GeoCode) . '</Sea>
						    <DepartureDate >' . date_fr($arr_conf['dateDebutXml_conf']) . '-' . date_fr($arr_conf['dateFinXml_conf']) . '</DepartureDate><Guests>2</Guests><Price>BKD</Price><Age1/><Age2/><Age3/><Age4/>
						  </SearchBySea></RequestSearchBySeaPricing>';

	// RECHERCHE SOAP
	$searchBySea = $soapClient->SearchBySea(array('Message' => $cdata_string, 'MessageXML' => $cdata_string)); // Message pour SearchBySeaPricing, MessageXML SearchBySea
	$simple      = $searchBySea->SearchBySeaResult;
	$simple      = new SimpleXMLElement($simple);
	$SearchBySea = $simple->SearchBySea;
	//
	$col_XD->update(array('codeXml_destination' => $GeoCode, 'idfournisseur' => $idfournisseur), array('$set' => array('dateRunXml_destination' => date('Y-m-d'), 'heureRunXml_destination' => date('H:i:s'))), array('upsert' => true));
	$info = (array)$SearchBySea->GeneralInfo;
	if (!empty($info['Warnings'])) skelMdl::send_cmd('act_notify', array('sticky' => true, 'msg' => '<i class="fa fa-warning"></i> ' . $CruiseLine . ' ' . $GeoCode . '  ' . $info['Warnings']), session_id());
	$col_debug->update(array('id' => $CruiseLine . ' ' . $GeoCode), array('$set' => $info + array('SearchBySea' => $SearchBySea,
	                                                                                              'debug'       => soapDebug($soapClient))), array('upsert' => true)); // SailingsList
	$PROG_MSG .= "\r\n" . $GeoCode;

	//
	$BATCH          = array();
	$PROGRESS_VALUE = 0;
	foreach ($SearchBySea as $SearchBySeaElement):
		$PROGRESS_MAX = (int)$SearchBySeaElement->GeneralInfo->HowMuch;
		$APP_XML_JOB->update(array('codeXml_job' => $PROGRESS_NAME), array('maxXml_job' => $PROGRESS_MAX));
		// ..
		skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME,
		                                        'progress_message' => $PROGRESS_NAME . ' max   ' . $PROGRESS_MAX . ', HowMuch ' . $SearchBySeaElement->GeneralInfo->HowMuch,
		                                        'progress_max'     => $PROGRESS_MAX), session_id());
		// SI RESULTATS :
		if ($SearchBySea->GeneralInfo->HowMuch != 0):
			// c'est ok
			foreach ($SearchBySeaElement->SailingsList->SailingsListElement as $SailingsListElement):
				$PROGRESS_VALUE++;

				skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME,
				                                        'progress_message' => $PROGRESS_VALUE . ' /   ' . $PROGRESS_MAX,
				                                        'progress_value'   => $PROGRESS_VALUE), session_id());

				$arr_cruise = array(); // pour ligne xml_cruise
				// $SailingsListElement
				$arr_cruise['DepartureDate'] = date_mysql((string)$SailingsListElement->DepartureDate);
				$arr_cruise['PortName']      = (string)$SailingsListElement->PortName;
				$arr_cruise['PortCode']      = (string)$SailingsListElement->PortName->attributes()->PortCode;
				$arr_cruise['ShipCode']      = (string)$SailingsListElement->ShipCode;
				$arr_cruise['ShipName']      = (string)$SailingsListElement->ShipName;
				$arr_cruise['CruiseLength']  = (string)$SailingsListElement->CruiseLength;
				$arr_cruise['GeoCode']       = (string)$SailingsListElement->GeoCode;
				$arr_cruise['SailingSt']     = (string)$SailingsListElement->SailingSt;
				if (empty($arr_cruise['PortCode'])) $arr_cruise['PortCode'] = preg_replace('/\s/', '', $arr_cruise['PortName']);

				//
				if ($CruiseLine != (string)$SailingsListElement->CruiseLine):
					if ($CruiseLine == 'CDF' && (string)$SailingsListElement->CruiseLine == 'PUL'):
						$SailingsListElement->CruiseLine = 'CDF';
					else :
						/*skelMdl::send_cmd('act_progress', array('progress_name' => $PROGRESS_NAME,
						                                        'progress_log'  => 'cool'), session_id());*/
						continue;
					endif;
				endif;

				/*skelMdl::send_cmd('act_progress', array('progress_name'  => $PROGRESS_NAME,
				                                        'progress_log'   => 'Continue 1 - ' . $PROGRESS_VALUE,
				                                        'progress_value' => $PROGRESS_VALUE), session_id());*/

				$arr_cruise['CruiseLine'] = (string)$SailingsListElement->CruiseLine; //$CruiseLine; //  ... RCCL devient CEL / RCC / AZA
				$PackageId                = $arr_cruise['PackageId'] = (string)$SailingsListElement->PackageId;

				$arr_cruise['SailingSt'] = (string)$SailingsListElement->SailingSt;
				// destination
				$arr_cruise['iddestination']  = (int)$arr_XD['iddestination'];
				$arr_cruise['nomDestination'] = $arr_XD['nomDestination'];

				if ($SailingsListElement->DepartureDate->attributes()->SailingID):
					$_SailingID = (string)$SailingsListElement->DepartureDate->attributes()->SailingID;
					if (!empty($_SailingID) && empty($arr_cruise['PackageId'])):
						$PackageId = $arr_cruise['PackageId'] = $_SailingID;
					endif;
				endif;

				$arr_cruise['nomXml_cruise'] = strtolower($arr_cruise['PackageId'] . ' ' . $arr_cruise['ShipName'] . ' ' . $arr_cruise['PortName'] . ' ' . $arr_cruise['GeoCode']);
				//
				$PROG_MSG = $arr_cruise['ShipCode'] . ' ' . $arr_cruise['PortName'] . ' ' . $arr_cruise['DepartureDate'];
				//
				// test bateau : ne pas créer !!!
				$test_T = $col_T->find(array('idfournisseur' => $idfournisseur, 'codeTransport' => $arr_cruise['ShipCode']));
				if ($test_T->count() == 0 && !empty($arr_cruise['ShipCode'])):
					if (empty($shall_pass[$arr_cruise['ShipCode']])):
						$shall_pass[$arr_cruise['ShipCode']] = 1;
					endif;
					$HAS_SOME_TRANSP = $APP_TRANSPORT->findOne(array('idfournisseur' => $idfournisseur));
					continue;

					$idtransport = (int)$APP->getNext('idtransport');
					$col_T->insert(array('idtransport'    => $idtransport,
					                     'idfournisseur'  => $idfournisseur,
					                     'codeTransport'  => $arr_cruise['ShipCode'],
					                     'nomTransport'   => $arr_cruise['ShipName'],
					                     'nomFournisseur' => $nomFournisseur));
				else :
					$arr_t       = $test_T->getNext();
					$idtransport = (int)$arr_t['idtransport'];
				endif;

				//  INSERT $arr_cruise => xml_cruise // UNIQ SUR PackageId
				$test_cruise = $APP_XML_CRUISE->findOne(array('PortCode'   => $arr_cruise['PortCode'],
					// 'DepartureDate' => $arr_cruise['DepartureDate'] ,
					                                          'CruiseLine' => $arr_cruise['CruiseLine'],
					                                          'PackageId'  => $arr_cruise['PackageId'],
					                                          'ShipCode'   => $arr_cruise['ShipCode']));
				if (empty($test_cruise['idxml_cruise'])):
					$arr_cruise['idxml_cruise'] = (int)$APP->getNext('idxml_cruise');
					$APP_XML_CRUISE->insert($arr_cruise);
				else:
					if ($arr_cruise['SailingSt'] != $test_cruise['SailingSt']):
						$APP_XML_CRUISE->update(array('idxml_cruise' => (int)$test_cruise['idxml_cruise']), array('$set' => $arr_cruise), array('upsert' => true));
					endif;
					$arr_cruise['idxml_cruise'] = (int)$test_cruise['idxml_cruise'];
				endif;

				// Maitenant les prix
				$z = 0;

				foreach ($SailingsListElement->PricesFrom->PricesList->PricesListElement as $elem_price) {
					//
					$arr_collect_price                       = array();
					$arr_collect_price['SailingID']          = $arr_cruise['SailingID'];
					$arr_collect_price['PackageId']          = $PackageId;
					$arr_collect_price['CruiseLine']         = $arr_cruise['CruiseLine'];
					$arr_collect_price['idxml_cruise']       = (int)$arr_cruise['idxml_cruise'];
					$arr_collect_price['DepartureDate']      = $arr_cruise['DepartureDate'];
					$arr_collect_price['dateDebutXml_price'] = $arr_cruise['DepartureDate'];
					$arr_collect_price['timeDebutXml_price'] = strtotime($arr_cruise['DepartureDate']);
					$arr_collect_price['ShipCode']           = $arr_cruise['ShipCode'];
					$arr_collect_price['nomXml_price']       = $arr_cruise['CruiseLine'] . ' ' . $arr_cruise['ShipCode'] . ' ' . date_fr($arr_cruise['DepartureDate']) . ' ' . $arr_cruise['SailingID'];

					foreach ($elem_price->children() as $key => $field):
						if ($key != '@attributes') {
							$arr_collect_price[$key] = (string)$field;
							if ($key == 'DepartureDate') {
								$arr_collect_price[$key] = date_mysql((string)$field);
							}
						}
					endforeach;
					foreach ($elem_price->attributes() as $key => $field):
						$arr_collect_price[$key] = (string)$field;
					endforeach;
					foreach ($elem_price->LAFPrice->attributes() as $key => $field):
						$arr_collect_price[$key] = (string)$field;
					endforeach;
					// override deckloc
					switch ($arr_collect_price['DeckLoc']):
						case 'I/S':
							$arr_collect_price['DeckLoc'] = 'I';
							break;
						case 'O/S':
							$arr_collect_price['DeckLoc'] = 'O';
							break;

					endswitch;
					//
					$arr_collect_price['codeXml_price']              = $arr_collect_price['DeckLoc'] . ' ' . $arr_collect_price['Category'];
					$arr_collect_price['prixXml_price']              = $arr_collect_price['PrecioCabina'];
					$arr_collect_price['heureModificationXml_price'] = date('H:i:s');

					$col_price_debug->update(array('PackageId' => $arr_cruise['PackageId'],
					                               'Category'  => $arr_collect_price['Category']), array('$set' => $arr_collect_price), array('upsert' => true));

					if (!empty($arr_collect_price['PackageId'])):
						// INSERTION
						$test_price = $col_price->find(array('DepartureDate' => $arr_collect_price['DepartureDate'],
						                                     'ShipCode'      => $arr_collect_price['ShipCode'],
						                                     'CruiseLine'    => $arr_collect_price['CruiseLine'],
						                                     'PriceProgram'  => $arr_collect_price['PriceProgram'],
						                                     'DeckLoc'       => $arr_collect_price['DeckLoc'],
						                                     'Category'      => $arr_collect_price['Category']));
						//
						if ($test_price->count() == 0):
							// skelMdl::send_cmd('act_notify', array('msg' => 'PackageId INSERT'), session_id());
							$arr_collect_price['idxml_price'] = (int)$APP->getNext('idxml_price');
							$col_price->insert($arr_collect_price);

						else:
							$arrPrice = $test_price->getNext();
							$col_price->update(['idxml_price' => (int)$arrPrice['idxml_price']], ['$set' => $arr_collect_price], ['upsert' => true]);
						endif;
					else:
						skelMdl::send_cmd('act_notify', array('msg' => 'PackageId vide!!!!!!!'), session_id());
					endif;
					//
				} // fin boucle date de dÃ©part , on garde $arr_collect_price['DepartureDate']

				$test_iti = $col_itinerary->find(array('PackageId' => $PackageId, 'CruiseLine' => $CruiseLine, 'ShipCode' => $arr_cruise['ShipCode']));

				if (!empty($test_cruise['XML_CODE'])):
					skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME,
					                                        'progress_message' => $PROGRESS_VALUE . ' /   ' . $PROGRESS_MAX,
					                                        'PROGRESS_VALUE'   => $PROGRESS_VALUE),
						session_id());
					continue;
				endif;
				//if ( $test_iti->count() == 0 || empty($test_cruise['XML_CODE']) ):

				if (!empty($arr_collect_price['DepartureDate']) && !empty($arr_cruise['PackageId']) && empty($ARR_STILL[$PackageId])):

					// skelMdl::runModule
					$BATCH[]               = array('PROGRESS_NAME' => $PROGRESS_NAME,
					                               'vars'          => array('GeoCode'       => $GeoCode,
					                                                        'ShipCode'      => $arr_cruise['ShipCode'],
					                                                        'PackageId'     => $arr_cruise['PackageId'],
					                                                        'DepartureDate' => $arr_collect_price['DepartureDate'],
					                                                        'idxml_cruise'  => $arr_cruise['idxml_cruise'],
					                                                        'CruiseLine'    => $CruiseLine));
					$ARR_STILL[$PackageId] = $PackageId;
				else:
					/*skelMdl::send_cmd('act_progress', array('progress_name'  => $PROGRESS_NAME,
					                                        'progress_log'   => 'deja enregistre ' . $PROGRESS_NAME,
					                                        'progress_value' => $PROGRESS_VALUE), session_id());*/
					//endif;
				endif;
			endforeach;

		else:
			/*skelMdl::send_cmd('act_progress', array('progress_name'  => $PROGRESS_NAME,
			                                        'progress_log'   => 'Aucun itineraire   ' . $PROGRESS_VALUE . ' ' . $PROGRESS_NAME,
			                                        'progress_max'   => 100,
			                                        'progress_value' => 100), session_id());*/
		endif;
		// FIN SI RESULTATS :
	endforeach; // fin sailinglistelemnt

	$APP_XML_JOB->update(array('codeXml_job' => $PROGRESS_NAME), array('maxXml_job' => sizeof($BATCH), 'donloadTime' => $DOWNLOAD_TIME));

	$DOWNLOAD_TIME = time() - $DOWNLOAD_TIME;
	skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME,
		                                    'progress_message' => ' total   ' . $PROGRESS_MAX . ', final ' . sizeof($BATCH) . ' ' . date('i:s', $DOWNLOAD_TIME),
		                                    'progress_max'     => empty(sizeof($BATCH)) ? 100 : sizeof($BATCH),
		                                    'progress_value'   => 100), session_id());
	skelMdl::send_cmd('act_progress', array('progress_name'    => $PROGRESS_NAME.'_panel',
		                                    'progress_message' => ' total   ' . $PROGRESS_MAX . ', final ' . sizeof($BATCH) . ' ' . date('i:s', $DOWNLOAD_TIME),
		                                    'progress_max'     => empty(sizeof($BATCH)) ? 100 : sizeof($BATCH),
		                                    'progress_value'   => 100), session_id());

	$PROGRESS_VALUE = 0;
	foreach ($BATCH as $key => $batch):
		$PROGRESS_VALUE++;
		$batch['PROGRESS_VALUE'] = $PROGRESS_VALUE;
		skelMdl::runModule('business/' . BUSINESS . '/app/app_xml/xml_ftp_in_itinerary', $batch);
	endforeach;
