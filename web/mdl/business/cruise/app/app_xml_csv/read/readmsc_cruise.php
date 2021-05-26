<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	set_time_limit(0);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	$CODE_FOURNISSEUR = 'MSC';
	$idfournisseur    = 21;

	$APP                     = new App();
	$APP_XML_VILLE           = new App('xml_ville');
	$APP_XML_DESTINATION     = new App('xml_destination');
	$APP_VILLE               = new App('ville');
	$APP_PAYS                = new App('pays');
	$APP_TRANSPORT           = new App('transport');
	$APP_PRODUIT             = new App('produit');
	$APP_PRODUIT_ETAPE       = new App('produit_etape');
	$APP_PRODUIT_TARIF       = new App('produit_tarif'); // da departs
	$APP_PRODUIT_TARIF_GAMME = new App('produit_tarif_gamme'); // da prix
	$APP_TRANSPORT_GAMME     = new App('transport_gamme'); // da prix

	$APP_CSV     = $APP->plug('sitebase_csv', 'flatfile_fra_air');
	$APP_CSV_ITI = $APP->plug('sitebase_csv', 'itinff_fra_fra');
	$rs_csv      = $APP_CSV->find()->sort(['CRUISE-ID' => 1]); //'ITIN-CD' => 'U8T1',[ 'CLUB-DISCOUNT' => 'NO'] // 'SHIP-CD' => 'LI'

	if (empty($_POST['run'])) {
		?>
		<div class="padding"><?= $rs_csv->count() ?> croisieres / tarifs</div>
		<?

	} else {

		$total    = $rs_csv->count();
		$COLLECT  = [];
		$i        = 0;
		$non_attr = 0;

		$arr_progr_main = ['progress_parent' => 'cruise_msc',
		                   'progress_name'   => 'xml_job_cruise',
		                   'progress_text'   => " XML . $total tarifs ",
		                   'progress_max'    => $total];

		while ($arr_csv = $rs_csv->getNext()) {
			$i++;
			$codeProduit = $arr_csv['SHIP-CD'] . $arr_csv['ITIN-CD'];
			if (!isset($COLLECT[$codeProduit])) {
				$insertProduit = [];
				//
				$codeTransport    = $arr_csv['SHIP-CD'];
				$codeVille        = $arr_csv['SAILING-PORT'];
				$codeVilleArrivee = $arr_csv['TERMINATION-PORT'];
				//
				$arrTransport    = $APP_TRANSPORT->findOne(['codeTransport' => $codeTransport, 'idfournisseur' => $idfournisseur]);
				$arrVille        = $APP_XML_VILLE->findOne(['codeXml_ville' => $codeVille, 'idfournisseur' => $idfournisseur]);
				$arrVilleArrivee = $APP_XML_VILLE->findOne(['codeXml_ville' => $codeVilleArrivee, 'idfournisseur' => $idfournisseur]);
				//
				if (!empty($arrVille['idville']) && !empty($arrVilleArrivee['idville']) && !empty($arrTransport['idtransport'])) {
					//
					$insertProduit['from_csv']          = $idfournisseur;
					$insertProduit['idfournisseur']     = $idfournisseur;
					$insertProduit['idville']           = (int)$arrVille['idville'];
					$insertProduit['idvilleArrivee']    = (int)$arrVilleArrivee['idville'];
					$insertProduit['idtransport']       = (int)$arrTransport['idtransport'];
					$insertProduit['idproduit_type']    = 2;
					$insertProduit['nomVilleArrivee']   = $arrVilleArrivee['nomVille'];
					$insertProduit['nomProduit']        = $arr_csv['ITIN-DESC'];
					$insertProduit['dureeJoursProduit'] = (int)$arr_csv['NIGHTS'] - 1;
					$insertProduit['dureeProduit']      = (int)$arr_csv['NIGHTS'];
					$insertProduit['dureeNuitProduit']  = (int)$arr_csv['NIGHTS'];
					$insertProduit['CRUISE-ID']         = $arr_csv['CRUISE-ID'];
					$insertProduit['ITIN-CD']           = $arr_csv['ITIN-CD'];
					$insertProduit['codeProduit']       = $codeProduit;
					$insertProduit['hasVol']            = ($arr_csv['CRUISE-ONLY'] == 'YES');
					// test produit
					$test_produit = $APP_PRODUIT->findOne(['codeProduit' => $codeProduit]);
					if (empty($test_produit['idproduit'])) {
						$idproduit = $APP_PRODUIT->insert($insertProduit);
						// etapes
						itineraire_csv($arr_csv['CRUISE-ID'], $arr_csv['ITIN-CD'], (int)$idproduit);
					} else {
						$idproduit = (int)$test_produit['idproduit'];
						if ($insertProduit['nomProduit'] != $test_produit['nomProduit']) {
							//	$APP_PRODUIT->update(['idproduit'=>$idproduit],['nomProduit'=>$insertProduit['nomProduit']]);
						}
						// itineraire_csv($arr_csv['CRUISE-ID'], $arr_csv['ITIN-CD'], $idproduit);

					}

				} else {
					$arr_progr_main['progress_log'] = " NO CITY $codeVille $codeVilleArrivee";
					skelMdl::send_cmd('act_progress', $arr_progr_main);
				};
				//
				$COLLECT[$codeProduit] = [];
				if (!empty($idproduit)) {
					$COLLECT[$codeProduit]['idproduit']   = $idproduit;
					$COLLECT[$codeProduit]['idtransport'] = (int)$arrTransport['idtransport'];
				}
				//
				$arr_progr_main['progress_log'] = $idproduit . ' - ' . $insertProduit['nomProduit'];
				skelMdl::send_cmd('act_progress', $arr_progr_main);
				unset($arr_progr_main['progress_log']);
			} else {

				if (!empty($COLLECT[$codeProduit]['idproduit'])) {

					if (!empty($arr_csv['SAILING-DATE'])) {
						$dateAR                  = DateTime::createFromFormat('d/m/y', $arr_csv['SAILING-DATE']);
						$arr_csv['SAILING-DATE'] = $dateAR->format('Y-m-d');
					}

					$idproduit           = (int)$COLLECT[$codeProduit]['idproduit'];
					$idtransport         = (int)$COLLECT[$codeProduit]['idtransport'];
					$arr_tarif           = $APP_PRODUIT_TARIF->findOne(['idproduit' => $idproduit, 'dateDebutProduit_tarif' => $arr_csv['SAILING-DATE']]);
					$arr_transport_gamme = $APP_TRANSPORT_GAMME->findOne(['codeTransport_gamme' => $arr_csv['CATEGORY'], 'idtransport' => $idtransport]);

					// seulement si $arr_transport_gamme['codeTransport_gamme']
					if (!empty($arr_transport_gamme['codeTransport_gamme'])) {
						//
						$insert_date['dateDebutProduit_tarif']  = $arr_csv['SAILING-DATE'];
						$insert_date['nomProduit_tarif']        = $CODE_FOURNISSEUR . ' ' . $arr_csv['SHIP-CD'] . ' ' . $arr_csv['ITIN-CD'] . ' ' . date_fr($arr_csv['SAILING-DATE']);
						$insert_date['codeProduit_tarif_gamme'] = $arr_csv['CRUISE-ID'];

						// création dates de départ
						$idproduit_tarif = !empty($arr_tarif['idproduit_tarif']) ? (int)$arr_tarif['idproduit_tarif'] : $APP_PRODUIT_TARIF->create_update(['idproduit' => $idproduit, 'dateDebutProduit_tarif' => $arr_csv['SAILING-DATE']], $insert_date);
						//
						$cast_prix                              = str_replace(',', '.', $arr_csv['2A']);
						$insert_prix                            = [];
						$insert_prix['idproduit']               = $idproduit;
						$insert_prix['idproduit_tarif']         = $idproduit_tarif;
						$insert_prix['idtransport_gamme']       = (int)$arr_transport_gamme['idtransport_gamme'];
						$insert_prix['idgamme']                 = (int)$arr_transport_gamme['idgamme'];
						$insert_prix['codeTransport_gamme']     = $arr_transport_gamme['codeTransport_gamme'];
						$insert_prix['prixProduit_tarif_gamme'] = ($arr_csv['2A'] != 'N/A') ? empty($cast_prix) ? null : $cast_prix : null;
						$insert_prix['prixProduit_tarif_gamme'] = (float)$insert_prix['prixProduit_tarif_gamme'];
						//
						$arr_key     = ['1A', '2A', '3A', '4A', '2A1C', '2A2C'];
						$arr_key_gfc = [$arr_csv['GFT-A'], $arr_csv['GFT-A'] * 2, $arr_csv['GFT-A'] * 3, $arr_csv['GFT-A'] * 4, ($arr_csv['GFT-A'] * 2) + $arr_csv['GFT-C'], ($arr_csv['GFT-A'] * 2) + ($arr_csv['GFT-C'] * 2)];
						foreach ($arr_key as $type_tarif) {
							$insert_prix['prixProduit_tarif_gamme_' . $type_tarif] = (float)($arr_csv[$type_tarif] != 'N/A') ? str_replace(',', '.', $arr_csv[$type_tarif]) : null;
						}

						// tarifications
						$arr_tarif_gamme = $APP_PRODUIT_TARIF_GAMME->findOne(['idproduit' => $idproduit, 'idproduit_tarif' => $idproduit_tarif, 'codeTransport_gamme' => $arr_csv['CATEGORY']]);
						if (empty($arr_tarif_gamme['idproduit_tarif_gamme'])) {
							$APP_PRODUIT_TARIF_GAMME->insert($insert_prix);
						} else {
							if ($arr_tarif_gamme['prixProduit_tarif_gamme'] != $cast_prix && empty($arr_tarif_gamme['m_mode'])) {
								$APP_PRODUIT_TARIF_GAMME->update(['idproduit_tarif_gamme' => (int)$arr_tarif_gamme['idproduit_tarif_gamme']], $insert_prix);
							}
						}

					}
				}
			}
			//
			//
			$arr_progr_main['progress_message'] = $i . ' / ' . $total . ' tarifs . ' . sizeof($COLLECT) . ' croisières';
			$arr_progr_main['progress_value']   = $i;

			if (!empty($msg)) $arr_progr_main['progress_log'] = $msg;
			//
			if (modulo_progress($i, 500, $total)) {
				skelMdl::send_cmd('act_progress', $arr_progr_main);
			}
		}
		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		skelMdl::run($PATH . 'read/readmsc_build', ['file'  => 'red',
		                                            'url'   => 'la suite et fin ',
		                                            'delay' => 10,
		                                            'run'   => 1]);
	}

	function itineraire_csv($CRUISE_ID, $ITIN_CD, $idproduit) {
		global $APP_PRODUIT;
		global $APP_XML_DESTINATION;
		global $APP_XML_VILLE;
		global $APP_PRODUIT_ETAPE;
		global $APP_CSV_ITI;
		global $idfournisseur;

		global $arr_progr_main;
		//
		$idproduit   = (int)$idproduit;
		$arr_produit = $APP_PRODUIT->findOne(['idproduit' => $idproduit]);
		//
		$rs_iti = $APP_CSV_ITI->find(['CRUISE-ID' => $CRUISE_ID, 'ITIN-CD' => $ITIN_CD])->sort(['DEP-DAY' => 1, 'ARR-DATE' => 1]);
		$ordre  = 0;
		while ($arr_iti = $rs_iti->getNext()) {
			//
			if (!(empty($arr_iti['DEP-TIME']))) {
				$dateAR              = DateTime::createFromFormat('Hi', $arr_iti['DEP-TIME']);
				$arr_iti['DEP-TIME'] = $dateAR->format('H:i');
			}
			if (!(empty($arr_iti['ARR-TIME']))) {
				$dateAR              = DateTime::createFromFormat('Hi', $arr_iti['ARR-TIME']);
				$arr_iti['ARR-TIME'] = $dateAR->format('H:i');
			}
			if (!empty($arr_iti['DEP-DATE'])) {
				$dateAR              = DateTime::createFromFormat('d/m/y', $arr_iti['DEP-DATE']);
				$arr_iti['DEP-DATE'] = $dateAR->format('Y-m-d');
			}
			if (!empty($arr_iti['ARR-DATE'])) {
				$dateAR              = DateTime::createFromFormat('d/m/y', $arr_iti['ARR-DATE']);
				$arr_iti['ARR-DATE'] = $dateAR->format('Y-m-d');
			}
			//
			$insert_etape = [];
			$arrVille     = $APP_XML_VILLE->findOne(['codeXml_ville' => $arr_iti['DEP-PORT'], 'idfournisseur' => $idfournisseur]);
			//
			$insert_etape['ordreProduit_etape']      = (int)$arr_iti['DEP-DAY'];
			$insert_etape['idproduit']               = $idproduit;
			$insert_etape['idville']                 = (int)$arrVille['idville'];
			$insert_etape['nomProduit_etape']        = $arrVille['nomVille'];
			$insert_etape['heureDebutProduit_etape'] = $arr_iti['ARR-TIME'];
			$insert_etape['heureFinProduit_etape']   = $arr_iti['DEP-TIME'];
			$insert_etape['dateDebutProduit_etape']  = $arr_iti['ARR-DATE'];
			$insert_etape['dateFinProduit_etape']    = $arr_iti['DEP-DATE'];
			//
			$test_etape = $APP_PRODUIT_ETAPE->findOne(['idproduit' => $idproduit, 'dateDebutProduit_etape' => $arr_iti['ARR-DATE']]);
			//
			if (empty($test_etape['idproduit_etape'])) {
				$APP_PRODUIT_ETAPE->insert($insert_etape);
				/*$arr_progr_main['progress_log'] = $arr_iti['DEP-DATE'] . " $idproduit create  ";
				skelMdl::send_cmd('act_progress', $arr_progr_main, session_id());*/
			} elseif($insert_etape['idville']!=$test_etape['idville'] || $insert_etape['ordreProduit_etape']!=$test_etape['ordreProduit_etape']) {
					$arr_progr_main['progress_log'] = $arr_iti['DEP-DATE'] . " $idproduit update  " . $test_etape['idproduit_etape'];
				skelMdl::send_cmd('act_progress', $arr_progr_main);
				$APP_PRODUIT_ETAPE->update(['idproduit_etape' => (int)$test_etape['idproduit_etape']], $insert_etape);
			}

			// si $ordre == 0 =>destination
			if ($ordre == 0) {
				$test_dest = $APP_XML_DESTINATION->findOne(['codeXml_destination' => $arr_iti['AREA/DEST'], 'idfournisseur' => $idfournisseur]);
				if (!empty($test_dest['iddestination']) && $arr_produit['iddestination'] != $test_dest['iddestination']) {
					$APP_PRODUIT->update(['idproduit' => $idproduit], ['iddestination' => (int)$test_dest['iddestination'], 'nomDestination' => $test_dest['nomDestination']]);
				}
			}
			$ordre++;
			$test_etape = [];

		}
		/*$arr_progr_main['progress_log'] = "$idproduit etapes ok";
		skelMdl::send_cmd('act_progress', $arr_progr_main, session_id());*/

	}