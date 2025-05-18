<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 30/11/2017
	 * Time: 19:13
	 */
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	$arr_dest = ['Mediterranean', 'Caribbean', 'Indian Ocean', 'North Europe', 'Pacific Asia', 'Persian Gulf', 'Round World', 'South America', 'Transatlantic'];
	$arr_dest = ['Pacific Asia'];
	// $arr_dest = [ 'Mediterranean' , 'Caribbean' , 'Indian Ocean' ];

	$CODE_FOURNISSEUR          = 'COSTA';
	$_POST['destination_file'] = $arr_dest[0];

	$APP                 = new App();
	$APP_DESTINATION     = new App('destination');
	$APP_FOURNISSEUR     = new App('fournisseur');
	$APP_PRODUIT         = new App('produit');
	$APP_PRODUIT_ETAPE   = new App('produit_etape');
	$APP_XML_VILLE       = new App('xml_ville');
	$APP_XML_DESTINATION = new App('xml_destination');

	$local_rep = XMLDIR . 'costa/';

	$file                 = XMLDIR . 'costa/iti.xml';
	$depth                = 0;
	$tree                 = [];
	$tree['name']         = "root";
	$stack[count($stack)] = &$tree;
	$collect_size         = 0;
	$DESTINATION          = '';
	$ITINERARY            = '';
	$my_collect           = [];

	function startElement($parser, $name, $attrs) {
		global $depth;
		global $stack;
		global $tree;
		global $DESTINATION;
		global $ITINERARY;
		global $my_collect;
		global $collect_size;

		$element         = [];
		$element['name'] = $name;
		foreach ($attrs as $key => $value) {
			$element[$key] = $value;
		}
		if ($element['CODE']) {
			// echo $element['CODE'];
		}
		if ($element['CODE'] && $element['name'] == 'DESTINATION') {
			$DESTINATION                             = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION] =  &$element;
		}
		if ($element['CODE'] && $element['name'] == 'ITINERARY') {
			$ITINERARY                                                        = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION]['ITINERARY'][$ITINERARY] =  &$element;
			$collect_size++;
		}
		if ($element['name'] && $element['name'] == 'STEPS') {
			$STEPS                                                                             = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION]['ITINERARY'][$ITINERARY]['STEPS'][$STEPS] =  &$element;
		}

		$last                   = &$stack[count($stack) - 1];
		$last[count($last) - 1] = &$element;
		$stack[count($stack)]   = &$element;

		$depth++;
	}

	function endElement($parser, $name) {
		global $depth;
		global $stack;
		array_pop($stack);
		$depth--;
	}

	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	if (!($fp = fopen($file, "r"))) {
		die("could not open XML input");
	}

	while ($data = fread($fp, 4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
	$tree_xml = $stack[0][0];

	$total = $collect_size;
	if (empty($_POST['run'])) {
		?>
		<div class="padding"><?= $total ?> Itinéraires costa</div><?
		exit;
	}

	$ARR_FOURNISSEUR = $APP_FOURNISSEUR->findOne(['codeFournisseur' => $CODE_FOURNISSEUR]);
	$idfournisseur   = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$arr_progress = ['progress_parent'  => 'build_itis_costa',
	                 'progress_name'    => 'xml_job_build_itis_costa',
	                 'progress_text'    => " XML . $total   ",
	                 'progress_message' => $i . ' / ' . $total,
	                 'progress_max'     => $total,
	                 'progress_value'   => $i];
	//
	if (!empty($msg)) {
		$arr_progress['progress_log'] = $msg;
	}
	$local_rep = XMLDIR . 'costa/';
	$xml       = simplexml_load_file($local_rep . 'iti.xml');
	$xml_p     = $xml->Destination;
	//vardump_async($xml_p);
	foreach ($xml_p as $key => $Destination) {
		$attr = $Destination->attributes();

		$codeDestination = trim((string)($attr['Code']));
		$nomDestination  = $attr['DisplayName'];

		$ARR_DESTINATION = $APP_XML_DESTINATION->findOne(['idfournisseur' => $idfournisseur, 'codeXml_destination' => $codeDestination]);
		$iddestination   = (int)$ARR_DESTINATION['iddestination'];

		vardump_async("$codeDestination $nomDestination", true);

		foreach ($Destination as $key_dest => $ITI) {
			$attr_iti = $ITI->attributes();

			$codeItineraire = trim((string)$attr_iti['Code']);
			$nomItineraire  = (string)$attr_iti['DisplayName'];

			vardump_async("$codeItineraire $nomItineraire");

			$ARR_PRODUIT = $APP_PRODUIT->findOne(['codeProduit' => $codeItineraire]);
			$idproduit   = (int)$ARR_PRODUIT['idproduit'];

			$arr_progress['progress_log'] = "$codeItineraire $nomItineraire";
			skelMdl::send_cmd('act_progress', $arr_progress);

			if (empty($idproduit)) {
				$arr_progress['progress_log'] = "Produit non trouvé";
				skelMdl::send_cmd('act_progress', $arr_progress);
				continue;
			}

			foreach ($ITI as $key_STEPS => $STEPS) {
				// vardump_async($STEPS, true);

				foreach ($STEPS as $key_STEP => $STEP) {
					$attr_step = $STEP->attributes();

					$codeVilleDepart  = (string)$attr_step['CodeDeparturePort'];
					$codeVilleArrivee = (string)$attr_step['CodeArrivelPort'];

					$ARR_Ville1 = $APP_XML_VILLE->findOne(['idfournisseur' => $idfournisseur, 'codeXml_ville' => $codeVilleDepart]);
					$ARR_Ville2 = $APP_XML_VILLE->findOne(['idfournisseur' => $idfournisseur, 'codeXml_ville' => $codeVilleArrivee]);

					$idville         = (int)$ARR_Ville1['idville'];
					$idville_arrivee = (int)$ARR_Ville2['idville'];

					if (empty($idville) || empty($idville_arrivee)) {
						vardump_async(['manquant ville ', $codeVilleDepart, $codeVilleArrivee,true]);
						$arr_progress['progress_log'] = "ville manquante codeVilleDepart $codeVilleDepart codeVilleArrivee $codeVilleArrivee";
						skelMdl::send_cmd('act_progress', $arr_progress);
						continue;
					}

					$TEST_ETAPE = $APP_PRODUIT_ETAPE->findOne(['idproduit' => $idproduit, 'idville' => $idville, 'ordreProduit_etape' => (int)$attr_step['DepartureDay']]);

					if (!empty($TEST_ETAPE['idproduit_etape'])) {
						$arr_progress['progress_log'] = "Etape déja présente $codeVilleDepart codeVilleArrivee $codeVilleArrivee";
						skelMdl::send_cmd('act_progress', $arr_progress);
						continue;
					}

					$out['idproduit']               = $idproduit;
					$out['idville']                 = $idville;
					$out['idville_arrivee']         = $idville_arrivee; // pour table produit
					$out['heureFinProduit_etape']   = trim((string)$attr_step['DepartureTime']);
					$out['heureDebutProduit_etape'] = trim((string)$attr_step['ArrivalTime']);
					$out['nomProduit_etape']        = (string)$attr_step['DeparturePortDescription'] . '  ' . (string)$attr_step['ArrivelPortDescrption'];
					$out['ordreProduit_etape']      = (int)$attr_step['DepartureDay'];
					$out['xml_mode']                = 1;
					$out['from_csv']                = (int)$idfournisseur;



					$APP_PRODUIT_ETAPE->insert($out);

					$arr_progress['progress_log'] = "Nlle etape $codeItineraire - $codeVilleDepart - $codeVilleArrivee";
					vardump_async($arr_progress['progress_log']);
					skelMdl::send_cmd('act_progress', $arr_progress);
				}
			}
		}
		// vardump_async($Destination,true);
	}
	foreach ($my_collect['DESTINATION'] as $key => $Destination) {
		continue;
		$codeDestination = trim((string)($Destination['CODE']));
		$nomDestination  = $Destination['DISPLAYNAME'];

		$ARR_DESTINATION = $APP_XML_DESTINATION->findOne(['idfournisseur' => $idfournisseur, 'codeXml_destination' => $codeDestination]);
		$iddestination   = (int)$ARR_DESTINATION['iddestination'];
		if ($codeDestination != "Pacific Asia") continue; // TYO14020
		vardump_async($nomDestination);
		foreach ($Destination['ITINERARY'] as $key_iti => $ITINERARY) {
			// unset($ITINERARY['STEPS']);
			vardump_async($ITINERARY['CODE'], true);

			$codeItineraire = trim((string)$ITINERARY['CODE']);
			$nomItineraire  = (string)$ITINERARY['DISPLAYNAME'];

			if ($codeItineraire == 'TYO14020') {
				vardump_async("$idproduit $codeItineraire $nomItineraire", true);
			}
			vardump_async("$codeItineraire $nomItineraire", true);

			$ARR_PRODUIT = $APP_PRODUIT->findOne(['codeProduit' => $codeItineraire]);
			$idproduit   = (int)$ARR_PRODUIT['idproduit'];

			$arr_progress['progress_log'] = "$codeItineraire $nomItineraire";
			skelMdl::send_cmd('act_progress', $arr_progress);
			if (empty($idproduit)) {
				$arr_progress['progress_log'] = "Produit non trouvé";
				skelMdl::send_cmd('act_progress', $arr_progress);
				continue;
			}
			$arr_progress['progress_log'] = "$codeItineraire $nomItineraire";
			skelMdl::send_cmd('act_progress', $arr_progress);
			foreach ($ITINERARY['STEPS'] as $key_STEPS => $STEPS) {
				//
				/*$msg = $STEPS[0]['CODEDEPARTUREPORT'];
				$arr_progress['progress_log'] = "$codeItineraire - $msg -";
				skelMdl::send_cmd('act_progress' , $arr_progress);*/
				unset($STEPS['name']);
				foreach ($STEPS as $key_STEP => $STEP) {
					$i++;
					unset($STEP['name']);
					/*vardump_async($key_STEP);*/
					if ($STEP == 'name') continue;
					// vardump_async($STEP,true);

					$codeVilleDepart  = (string)$STEP['CODEDEPARTUREPORT'];
					$codeVilleArrivee = (string)$STEP['CODEARRIVELPORT'];

					$ARR_Ville1 = $APP_XML_VILLE->findOne(['idfournisseur' => $idfournisseur, 'codeXml_ville' => $codeVilleDepart]);
					$ARR_Ville2 = $APP_XML_VILLE->findOne(['idfournisseur' => $idfournisseur, 'codeXml_ville' => $codeVilleArrivee]);

					$idville         = (int)$ARR_Ville1['idville'];
					$idville_arrivee = (int)$ARR_Ville2['idville'];

					if (empty($idville) || empty($idville_arrivee)) {
						vardump_async([$codeVilleDepart, $codeVilleArrivee]);
						/*vardump_async([$key_STEP,$STEP],true);
						vardump_async([$STEPS],true);*/
						$arr_progress['progress_log'] = "ville manquante codeVilleDepart $codeVilleDepart codeVilleArrivee $codeVilleArrivee";
						skelMdl::send_cmd('act_progress', $arr_progress);
						continue;
					}

					$TEST_ETAPE = $APP_PRODUIT_ETAPE->findOne(['idproduit' => $idproduit, 'idville' => $idville, 'ordreProduit_etape' => (int)$STEP['DEPARTUREDAY']]);

					if (!empty($TEST_ETAPE['idproduit_etape'])) {
						$arr_progress['progress_log'] = "Etape déja présente $codeVilleDepart codeVilleArrivee $codeVilleArrivee";
						skelMdl::send_cmd('act_progress', $arr_progress);
						continue;
					}
					$out['idproduit']               = $idproduit;
					$out['idville']                 = $idville;
					$out['idville_arrivee']         = $idville_arrivee; // pour table produit
					$out['heureFinProduit_etape']   = $STEP['DEPARTURETIME'];
					$out['heureDebutProduit_etape'] = $STEP['ARRIVALTIME'];
					$out['nomProduit_etape']        = (string)$STEP['DEPARTUREPORTDESCRIPTION'] . '  ' . (string)$STEP['ARRIVELPORTDESCRPTION'];
					$out['ordreProduit_etape']      = (int)$STEP['DEPARTUREDAY'];
					$out['xml_mode']                = 1;
					$out['from_csv']                = (int)$idfournisseur;
					/*$out['dateFinProduit_etape']    = (string)$STEP['DEPARTUREDAY'];
					$out['dateDebutProduit_etape']  = (string)$STEP['ARRIVALDAY'];*/

					$APP_PRODUIT_ETAPE->insert($out);

					$arr_progress['progress_log'] = "Nlle etape $codeItineraire - $codeVilleDepart - $codeVilleArrivee";
					skelMdl::send_cmd('act_progress', $arr_progress);
				}

				$arr_progress['progress_log']   = "$i $total : $codeItineraire - $idproduit";
				$arr_progress['progress_value'] = $i;
				skelMdl::send_cmd('act_progress', $arr_progress);

			}

		}

	}
	$arr_progress['progress_log']   = "Terminé";
	$arr_progress['progress_value'] = 100;
	$arr_progress['progress_max']   = 100;
	skelMdl::send_cmd('act_progress', $arr_progress);