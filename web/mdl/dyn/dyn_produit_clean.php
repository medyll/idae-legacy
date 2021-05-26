<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 17/02/15
	 * Time: 00:33
	 *
	 * Nettoyage aprés suppression produits
	 */
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$APP_PRODUIT     = new App('produit');
	$APP_TARIF       = new App('produit_tarif');
	$APP_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_ETAPE       = new App('produit_etape');

	$rs_t = $APP_TARIF->find();
	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage tarifs'), session_id());
	while ($arr_t = $rs_t->getNext()):
		$idproduit = (int)$arr_t['idproduit'];
		$test_p    = $APP_PRODUIT->find(['idproduit' => $idproduit]);
		if ($test_p->count() == 0):
			//echo "<br>delete tarif  " . $idproduit;
			$APP_TARIF->remove(['idproduit' => $idproduit]);
		endif;
	endwhile;
	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage tarifs terminé'), session_id());

	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage etape'), session_id());
	$rs_t = $APP_ETAPE->find();
	while ($arr_t = $rs_t->getNext()):
		$idproduit = (int)$arr_t['idproduit'];
		$test_p    = $APP_PRODUIT->find(['idproduit' => $idproduit]);
		if ($test_p->count() == 0):
			//echo "<br>delete etape " . $idproduit;
			$APP_ETAPE->remove(['idproduit' => $idproduit]);
		else:
			$rs_tt = $APP_ETAPE->find(['idproduit_etape'=>['$ne'=>(int)$arr_t['idproduit_etape']],'idville'=>0,'idproduit' => $idproduit],['ordreProduit_etape' => (int)$arr_t['ordreProduit_etape']]);
			if ($rs_tt->count() > 0):
				// skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage etape '.$idproduit), session_id());
				// $APP_ETAPE->remove(['idproduit_etape' => (int)$arr_t['idproduit_etape']]);
				$APP_ETAPE->remove(['idproduit_etape'=>['$ne'=>(int)$arr_t['idproduit_etape']],'idville'=>0,'idproduit' => $idproduit],['ordreProduit_etape' => (int)$arr_t['ordreProduit_etape']]);
			endif;
		endif;
	endwhile;
	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage etape terminé'), session_id());

	$rs_t = $APP_TARIF_GAMME->find();
	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage prix'), session_id());
	while ($arr_t = $rs_t->getNext()):
		if(empty($arr_t['idproduit']) && !empty($arr_t['idproduit_tarif'])){
			$ar_t = $APP_TARIF->findOne(['idproduit_tarif'=>(int)$arr_t['idproduit_tarif']]);
			if( !empty($ar_t['idproduit'])){
			 	$APP_TARIF_GAMME->update(['idproduit_tarif_gamme'=>$arr_t['idproduit_tarif_gamme']],['idproduit'=>(int)$ar_t['idproduit']]);
				//skelMdl::send_cmd('act_notify', array('msg' => 'Repair '.$ar_t['idproduit']), session_id());
			}
		}
		$idproduit = (int)$arr_t['idproduit'];
		$test_p    = $APP_PRODUIT->find(['idproduit' => $idproduit]);
		if ($test_p->count() == 0):
			//echo "<br>delete prix " . $idproduit;
			$APP_TARIF_GAMME->remove(['idproduit' => $idproduit]);
		endif;
	endwhile;
	skelMdl::send_cmd('act_notify', array('msg' => 'Nettoyage prix terminé'), session_id());

	skelMdl::send_cmd('act_notify', array( 'options'=>['sticky'=>true],'msg' => 'Nettoyage terminé'), session_id());