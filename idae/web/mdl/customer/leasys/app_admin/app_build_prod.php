<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	ignore_user_abort(true);
	$time = time();
	$vars = array( 'notify' => 'Début mise en production' );

	$db                     = $APP->plug_base('sitebase_production');
	$collection             = $db->produit;
	$collection_tarif       = $db->produit_tarif;
	$collection_tarif_gamme = $db->produit_tarif_gamme;

?>
<?
	if(empty($_POST['run'])):
		$ct_old = $collection_tarif->find(['dateDebutProduit_tarif'=>['$lte'=>date('Y-m-d')]]);
		$ct_tar =  $ct_old->count();
		// dateDebutProduit_tarif
?>
	<div style="width:350px;">
		<table class="tabletop">
			<tr>
				<td style="width:90px;text-align:center"><br>
					<img src="<?=ICONPATH?>alert32.png" /></td>
				<td class="texterouge"><br>
					Voulez vous lancer une mise en production ?<br>
					<br>
					<?=$ct_tar?>  dates passées seront effacées.

				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_prod_job"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Mise en production" onclick="$('frame_xmlte').loadModule('app/app_admin/app_build_prod','run=1')"  >
			<input type="reset" value="Fermer" class="cancelClose" >
		</div>
		<div style="display:none;width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte" scrolling="auto"></div>
	</div>
<?
	return;
