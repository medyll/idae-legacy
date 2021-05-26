<?php

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors' , 55);
    // netoyer les produits // tarifs // dates // ajouter cabines

	$APP = new App();
	$APP_TARIF       = new App('produit_tarif');
	$APP_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_GAMME       = new App('gamme');
	$APP_ETAPE       = new App('produit_etape');
	$APP_PRODUIT     = new App('produit');

	$APP_RS = $APP->plug('sitebase_production','produit_tarif_gamme');
	$APP_RS_DEVIS = $APP->plug('sitebase_devis','devis');
	$APP_PROD = $APP->plug('sitebase_production','produit');
	$APP_RS_CAB = $APP->plug('sitebase_production','transport_cabine');
	$APP_RS_TGAMME = $APP->plug('sitebase_production','transport_gamme');

	$rs = $APP->plug('sitebase_production','produit_tarif_gamme')->find();


	$rse = $APP->plug('sitebase_production','produit_etape')->find();
	while($arr= $rse->getNext()):
		$arr_P = $APP_PROD->findOne(array('idproduit'=>(int)$arr['idproduit']));
		if($arr_P['idfournisseur']==7):
			          echo 'red baam <br>';
			$APP->plug('sitebase_production','produit_etape')->remove(['idproduit_etape'=>(int)$arr['idproduit_etape']]);
			endif;
	endwhile;
		                exit;

	$dist_devis = $APP_RS_DEVIS->distinct('idtransport_gamme');

	while($arr= $rs->getNext()):
		$arr_P = $APP_PROD->findOne(array('idproduit'=>(int)$arr['idproduit']));
		$idgamme = (int)$arr['idgamme'];
		$idtransport = (int)$arr_P['idtransport'];
		$test_cab = $APP_RS_CAB->find(array( 'idtransport' => $idtransport ,
		                                     'idgamme'     => $idgamme ));
		$arr_gamme = $APP_GAMME->query_one(array( 'idgamme'     => $idgamme ));

		if( empty($arr_P['idproduit']) && empty($arr_P['idtransport']) )      :
			echo "no prod " ;
			$APP_RS->remove(array('idproduit'=>(int)$arr['idproduit']));
			continue;
		endif;

		if ( $test_cab->count() == 0 && !empty($arr_P['idproduit']) && !empty($arr_P['idtransport']) && !empty($arr_gamme['codeGamme']) ):
			echo $arr_gamme['nomGamme'].' / '.$arr_P['nomProduit'].' / '.$arr_P['nomTransport'].'<br>' ;
			$idtransport_cabine = (int)$APP->getNext('idtransport_cabine');

			$APP_RS_CAB->insert(array( 'idtransport_cabine'   => $idtransport_cabine ,
			                                     'idtransport'          => $idtransport ,
			                                     'idgamme'              => $idgamme ,
			                                     'nomTransport' => $arr_P['nomTransport'] ,
			                                     'codeTransport_cabine' => $arr_gamme['codeGamme'] ,
			                                     'ordreTransport_cabine' => $arr_gamme['ordreGamme'] ,
			                                     'nomGamme' => $arr_gamme['nomGamme'] ,
			                                     'nomTransport_cabine'  => $arr_gamme['nomGamme'] ));
		endif;

		$testG = $APP_GAMME->query(array('idgamme'=>(int)$arr['idgamme']));
		if($testG->count() === 0):
			$i++;
			echo "no gamme ".$arr['idproduit_tarif_gamme'].' / '.$arr['idgamme'].' / '.$arr['nomGamme'].'<br>';
			$APP_RS->remove(array('idproduit_tarif_gamme'=>(int)$arr['idproduit_tarif_gamme']));
		endif;

	endwhile;
	echo $i. ' /  '.$rs->count();
