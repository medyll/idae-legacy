<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 30/11/2017
	 * Time: 19:13
	 */

	include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55);
	$CODE_FOURNISSEUR = 'COSTA';

	$APP                     = new App();
	$APP_FOURNISSEUR         = new App('fournisseur');
	$APP_XML_VILLE           = new App('xml_ville');
	$APP_XML_DESTINATION     = new App('xml_destination');
	$APP_DESTINATION         = new App('destination');
	$APP_PAYS                = new App('pays');
	$APP_VILLE               = new App('ville');
	$APP_PRODUIT             = new App('produit');
	$APP_TRANSPORT           = new App('transport');
	$APP_TRANSPORT_CABINE    = new App('transport_cabine');
	$APP_TRANSPORT_GAMME     = new App('transport_gamme');
	$APP_PRODUIT_TARIF       = new App('produit_tarif');
	$APP_PRODUIT_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_XML_VILLE           = new App('xml_ville');
	$APP_XML_DESTINATION     = new App('xml_destination');
	$APP_VILLE               = new App('ville');
	$APP_PAYS                = new App('pays');
	$APP_GAMME               = new App('gamme');

	$ARR_FOURNISSEUR = $APP_FOURNISSEUR->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR ]);
	$idfournisseur   = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$i        = 0;
	$non_attr = 0;
	$orphans  = 0;

	$PATH      = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
	$local_rep = XMLDIR . 'costa/';

	$xml   = simplexml_load_file($local_rep . 'catalog.xml');
	$total = 0;
	foreach ($xml->CostaCruiseCatalog->Destination as $key => $Destination) {
		foreach ($Destination->Itinerary as $key_iti => $Itinerary) {
			$total += sizeof($Itinerary);
		}
	}
	/*skelMdl::run(  $PATH . 'notify' , [ 'file'  => 'red' ,
	                                           'url'   => 'redf' ,
	                                           'run'   => 1 ]);*/
	if ( empty($_POST['run']) ) {
		?>
		<div class = "padding"><?= $total ?> dates de départ</div><?

	} else {
		$arr_progress = [ 'progress_parent'  => 'datedepart_iti' ,
		                  'progress_name'    => 'xml_job_datedepart_iti' ,
		                  'progress_text'    => " XML . $total itinéraires " ,
		                  'progress_message' => $i . ' / ' . $total ,
		                  'progress_max'     => $total ,
		                  'progress_value'   => $i ];
		//
		if ( !empty($msg) ) {
			$arr_progress['progress_log'] = $msg;
		}
		//
		skelMdl::send_cmd('act_progress' , $arr_progress);

		foreach ($xml->CostaCruiseCatalog->Destination as $key => $Destination) {

			$attr             = $Destination->attributes();
			$CODE_DESTINATION = strtoupper((string)$attr['Code']);
			$NOM_DESTINATION  = (string)$attr['DisplayName'];

			$ARR_XML_DESTINATION = $APP_XML_DESTINATION->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR , 'codeXml_destination' => trim($CODE_DESTINATION) ]);
			$iddestination       = (int)$ARR_XML_DESTINATION['iddestination'];
			//
			$msg        = '';
			$arr_insert = [ ];
			//


			//  idxml_destination dans xml_destination avec iddestination null
			if ( !empty($ARR_XML_DESTINATION['idxml_destination']) && !empty($ARR_XML_DESTINATION['iddestination']) ):

				$iddestination = (int)$ARR_XML_DESTINATION['iddestination'];
				foreach ($Destination->Itinerary as $key_iti => $Itinerary) {

					$attr      = $Itinerary->attributes();
					$ITIN_CD   = strtoupper((string)$attr['Code']);
					$ITIN_NAME = (string)$attr['DisplayName'];
					//
					if ( sizeof($Itinerary->Cruise) === 0 ) {
						continue;
					}

					foreach ($Itinerary->Cruise as $key_cruise => $Cruise) {
						$i ++;
						// TEST cruise->attributes pour produit_tarif
						$attr               = $Cruise->attributes();
						$CODE_PRODUIT_TARIF = strtoupper((string)$attr['Code']);
						$NOM_PRODUIT_TARIF  = (string)$ITIN_NAME;
						$HAS_HOTEL          = (string)$attr['HotelMandatory'];
						$AVAILABLE          = (string)$attr['Availability'];
						$HAS_VOL            = (string)$attr['FlightMandatory'];
						$IMMEDIATE          = (string)$attr['IsImmediateConfirm'];
						$ACTIF              = ((string)$attr['Sellability'] === 'Y');
						$DATE_DEBUT         = coupeChaine((string)$attr['DepartureDate'] , 10 , null);
						$DATE_ARRIVEE       = coupeChaine((string)$attr['ArrivalDate'] , 10 , null);
						$VILLE_ARRIVEE      = (string)$attr['ArrivalPort'];
						$VILLE_DEPART       = (string)$attr['DeparturePort'];
						$CODE_TRANSPORT     = (string)$attr['Ship'];

						$ARR_TRANSPORT = $APP_TRANSPORT->findOne([ 'codeTransport' => $CODE_TRANSPORT , 'idfournisseur' => $idfournisseur ]);
						if ( empty($ARR_TRANSPORT['idtransport']) ) {
							$arr_progress['progress_log'] = 'pas de transport ' . $CODE_TRANSPORT;
							skelMdl::send_cmd('act_progress' , $arr_progress);
							continue;
						}
						$idtransport = (int)$ARR_TRANSPORT['idtransport'];
						// test ville
						$ARR_VILLE_DEPART  = $APP_XML_VILLE->findOne([ 'codeXml_ville' => $VILLE_DEPART , 'idfournisseur' => $idfournisseur ]);
						$ARR_VILLE_ARRIVEE = $APP_XML_VILLE->findOne([ 'codeXml_ville' => $VILLE_ARRIVEE , 'idfournisseur' => $idfournisseur ]);
						if ( empty($ARR_VILLE_DEPART['idville']) || empty($ARR_VILLE_ARRIVEE['idville']) || empty($ARR_VILLE_DEPART['nomVille']) || empty($ARR_VILLE_ARRIVEE['nomVille']) ) {
							$arr_progress['progress_log'] = 'pas de ville ' . $VILLE_DEPART . ' ou ' . $VILLE_ARRIVEE;
							skelMdl::send_cmd('act_progress' , $arr_progress);
							continue;
						}
						$idville         = (int)$ARR_VILLE_DEPART['idville'];
						$idville_arrivee = (int)$ARR_VILLE_ARRIVEE['idville'];
						//
						$datetime1         = new DateTime($DATE_DEBUT);
						$datetime2         = new DateTime($DATE_ARRIVEE);
						$interval          = $datetime1->diff($datetime2);
						$dureeJoursproduit = (int)$interval->format('%a');
						// test produit
						$ARR_PRODUIT = $APP_PRODUIT->findOne([ 'codeProduit' => $ITIN_CD , 'idfournisseur' => $idfournisseur ]);
						if ( empty($ARR_PRODUIT['idproduit']) ) {
							$idproduit = $APP_PRODUIT->insert([ 'codeProduit'       => $ITIN_CD ,
							                                    'nomProduit'        => $ITIN_NAME ,
							                                    'idfournisseur'     => $idfournisseur ,
							                                    'iddestination'     => $iddestination ,
							                                    'codeFournisseur'   => $CODE_FOURNISSEUR ,
							                                    'idtransport'       => $idtransport ,
							                                    'idville'           => $idville ,
							                                    'idville_arrivee'   => $idville_arrivee ,
							                                    'dureeJoursProduit' => $dureeJoursproduit ,
							                                    'dureeProduit'      => $dureeJoursproduit ,
							                                    'estActifProduit'   => 0 ]);
							$ARR_PRODUIT = $APP_PRODUIT->findOne([  'idproduit' => $idproduit ]);
						} else {
							$idproduit = (int)$ARR_PRODUIT['idproduit'];
						}

						// test produit_tarif (date)
						$ARR_PRODUIT_TARIF = $APP_PRODUIT_TARIF->findOne([ 'idproduit' => $idproduit , 'codeProduit_tarif' => $CODE_PRODUIT_TARIF ]);
						if ( empty($ARR_PRODUIT_TARIF['idproduit_tarif']) ) {
							// insert cruise here
							$insert_vars     = [ 'idproduit'              => $idproduit ,
							                     'idproduit_type'         => 2 ,
							                     'codeProduit_tarif'      => $CODE_PRODUIT_TARIF ,
							                     'nomProduit_tarif'       => $DATE_DEBUT . ' ' . $CODE_FOURNISSEUR . ' ' . $CODE_TRANSPORT ,
							                     'dateDebutProduit_tarif' => $DATE_DEBUT ,
							                     'dateProduit_tarif'      => $DATE_DEBUT ,
							                     'dateFinProduit_tarif'   => $DATE_ARRIVEE ,
							                     'xml_mode'               => 1 ,
							                     'from_csv'               => $idfournisseur ];
							$idproduit_tarif = $APP_PRODUIT_TARIF->insert($insert_vars);
							//
							$arr_progress['progress_log'] = "insertion prix $CODE_PRODUIT_TARIF $NOM_PRODUIT_TARIF $DATE_DEBUT";
							skelMdl::send_cmd('act_progress' , $arr_progress);
						} else {
							$idproduit_tarif = (int)$ARR_PRODUIT_TARIF['idproduit_tarif'];
						}


						$change = false;
						if($ARR_PRODUIT['iddestination']!=$iddestination){
							$change = true;
						}
						// update produit with transport or else if not
						if ( empty($ARR_PRODUIT['m_mode']) && $change || empty($ARR_PRODUIT['dureeProduit']) || empty($ARR_PRODUIT['from_csv']) || empty($ARR_PRODUIT['idtransport']) || empty($ARR_PRODUIT['idville']) || empty($ARR_PRODUIT['idville_arrivee']) ) {
							$update_vars_produit = [ 'estActifProduit'   => 0 ,
							                         'idproduit'         => (int)$idproduit ,
							                         'idproduit_type'    => 2 ,
							                         'idtransport'       => $idtransport ,
							                         'idville'           => $idville ,
							                         'idville_arrivee'   => $idville_arrivee ,
							                         'iddestination'     => $iddestination ,
							                         'dureeJoursProduit' => $dureeJoursproduit ,
							                         'dureeProduit'      => $dureeJoursproduit ,
							                         'xml_mode'          => 1 ,
							                         'from_csv'          => $idfournisseur ];

							$APP_PRODUIT->update(['idproduit'=>(int)$idproduit],$update_vars_produit);
							$ARR_PRODUIT = array_merge($ARR_PRODUIT,$update_vars_produit);
							// $ARR_PRODUIT = $APP_PRODUIT->findOne([ 'codeProduit' => $ITIN_CD , 'idfournisseur' => $idfournisseur ]);

							$arr_progress['progress_log'] = "Mise à jour produit $dureeJoursproduit -  $idproduit .  $idtransport .  $idville .  $idville_arrivee";
							skelMdl::send_cmd('act_progress' , $arr_progress);
						}

						if ( !empty($ARR_PRODUIT['idproduit']) ) {


						}

					}

				}
			else:
				echo $CODE_DESTINATION . ' ' . $NOM_DESTINATION . ' manquante<br>';
			endif;
			//

			//
			$arr_progress = [ 'progress_parent'  => 'datedepart_iti' ,
			                  'progress_name'    => 'xml_job_datedepart_iti' ,
			                  'progress_text'    => " XML . $total itinéraires " ,
			                  'progress_message' => $i . ' / ' . $total ,
			                  'progress_max'     => $total ,
			                  'progress_value'   => $i ];
			//
			if ( !empty($msg) ) {
				$arr_progress['progress_log'] = $msg;
			}
			//
			skelMdl::send_cmd('act_progress' , $arr_progress);
		}
		vardump_async([ 'fin traitement date de depart' ]);


		skelMdl::run($PATH . 'read/readcosta_cruise' , [ 'file' => 'red' ,
		                                                 'url'  => 'la suite et fin ' ,
		                                                 'run'  => 10000 ]);
	}?>
	FIN read_costa_datedepart