endif;
	set_time_limit(0);
	ini_set('max_execution_time' , 0);
	ini_set('max_input_time' , 0);
	ini_set('display_errors' , 55);
	////////////////////////////////////////////////////

	$_GET = empty($_GET) ? $_POST : $_GET;

	$nbRows = empty($_GET['nbRows']) ? 45 : $_GET['nbRows'];
	$page   = empty($_GET['page']) ? 1 : $_GET['page'];
	$vars   = empty($_GET['vars']) ? array() : fonctionsProduction::cleanPostMongo($_GET['vars'] , 1);

	$collection_tarif->remove(['dateDebutProduit_tarif'=>['$lte'=>date('Y-m-d')]]);

	//
	if(sizeof($vars)==0):
		$rsP = $collection->find($vars + array( 'estActifProduit' => 1 ))->sort(array( 'idproduit' => - 1 )); //,'idproduit'=>11003
		else:
			$rsP = $collection->find($vars)->sort(array( 'idproduit' => - 1 )); //,'idproduit'=>11003
		endif;



	$APP_TARIF       = new App('produit_tarif');
	$APP_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_GAMME       = new App('gamme');
	$APP_ETAPE       = new App('produit_etape');
	$APP_PRODUIT     = new App('produit');
	$APP_XML         = new App('xml_cruise');
	$APP_VILLE       = new App('ville');
	$APP_PAYS       = new App('pays');

	$APP_RS_TARIF_GAMME   = $APP->plug('sitebase_production' , 'produit_tarif_gamme');
	$col_transport_cabine = $APP->plug('sitebase_production' , 'transport_cabine');


	while ($arrP = $rsP->getNext()) {
		$i ++;
		//
		$arrClean = fonctionsProduction::cleanPostMongo($arrP , 1);
		//
		echo $idproduit     = (int)$arrClean['idproduit'];
		$idfournisseur = (int)$arrClean['idfournisseur'];
		$idtransport   = (int)$arrClean['idtransport'];
		//
		$arr_grilleDate = $db->produit_tarif->distinct('dateDebutProduit_tarif' , array( 'idproduit' => $idproduit )); // ,'dateDebutProduit_tarif'=>array('gt'=>date('Y-m-d'))

		$arrClean['grilleDateProduit'] = $arr_grilleDate;

		$testEtape = $APP_ETAPE->query(array( 'idproduit' => $idproduit ));

		if ( $testEtape->count() == 0 ):
			$arrClean['estActifProduit'] = 0;
		else:
			$arrLastVille                = $testEtape->sort(array( 'ordreProduit_etape' => - 1 ))->limit(1)->getNext();
			$arrClean['nomVilleArrivee'] = $arrLastVille['nomVille'];
			$arrClean['idvilleArrivee'] = (int)$arrLastVille['idville'];
		endif;
		if (! empty($arrP['idville']) &&  empty($arrP['idpays']) ):
			$arrV   = $APP_VILLE->findOne(array('idville'=>(int)$arrP['idville']))  ;
			$idpays = (int)$arrV['idpays'];
			$arrPays   = $APP_PAYS->findOne(array('idpays'=>(int)$idpays))  ;
			$arrClean['idpays']     =  (int)$arrPays    ['idpays']  ;
		    $arrClean['nomPays']    =    $arrPays['nomPays'];
		endif;
		if ( ! empty($arrP['XML_CODE']) ):
			$rs_tarif = $APP_TARIF->query(array( 'idproduit' => $idproduit ));
			while ($arr_tarif = $rs_tarif->getNext()):
				$test_xml = $APP_XML->query(array( 'DepartureDate' => $arr_tarif['dateDebutProduit_tarif'] , 'XML_CODE' => $arrP['XML_CODE'] ));
				$wu ++;
				if ( $test_xml->count() == 0 ) {
					$u ++;
					$collection_tarif->remove(array( 'idproduit' => $idproduit , 'idproduit_tarif' => (int)$arr_tarif['idproduit_tarif'] ));
					$collection_tarif_gamme->remove(array( 'idproduit' => $idproduit , 'idproduit_tarif' => (int)$arr_tarif['idproduit_tarif'] ) , array( 'multi' => 1 ));
					// skelMdl::send_cmd('act_notify' , array( 'msg'    => $arrP['XML_CODE'].' '.$idproduit.' '.date_fr($arr_tarif['dateDebutProduit_tarif']) ) , session_id());
				}
			endwhile;
		endif;
		// repare cabine
		$rs_tarifgamme = $APP_TARIF_GAMME->distinct('idgamme',array( 'idproduit' => $idproduit ));
		// while ($arr_tarif_gamme = $rs_tarifgamme->getNext()):
			foreach($rs_tarifgamme as $key=>$value) :
				$idgamme = (int)$value;

				$test_cab = $col_transport_cabine->find(array( 'idtransport' => $idtransport ,
				                                               'idgamme'     => $idgamme ));
				$arr_gamme = $APP_GAMME->query_one(array( 'idgamme'     => $idgamme ));

				if ( $test_cab->count() == 0 && !empty($arr_gamme['codeGamme']) ):
					$idtransport_cabine = (int)$APP->getNext('idtransport_cabine');

					$col_transport_cabine->insert(array( 'idtransport_cabine'   => $idtransport_cabine ,
					                                     'idtransport'          => $idtransport ,
					                                     'idgamme'              => $idgamme ,
					                                     'codeTransport_cabine' => $arr_gamme['codeGamme'] ,
					                                     'ordreTransport_cabine' => $arr_gamme['ordreGamme'] ,
					                                     'nomGamme' => $arr_gamme['nomGamme'] ,
					                                     'nomTransport_cabine'  => $arr_gamme['nomGamme'] ));
				endif;
		endforeach;
		// endwhile;


		$rs_tarifgamme = $APP_TARIF_GAMME->query(array( 'idproduit' => $idproduit ))->sort(array( 'prixProduit_tarif_gamme' => 1 ))->limit(1);
		//
		if ( $rs_tarifgamme->hasNext() ) {
			$arr_tarif               = $rs_tarifgamme->getNext();
			$arrClean['prixProduit'] = $arr_tarif['prixProduit_tarif_gamme'];
			if ( ! empty($arr_tarif['oldPrixProduit_tarif_gamme']) && ((int)$arr_tarif['oldPrixProduit_tarif_gamme'] > (int)$arr_tarif['prixProduit_tarif_gamme']) ):
				$arrClean['oldPrixProduit']  = $arr_tarif['oldPrixProduit_tarif_gamme'];
				$arrClean['estPromoProduit'] = 1;
			else:
				$arrClean['oldPrixProduit']  = '';
				$arrClean['estPromoProduit'] = 0;
			endif;
		} else {
			$arrClean['estActifProduit'] = 0;
		}

		// $arrClean['oldPrixProduit'] = '';
		$collection->update(array( 'idproduit' => (int)$idproduit ) , array( '$set' => $arrClean ) , array( 'upsert' => true ));

		//
		skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'prod_job' ,
		                                          'progress_value'   => $i ,
		                                          'progress_max'     => ($rsP->count()) ,
		                                          'progress_message' => $idproduit ) , session_id());

	}

	echo "DONE " . $i . ' ' . $wu . ' ' . $u;