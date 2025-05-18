<?
	include_once($_SERVER['CONF_INC']);

	array_walk_recursive($_POST , 'CleanStr' , $_POST);
	ini_set('display_errors' , 55);

	$APP = new App('promo_zone');
	$APP_ITEM = new App('promo_zone_item');

	if ( isset($_POST['F_action']) ) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	switch ($F_action) {
		case "update_block_promo_zone": // update_block_promo_zone
			if ( empty($_POST['idpromo_zone']) ) {
				exit;
			}
			if ( empty($_POST['idpromo_zone_item']) ) {
				exit;
			}
			$out              = fonctionsProduction::cleanPostMongo($_POST , true);
			$idpromo_zone     = (int)$out['idpromo_zone'];
			$idpromo_zone_item     = (int)$out['idpromo_zone_item'];
			$uid_grille_block = $out['uid_grille_block'];
			$uid_grille_mdl   = $out['uid_grille_mdl'];
			$key_mdl          = $out['key_mdl'];
			$key_tag          = $out['key_tag'];
			$rs               = $APP->query_one(array( 'idpromo_zone' => $idpromo_zone , 'grilleBlock.uid_grille_block' => $uid_grille_block ) , array( 'grilleBlock.$' => 1 ));
			$grilleBlock      = $rs['grilleBlock'][0];

			// direcxt
			$to_alter = array();
			if ( $key_mdl == 'mdl_idproduit' && ! empty($_POST['insertMore']) ):
				$idproduit = (int)$out[$key_mdl];
				// $arrP = skelMongo::connect('produit_preprod','sitebase_production')->findOne(array('idproduit'=>$idproduit));
				// $arrPays = skelMongo::connect('produit_preprod','sitebase_production')->distinct('grilleEtapeProduit.nomPays',array('idproduit'=>$idproduit));
				$to_alter['mdl_url']     = fonctionsSite::lienProduit($arrP['idproduit']);
				$to_alter['idproduit']   = $idproduit;
				$to_alter['mdl_titre']   = $arrP['nomProduit'];
				$to_alter['mdl_sstitre'] = fonctionsProduction::andLast($arrPays);
				$to_alter['mdl_prix']    = maskNbre($arrP['prixProduit']);
				// la descriotn
				$arrDescription                    = array( $arrP['nomTransport_type'] . ' ' . $arrP['nomTransport'] ,
				                                            $arrP['dureeProduit'] . ' jours ' ,
				                                            $arrP['nomVille'].' ' ,
				                                            $arrP['nomFournisseur'] );
				$to_alter['mdl_description'] = implode('<br>' , $arrDescription);


			endif;
			$to_alter[$key_mdl] = $out[$key_mdl];
			$to_alter['key']    = $key_mdl;
			$APP_ITEM->update(array('idpromo_zone_item'=>$idpromo_zone_item),$to_alter);
			$to_alter = $grilleBlock['vignette'];
			foreach ($grilleBlock['vignette'] as $key => $arrVign) :
				if ( $arrVign['uid_grille_mdl'] == $uid_grille_mdl ) :
					// si idproduit => titre + description
					if ( $key_mdl == 'mdl_idproduit' && ! empty($_POST['insertMore']) ):
						$idproduit = (int)$out[$key_mdl];
						// $arrP = skelMongo::connect('produit_preprod','sitebase_production')->findOne(array('idproduit'=>$idproduit));
						// $arrPays = skelMongo::connect('produit_preprod','sitebase_production')->distinct('grilleEtapeProduit.nomPays',array('idproduit'=>$idproduit));
						$to_alter[$key]['mdl_url']     = fonctionsSite::lienProduit($arrP['idproduit']);
						$to_alter[$key]['mdl_titre']   = $arrP['nomProduit'];
						$to_alter[$key]['mdl_sstitre'] = fonctionsProduction::andLast($arrPays);
						$to_alter[$key]['mdl_prix']    = maskNbre($arrP['prixProduit']);
						// la descriotn
						$arrDescription                    = array( $arrP['nomTransport_type'] . ' ' . $arrP['nomTransport'] ,
						                                            $arrP['dureeProduit'] . ' jours ' ,
						                                            $arrP['nomVille'].' ' ,
						                                            $arrP['nomFournisseur'] );
						$to_alter[$key]['mdl_description'] = implode('<br>' , $arrDescription);


					endif;
					$to_alter[$key][$key_mdl] = $out[$key_mdl];
					$to_alter[$key]['key']    = $key_mdl;
				endif;
			endforeach;

			$APP->update(array( 'idpromo_zone' => (int)$idpromo_zone , 'grilleBlock.uid_grille_block' => $uid_grille_block ) , array( 'grilleBlock.$.vignette' => $to_alter ));

			break;
		case "delete_block_enews":
			if ( empty($_POST['idpromo_zone']) ) {
				exit;
			}
			$out              = fonctionsProduction::cleanPostMongo($_POST , true);
			$idpromo_zone     = $out['idpromo_zone'];
			$uid_grille_block = $out['uid_grille_block'];
			//
			$APP->plug('sitebase_production' , 'promo_zone')->update(array( 'idpromo_zone' => (int)$idpromo_zone ) , array( '$pull' => array( 'grilleBlock' => array( 'uid_grille_block' => $uid_grille_block ) ) ));

			break;
		case "reorder_block_enews":
			if ( empty($_POST['idpromo_zone']) ) {
				exit;
			}
			if ( empty($_POST['uid_grille_block']) ) {
				exit;
			}
			if ( empty($_POST['ordreBlock']) ) {
				exit;
			}
			$out          = fonctionsProduction::cleanPostMongo($_POST , true);
			$idpromo_zone = (int)$out['idpromo_zone'];
			$i            = 0;
			foreach ($out['ordreBlock'] as $index => $uid_grille_block):
				$i ++;
				$APP->plug('sitebase_production' , 'promo_zone')->update(array( 'idpromo_zone'                 => (int)$idpromo_zone ,
				                                                                'grilleBlock.uid_grille_block' => $uid_grille_block ) , array( '$set' => array( 'grilleBlock.$.sort' => $i ) ));
			endforeach;

			$arr = $APP->plug('sitebase_production' , 'promo_zone')->findOne(array( 'idpromo_zone' => (int)$idpromo_zone ));
			$out = $arr['grilleBlock'];
			usort($out , 'custom_sort_newsblock');
			$out = fonctionsProduction::cleanPostMongo($out , true);
			//
			$APP->plug('sitebase_production' , 'promo_zone')->update(array( 'idpromo_zone' => (int)$idpromo_zone ) , array( '$set' => array( 'grilleBlock' => $out ) ));

			$_POST['scope'] = 'idpromo_zone';
			break;
		case "create_block_promo_zone":
			if ( empty($_POST['idpromo_zone']) ) {
				exit;
			}
			$out                    = fonctionsProduction::cleanPostMongo($_POST , true);
			$idpromo_zone           = $out['idpromo_zone'];
			$insert                 = array();
			$insert['idpromo_zone'] = (int)$idpromo_zone;
			$_POST['nbreVign']      = 1;
			for ($compte = 1; $compte <= $_POST['nbreVign']; $compte ++):
				$final[] = array( 'uid_grille_mdl' => uniqid() , 'idpromo_zone' => $idpromo_zone , 'ordreVignette' => $compte );
			endfor;
			$insert['vignette']         = $final;
			$insert['type']             = 'AUTOBLOCK';
			$insert['nomPromo_zone_item']       = $_POST['nomPromo_zone_item'];
			$insert['uid_grille_block'] = uniqid();
			$insert['idpromo_zone_item'] = (int)$APP->getNext('idpromo_zone_item');
			$APP->plug('sitebase_production' , 'promo_zone')->update(array( 'idpromo_zone' => (int)$idpromo_zone ) , array( '$push' => array( 'grilleBlock' => $insert ) ) , array( 'upsert' => true ));

			$APP->plug('sitebase_production' , 'promo_zone_item')->update(array( 'idpromo_zone_item' => (int)$insert['idpromo_zone_item'] ) , array( '$set' => $insert ) , array( 'upsert' => true ));

			break;
	}

	include_once(DOCUMENTROOT . '/postAction.php');