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

?>
<?
	if (empty($_POST['run'])):
		$ct_old = $collection_tarif->find(['dateDebutProduit_tarif' => ['$lte' => date('Y-m-d')]]);
		$ct_tar = $ct_old->count();
		// dateDebutProduit_tarif
		?>
		<div class="flex_h blanc" style="height:100%">
			<div style="width:60px;" class="aligncenter">
				<br>
				<i class="fa fa-code fa-2x"></i></div>
			<div class="flex_main">
				<div class="flex_v" style="height:100%">
					<table class="tabletop">
						<tr>
							<td class="texterouge">
								<br>
								Voulez vous lancer une mise en production ?
								<br>
								<br>
								<?= $ct_tar ?> dates passées seront effacées.
							</td>
						</tr>
					</table>
					<div class="padding">
						<input type="button" class="validButton" value="Mise en production" onclick="$('frame_xmlte').loadModule('business/<?= BUSINESS ?>/app/app_admin/app_build_pre_prod','run=1')">
					</div>
					<div class="padding flex_main" style="overflow:auto">
						<progress value="0" id="auto_prod_job"></progress>
					</div>
				</div>
				<div style="display:none;width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte"></div>
			</div>
		</div>
		<?
		return;
	endif;
	set_time_limit(0);
	ini_set('max_execution_time', 0);
	ini_set('max_input_time', 0);
	ini_set('display_errors', 55);
	////////////////////////////////////////////////////

	$_GET = empty($_GET) ? $_POST : $_GET;

	$nbRows = empty($_GET['nbRows']) ? 45 : $_GET['nbRows'];
	$page   = empty($_GET['page']) ? 1 : $_GET['page'];
	$vars   = empty($_GET['vars']) ? [] : fonctionsProduction::cleanPostMongo($_GET['vars'], 1);

	$collection_tarif->remove(['dateDebutProduit_tarif' => ['$lte' => date('Y-m-d')]]);

	//
	if (sizeof($vars) == 0):
		$rsP = $collection->find()->sort(['idproduit' => 1]); //,'idproduit'=>11003 + array( 'estActifProduit' => 1 )
	else:
		$rsP = $collection->find($vars)->sort(['idproduit' => 1]); //,'idproduit'=>11003
	endif;

	$APP_FOURNISSEUR = new App('fournisseur');
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

		// luxe, prestige ...
		$ARR_FOURNISSEUR                       = $APP_FOURNISSEUR->findOne(['idfournisseur' => $idfournisseur]);
		$arrClean['idfournisseur_categorie']   = (int)$ARR_FOURNISSEUR['idfournisseur_categorie'];
		$arrClean['codeFournisseur_categorie'] = $ARR_FOURNISSEUR['codeFournisseur_categorie'];
		$arrClean['nomFournisseur_categorie']  = $ARR_FOURNISSEUR['nomFournisseur_categorie'];

		$testEtape = $APP_ETAPE->find(['idproduit' => $idproduit]);

		if ($testEtape->count() == 0):
			$arrClean['estActifProduit'] = 0;
		else:
			$arrLastVille                = $testEtape->sort(['ordreProduit_etape' => -1])->limit(1)->getNext();
			$arrClean['nomVilleArrivee'] = $arrLastVille['nomVille'];
			$arrClean['idvilleArrivee']  = (int)$arrLastVille['idville'];
		endif;
		if (empty($arrP['idville'])) {
			$testEtape->reset();
			$arrFirstVille        = $testEtape->sort(['ordreProduit_etape' => 1])->limit(1)->getNext();
			$arrClean['nomVille'] = $arrFirstVille['nomVille'];
			$arrClean['idville']  = (int)$arrFirstVille['idville'];
		}

		if (!empty($arrP['idville']) && empty($arrP['idpays'])):
			$arrV                = $APP_VILLE->findOne(['idville' => (int)$arrP['idville']]);
			$idpays              = (int)$arrV['idpays'];
			$arrPays             = $APP_PAYS->findOne(['idpays' => (int)$idpays]);
			$arrClean['idpays']  = (int)$arrPays    ['idpays'];
			$arrClean['nomPays'] = $arrPays['nomPays'];
		endif;
		$newNom = fonctionsSite::buildTitreProduit($arrP['idproduit']);
		if (empty($arrP['m_mode']) && $arrP['nomProduit'] != $newNom && !empty($newNom)):
			$arrClean['nomProduit'] = $newNom;
			skelMdl::send_cmd('act_progress', ['progress_name'    => 'prod_job',
			                                   'progress_log'     => $arrP['idproduit'] . ' rename !! ' . $newNom,
			                                   'progress_message' => $idproduit], session_id());
		endif;
		if (!empty($arrP['XML_CODE'])):
			$rs_tarif = $APP_TARIF->query(['idproduit' => $idproduit]);
			while ($arr_tarif = $rs_tarif->getNext()):
				$test_xml = $APP_XML->query(['DepartureDate' => $arr_tarif['dateDebutProduit_tarif'], 'XML_CODE' => $arrP['XML_CODE']]);
				$wu++;
				if ($test_xml->count() == 0) {
					$u++;
					$collection_tarif->remove(['idproduit' => $idproduit, 'idproduit_tarif' => (int)$arr_tarif['idproduit_tarif']]);
					$collection_tarif_gamme->remove(['idproduit' => $idproduit, 'idproduit_tarif' => (int)$arr_tarif['idproduit_tarif']], ['multi' => 1]);
					// skelMdl::send_cmd('act_notify' , array( 'msg'    => $arrP['XML_CODE'].' '.$idproduit.' '.date_fr($arr_tarif['dateDebutProduit_tarif']) ) , session_id());
				}
			endwhile;
		endif;
		// repare cabine
		$rs_tarifgamme = $APP_TARIF_GAMME->distinct('idgamme', ['idproduit' => $idproduit]);
		// while ($arr_tarif_gamme = $rs_tarifgamme->getNext()):
		foreach ($rs_tarifgamme as $key => $value) :
			$idgamme = (int)$value;

			$test_cab  = $col_transport_cabine->find(['idtransport' => $idtransport,
			                                          'idgamme'     => $idgamme]);
			$arr_gamme = $APP_GAMME->query_one(['idgamme' => $idgamme]);

			if ($test_cab->count() == 0 && !empty($arr_gamme['codeGamme'])):
				$idtransport_cabine = (int)$APP->getNext('idtransport_cabine');

				$col_transport_cabine->insert(['idtransport_cabine'    => $idtransport_cabine,
				                               'idtransport'           => $idtransport,
				                               'idgamme'               => $idgamme,
				                               'codeTransport_cabine'  => $arr_gamme['codeGamme'],
				                               'ordreTransport_cabine' => $arr_gamme['ordreGamme'],
				                               'nomGamme'              => $arr_gamme['nomGamme'],
				                               'nomTransport_cabine'   => $arr_gamme['nomGamme']]);
			endif;
		endforeach;
		// endwhile;

		// des prix ?
		$rs_tarifgamme = $APP_TARIF_GAMME->find(['idproduit' => $idproduit, 'prixProduit_tarif_gamme' => ['$nin' => [null, '', 0]]])->sort(['prixProduit_tarif_gamme' => 1])->limit(1);
		//
		if ($rs_tarifgamme->hasNext()) {
			$arrClean['estActifProduit'] = 1;
			$arr_tarif                   = $rs_tarifgamme->getNext();
			$arrClean['prixProduit']     = $arr_tarif['prixProduit_tarif_gamme'];
			if (!empty($arr_tarif['oldPrixProduit_tarif_gamme']) && ((int)$arr_tarif['oldPrixProduit_tarif_gamme'] > (int)$arr_tarif['prixProduit_tarif_gamme'])):
				$arrClean['oldPrixProduit']  = $arr_tarif['oldPrixProduit_tarif_gamme'];
				$arrClean['estPromoProduit'] = 1;
			else:
				$arrClean['oldPrixProduit']  = '';
				$arrClean['estPromoProduit'] = 0;
			endif;
		} else {
			$arrClean['estActifProduit'] = 0;
		}
		// des dates  ?
		$rs_tarif = $APP_TARIF->find(['idproduit' => $idproduit])->limit(1);
		if (!$rs_tarif->hasNext()) {
			$arrClean['estActifProduit'] = 0;
		}
		// des etapes  ?
		$rs_tarif = $APP_ETAPE->find(['idproduit' => $idproduit]);
		if ($rs_tarif->count() == 0) {
			$arrClean['estActifProduit'] = 0;
		}

		if ($arrClean['dureeJoursProduit'] < 4) {
			$arrClean['estActifProduit'] = 0;
		}
		// $arrClean['oldPrixProduit'] = '';
		$collection->update(['idproduit' => (int)$idproduit], ['$set' => $arrClean], ['upsert' => true]);

		//
		skelMdl::send_cmd('act_progress', ['progress_name'    => 'prod_job',
		                                   'progress_value'   => $i,
		                                   'progress_log'     => $arrP['estActifProduit'] . ' ' . $arrP['idproduit'] . ' ' . $arrP['nomFournisseur'] . ' ' . $arrP['nomProduit'] . ' ' . $rs_tarifgamme->count(),
		                                   'progress_max'     => ($rsP->count()),
		                                   'progress_message' => $idproduit], session_id());

	}

	echo "DONE " . $i . ' ' . $wu . ' ' . $u;