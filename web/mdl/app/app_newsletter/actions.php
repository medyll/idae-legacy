<?
	include_once($_SERVER['CONF_INC']);

	array_walk_recursive($_POST, 'CleanStr', $_POST);
	ini_set('display_errors', 55);

	$APP      = new App('newsletter');
	$APP_ITEM = new App('newsletter_item');

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	switch ($F_action) {
		case "update_block_newsletter": // update_block_newsletter
			if (empty($_POST['idnewsletter'])) {
				exit;
			}
			if (empty($_POST['idnewsletter_item'])) {
				exit;
			}
			$out               = fonctionsProduction::cleanPostMongo($_POST, true);
			$idnewsletter      = (int)$out['idnewsletter'];
			$idnewsletter_item = (int)$out['idnewsletter_item'];
			$uid_grille_block  = $out['uid_grille_block'];
			$uid_grille_mdl    = $out['uid_grille_mdl'];
			$key_mdl           = $out['key_mdl'];
			$key_tag           = $out['key_tag'];
			$rs                = $APP->query_one(['idnewsletter' => $idnewsletter, 'grilleBlock.uid_grille_block' => $uid_grille_block], ['grilleBlock.$' => 1]);
			$grilleBlock       = $rs['grilleBlock'][0];

			// direcxt
			$to_alter = [];
			if ($key_mdl == 'mdl_idproduit' && !empty($_POST['insertMore'])):
				$idproduit = (int)$out[$key_mdl];
				// $arrP = skelMongo::connect('produit_preprod','sitebase_production')->findOne(array('idproduit'=>$idproduit));
				// $arrPays = skelMongo::connect('produit_preprod','sitebase_production')->distinct('grilleEtapeProduit.nomPays',array('idproduit'=>$idproduit));
				$to_alter['mdl_url']     = fonctionsSite::lienProduit($arrP['idproduit']);
				$to_alter['idproduit']   = $idproduit;
				$to_alter['mdl_titre']   = $arrP['nomProduit'];
				$to_alter['mdl_sstitre'] = fonctionsProduction::andLast($arrPays);
				$to_alter['mdl_prix']    = maskNbre($arrP['prixProduit']);
				// la descriotn
				$arrDescription              = [$arrP['nomTransport_type'] . ' ' . $arrP['nomTransport'],
					$arrP['dureeProduit'] . ' jours ',
					$arrP['nomVille'] . ' ',
					$arrP['nomFournisseur']];
				$to_alter['mdl_description'] = implode('<br>', $arrDescription);

			endif;
			$to_alter[$key_mdl] = $out[$key_mdl];
			$to_alter['key']    = $key_mdl;
			$APP_ITEM->update(['idnewsletter_item' => $idnewsletter_item], $to_alter);
			$to_alter = $grilleBlock['vignette'];
			foreach ($grilleBlock['vignette'] as $key => $arrVign) :
				if ($arrVign['uid_grille_mdl'] == $uid_grille_mdl) :
					// si idproduit => titre + description
					if ($key_mdl == 'mdl_idproduit' && !empty($_POST['insertMore'])):
						$idproduit = (int)$out[$key_mdl];
						// $arrP = skelMongo::connect('produit_preprod','sitebase_production')->findOne(array('idproduit'=>$idproduit));
						// $arrPays = skelMongo::connect('produit_preprod','sitebase_production')->distinct('grilleEtapeProduit.nomPays',array('idproduit'=>$idproduit));
						$to_alter[$key]['mdl_url']     = fonctionsSite::lienProduit($arrP['idproduit']);
						$to_alter[$key]['mdl_titre']   = $arrP['nomProduit'];
						$to_alter[$key]['mdl_sstitre'] = fonctionsProduction::andLast($arrPays);
						$to_alter[$key]['mdl_prix']    = maskNbre($arrP['prixProduit']);
						// la descriotn
						$arrDescription                    = [$arrP['nomTransport_type'] . ' ' . $arrP['nomTransport'],
							$arrP['dureeProduit'] . ' jours ',
							$arrP['nomVille'] . ' ',
							$arrP['nomFournisseur']];
						$to_alter[$key]['mdl_description'] = implode('<br>', $arrDescription);

					endif;
					$to_alter[$key][$key_mdl] = $out[$key_mdl];
					$to_alter[$key]['key']    = $key_mdl;
				endif;
			endforeach;

			$APP->update(['idnewsletter' => (int)$idnewsletter, 'grilleBlock.uid_grille_block' => $uid_grille_block], ['grilleBlock.$.vignette' => $to_alter]);

			break;
		case "delete_block_enews":
			if (empty($_POST['idnewsletter'])) {
				exit;
			}
			$out              = fonctionsProduction::cleanPostMongo($_POST, true);
			$idnewsletter     = $out['idnewsletter'];
			$uid_grille_block = $out['uid_grille_block'];
			//
			$APP->plug('sitebase_production', 'newsletter')->update(['idnewsletter' => (int)$idnewsletter], ['$pull' => ['grilleBlock' => ['uid_grille_block' => $uid_grille_block]]]);

			break;
		case "reorder_block_enews":
			if (empty($_POST['idnewsletter'])) {
				exit;
			}
			if (empty($_POST['uid_grille_block'])) {
				exit;
			}
			if (empty($_POST['ordreBlock'])) {
				exit;
			}
			$out          = fonctionsProduction::cleanPostMongo($_POST, true);
			$idnewsletter = (int)$out['idnewsletter'];
			$i            = 0;
			foreach ($out['ordreBlock'] as $index => $uid_grille_block):
				$i++;
				$APP->plug('sitebase_production', 'newsletter')->update(['idnewsletter'                 => (int)$idnewsletter,
				                                                         'grilleBlock.uid_grille_block' => $uid_grille_block], ['$set' => ['grilleBlock.$.sort' => $i]]);
			endforeach;

			$arr = $APP->plug('sitebase_production', 'newsletter')->findOne(['idnewsletter' => (int)$idnewsletter]);
			$out = $arr['grilleBlock'];
			usort($out, 'custom_sort_newsblock');
			$out = fonctionsProduction::cleanPostMongo($out, true);
			//
			$APP->plug('sitebase_production', 'newsletter')->update(['idnewsletter' => (int)$idnewsletter], ['$set' => ['grilleBlock' => $out]]);

			$_POST['scope'] = 'idnewsletter';
			break;
		case "create_block_newsletter":
			$APP_ITEM_LGN = new App('newsletter_item_ligne');
			if (empty($_POST['idnewsletter'])) {
				exit;
			}
			$out                    = fonctionsProduction::cleanPostMongo($_POST, true);
			$idnewsletter           = $out['idnewsletter'];
			$idnewsletter_item      = (int)$APP->getNext('idnewsletter_item');
			$insert                 = [];
			$insert['idnewsletter'] = (int)$idnewsletter;
			$_POST['nbreVign']      = 5;
			for ($compte = 1; $compte <= $_POST['nbreVign']; $compte++):
				$final[]                                  = ['uid_grille_mdl' => uniqid(), 'idnewsletter' => $idnewsletter, 'ordreVignette' => $compte];
				$arr_item_lgn                             = ['ordreNewsletter_item_ligne' => $compte, 'idnewsletter' => $idnewsletter, 'idnewsletter_item' => $idnewsletter_item];
				$arr_item_lgn['nomNewsletter_item_ligne'] = $_POST['nomNewsletter_item'] . ' ' . $compte;
				$APP_ITEM_LGN->create_update($arr_item_lgn);
			endfor;
			$insert['vignette']           = $final;
			$insert['type']               = 'AUTOBLOCK';
			$insert['nomNewsletter_item'] = $_POST['nomNewsletter_item'];
			$insert['uid_grille_block']   = uniqid();

			$APP->plug('sitebase_production', 'newsletter')->update(['idnewsletter' => (int)$idnewsletter], ['$push' => ['grilleBlock' => $insert]], ['upsert' => true]);

			$APP->plug('sitebase_production', 'newsletter_item')->update(['idnewsletter_item' => (int)$insert['idnewsletter_item']], ['$set' => $insert], ['upsert' => true]);

			break;
	}

	include_once(DOCUMENTROOT . '/postAction.php');