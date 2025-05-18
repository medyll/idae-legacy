<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 30/11/2017
	 * Time: 19:13
	 */
	include_once($_SERVER['CONF_INC']);

	$PATH      = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

	$arr_dest = ['Mediterranean', 'Caribbean', 'Indian Ocean', 'North Europe', 'Pacific Asia', 'Persian Gulf', 'Round World', 'South America', 'Transatlantic'];
	// $arr_dest = [ 'Mediterranean' , 'Caribbean' , 'Indian Ocean' ];

	$CODE_FOURNISSEUR          = 'COSTA';
	$_POST['destination_file'] = $arr_dest[0];
	$destination_file          = 'finito';//$_POST['destination_file'];

	$APP                     = new App();
	$APP_DESTINATION         = new App('destination');
	$APP_FOURNISSEUR         = new App('fournisseur');
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



	$ARR_FOURNISSEUR = $APP_FOURNISSEUR->findOne(['codeFournisseur' => $CODE_FOURNISSEUR]);
	$idfournisseur   = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$arr_progress = ['progress_parent'  => 'xml_cruise_costa',
	                 'progress_name'    => 'xml_job_xml_cruise_costa',
	                 'progress_text'    => " XML . $total   ",
	                 'progress_message' => $i . ' / ' . $total,
	                 'progress_max'     => $total,
	                 'progress_value'   => $i];
	//
	if (!empty($msg)) {
		$arr_progress['progress_log'] = $msg;
	}
	clearstatcache();
	/*foreach ($arr_dest as $key => $destination_file) {
		$ARR_DESTINATION = $APP_DESTINATION->findOne([ 'codeDestination' => $destination_file ]);
		$iddestination   = (int)$ARR_DESTINATION['iddestination'];*/

	$i            = 0;
	$non_attr     = 0;
	$orphans      = 0;
	$collect_ship = [];

	$local_rep = XMLDIR . 'costa/';
	// $xml       = simplexml_load_file($local_rep . 'prices_' . $destination_file . '.xml');
	$xml   = simplexml_load_file($local_rep . 'prices_costa.xml');
	$xml_p = $xml->CruisePriceCatalog;

	$total = sizeof($xml_p->Cruises->Cruise);
	if (empty($_POST['run'])) {
		?>
		<div class="padding"><?=$total?> Prix et cabines</div><?
		exit;
	} elseif (!empty($_POST['run']) && empty($_POST['run_delay'])) {

	}

	/*$arr_progress['progress_log'] = $destination_file;
	skelMdl::send_cmd('act_progress' , $arr_progress);*/

	foreach ($xml_p->Cruises as $key => $Cruises) {

		foreach ($Cruises->Cruise as $key_iti => $Cruise) {
			$i++;

			$arr_progress['progress_value'] = ++$i;
			$attr                           = $Cruise->attributes();

			$CRUISE_NAME = (string)$attr['DisplayName'];
			$ITIN_CD     = (string)$attr['Code'];

			// dates de départ
			$TEST_PRODUIT_TARIF = $APP_PRODUIT_TARIF->findOne(['codeProduit_tarif' => $ITIN_CD]);
			$TEST_PRODUIT       = $APP_PRODUIT->findOne(['idproduit' => (int)$TEST_PRODUIT_TARIF['idproduit']]);
			//
			if (empty($TEST_PRODUIT_TARIF['idproduit']) || empty($TEST_PRODUIT_TARIF['idproduit_tarif']) || empty($TEST_PRODUIT['idtransport'])) {

				$arr_progress['progress_log'] = 'pas de produit ' . $ITIN_CD . ' ' . $destination_file;
				skelMdl::send_cmd('act_progress', $arr_progress);
				continue;
			}

			$idproduit       = (int)$TEST_PRODUIT_TARIF['idproduit'];
			$idtransport     = (int)$TEST_PRODUIT['idtransport'];
			$codeTransport   = (int)$TEST_PRODUIT['codeTransport'];
			$idproduit_tarif = (int)$TEST_PRODUIT_TARIF['idproduit_tarif'];

			foreach ($Cruise->Categories as $key_cr => $Categories) {
				foreach ($Categories as $key_cat => $Categorie) {
					$attr = $Categorie->attributes();
					//
					$out['Code']                    = (string)$attr['Code'];
					$out['codeProduit_tarif_gamme'] = (string)$attr['Code'].'-'.$ITIN_CD;
					$out['Description']             = (string)$attr['Description'];
					$out['Discount']                = (string)$attr['Discount'];// si Basic
					$out['BestPrice']               = (string)$attr['BestPrice'];
					$out['ListPrice']               = (string)$attr['ListPrice'];
					$out['prixProduit_tarif_gamme'] = (int)$attr['BestPrice'];
					// $out['oldPrixProduit_tarif_gamme'] = (int)$attr['BestPrice'];
					$out['nomProduit_tarif_gamme']  = 'COSTA ' . $codeTransport . ' ' . $out['Description'];
					$out['1A']                      = (int)$attr['SingleSupplement'];
					/*$out['FirstAdult']              = (string)$attr['FirstAdult'];
					$out['ThirdAdult']              = (string)$attr['ThirdAdult'];
					$out['FourthAdult']             = (string)$attr['FourthAdult'];
					$out['ThirdChild']              = (string)$attr['ThirdChild'];
					$out['ThirdJunior']             = (string)$attr['ThirdJunior'];
					$out['CurrencyCode']            = (string)$attr['CurrencyCode'];
					$out['MandatoryFlight']         = (string)$attr['MandatoryFlight'];
					$out['HotelMandatory']          = (string)$attr['Availability'];
					$out['HotelMandatory']          = (string)$attr['HotelMandatory'];*/

					// dates de départ
					$TEST_TRANSPORT_GAMME = $APP_TRANSPORT_GAMME->findOne(['idtransport' => $idtransport, 'codeTransport_gamme' => $out['Code']]);

					$idtransport_gamme = (int)$TEST_TRANSPORT_GAMME['idtransport_gamme'];
					$idgamme           = (int)$TEST_TRANSPORT_GAMME['idgamme'];
					if (empty($idtransport_gamme)) {
						$arr_progress['progress_log'] = 'pas de cabine ' . $ITIN_CD . ' ' . $TEST_PRODUIT['nomTransport'] . ' ' . $out['Code'];
						skelMdl::send_cmd('act_progress', $arr_progress);
						continue;
					}
					//
					$TEST_PRODUIT_TARIF_GAMME = $APP_PRODUIT_TARIF_GAMME->findOne(['idproduit_tarif' => $idproduit_tarif, 'idtransport_gamme' => $idtransport_gamme,'codeProduit_tarif_gamme'=>$out['codeProduit_tarif_gamme']]);


					if (empty($TEST_PRODUIT_TARIF_GAMME['idproduit_tarif_gamme'])) {
						$arr_progress['progress_name'] = "xml_produit_prix_$idproduit";
						$arr_progress['progress_log'] = 'Creation prix ' . $ITIN_CD . ' ' . $TEST_PRODUIT['nomTransport'] . ' ' . $out['Code'].' '.$out['BestPrice'];
						skelMdl::send_cmd('act_progress', $arr_progress);
						$out['idproduit']         = $idproduit;
						$out['idtransport']       = $idtransport;
						$out['idproduit_tarif']   = $idproduit_tarif;
						$out['idtransport_gamme'] = $idtransport_gamme;
						$out['idgamme']           = $idgamme;
						$APP_PRODUIT_TARIF_GAMME->insert($out);
					} else {

						if ($TEST_PRODUIT_TARIF_GAMME['prixProduit_tarif_gamme'] > $out['prixProduit_tarif_gamme']) {
							$upd['oldPrixProduit_tarif_gamme'] = (int)$TEST_PRODUIT_TARIF_GAMME['prixProduit_tarif_gamme'];
							$upd['prixProduit_tarif_gamme']     = (int)$out['prixProduit_tarif_gamme'];
						}

						$upd['idproduit']         = $idproduit;
						$upd['idtransport']       = $idtransport;
						$upd['idproduit_tarif']   = $idproduit_tarif;
						$upd['idtransport_gamme'] = $idtransport_gamme;
						$upd['idgamme']           = $idgamme;
						$APP_PRODUIT_TARIF_GAMME->update(['idproduit_tarif_gamme' => (int)$TEST_PRODUIT_TARIF_GAMME['idproduit_tarif_gamme']], $upd);
					}
				}
			}

			$idprice    = null;
			$msg        = '';
			$arr_insert = [];

			//
			/*$final_vars['']=$idproduit;
			$final_vars['']=$idproduit_tarif; */
			$arr_progress['progress_value'] = $i;
			skelMdl::send_cmd('act_progress', $arr_progress);

		}

	}
	skelMdl::run($PATH . 'read/readcosta_iti' , [ 'file' => 'red' ,
	                                                 'url'  => 'la suite et fin ' ,
	                                                 'run'  => 10000 ]);

?>
FIN read_costa_cruise
