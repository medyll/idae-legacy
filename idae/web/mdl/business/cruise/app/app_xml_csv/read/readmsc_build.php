<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	ignore_user_abort(true);
	$time = time();
	$vars = ['notify' => 'DÃ©but mise en production'];

	$db                     = $APP->plug_base('sitebase_production');
	$collection             = $db->produit;
	$collection_tarif       = $db->produit_tarif;
	$collection_tarif_gamme = $db->produit_tarif_gamme;

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	$CODE_FOURNISSEUR = 'MSC';
	$idfournisseur    = 21;

	if (empty($_POST['run'])):
		$ct_old = $collection->find(['from_csv'=>$idfournisseur,'idfournisseur'=>$idfournisseur]);
		$ct_tar = $ct_old->count();
		// dateDebutProduit_tarif
		?> BUILD <?=$ct_tar?>
		<?
		return;
	endif;
	set_time_limit(0);
	ini_set('max_execution_time', 0);
	ini_set('max_input_time', 0);
	ini_set('display_errors', 55);
	////////////////////////////////////////////////////



	$nbRows = empty($_GET['nbRows']) ? 45 : $_GET['nbRows'];
	$page   = empty($_GET['page']) ? 1 : $_GET['page'];
	$vars   = empty($_GET['vars']) ? [] : fonctionsProduction::cleanPostMongo($_GET['vars'], 1);


	//
	$rsP = $collection->find(['idfournisseur'=>$idfournisseur])->sort(['idproduit' => 1]); //,'idproduit'=>11003 + array( 'estActifProduit' => 1 )


	$APP_TARIF       = new App('produit_tarif');
	$APP_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_GAMME       = new App('gamme');
	$APP_ETAPE       = new App('produit_etape');
	$APP_PRODUIT     = new App('produit');
	$APP_XML         = new App('xml_cruise');
	$APP_VILLE       = new App('ville');
	$APP_PAYS        = new App('pays');

	$APP_RS_TARIF_GAMME   = $APP->plug('sitebase_production', 'produit_tarif_gamme');
	$col_transport_cabine = $APP->plug('sitebase_production', 'transport_cabine');

	while ($arrP = $rsP->getNext()) {
		$i++;
		//
		$arrClean = fonctionsProduction::cleanPostMongo($arrP, 1);
		//
		$idproduit     = (int)$arrClean['idproduit'];
		$idfournisseur = (int)$arrClean['idfournisseur'];
		$idtransport   = (int)$arrClean['idtransport'];
		//
		$arr_grilleDate                = $db->produit_tarif->distinct('dateDebutProduit_tarif', ['idproduit' => $idproduit]); // ,'dateDebutProduit_tarif'=>array('gt'=>date('Y-m-d'))
		$arrClean['grilleDateProduit'] = $arr_grilleDate;
		// dateDebutProduit
		$arr_firstDate                = $db->produit_tarif->find(['idproduit' => $idproduit])->sort(['dateDebutProduit_tarif' => 1])->getNext(); // ,'dateDebutProduit_tarif'=>array('gt'=>date('Y-m-d'))
		$arrClean['dateDebutProduit'] = $arr_firstDate['dateDebutProduit_tarif'];
		// des prix ?
		$rs_tarifgamme = $APP_TARIF_GAMME->find(['idproduit' => $idproduit,'prixProduit_tarif_gamme'=>['$nin'=>[null,'',0]]])->sort(['prixProduit_tarif_gamme' => 1])->limit(1);
		//
		if ($rs_tarifgamme->hasNext()) {
			$arrClean['estActifProduit'] = 1;
			$arr_tarif                   = $rs_tarifgamme->getNext();
			$arrClean['prixProduit']     = $arr_tarif['prixProduit_tarif_gamme'];
			if (!empty($arr_tarif['oldPrixProduit_tarif_gamme']) && ((int)$arr_tarif['oldPrixProduit_tarif_gamme'] > (int)$arr_tarif['prixProduit_tarif_gamme'])):
				$arrClean['oldPrixProduit']  = $arr_tarif['oldPrixProduit_tarif_gamme'];
				// $arrClean['estPromoProduit'] = 1;
			else:
				$arrClean['oldPrixProduit']  = '';
				// $arrClean['estPromoProduit'] = 0;
			endif;
		} else {
			$arrClean['estActifProduit'] = 0;
		}
		// des dates  ?
		$rs_tarif = $APP_TARIF->find(['idproduit' => $idproduit])->limit(1);
		if ($rs_tarif->count() == 0) {
			$arrClean['estActifProduit'] = 0;
		}
		if ($arrClean['dureeJoursProduit'] < 4) {
			$arrClean['estActifProduit'] = 0;
		}


		$collection->update(['idproduit' => (int)$idproduit], ['$set' => $arrClean], ['upsert' => true]);

		//
		if ($arrClean['estActifProduit'] != $arrP['estActifProduit']) {

			// vardump_async($arrClean['estActifProduit'], true);

			/*skelMdl::send_cmd('act_progress', ['progress_parent'  => 'build_msc','progress_name'    => 'prod_job',
			                                   'progress_value'   => $i,
			                                   'progress_log'     => $arrClean['estActifProduit'] . ' ' . $arrP['idproduit'] . ' ' . $arrClean['nomFournisseur'] . ' ' . $arrClean['nomProduit'] . ' ' . $rs_tarifgamme->count(),
			                                   'progress_max'     => ($rsP->count()),
			                                   'progress_message' => $idproduit], session_id());*/
		}
		//

		skelMdl::send_cmd('act_progress', ['progress_parent'  => 'build_msc','progress_name'  => 'prod_job',
		                                   'progress_value' => $i,
		                                   'progress_message' => $i,
		                                   'progress_log'  => $arrP['estActifProduit'] . ' ' . $arrP['idproduit'] . ' ' . $arrP['nomProduit'] ,
		                                   'progress_max'   => ($rsP->count())]);

	}

	skelMdl::send_cmd('act_progress', ['progress_parent'  => 'build_msc','progress_name'  => 'prod_job',
	                                   'progress_value' => 100,
	                                   'progress_text'  => ' TERMINE ',
	                                   'progress_message'  => ' TERMINE ',
	                                   'progress_max'   => 100]);
	// echo "DONE " . $i . ' ' . $wu . ' ' . $u;