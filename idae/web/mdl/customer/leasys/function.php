<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 19/09/2016
	 * Time: 14:00
	 */

	function get_artis_table() {

		return $arr_tables = [
			't_typprod',
			't_classif',
			't_classiforga',
			't_categ',
			't_categprofilclt',
			't_rachat',
			't_fact',
			't_lgnfact',
			't_rachatbiens',
			't_relationclientfournisseur',
			't_cl',
			't_sect',
			't_statutmoduleservclt',
			't_lieubien',
			't_reglecltsurbiens',
			't_reglefactuclt',
			't_livr',
			't_lgnlivr',
			't_cout',
			't_statutsolservclt',
			't_grandeur',
			't_manierecontacter',
			't_couvbienparserv',
			't_biengereparorg',
			't_bienpossedeparorg',
			't_subadmin',
			't_codepost',
			't_lieufonction',
			't_tradcyclevieinter',
			't_dmdeinter',
			't_inter',
			't_interinfoprincipales',
			't_interrealisee',
			't_affaire',
			't_typfinancement',
			't_financement',
			't_lgnfinancee',
			't_echeancierfinancement',
			't_servclt',
			't_moduleservclt',
			't_solservclt',
			't_site',
			't_siteorg',
			't_art',
			't_bien',
			't_partie',
			't_transactioncpta',
			't_adrmedia',
			't_volumemoyenmensuel',
			't_cmde',
			't_lgncmde',
			't_regle',
			't_interplanifiee',
			't_marque',
			't_infocontact',
			't_valori'];

	}
	/*'t_cptcpta' ,*/

	function do_artis_index($tb, $key) {
		$APP       = new App();
		$BASE_SYNC = $APP->plug_base('sitebase_sync');

		$BASE_SYNC->$tb->createIndex([$key => 1]);
		$BASE_SYNC->$tb->createIndex([$key => -1]);
	}

	function do_artis_rows($tb, $ROW) {
		foreach ($ROW as $kk => $vv):
			if (is_numeric($kk)) {
				unset($ROW[$kk]);
			}
		endforeach;

		// do_artis_index($tb , 'N_ID');
		MongoCursor::$timeout = -1;
		// FAIRMONT C0000322
		// t_cptcpta.N_ID 11621  t_partie.N_ID 19118
		// t_partie.N_CORRESPA_LIEUFONCTIONENTITEJURIDIQ_ID    :  40236
		// t_partie.N_ID = 19118
		// t_lieufonction.N_ID : 40236
		// t_solservclt.N_ESTCONCLUECLIENT_LIEUFONCTION_ID     :  40236
		// possede bien M6393700530 t_bien.N_ID : 46642
		// t_lieubien.N_ID 13279
		// t_lieubien.N_ESTPOSSEDEPAR_ORG_ID VALUE : 19118 (t_partie.N_ID)
		ini_set('display_errors', 55);
		$APP       = new App();
		$BASE_SYNC = $APP->plug_base('sitebase_sync');
		// LAST ROW
		switch ($tb):
			case "t_typprod": // PRODUIT ou ACCESSOIRE !
				$NEW_ROW  = [];
				$APP_MAR  = new App('marque');
				$APP_PROD = new App('produit');
				$APP_ACC  = new App('accessoire');
				// marque
				$arrm  = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTFABRIQUEPAR_ORG_ID']]);
				$codem = $arrm['C_CODE_ORG'];
				$testm = $APP_MAR->query(['codeMarque' => $codem]);

				if ($testm->count() == 0):
					// création
					$idmarque                      = (int)$APP_MAR->getNext('idmarque');
					$NEW_ROW['idmarque']           = $idmarque;
					$NEW_ROW['codeMarque']         = $codem;
					$NEW_ROW['nomMarque']          = $codem;
					$NEW_ROW['N_ID']               = $ROW['N_ID'];
					$NEW_ROW['dateCreationMarque'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));

					$APP_MAR->insert($NEW_ROW);

				else:
					$arr_c               = $testm->getNext();
					$idmarque            = (int)$arr_c['idmarque'];
					$NEW_ROW['idmarque'] = $idmarque;
				endif;

				switch ($ROW['C_TYPE_IDX']) :
					case "com.artis.business.articles.Mod":
						$test = $APP_PROD->query(['codeProduit' => $ROW['C_CODEBARRE_TYPPROD']]);
						if ($test->count() == 0):
							// création
							$idproduit                      = (int)$APP_PROD->getNext('idproduit');
							$NEW_ROW['idproduit']           = $idproduit;
							$NEW_ROW['codeProduit']         = $ROW['C_CODEBARRE_TYPPROD'];
							$NEW_ROW['nomProduit']          = $ROW['C_LIB_TYPPROD'];
							$NEW_ROW['dateCreationProduit'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
							$NEW_ROW['N_ID']                = $ROW['N_ID'];

							$APP_PROD->insert($NEW_ROW);

						else:
							$arr_c     = $test->getNext();
							$idproduit = (int)$arr_c['idproduit'];
							$APP_PROD->update(['idproduit' => $idproduit], $NEW_ROW);

						endif;
						break;
					case "com.artis.business.articles.TypComp":
						$APP_PROD->remove(['codeProduit' => $ROW['C_CODEBARRE_TYPPROD']]);

						$test = $APP_ACC->query(['codeAccessoire' => $ROW['C_CODEBARRE_TYPPROD']]);
						if ($test->count() == 0):
							// création
							$idaccessoire                      = (int)$APP_ACC->getNext('idaccessoire');
							$NEW_ROW['idaccessoire']           = $idaccessoire;
							$NEW_ROW['codeAccessoire']         = $ROW['C_CODEBARRE_TYPPROD'];
							$NEW_ROW['nomAccessoire']          = $ROW['C_LIB_TYPPROD'];
							$NEW_ROW['dateCreationAccessoire'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
							$NEW_ROW['N_ID']                   = $ROW['N_ID'];

							$APP_ACC->insert($NEW_ROW);

						else:
							$arr_c        = $test->getNext();
							$idaccessoire = (int)$arr_c['idaccessoire'];
							$APP_ACC->update(['idaccessoire' => $idaccessoire], $NEW_ROW);
						endif;
						break;
					default:
						$APP_PROD->remove(['codeProduit' => $ROW['C_CODEBARRE_TYPPROD']]);
						break;
				endswitch;

				break;
			case "t_sect": // SECTEUR
				// C_CODE_SECT
				// C_LIB_SECT
				// N_APPARTIENTA_ORG_ID
				$NEW_ROW = [];
				$APP_SEC = new App('secteur');
				//
				$NEW_ROW['codeSecteur']         = $ROW['C_CODE_SECT'];
				$NEW_ROW['nomSecteur']          = strtolower($ROW['C_LIB_SECT']);
				$NEW_ROW['N_ID']                = $ROW['N_ID'];
				$NEW_ROW['dateCreationSecteur'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));

				$idsecteur = $APP_SEC->create_update(['codeSecteur' => $ROW['C_CODE_SECT']], $NEW_ROW);

				break;
			case "t_marque": // MARQUE
				$NEW_ROW = [];
				$APP_MAR = new App('marque');
				$test    = $APP_MAR->query(['codeMarque' => $ROW['C_CODE_MARQUE']]);
				if ($test->count() == 0):
					// création
					$idmarque                      = (int)$APP_MAR->getNext('idmarque');
					$NEW_ROW['idmarque']           = $idmarque;
					$NEW_ROW['codeMarque']         = $ROW['C_CODE_MARQUE'];
					$NEW_ROW['nomMarque']          = $ROW['C_LIB_MARQUE'];
					$NEW_ROW['N_ID']               = $ROW['N_ID'];
					$NEW_ROW['dateCreationMarque'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));

					$APP_MAR->insert($NEW_ROW);
				// vardump($NEW_ROW);
				else:
					$arr_c    = $test->getNext();
					$idmarque = (int)$arr_c['idmarque'];
				endif;
				break;
			case "t_bien": // MATERIEL // BIEN
				$NEW_ROW = [];
				// C_IDENTIFIANTFABRICANT_BIENIMMA numeroSerieMateriel
				// N_CORRESPABG_TYPPROD_ID 	: typeProduit => t_typprod => devient produit
				// C_COMMENTAIRES_BIEN 		: commentaireMateriel
				$APP_M = new App('materiel');
				$APP_P = new App('produit');
				$APP_C = new App('contrat');
				//
				$produit_id = $ROW['N_CORRESPABG_TYPPROD_ID'];
				$numserie   = $ROW['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				$com        = nl2br($ROW['C_COMMENTAIRES_BIEN']);
				//
				$rstest = $APP_M->find(['codeMateriel' => $numserie]);

				$NEW_ROW['codeMateriel']         = $numserie;
				$NEW_ROW['referenceMateriel']    = $numserie;
				$NEW_ROW['nomMateriel']          = $numserie;
				$NEW_ROW['N_ID']                 = $ROW['N_ID'];
				$NEW_ROW['dateCreationMateriel'] = $ROW['T_CREATE_DATE'];
				$NEW_ROW['descriptionMateriel']  = $com;
				//	$NEW_ROW['dateModificationMateriel'] = date('Y-m-d');

				// 	LE PRODUIT N_CORRESPABG_TYPPROD_ID = N_ID du produit ....
				$arr                   = $APP_P->query_one(['N_ID' => $ROW['N_CORRESPABG_TYPPROD_ID']]);
				$NEW_ROW['idtypeprod'] = $ROW['N_CORRESPABG_TYPPROD_ID'];

				if (!empty($arr['N_ID'])):
					// Dans t_typprod type_produit  // N_ESTFABRIQUEPAR_ORG_ID  => fournisseur
					// Libelle 		: C_LIB_TYPPROD => nomPRoduit
					// Reference 	: C_REFERENCE_TYPPROD codeProduit devient codeMateriel
					// skelMdl::send_cmd('act_notify', ['msg' => 'Artis update find t_typprod ' . $arr['N_ID'] . ' pour ' . $produit_id]);
					//
					$NEW_ROW['idproduit']   = (int)$arr['idproduit'];
					$NEW_ROW['nomMateriel'] = $arr['nomProduit'] . ' ' . $numserie;

				else:
					// REC;

					break;
				endif;
				$APP_M->create_update(['codeMateriel' => $NEW_ROW['codeMateriel']], $NEW_ROW);
				// client ? // dans solution de service

				break;
			case "t_lieubien": // N_ESTLIEUDU_BIEN_ID // N_ESTUTILISEPAR_ORG_ID ( t_partie.N_ID ) // N_ESTSITUESUR_SITE_ID ( t_site.N_ID )
				$NEW_ROW  = [];
				$APP_MAT  = new App('materiel');
				$APP_SITE = new App('site');
				$APP_CLI = new App('client');
				$debug='';
				if (empty($ROW['N_ESTSITUESUR_SITE_ID'])) {
					break;
				}
				$test_mat = $BASE_SYNC->t_bien->findOne(['N_ID' => $ROW['N_ESTLIEUDU_BIEN_ID']]);  // sans date fin => actif
				if (empty($test_mat['N_ID']) || empty($test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA']) ) {
					// echo "vide";
					break;
				}
				// N_APPARTIENTAUSTOCKDUDEPOT_DEPOTSTOCKAGE_ID
				// selectionner lieubien anterieurs avec meme bie => exit ;
				$test_exist_newer = $BASE_SYNC->t_lieubien->find(['N_ID'=>['$ne'=>$ROW['N_ID']],'D_DATEDEBUT_LIEUBIEN'=>['$gte'=>$ROW['D_DATEDEBUT_LIEUBIEN']],'N_ESTLIEUDU_BIEN_ID' => $ROW['N_ESTLIEUDU_BIEN_ID']]);  // sans date fin => actif
				$debug .= 'N_ID '.$ROW['N_ID'].'  '.$test_mat['N_ID'].' =>>>> '.$test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA']. '<br>';

				if($test_exist_newer->count()!=0){ // inscrire dans historique
					// $debug .=  $test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA'].' '.$test_exist_newer->count().' autre lieu plus recent <br> ';
					break;
				}

				// site
				$numserie = $test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				$arr_mat  = $APP_MAT->findOne(['codeMateriel' => $numserie]);
				$arr_site = $APP_SITE->findOne(['N_ID' => $ROW['N_ESTSITUESUR_SITE_ID']]);
				if (empty($arr_site['idsite'])) {
					$test_site = $BASE_SYNC->t_site->findOne(['N_ID' => $ROW['N_ESTSITUESUR_SITE_ID']]);
					if (!empty($test_site['N_ID'])) {
						do_artis_rows('t_site', $test_site);
					}
					$arr_site = $APP_SITE->findOne(['N_ID' => $ROW['N_ESTSITUESUR_SITE_ID']]);
				}

				if (empty($arr_mat['idmateriel']) || empty($arr_site['idsite'])) {
					break;
				}

				// break;
				if(!empty($ROW['N_APPARTIENTAUSTOCKDUDEPOT_DEPOTSTOCKAGE_ID']) && empty($ROW['D_DATEINSTALLATION_LIEUBIEN'])){
					$debug .=  $test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA'].' semble au dépot <br> ';
					$NEW_ROW['idsite'] = $NEW_ROW['nomSite'] = $NEW_ROW['idclient']   = $NEW_ROW['nomClient'] = '';
					$APP_MAT->update(['idmateriel' => (int)$arr_mat['idmateriel']], $NEW_ROW);
					//return $debug;
					break;
				}


				// non, client non obligatoire à ce stade.


				$NEW_ROW['idsite'] = (int)$arr_site['idsite'];

				if (!empty($arr_site['idclient'])) { // empty($arr_mat['idclient']) && le client peut changer de materiel
					$NEW_ROW['idclient'] = (int)$arr_site['idclient'];
				}
				if (!empty($ROW['D_DATEFIN_LIEUBIEN'])) {
					/*$arrtestbien = $BASE_SYNC->t_lieubien->find(['N_ESTLIEUDU_BIEN_ID'=>$ROW['N_ESTLIEUDU_BIEN_ID'],'D_DATEFIN_LIEUBIEN'=>''])->getNext();
					if(empty($arrtestbien['D_DATEFIN_LIEUBIEN'])){
						$NEW_ROW['idsite'] = $NEW_ROW['idclient'] = '';
					}*/
					$debug = 'date fin !!!<br>';
				}
				// return $debug;
				if (!empty($ROW['N_ESTPOSSEDEPAR_ORG_ID'])) {
					$test_org = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTPOSSEDEPAR_ORG_ID']]);
					if(!empty($test_org['N_ID'])){
						$arr_cli = $APP_CLI->findOne(['codeClient' => $test_org['C_CODE_ORG']]);
						if(!empty($arr_cli['idclient'])){
							$NEW_ROW['idclient'] = (int)$arr_cli['idclient'];
						}
					}


				}
				if (!empty($ROW['D_DATEDEBUT_LIEUBIEN'])) {
					$NEW_ROW['dateDebutMateriel']        = $ROW['D_DATEDEBUT_LIEUBIEN'];

				}
				if (!empty($ROW['D_DATEINSTALLATION_LIEUBIEN'])) {
					$NEW_ROW['dateInstallationMateriel'] = $ROW['D_DATEINSTALLATION_LIEUBIEN'];
				}

				$APP_MAT->update(['idmateriel' => (int)$arr_mat['idmateriel']], $NEW_ROW);

				break;
			case "t_siteorg":
				$APP_SITE = new App('site');
				$APP_CLI  = new App('client');
				// N_SOURCE_SITE_ID
				// N_TARGET_ORG_ID
				$arr_site   = $BASE_SYNC->t_site->findOne(['N_ID' => $ROW['N_SOURCE_SITE_ID']]);
				$arr_partie = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_TARGET_ORG_ID']]);

				$codeSite   = $arr_site['N_ID'];
				$codeClient = $arr_partie['C_CODE_ORG'];

				// echo $codeSite.' '.$codeClient.'<br>';

				$ARR_CLI  = $APP_CLI->findOne(['codeClient' => $codeClient]);
				$ARR_SITE = $APP_SITE->findOne(['codeSite' => $codeSite]);

				if (empty($ARR_CLI['idclient']) || empty($ARR_SITE['idsite'])) break;
				// echo "red";

				$APP_SITE->update(['idsite' => (int)$ARR_SITE['idsite']], ['idclient' => (int)$ARR_CLI['idclient']]);
				break;
			case "t_bienpossedeparorg": // materiel // client
				break;
				$NEW_ROW = [];
				$APP_MAT = new App('materiel');
				$APP_CLI = new App('client');

				$ARR_PARTIE = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_TARGET_ORG_ID']]);

				$ARR_CLI             = $APP_CLI->query_one(['codeClient' => $ARR_PARTIE['C_CODE_ORG']]);
				$NEW_ROW['idclient'] = (int)$ARR_CLI['idclient'];

				$ARR_BIEN              = $BASE_SYNC->t_bien->findOne(['N_ID' => $ROW['N_SOURCE_BIEN_ID']]);
				$ARR_MAT               = $APP_MAT->query_one(['codeMateriel' => $ARR_BIEN['C_IDENTIFIANTFABRICANT_BIENIMMA']]);
				$NEW_ROW['idmateriel'] = (int)$ARR_MAT['idmateriel'];

				// vardump($ROW);

				if ($ARR_MAT['idclient'] != $NEW_ROW['idclient']):
					// $APP_MAT->update(['idmateriel' => (int)$NEW_ROW['idmateriel']], $NEW_ROW);
				endif;

				break;
			case "t_fact":
				$NEW_ROW  = [];
				$APP_FACT = new App('facture');
				$APP_CLI  = new App('client');
				$APP_CON  = new App('contrat');

				$NEW_ROW['codeFacture']        = $ROW['C_NUMEROINTERNE_FACT'];
				$NEW_ROW['descriptionFacture'] = $ROW['C_COMMENTAIRES_FACT'];
				$NEW_ROW['totalHtFacture']     = (float)$ROW['R_TOTALHTFACTURENB_FACT'];
				$NEW_ROW['totalTtcFacture']    = (float)$ROW['R_TOTALTTCFACTURENB_FACT'];
				$NEW_ROW['totalTvaFacture']    = (float)$ROW['R_TOTALTVAFACTURENB_FACT'];
				$NEW_ROW['dateFacture']        = $ROW['D_DATEDEFACTURE_FACT'];
				$NEW_ROW['dateEnvoiFacture']   = date('Y-m-d',strtotime($ROW['T_DATEENVOI_FACTCLT']));

				$arr_lf     = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ESTRECUEPAR_LIEUFONCTION_ID']]);// $ROW['N_ESTRECUEPAR_LIEUFONCTION_ID'];
				$arr_p      = $BASE_SYNC->t_partie->findOne(['N_ID' => $arr_lf['N_CORRESPA_ORG_ID']]);
				$codeClient = $arr_p['C_CODE_ORG'];
				// client
				$ARR_CL = $APP_CLI->findOne(['codeClient' => $codeClient]);
				if (empty($ARR_CL['idclient'])) break;
				// solution de service
				$arr_sol = $BASE_SYNC->t_solservclt->findOne(['N_ESTCONCLUECLIENT_LIEUFONCTION_ID' => $ROW['N_ESTRECUEPAR_LIEUFONCTION_ID']]); //  N_ESTCONCLUECLIENT_LIEUFONCTION_ID
				$ARR_CON = $APP_CON->findOne(['codeContrat' => $arr_sol['C_CODE_SOLSERVCLT']]);
				if (empty($ARR_CON['idcontrat'])) break;

				$NEW_ROW['idcontrat']  = (int)$ARR_CON['idcontrat'];
				$NEW_ROW['idclient']   = (int)$ARR_CL['idclient'];
				$NEW_ROW['nomFacture'] = $NEW_ROW['codeFacture'] . ' ' . strtoupper(substr(str_replace(' ', '', $ARR_CL['nomClient']), 0, 12)) . ' ' . date('d-m-Y', strtotime($ROW['T_CREATE_DATE']));
				//
				$APP_FACT->create_update(['codeFacture' => $ROW['C_NUMEROINTERNE_FACT']], $NEW_ROW);
				// les lignes( article ) N_FACTURE_ART_ID dans lignes
				break;
			case "t_lgnfact":
				$NEW_ROW      = [];
				$APP_FACT     = new App('facture');
				$APP_FACT_LGN = new App('facture_ligne');
				$APP_CLI      = new App('client');
				$APP_CON      = new App('contrat');

				$NEW_ROW['codeFacture_ligne']        = $ROW['N_ID'];
				$NEW_ROW['descriptionFacture_ligne'] = $ROW['C_COMMENTAIRES_LGNFACT'];
				$NEW_ROW['nomFacture_ligne']         = $ROW['C_LIBARTICLE_LGNDETAILFACT'];
				$NEW_ROW['quantiteFacture_ligne']    = (int)$ROW['R_QTEFACTUREE_LGNFACT'];
				$NEW_ROW['prixFacture_ligne']        = (float)$ROW['R_PRIXUNITAIREHTNB_LGNFACT'];
				$NEW_ROW['totalHtFacture_ligne']     = (float)$ROW['R_TOTALHTLIGNENB_LGNFACT'];
				$NEW_ROW['totalTtcFacture_ligne']    = (float)$ROW['R_TOTALTTCLIGNENB_LGNFACT'];
				$NEW_ROW['totalTvaFacture_ligne']    = (float)$ROW['R_TOTALTVALIGNENB_LGNFACT'];

				// facture
				$arr_f    = $BASE_SYNC->t_fact->findOne(['N_ID' => $ROW['N_ESTRATTACHEEA_FACT_ID']]);// $ROW['N_ESTRECUEPAR_LIEUFONCTION_ID'];
				$ARR_FACT = $APP_FACT->findOne(['codeFacture' => $arr_f['C_NUMEROINTERNE_FACT']]);

				if (empty($ARR_FACT['idfacture'])) break;

				$NEW_ROW['idfacture'] = (int)$ARR_FACT['idfacture'];
				$APP_FACT_LGN->create_update(['codeFacture_ligne' => $ROW['N_ID']], $NEW_ROW);
				// N_FACTURE_ART_ID  // ENGAGEMENT COULEUR // ENGAGEMENT NOIR
				//
				$testdate = strtotime($ARR_FACT['dateFacture']);
				if (date('Y-m', $testdate) != '2015-10' && date('Y-m', $testdate) != '2015-09' && date('Y-m', $testdate) != '2015-08' && date('Y-m', $testdate) != '2015-07') break;
				// echo "<br>";
				// echo date('Y-m',strtotime($ARR_FACT['dateFacture']));

				/*$arr_art = $BASE_SYNC->t_art->findOne(['N_ID' => $ROW['N_FACTURE_ART_ID']]);
				if ($arr_art['C_LIB_ART'] == 'ENGAGEMENT COULEUR' || $arr_art['C_LIB_ART'] == 'ENGAGEMENT NOIR') {
					// on fixe la valeur dans le contrat t_solservclt !!! ( en passant par la facture )
					// R_PRIXUNITAIREHTNB_LGNFACT
					if ($arr_art['C_LIB_ART'] == 'ENGAGEMENT COULEUR') $field_name = 'cccoulContrat';
					if ($arr_art['C_LIB_ART'] == 'ENGAGEMENT NOIR') $field_name = 'ccnContrat';
					if (!empty($ARR_FACT['idcontrat'])) {
						$APP_CON->update(['idcontrat' => (int)$ARR_FACT['idcontrat']], [$field_name => (float)$ROW['R_PRIXUNITAIREHTNB_LGNFACT']]);
					}
				};*/
				break;
			case "t_cout":
				$NEW_ROW  = [];
				$APP_FACT = new App('facture');
				$APP_CLI  = new App('client');
				$APP_CON  = new App('contrat');

				break;
				if (strtolower($ROW['B_ACTIF_COUT_IDX']) == 'non') break;

				$test = $BASE_SYNC->t_art->findOne(['N_ID' => $ROW['N_ESTFACTUREVIA_ART_ID']]);
				if (in_array($ROW['N_ESTFACTUREVIA_ART_ID'], ["60640", "60638", "60963", "60090", "60632", "60630", "60636", "60634"])) {
					if ($ROW['N_ESTFACTUREVIA_ART_ID'] == '60636') $field_name = 'cccoulContrat';
					if ($ROW['N_ESTFACTUREVIA_ART_ID'] == '60634') $field_name = 'ccnContrat';

					if ($test['C_LIBCLT_ARTVENDU'] == 'ENGAGEMENT NOIR') {
						$field_name = 'ccnContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}
					if ($test['C_LIBCLT_ARTVENDU'] == 'ENGAGEMENT COULEUR') {
						$field_name = 'cccoulContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}
					if ($test['C_LIBCLT_ARTVENDU'] == 'RELEVES COPIES NOIRES') {
						$field_name = 'ccnContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}
					if ($test['C_LIBCLT_ARTVENDU'] == 'RELEVES COPIES COULEUR') {
						$field_name = 'cccoulContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}
					if ($test['C_LIBCLT_ARTVENDU'] == 'COPIES SUPPLEMENTAIRES NOIRES') {
						$field_name = 'ccnsupContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}
					if ($test['C_LIBCLT_ARTVENDU'] == 'COPIES SUPPLEMENTAIRES COULEURS') {
						$field_name = 'cccoulsupContrat';
						$artis_name = 'R_FACTURENB_COUTUNIFORME';
					}

					if (empty($ROW[$artis_name])) break;
					//echo $ROW['N_ESTFACTUREVIA_ART_ID'].' -' .$test['C_LIBCLT_ARTVENDU'].' ' .$field_name.' - ' .$ROW[$artis_name].' - '.$ROW['T_DATEDECHANGEMENT_COUT'];
// echo "<br>";
					if (!empty($field_name)) {

						//echo $ROW['N_ID'] . ' - ' . $ROW['N_ESTFACTUREA_LIEUFONCTION_ID'];
						// plusieurs solservclt
						$rs_solservclt = $BASE_SYNC->t_solservclt->find(['N_ESTCONCLUECLIENT_LIEUFONCTION_ID' => $ROW['N_ESTFACTUREA_LIEUFONCTION_ID']])->sort(['N_ID' => 1]);
						while ($arr_solservclt = $rs_solservclt->getNext()) {
							$arr_statsolservclt = $BASE_SYNC->t_statutsolservclt->findOne(['N_ID' => $arr_solservclt['N_UTILISE_STATUTSOLSERVCLT_ID']]);
							//	echo ' CODE CONTRAT : ' . $arr_solservclt['C_CODE_SOLSERVCLT'] . ' ';
							//	echo $arr_statsolservclt['C_STATUTVAL_STATUTSOLSERVCLT_IDX'];
							if (in_array($arr_statsolservclt['C_STATUTVAL_STATUTSOLSERVCLT_IDX'], ['Pré-créée', 'Validée', 'Validée.Prolongée'])) {
								$ARR_CONTR = $APP_CON->findOne(['codeContrat' => $arr_solservclt['C_CODE_SOLSERVCLT']]);
								if (!empty($ARR_CONTR['idcontrat'])) {
									$val              = (float)$ROW[$artis_name];
									$OUT[$field_name] = $val;
									$APP_CON->update(['idcontrat' => (int)$ARR_CONTR['idcontrat']], $OUT);

									echo '<br> =>  ' . $arr_solservclt['C_CODE_SOLSERVCLT'] . ' - ' . $ARR_CONTR['idcontrat'] . ' ' . $test['C_LIBCLT_ARTVENDU'] . ' ' . $val . ' - ' . $field_name . ' - ' . $ROW['T_DATEDECHANGEMENT_COUT'];

								}
							}
						}

						/*if(!empty($arr_solservclt['N_ID'])){

						}*/
					}
				}

				break;
			case "t_reglefactuclt":
				$NEW_ROW  = [];
				$APP_FACT = new App('facture');
				$APP_CLI  = new App('client');
				$APP_CON  = new App('contrat');

				$DEBUG = " ";
				// si inactif, exit
				if (strtolower($ROW['B_INACTIVE_REGLEFACTUCLT_IDX']) == 'oui') { // !!! inactive = pas de factu ;)
					//$DEBUG .= ' B_INACTIVE_REGLEFACTUCLT_IDX : '.$ROW['B_INACTIVE_REGLEFACTUCLT_IDX'];
					//echo $DEBUG;
					// break;
				}
				$DEBUG .= 'N_ID ' . $ROW['N_ID'] . ' N_FACT : ' . $ROW['N_FACTURE_MODULESERVCLT_ID'];
//echo "<br>"; break;
				// t_statutmoduleservclt
				$ARR_STATUT_MODULE = $BASE_SYNC->t_statutmoduleservclt->findOne(["N_SEREFEREA_MODULESERVCLT_ID" => $ROW['N_FACTURE_MODULESERVCLT_ID']]);
				//
				$DEBUG .= ' STATUTMDL : ' . $ARR_STATUT_MODULE['C_STATUTVAL_STATUTMODULESERVCLT_IDX'];

				if (in_array($ARR_STATUT_MODULE['C_STATUTVAL_STATUTMODULESERVCLT_IDX'], ["Validé.Prolongé", "Validé"])) { // Pré-créé
					// $DEBUG .= ' KEEP -- ';
					$RS_COUT = $BASE_SYNC->t_cout->find(['N_DEPEND_RFCUC_ID' => $ROW['N_ID'], 'B_ACTIF_COUT_IDX' => 'Oui']); // N_DEPEND_RFCUC_ID // N_REGUL_RFCFRNONGLOBALISEE_ID
					$DEBUG .= ' ' . $RS_COUT->count();
					if ($RS_COUT->count() == 0) {
						$RS_COUT = $BASE_SYNC->t_cout->find(['N_REGUL_RFCFRNONGLOBALISEE_ID' => $ROW['N_ID'], 'B_ACTIF_COUT_IDX' => 'Oui']); // N_DEPEND_RFCUC_ID // N_REGUL_RFCFRNONGLOBALISEE_ID
						$DEBUG .= ' ' . $RS_COUT->count();
					}
					if ($RS_COUT->count() == 1) {
						$ARR_COUT = $RS_COUT->getNext();
						$DEBUG .= ' COUT N_ID :  ' . $ARR_COUT['N_ID'] . ' : ' . $ARR_COUT['R_FACTURENB_COUTUNIFORME'] . ' ' . $ARR_COUT['B_ACTIF_COUT_IDX'];

						//
						$ARR_MODULE = $BASE_SYNC->t_moduleservclt->findOne(['N_ID' => $ROW['N_FACTURE_MODULESERVCLT_ID']]);
						$DEBUG .= ' ' . $ARR_MODULE['N_ESTCOMPRISDS_SOLSERVCLT_ID'];
						$ARR_SOLSERV = $BASE_SYNC->t_solservclt->findOne(['N_ID' => $ARR_MODULE['N_ESTCOMPRISDS_SOLSERVCLT_ID']]);

						if (!empty($ARR_SOLSERV['N_ID'])) {
							// type de cout ccn cccoul ...
							if (in_array($ARR_COUT['N_ESTFACTUREVIA_ART_ID'], ["60640", "60638", "60963", "60090", "60632", "60630", "60636", "60634"])) {

								$ARR_ART = $BASE_SYNC->t_art->findOne(['N_ID' => $ARR_COUT['N_ESTFACTUREVIA_ART_ID']]);
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'ENGAGEMENT NOIR') {
									$field_name = 'ccnContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'ENGAGEMENT COULEUR') {
									$field_name = 'cccoulContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'RELEVES COPIES NOIRES') {
									$field_name = 'ccnContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'RELEVES COPIES COULEUR') {
									$field_name = 'cccoulContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'COPIES SUPPLEMENTAIRES NOIRES') {
									$field_name = 'ccnsupContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}
								if ($ARR_ART['C_LIBCLT_ARTVENDU'] == 'COPIES SUPPLEMENTAIRES COULEURS') {
									$field_name = 'cccoulsupContrat';
									$artis_name = 'R_FACTURENB_COUTUNIFORME';
								}

								$DEBUG .= ' ' . $field_name;

								$ARR_CONTR = $APP_CON->findOne(['codeContrat' => $ARR_SOLSERV['C_CODE_SOLSERVCLT']]);
								if (!empty($ARR_CONTR['idcontrat'])) {
									$val              = (float)$ARR_COUT[$artis_name];
									$OUT[$field_name] = $val;
									$DEBUG .= ' => code : ' . $ARR_CONTR['codeContrat'];
									$APP_CON->update(['idcontrat' => (int)$ARR_CONTR['idcontrat']], $OUT);
									//	echo  $DEBUG;
//echo "<br>";
									// return '.';

								}
							}
						}
					}
				}

				// echo  $DEBUG;
				// echo "<br>";
				break;
			case "t_tradcyclevieinter": // statut intervention
				$NEW_ROW        = [];
				$APP_INTER_STAT = new App('intervention_statut');
				// $NEW_ROW[''] = $ROW[''];

				break;
			case "t_dmdeinter":  // la demande dinter est linter
				// T_DATEHEUREENREGISTREMENT_DMDEINTER
				// N_FOURNIT_INTERINFOPRINCIPALES_ID
				// C_NUMERO_DMDEINTER
				// C_ETAT_DMDEINTER_IDX => état
				$NEW_ROW        = [];
				$APP_MAT        = new App('materiel');
				$APP_INTER      = new App('intervention');
				$APP_INTER_STAT = new App('intervention_statut');
				//
				$t_interinfoprincipales = $BASE_SYNC->t_interinfoprincipales;
				$t_dmdeinter            = $BASE_SYNC->t_dmdeinter;
				$t_inter                = $BASE_SYNC->t_inter;

				if (empty($ROW['C_NUMERO_DMDEINTER'])) {
					break;
				}
				//
				$CODE_INTER = $ROW['C_NUMERO_DMDEINTER'];
				$ARR_INTER  = $APP_INTER->query_one(['codeIntervention' => $CODE_INTER]);

				if (empty($ARR_INTER['codeIntervention'])):
					$idinter = (int)$APP->getNext('idintervention');
				else:
					$idinter = (int)$ARR_INTER['idintervention'];
				endif;
				//
				$codeStatut = niceUrl($ROW['C_ETAT_DMDEINTER_IDX']);
				$arr_st     = $APP_INTER_STAT->findOne(['referenceIntervention_statut' => $codeStatut]);
				if (empty($arr_st['idintervention_statut'])) {
					$ROWSTAT['referenceIntervention_statut'] = $codeStatut;
					$ROWSTAT['nomIntervention_statut']       = $ROW['C_ETAT_DMDEINTER_IDX'];
					$NEW_ROW['idintervention_statut']        = $APP_INTER_STAT->create_update(['referenceIntervention_statut' => $codeStatut], $ROWSTAT);
				} else {
					$NEW_ROW['idintervention_statut'] = (int)$arr_st['idintervention_statut'];
				}
				if (empty($codeStatut)) {
					$codeStatut = 'cloturee';
					$arr_st     = $APP_INTER_STAT->findOne(['referenceIntervention_statut' => $codeStatut]);
					if (empty($arr_st['idintervention_statut'])) {
						$ROWSTAT['referenceIntervention_statut'] = $codeStatut;
						$ROWSTAT['nomIntervention_statut']       = $ROW['C_ETAT_DMDEINTER_IDX'];
						$NEW_ROW['idintervention_statut']        = $APP_INTER_STAT->create_update(['referenceIntervention_statut' => $codeStatut], $ROWSTAT);
					} else {
						$NEW_ROW['idintervention_statut'] = (int)$arr_st['idintervention_statut'];
					}
				}
				//
				$NEW_ROW['idintervention']               = $idinter;
				$NEW_ROW['codeIntervention']             = $CODE_INTER;
				$NEW_ROW['nomIntervention']              = empty($ARR_INTER['nomIntervention']) ? $CODE_INTER : $ARR_INTER['nomIntervention'];
				$NEW_ROW['dateCreationIntervention']     = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['heureCreationIntervention']    = date('H:i:s', strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['dateModificationIntervention'] = $ROW['T_UPDATE_DATE'];
				//
				$inser = $APP_INTER->update(['idintervention' => $idinter], $NEW_ROW);
			//
			//break;
			// INTERVENTION  // N_ESTPROVOQUEEPAR_DMDEINTER_ID.t_interinfoprincipales.N_ID
			case "t_inter":  // codeIntervention
				return;
				// N_GENERE_INTERREALISEE_ID
				// N_GENERE_INTERPLANIFIEE_ID
				// C_NUMERO_INTER
				// N_POSSEDE_INTERINFOPRINCIPALES_ID
				//
				$NEW_ROW                = [];
				$APP_MAT                = new App('materiel');
				$APP_INTER              = new App('intervention');
				$t_interinfoprincipales = $BASE_SYNC->t_interinfoprincipales;
				// $COLL_INTER 	= 	$APP->plug('sitebase_sync','t_inter');
				//
				$CODE_INTER = $ROW['C_NUMERO_INTER'];
				$ARR_INTER  = $APP_INTER->query_one(['codeIntervention' => $CODE_INTER]);
				if (empty($ARR_INTER['codeIntervention'])):
					$idinter = (int)$APP->getNext('idintervention');
				else:
					$idinter = (int)$ARR_INTER['idintervention'];
				endif;

				//
				$NEW_ROW['idintervention']               = $idinter;
				$NEW_ROW['codeIntervention']             = $CODE_INTER;
				$NEW_ROW['nomIntervention']              = $CODE_INTER;
				$NEW_ROW['dateCreationIntervention']     = $ROW['T_CREATE_DATE'];
				$NEW_ROW['dateModificationIntervention'] = $ROW['T_UPDATE_DATE'];

				if (!empty($ROW['N_POSSEDE_INTERINFOPRINCIPALES_ID'])):
					$arr_inf                    = $t_interinfoprincipales->findOne(['N_ID' => $ROW['N_POSSEDE_INTERINFOPRINCIPALES_ID']]);
					$NEW_ROW['nomIntervention'] = $arr_inf['N_PORTEPRECISEMENTSUR_BIEN_ID'];
				endif;
				// REC
				$inser = $APP_INTER->update(['idintervention' => $idinter], $NEW_ROW);
				$APP_INTER->consolidate_scheme($idinter);
				break;
			// PUIS INTERVENTION LIGNE, retrouver et mettre à jour t_inter ...
			case "t_interinfoprincipales":
				$time = time();
				ini_set('display_errors', 55);
				// return; //
				$NEW_ROW = [];
				//  lien N_SONTFOURNIESPAR_DMDEINTER_ID  t_dmdeinter
				// 	N_PORTEPRECISEMENTSUR_BIEN_ID => matos t_bien
				//	C_PROBLEME_INTERINFOPRINCIPALES => description
				//  N_ESTINTERSUR_SITE_ID => site N_ID
				//  N_ESTPRCPT_LIEUFONCTION_ID
				$t_interinfoprincipales = $BASE_SYNC->t_interinfoprincipales;
				$t_dmdeinter            = $BASE_SYNC->t_dmdeinter;
				$t_inter                = $BASE_SYNC->t_inter;
				$t_lieufonction         = $BASE_SYNC->t_lieufonction;
				$t_partie               = $BASE_SYNC->t_partie;
				//
				$ID_BIEN         = $ROW['N_PORTEPRECISEMENTSUR_BIEN_ID'];
				$ID_DMDEINTER    = $ROW['N_SONTFOURNIESPAR_DMDEINTER_ID']; // LINK
				$COLL_INTER_MAIN = $APP->plug('sitebase_sync', 't_inter');
				$COLL_INTER      = $APP->plug('sitebase_sync', 't_interinfoprincipales');
				$COLL_BIEN       = $APP->plug('sitebase_sync', 't_bien')->findOne(['N_ID' => $ID_BIEN]);
				//

				//
				$APP_MAT        = new App('materiel');
				$APP_INTER      = new App('intervention');
				$APP_INTER_TYPE = new App('intervention_type');
				$APP_CLI        = new App('client');
				// retrouver intervention principale dans t_inter : pas simple // code ?
				$arrMain    = $t_dmdeinter->findOne(['N_ID' => $ID_DMDEINTER]);
				$CODE_INTER = $arrMain['C_NUMERO_DMDEINTER'];
				$ID_LIEUFCT = $ROW['N_ESTPRCPT_LIEUFONCTION_ID'];
				if (empty($CODE_INTER)) break;
				// inter
				$arr_tmp_inter = $APP_INTER->findOne(['codeIntervention' => $CODE_INTER]);
				if (empty($arr_tmp_inter['idintervention'])) break;

				$NEW_ROW['idintervention'] = (int)$arr_tmp_inter['idintervention'];
				// MATOS
				$SERIE_BIEN = $COLL_BIEN['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				$arr_mat    = $APP_MAT->findOne(['codeMateriel' => $SERIE_BIEN]);
				// SITE, CLIENT
				$arr_fct    = $t_lieufonction->findOne(['N_ID' => $ID_LIEUFCT]);
				$ID_PARTIE  = $arr_fct['N_CORRESPA_ORG_ID'];
				$arr_partie = $t_partie->findOne(['N_ID' => $ID_PARTIE]);
				$codeClient = $arr_partie['C_CODE_ORG'];
				$arr_cli    = $APP_CLI->findOne(['codeClient' => $codeClient]);
				// type C_TYPETRAVAIL_INTERINFOPRINCIPALES_IDX
				//
				$codeType = niceUrl($ROW['C_TYPETRAVAIL_INTERINFOPRINCIPALES_IDX']);
				$nomType  = $ROW['C_TYPETRAVAIL_INTERINFOPRINCIPALES_IDX'];
				if ($codeType != 'false' && !empty($codeType)) {
					$arr_ty = $APP_INTER_TYPE->findOne(['codeIntervention_type' => $codeType]);
					if (!empty($arr_ty['idintervention_type'])) {
						$NEW_ROW['idintervention_type'] = (int)$arr_ty['idintervention_type'];
					} else {
						$NEW_ROW['idintervention_type'] = $APP_INTER_TYPE->create_update(['codeIntervention_type' => $codeType], ['nomIntervention_type' => $nomType]);
					}
				} else {
					$codeType = 'OFFICE';
					$nomType  = 'Bureau';
					$arr_ty   = $APP_INTER_TYPE->findOne(['codeIntervention_type' => $codeType]);
					if (!empty($arr_ty['idintervention_type'])) {
						$NEW_ROW['idintervention_type'] = (int)$arr_ty['idintervention_type'];
					} else {
						$NEW_ROW['idintervention_type'] = $APP_INTER_TYPE->create_update(['codeIntervention_type' => $codeType], ['nomIntervention_type' => $nomType]);
					}
				};
				//
				if (empty($arrMain['N_ID'])) {
					break;
				}
				if (empty($COLL_BIEN['N_ID'])) {
					break;
				}
				if (!empty($arr_cli['idclient'])) {
					$NEW_ROW['idclient'] = (int)$arr_cli['idclient'];
				};
				if (!empty($arr_mat['idmateriel'])) {
					$NEW_ROW['idmateriel']  = (int)$arr_mat['idmateriel'];
					$NEW_ROW['nomMateriel'] = $arr_mat['nomMateriel'];
					if (!empty($arr_mat['idsite'])) {
						$NEW_ROW['idsite'] = (int)$arr_mat['idsite'];
					}
				};
				//

				$NEW_ROW['nomIntervention']         = $arr_cli['nomClient'] . ' ' . coupeChaine($ROW['C_PROBLEME_INTERINFOPRINCIPALES'], 30);
				$NEW_ROW['descriptionIntervention'] = br2nl($ROW['C_PROBLEME_INTERINFOPRINCIPALES']);
				// $NEW_ROW['nomIntervention']              = $CODE_INTER;
				//

				$inser = $APP_INTER->create_update(['codeIntervention' => $CODE_INTER], $NEW_ROW);

				// echo (time()- $time).'<br>';
				// vardump($NEW_ROW);
				//
				// exit;
				break;
			//
			case "t_interplanifiee":
				// N_ESTFOURNIEPAR_DMDEINTER_ID
				// N_ESTFAITESUITEA_INTER_ID
				// agent : N_ESTPLANIFIEEPR_COLLABO_ID => t_partie.N_ID

				$NEW_ROW   = []; //
				$APP_INTER = new App('intervention');
				$APP_AGENT = new App('agent');
				//
				$ID_DMDEINTER = $ROW['N_ESTFOURNIEPAR_DMDEINTER_ID'];

				$arrMain    = $BASE_SYNC->t_dmdeinter->findOne(['N_ID' => $ID_DMDEINTER]);
				$CODE_INTER = $arrMain['C_NUMERO_DMDEINTER'];
				//01
				$arr_inter = $APP_INTER->findOne(['codeIntervention' => $CODE_INTER]);
				if (empty($arr_inter['idintervention'])) {
					break;
				}
				$idintervention = (int)$NEW_ROW['idintervention'] = (int)$arr_inter['idintervention'];

				// AGENT : N_ESTPLANIFIEEPR_COLLABO_ID
				$arr_partie         = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTPLANIFIEEPR_COLLABO_ID']]);
				$C_CODE_ORG         = $arr_partie['C_MATRICULE_COLLABO'];
				$arr_agent          = $APP_AGENT->findOne(['codeAgent' => $C_CODE_ORG]);
				$NEW_ROW['idagent'] = (int)$arr_agent['idagent'];

				// T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE
				// T_DATEHEUREENREGISTREMENT_INTERPLANIFIEE
				// T_DATEHEUREFINPREVUE_INTERPLANIFIEE
				// T_DATEHEUREMAXIINTERVENTION_INTERPLANIFIEE
				$time_debut = strtotime($ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE']);
				$time_fin   = strtotime($ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']);
				$time_max   = strtotime($ROW['T_DATEHEUREMAXIINTERVENTION_INTERPLANIFIEE']);

				$NEW_ROW['dateDebutIntervention']  = date('Y-m-d', $time_debut);
				$NEW_ROW['heureDebutIntervention'] = date('H:i:s', $time_debut);
				$NEW_ROW['dateFinIntervention']    = date('Y-m-d', $time_fin);
				$NEW_ROW['heureFinIntervention']   = date('H:i:s', $time_fin);
				$NEW_ROW['dateMaxIntervention']    = date('Y-m-d', $time_max);
				$NEW_ROW['heureMaxIntervention']   = date('H:i:s', $time_max);

				//
				$APP_INTER->update(['idintervention' => $idintervention], $NEW_ROW);
				break;
			// AFFAIRE
			case "t_affaire":
				// 	if($ROW['C_NUMEROINTERNE_AFFAIRE']!='00088') break;
				//  D_DATEDEAFFAIRE_AFFAIRE 	=> dateDebut
				//	C_NUMEROINTERNE_AFFAIRE 	=> codeAffaire
				//  C_COMMENTAIRES_AFFAIRE 		=> commentaire affaire
				// 	N_ACOMMECLIENT_LIEUFONCTION_ID => 38106
				//
				$NEW_ROW     = []; //
				$APP_AFFAIRE = new App('affaire');
				$APP_AGENT   = new App('agent');
				$APP_CLIENT  = new App('client');
				$APP_ENT     = new App('entite');
				//
				$NEW_ROW['dateDebutAffaire']   = date('Y-m-d', strtotime($ROW['D_DATEDEAFFAIRE_AFFAIRE'])); // $ROW['D_DATEDEAFFAIRE_AFFAIRE'];
				$NEW_ROW['codeAffaire']        = $ROW['C_NUMEROINTERNE_AFFAIRE'];
				$NEW_ROW['descriptionAffaire'] = $ROW['C_COMMENTAIRES_AFFAIRE'];
				$lieu_fonction                 = $ROW['N_ACOMMECLIENT_LIEUFONCTION_ID'];
				$collabo                       = $ROW['N_ESTVENDUEPAR_COLLABO_ID'];
				$entite                        = $ROW['N_ACOMMEPRESTATAIRE_LIEUFONCTION_ID']; // 7

				// client
				$ARR_LF = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $lieu_fonction]);
				$ARR_CL = $APP_CLIENT->findOne(['codeClient' => $ARR_LF['C_CODE_LIEUFONCTION']]);

				if (empty($ARR_CL['idclient'])) {
					break;
				}

				$NEW_ROW['idclient']   = (int)$ARR_CL['idclient'];
				$NEW_ROW['nomAffaire'] = $ROW['C_NUMEROINTERNE_AFFAIRE'] . ' ' . substr(str_replace(' ', '', $ARR_CL['nomClient']), 0, 12);
				// entite
				$ent1                = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $entite]);
				$partie1             = $BASE_SYNC->t_partie->findOne(['N_ID' => $ent1['N_CORRESPA_ORG_ID']]);
				$code_ent            = $partie1['C_CODE_ORG'];
				$arr_ent             = $APP_ENT->findOne(['codeEntite' => $code_ent]);
				$NEW_ROW['identite'] = (int)$arr_ent['identite'];
				// agent
				$ARR_LF = $BASE_SYNC->t_partie->findOne(['N_ID' => $collabo]);
				$ARR_CL = $APP_AGENT->findOne(['codeAgent' => $ARR_LF['C_MATRICULE_COLLABO']]);
				if (!empty($ARR_CL['idagent'])) {
					$NEW_ROW['idagent'] = (int)$ARR_CL['idagent'];
				}
				// affaire
				$test = $APP_AFFAIRE->query(['codeAffaire' => $ROW['C_NUMEROINTERNE_AFFAIRE']]);
				if ($test->count() == 0):
					$idaffaire            = (int)$APP->getNext('idaffaire');
					$NEW_ROW['idaffaire'] = $idaffaire;

					$APP_AFFAIRE->insert($NEW_ROW);
				else:
					$arr_c     = $test->getNext();
					$idaffaire = (int)$arr_c['idaffaire'];
					$APP_AFFAIRE->update(['idaffaire' => $idaffaire], $NEW_ROW);
				endif;

				//  A prendre dans t_cmde(s)
				$RS_CMDE = $BASE_SYNC->t_cmde->find(['N_ESTINCLUEDS_AFFAIRE_ID' => $ROW['N_ID']]);
				if ($RS_CMDE->count() != 0) {

					// MAJ affaire totaux
					$NEW_ROW_AFF                   = [];
					$NEW_ROW_AFF['totalHtAffaire'] = $NEW_ROW_AFF['totalTtcAffaire'] = $NEW_ROW_AFF['totalTvaAffaire'] = 0;
					while ($ROW_CMDE = $RS_CMDE->getNext()):
						// R_TOTALHTCOMMANDENB_CMDE  // R_TOTALHTTAXABLECOMMANDENB_CMDE  // R_TOTALTTCCOMMANDENB_CMDE // R_TOTALTVACOMMANDENB_CMDE
						$NEW_ROW_AFF['montantHtAffaire'] += $ROW_CMDE['R_TOTALHTTAXABLECOMMANDENB_CMDE'];
						/*$NEW_ROW_AFF['totalHtAffaire'] += $ROW_CMDE['R_TOTALHTTAXABLECOMMANDENB_CMDE'];
						$NEW_ROW_AFF['totalTtcAffaire'] += $ROW_CMDE['R_TOTALTTCCOMMANDENB_CMDE'];
						$NEW_ROW_AFF['totalTvaAffaire'] += $ROW_CMDE['R_TOTALTVACOMMANDENB_CMDE'];*/
					endwhile;
					$APP_AFFAIRE->update(['idaffaire' => (int)$idaffaire], $NEW_ROW_AFF);
				}
				break;
			case "t_cmde":
				// $ROW['C_NUMEROINTERNE_CMDE'] ;
				// $ROW['C_COMMENTAIRES_CMDE'];
				// $ROW['D_DATEDECOMMANDE_CMDE'];
				// $ROW['N_ESTEMISEPAR_LIEUFONCTION_ID'];
				// $ROW['N_ESTINCLUEDS_AFFAIRE_ID'];
				// $ROW['N_ESTRECUEPAR_LIEUFONCTION_ID']; // entite
				// $ROW['N_ESTSUIVIEPAR_COLLABO_ID'];
				// $ROW['R_TOTALHTCOMMANDENB_CMDE'] ;// prix vte
				// $ROW['R_COUTTOTALDEREVIENTNB_CMDE'] ;// prix ha
				// $ROW['C_ETATDEFACTURATION_CMDE_IDX'] ;//  Facturable
				// $ROW['C_ETATDEPREPARATION_CMDECLT_IDX']; // Sans preparation
				// $ROW['{N_ESTINCLUEDS_AFFAIRE_ID']; // AFFAIRE
				//

				$NEW_ROW             = []; //
				$APP_COMMANDE        = new App('commande');
				$APP_COMMANDE_STATUT = new App('commande_statut');
				$APP_AGENT           = new App('agent');
				$APP_CLIENT          = new App('client');
				$APP_AFFAIRE         = new App('affaire');
				//
				$NEW_ROW['dateDebutCommande'] = date('Y-m-d', strtotime($ROW['D_DATEDECOMMANDE_CMDE']));
				$NEW_ROW['codeCommande']      = $ROW['C_NUMEROINTERNE_CMDE'];

				$NEW_ROW['descriptionCommande'] = $ROW['C_COMMENTAIRES_CMDE'];
				$lieu_fonction                  = $ROW['N_ESTEMISEPAR_LIEUFONCTION_ID'];
				$collabo                        = $ROW['N_ESTSUIVIEPAR_COLLABO_ID'];
				// client
				// echo "collab ".$collabo."<br>";
				// client
				$ARR_LF = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $lieu_fonction]);
				$ARR_CL = $APP_CLIENT->findOne(['codeClient' => $ARR_LF['C_CODE_LIEUFONCTION']]);

				if (empty($ARR_CL['idclient'])) {
					break;
				}
				$NEW_ROW['idclient']    = (int)$ARR_CL['idclient'];
				$NEW_ROW['nomCommande'] = $ROW['C_NUMEROINTERNE_CMDE'] . ' ' . $ARR_CL['codeClient'] . ' ' . substr(str_replace(' ', '', $ARR_CL['nomClient']), 0, 12);
				// agent
				$ARR_LF = $BASE_SYNC->t_partie->findOne(['N_ID' => $collabo]);
				$ARR_CL = $APP_AGENT->findOne(['codeAgent' => $ARR_LF['C_MATRICULE_COLLABO']]);
				if (!empty($ARR_CL['idagent'])) {
					$NEW_ROW['idagent'] = (int)$ARR_CL['idagent'];
				}
				//  affaire // N_ESTINCLUEDS_AFFAIRE_ID //  envoyer prix ??
				$ARR_AF  = $BASE_SYNC->t_affaire->findOne(['N_ID' => $ROW['N_ESTINCLUEDS_AFFAIRE_ID']]);
				$test_af = $APP_AFFAIRE->query_one(['codeAffaire' => $ARR_AF['C_NUMEROINTERNE_AFFAIRE']]);
				if (!empty($test_af['idaffaire'])) {
					$NEW_ROW['idaffaire'] = (int)$test_af['idaffaire'];
				}
				// echo $ROW['C_ETAT_CMDEFOURN_IDX'];
				// statut COMMANDE C_ETAT_CMDEFOURN_IDX
				if (!empty($ROW['C_ETAT_CMDEFOURN_IDX']) && $ROW['C_ETAT_CMDEFOURN_IDX'] != 'false' && $ROW['C_ETAT_CMDEFOURN_IDX'] != false) {
					// $codeCommande_statut
					$codeCommande_statut = niceUrl($ROW['C_ETAT_CMDEFOURN_IDX']);
					$ARR_STATUT          = $APP_COMMANDE_STATUT->findOne(['codeCommande_statut' => $codeCommande_statut]);
					if (empty($ARR_STATUT['idcommande_statut'])) {
						$NEW_ROW['idcommande_statut'] = $APP_COMMANDE_STATUT->create_update(['codeCommande_statut' => $codeCommande_statut], ['nomCommande_statut' => $ROW['C_ETAT_CMDEFOURN_IDX']]);
					} else {
						$NEW_ROW['idcommande_statut'] = (int)$ARR_STATUT['idcommande_statut'];
					}
				}

				// COMMANDE
				// $NEW_ROW['montantCommande']     = $ROW['R_TOTALHTCOMMANDENB_CMDE'];
				// $NEW_ROW['prixCommande']        = $ROW['R_TOTALHTCOMMANDENB_CMDE'];
				$NEW_ROW['totalHtCommande']  = $ROW['R_TOTALHTCOMMANDENB_CMDE'];
				$NEW_ROW['totalTtcCommande'] = $ROW['R_TOTALTTCCOMMANDENB_CMDE'];

				// commande
				$test = $APP_COMMANDE->query(['codeCommande' => $ROW['C_NUMEROINTERNE_CMDE']]);
				if ($test->count() == 0):
					$idcommande            = (int)$APP->getNext('idcommande');
					$NEW_ROW['idcommande'] = $idcommande;

					$APP_COMMANDE->insert($NEW_ROW);
				else:
					$arr_c      = $test->getNext();
					$idcommande = (int)$arr_c['idcommande'];
					$APP_COMMANDE->update(['idcommande' => $idcommande], $NEW_ROW);
				endif;

				if (!empty($test_af['idaffaire'])) {
					// MAJ affaire totaux
					$NEW_ROW_AFF = [];
					// R_TOTALHTCOMMANDENB_CMDE  // R_TOTALHTTAXABLECOMMANDENB_CMDE  // R_TOTALTTCCOMMANDENB_CMDE // R_TOTALTVACOMMANDENB_CMDE
					$NEW_ROW_AFF['totalHtAffaire']  = $ROW['R_TOTALHTTAXABLECOMMANDENB_CMDE'];
					$NEW_ROW_AFF['totalTtcAffaire'] = $ROW['R_TOTALTTCCOMMANDENB_CMDE'];
					$NEW_ROW_AFF['totalTvaAffaire'] = $ROW['R_TOTALTVACOMMANDENB_CMDE'];

					$APP_AFFAIRE->update(['idaffaire' => (int)$test_af['idaffaire']], $NEW_ROW_AFF);
				}

				break;
			case "t_lgncmde":
				// N_ESTRATTACHEEA_CMDE_ID
				// C_LIBARTICLE_LGNDETAILCMDE
				// N_CMDE_ART_ID accessoire si pas vide, sinon materiel N_CMDEPR_BIEN_ID
				// R_QTECONDITIONNEE_LGNCMDE
				$NEW_ROW            = []; //
				$APP_COMMANDE       = new App('commande');
				$APP_COMMANDE_LIGNE = new App('commande_ligne');
				$APP_AGENT          = new App('agent');
				$APP_CLIENT         = new App('client');
				$APP_AFFAIRE        = new App('affaire');
				$APP_MATERIEL       = new App('materiel');

				$test_com = $BASE_SYNC->t_cmde->findOne(['N_ID' => $ROW['N_ESTRATTACHEEA_CMDE_ID']]);
				if (empty($test_com['N_ID'])) {
					break;
				}
				$test = $APP_COMMANDE->query_one(['codeCommande' => $test_com['C_NUMEROINTERNE_CMDE']]);
				if (empty($test['idcommande'])) {
					break;
				}
				$NEW_ROW['idcommande']             = (int)$test['idcommande'];
				$NEW_ROW['nomCommande_ligne']      = $ROW['C_LIBARTICLE_LGNDETAILCMDE'];
				$NEW_ROW['quantiteCommande_ligne'] = (int)$ROW['R_QTECONDITIONNEE_LGNCMDE'];
				$NEW_ROW['prixCommande_ligne']     = $ROW['R_PRIXUNITAIREHTNB_LGNCMDE'];
				$NEW_ROW['totalCommande_ligne']    = $ROW['R_TOTALHTLIGNENB_LGNCMDE'];
				$NEW_ROW['N_ID']                   = $ROW['N_ID'];
				if (!empty($ROW['N_CMDE_ART_ID'])) {

				} else {

				}
				if (!empty($ROW['N_CMDEPR_BIEN_ID'])):
					$test_mat = $BASE_SYNC->t_bien->findOne(['N_ID' => $ROW['N_CMDEPR_BIEN_ID']]);
					if (empty($test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA'])) {
						break;
					}
					$test = $APP_MATERIEL->query_one(['codeMateriel' => $test_mat['C_IDENTIFIANTFABRICANT_BIENIMMA']]);
					if (empty($test['idmateriel'])) {
						break;
					}
					$NEW_ROW['idmateriel'] = (int)$test['idmateriel'];
				endif;
				// commande_ligne

				$APP_COMMANDE_LIGNE->create_update(['N_ID' => $ROW['N_ID']], $NEW_ROW);

				// ligne de commande / commande / affaire / financement / leaser !
				break;
			case "t_typfinancement":
				$NEW_ROW     = []; //
				$APP_FINATYP = new App('financement_type');
				// vardump($ROW);
				$NEW_ROW['codeFinancement_type']  = $ROW['C_CODE_TYPFINANCEMENT'];
				$NEW_ROW['nomFinancement_type']   = $ROW['C_LIB_TYPFINANCEMENT'];
				$NEW_ROW['ordreFinancement_type'] = (int)$ROW['N_ID'];

				$APP_FINATYP->create_update(['codeFinancement_type' => $NEW_ROW['codeFinancement_type']], $NEW_ROW);

				break;
			case "t_financement":

				// vardump($ROW);
				// N_ACOMMECLIENT_LIEUFONCTION_ID
				// N_ACOMMELEASER_LIEUFONCTION_ID
				// N_ACOMMEPRESTATAIRE_LIEUFONCTION_ID   entite
				//  N_ESTINCLUDS_AFFAIRE_ID    => AFFAIRE !!!!
				// N_ESTSUIVIPAR_COLLABO_ID
				// N_ESTVENDUPAR_COLLABO_ID
				// C_NUMEROINTERNE_FINANCEMENT
				// N_A_TYPFINANCEMENT_ID
				// R_COEFFICIENTDEFINANCEMENT_FINANCEMENT   // C_NUMEROEXTERNE_FINANCEMENT
				// R_MONTANTFINANCE_FINANCEMENT
				$NEW_ROW     = []; //
				$APP_AGENT   = new App('agent');
				$APP_CLIENT  = new App('client');
				$APP_AFFAIRE = new App('affaire');
				$APP_FINA    = new App('financement');
				$APP_FINATYP = new App('financement_type');
				$APP_ENT     = new App('entite');
				$APP_LEASER  = new App('leaser');

				if (empty($ROW['C_NUMEROINTERNE_FINANCEMENT'])) break;
				// date !!
				$ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT'] = date('Y-m-d', strtotime($ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT']));
				// client
				$ARR_LF = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ACOMMECLIENT_LIEUFONCTION_ID']]);
				$ARR_CL = $APP_CLIENT->findOne(['codeClient' => $ARR_LF['C_CODE_LIEUFONCTION']]);
				if (empty($ARR_CL['idclient'])) {
					//	echo " Client lieufonction " . $ROW['N_ACOMMECLIENT_LIEUFONCTION_ID'];
					//	echo "<br>Client non créé " . $ARR_LF['C_CODE_LIEUFONCTION'];
					//	echo " fail ".$ARR_LF['C_CODE_LIEUFONCTION'].'=>'.$ROW['C_NUMEROINTERNE_FINANCEMENT'].'<br>';
					break;
				}
				$NEW_ROW['idclient'] = (int)$ARR_CL['idclient'];

				// entite
				$ent1                = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ACOMMEPRESTATAIRE_LIEUFONCTION_ID']]);
				$partie1             = $BASE_SYNC->t_partie->findOne(['N_ID' => $ent1['N_CORRESPA_ORG_ID']]);
				$code_ent            = $partie1['C_CODE_ORG'];
				$arr_ent             = $APP_ENT->findOne(['codeEntite' => $code_ent]);
				$NEW_ROW['identite'] = (int)$arr_ent['identite'];

				// agent
				$ARR_LF             = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTVENDUPAR_COLLABO_ID']]);
				$ARR_AG             = $APP_AGENT->findOne(['codeAgent' => $ARR_LF['C_MATRICULE_COLLABO']]);
				$NEW_ROW['idagent'] = (int)$ARR_AG['idagent'];

				//  affaire // N_ESTINCLUEDS_AFFAIRE_ID //  envoyer prix ??
				$ARR_AF  = $BASE_SYNC->t_affaire->findOne(['N_ID' => $ROW['N_ESTINCLUDS_AFFAIRE_ID']]);
				$test_af = $APP_AFFAIRE->query_one(['codeAffaire' => $ARR_AF['C_NUMEROINTERNE_AFFAIRE']]);
				if (!empty($test_af['idaffaire'])) {
					$NEW_ROW['idaffaire'] = (int)$test_af['idaffaire'];
					$APP_AFFAIRE->update(['idaffaire' => (int)$test_af['idaffaire']],['dateFinAffaire' => $ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT']]);
				}

				// LEASER
				$ARR_LF     = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ACOMMELEASER_LIEUFONCTION_ID']]);
				$ARR_PARTIE = $BASE_SYNC->t_partie->findOne(['N_ID' => $ARR_LF['N_CORRESPA_ORG_ID']]);
				$ARR_LEASER = $APP_LEASER->findOne(['codeLeaser' => $ARR_PARTIE['C_CODE_ORG']]);

				if (empty($ARR_LEASER['codeLeaser']) && !empty($ARR_PARTIE['C_CODE_ORG'])):
					$NEW_ROW['idleaser'] = (int)$APP_LEASER->create_update(['codeLeaser' => $ARR_PARTIE['C_CODE_ORG']], ['codeLeaser' => $ARR_PARTIE['C_CODE_ORG'], 'nomLeaser' => strtolower($ARR_PARTIE['C_CODE_ORG'])]);
				else :
					$NEW_ROW['idleaser'] = (int)$ARR_LEASER['idleaser'];
				endif;

				// FINANCEMENT
				$NEW_ROW['dureeFinancement']   = (int)$ROW['N_DUREEIRREVOCABLEDELALOCATIONENMOIS_FINANCEMENT'];
				$NEW_ROW['dateFinFinancement'] = $ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT'];
				// $NEW_ROW['montantFinancement']   = $ROW['R_MONTANTFINANCE_FINANCEMENT'];
				$NEW_ROW['codeFinancement']      = $ROW['C_NUMEROINTERNE_FINANCEMENT'];
				$NEW_ROW['referenceFinancement'] = $ROW['C_NUMEROEXTERNE_FINANCEMENT'];
				$NEW_ROW['tauxFinancement']      = $ROW['R_COEFFICIENTDEFINANCEMENT_FINANCEMENT'];
				// un tour par commande / affaire pour debusquer   la machine et inscrire le leaser dans le contrat

				//  type
				$ARR_TY                        = $BASE_SYNC->t_typfinancement->findOne(['N_ID' => $ROW['N_A_TYPFINANCEMENT_ID']]);
				$ARR_TYPE                      = $APP_FINATYP->findOne(['codeFinancement_type' => $ARR_TY['C_CODE_TYPFINANCEMENT']]);
				$NEW_ROW['idfinancement_type'] = (int)$ARR_TYPE['idfinancement_type'];

				$NEW_ROW['nomFinancement'] = $ROW['C_NUMEROINTERNE_FINANCEMENT'] . ' ' . $ARR_TY['C_CODE_TYPFINANCEMENT'] . ' ' . $ARR_CL['nomClient'];

				$APP_FINA->create_update(['codeFinancement' => $ROW['C_NUMEROINTERNE_FINANCEMENT']], $NEW_ROW);
				//  si affaire

				break;
			case "t_lgnfinancee": // rattache => fianncement , ligne de commande
				// N_ESTINCLUSEDS_FINANCEMENTCLT_ID papa
				// N_APRLIGNECMDE_LGNCMDE_ID
				// R_MONTANTAFINANCER_LGNFINANCEE => montant
				// R_MONTANTDEECHEANCE_LGNFINANCEE => échéance
				$NEW_ROW            = []; //
				$APP_COMMANDE       = new App('commande');
				$APP_COMMANDE_LIGNE = new App('commande_ligne');
				$APP_AGENT          = new App('agent');
				$APP_CLIENT         = new App('client');
				$APP_AFFAIRE        = new App('affaire');
				$APP_MATERIEL       = new App('materiel');
				$APP_FINA           = new App('financement');
				$APP_FINA_LGN       = new App('financement_ligne');
				$APP_ENT            = new App('entite');

				// financement
				$arr_papa = $BASE_SYNC->t_financement->findOne(['N_ID' => $ROW['N_ESTINCLUSEDS_FINANCEMENTCLT_ID']]);
				$arr_f    = $APP_FINA->findOne(['codeFinancement' => $arr_papa['C_NUMEROINTERNE_FINANCEMENT']]);
				if (empty($arr_f['idfinancement'])) {
					// echo "<br>fail ".$arr_papa['C_NUMEROINTERNE_FINANCEMENT'];
					break;
				}
				$NEW_ROW['idfinancement'] = $idfinancement = (int)$arr_f['idfinancement'];

				// ligne commande
				// 	$arr_papa                    = $BASE_SYNC->t_cmde->findOne([ 'N_ID' => $ROW['N_APRLIGNECMDE_LGNCMDE_ID'] ]);

				$arr_f = $APP_COMMANDE_LIGNE->findOne(['N_ID' => $ROW['N_APRLIGNECMDE_LGNCMDE_ID']]);
				if (!empty($arr_f['idcommande_ligne'])) {
					$NEW_ROW['idcommande_ligne']     = $idcommande_ligne = (int)$arr_f['idcommande_ligne'];
					$NEW_ROW['nomFinancement_ligne'] = $arr_f['nomCommande_ligne'];
					if (!empty($arr_f['idmateriel'])) {
						$NEW_ROW['idmateriel'] = $idmateriel = (int)$arr_f['idmateriel'];
						$ins                   = ['idfinancement' => $idfinancement];
						// idfinancement dans materiel =>
						if (!empty($arr_f['idleaser'])) {
							$ins['idleaser'] = (int)$arr_f['idleaser'];
						}
						$APP_MATERIEL->update(['idmateriel' => $idmateriel], $ins);
					}
				}
				//
				$NEW_ROW['N_ID']                             = $ROW['N_ID'];
				$NEW_ROW['prixFinancement_ligne']            = $ROW['R_MONTANTAFINANCER_LGNFINANCEE'];
				$NEW_ROW['quantiteFinancement_ligne']        = 1;
				$NEW_ROW['montantFinancement_ligne']         = $ROW['R_MONTANTAFINANCER_LGNFINANCEE'];
				$NEW_ROW['montantEcheanceFinancement_ligne'] = $ROW['R_MONTANTDEECHEANCE_LGNFINANCEE'];
				//
				$APP_FINA_LGN->create_update(['N_ID' => $ROW['N_ID']], $NEW_ROW); //
				// Somme des lignes pour financement.
				if (empty($arr_papa['R_MONTANTFINANCE_FINANCEMENT']) && empty($arr_f['montantFinancement'])):
					$test = $APP_FINA_LGN->distinct('idfinancement_ligne', ['idfinancement' => $idfinancement], '', 'no_full', 'montantFinancement_ligne');
					$test = $APP_FINA_LGN->find(['idfinancement' => $idfinancement], ['montantFinancement_ligne' => 1, '_id' => 0]);
					$tot  = 0;
					foreach (iterator_to_array($test) as $keyy => $vvalue) {
						$tot += (int)$vvalue['montantFinancement_ligne'];
					}
					$APP_FINA->update(['idfinancement' => $idfinancement], ['montantFinancement' => $tot]);
				endif;

				break;
			case "t_echeancierfinancement":
				$NEW_ROW  = []; //
				$APP_FINA = new App('financement');

				$NEW_ROW['N_ID'] = $ROW['N_ID'];
				// $NEW_ROW['N_ID']                             = $ROW['N_ESTECHEANCIER_FINANCEMENT_ID'];
				// $NEW_ROW['prixFinancement_ligne']            = $ROW['D_DATEDEDERNECHEANCE_ECHEANCIERFINANCEMENT'];
				// $NEW_ROW['montantEcheanceFinancement_ligne'] = $ROW['D_DATEDEDERNECHEANCE_ECHEANCIERFINANCEMENT'];
				$NEW_ROW['dateDebutFinancement'] = $ROW['D_DATEDEPREMIEREECHEANCE_ECHEANCIERFINANCEMENT'];

				$arr_bf = $BASE_SYNC->t_financement->findOne(['N_ID' => $ROW['N_ESTECHEANCIER_FINANCEMENT_ID']]);

				if (empty($arr_bf['N_ID'])) break;
				$arr_f = $APP_FINA->findOne(['codeFinancement' => $arr_bf['C_NUMEROINTERNE_FINANCEMENT']]);

				if (empty($arr_f['idfinancement'])) break;
				$NEW_ROW['idfinancement']              = (int)$arr_f['idfinancement'];
				$NEW_ROW['dateQuantiemeFinancement']   = date('Y-m-d', strtotime($ROW['D_DATEDEPREMIEREECHEANCE_ECHEANCIERFINANCEMENT']));
				$NEW_ROW['dateFinFinancement']         = date('Y-m-d', strtotime($ROW['D_DATEDEDERNECHEANCE_ECHEANCIERFINANCEMENT']));
				$NEW_ROW['montantEcheanceFinancement'] = $ROW['R_MONTANTDULOYERHT_ECHEANCIERFINANCEMENT'];

				$APP_FINA->update(['idfinancement' => $NEW_ROW['idfinancement']], $NEW_ROW);
				// inscription quantieme dans le contrat ?
				// lgn de financement => materiel => contrat

				break;
			case "t_rachat":
				// C_NUMEROINTERNE_RACHAT
				// C_COMMENTAIRES_RACHAT
				// R_SOLDEDUFINANCEMENTRACHETENB_RACHAT
				//
				// N_ACOMMEPRESTATAIRE_LIEUFONCTION_ID
				// N_ACOMMECLIENT_LIEUFONCTION_ID
				// D_DATEDEVALIDDUSOLDE_RACHAT
				// N_ESTINCLUDS_AFFAIRE_ID
				// N_ESTVENDUPAR_COLLABO_ID
				// C_ETATDEFATURATION_RACHAT_IDX
				// C_ETATDUDOSSIER_RACHAT_IDX

				$NEW_ROW      = []; //
				$APP_AFFAIRE  = new App('affaire');
				$APP_MATERIEL = new App('materiel');
				$APP_FINA     = new App('financement');
				$APP_FINA_LGN = new App('financement_ligne');
				$APP_ENT      = new App('entite');
				$APP_RACHAT   = new App('rachat');
				$APP_CLIENT   = new App('client');
				$APP_AGENT    = new App('agent');

				$NEW_ROW['codeRachat']        = $ROW['C_NUMEROINTERNE_RACHAT'];
				$NEW_ROW['commentaireRachat'] = $ROW['C_COMMENTAIRES_RACHAT'];
				$NEW_ROW['totalRachat']       = (float)$ROW['R_SOLDEDUFINANCEMENTRACHETENB_RACHAT'];
				// client
				$ARR_LF = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ACOMMECLIENT_LIEUFONCTION_ID']]);
				$ARR_CL = $APP_CLIENT->findOne(['codeClient' => $ARR_LF['C_CODE_LIEUFONCTION']]);
				if (!empty($ARR_CL['idclient'])) {
					$NEW_ROW['idclient'] = (int)$ARR_CL['idclient'];
				} else {
					break;
				}
				// agent
				$ARR_LF = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTVENDUPAR_COLLABO_ID']]);
				$ARR_AG = $APP_AGENT->findOne(['codeAgent' => $ARR_LF['C_MATRICULE_COLLABO']]);
				if (!empty($ARR_CL['idagent'])) {
					$NEW_ROW['idagent'] = (int)$ARR_AG['idagent'];
				}
				// affaire ?
				$ARR_AF  = $BASE_SYNC->t_affaire->findOne(['N_ID' => $ROW['N_ESTINCLUDS_AFFAIRE_ID']]);
				$test_af = $APP_AFFAIRE->query_one(['codeAffaire' => $ARR_AF['C_NUMEROINTERNE_AFFAIRE']]);
				if (!empty($test_af['idaffaire'])) {
					$NEW_ROW['idaffaire'] = (int)$test_af['idaffaire'];
				}
				$NEW_ROW['nomRachat'] = strtoupper(substr(str_replace(' ', '', $ARR_CL['nomClient']), 0, 12)) . ' ' . $ARR_CL['codeClient'];
				$NEW_ROW['dateCreationRachat'] = date('Y-m-d',strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['dateReglementRachat'] = $ROW['D_DATEDEREGLEMENT_RACHAT'];

				$idrachat = $APP_RACHAT->create_update(['codeRachat' => $ROW['C_NUMEROINTERNE_RACHAT']], $NEW_ROW);

				break;
			case "t_rachatbiens":   // => rachat_ligne liée à lgncmde
				// N_SOURCE_BIEN_ID
				// N_TARGET_RACHAT_ID
				// R_SOLDENB_RACHATBIENS
				// R_VALORISATIONDUBIENNB_RACHATBIENS 1.000000 => argus
				// N_SERAREPRISDS_LGNCMDEARTVENDU_ID
				// C_COMMENTAIRES_RACHATBIENS + C_ETAPESRESTANTESDELAREPRISEDUBIEN_RACHATBIENS_IDX + C_FINDEFINANCEMENTPREVUE_RACHATBIENS_IDX

				$NEW_ROW        = []; //
				$APP_AFFAIRE    = new App('affaire');
				$APP_MATERIEL   = new App('materiel');
				$APP_FINA       = new App('financement');
				$APP_FINA_LGN   = new App('financement_ligne');
				$APP_ENT        = new App('entite');
				$APP_RACHAT     = new App('rachat');
				$APP_RACHAT_LGN = new App('rachat_ligne');
				$APP_CLIENT     = new App('client');
				$APP_AGENT      = new App('agent');

				$NEW_ROW['N_ID']                    = $ROW['N_ID'];
				$NEW_ROW['codeRachat_ligne']        = $ROW['N_ID'];
				$NEW_ROW['commentaireRachat_ligne'] = $ROW['C_FINDEFINANCEMENTPREVUE_RACHATBIENS_IDX'] . ' ' . $ROW['C_ETAPESRESTANTESDELAREPRISEDUBIEN_RACHATBIENS_IDX'] . ' ' . $ROW['C_COMMENTAIRES_RACHATBIENS'];
				$NEW_ROW['valeurArgusRachat_ligne'] = $ROW['R_VALORISATIONDUBIENNB_RACHATBIENS'];
				// rachat
				$ARR_LF = $BASE_SYNC->t_rachat->findOne(['N_ID' => $ROW['N_TARGET_RACHAT_ID']]);
				$ARR_RA = $APP_RACHAT->findOne(['codeRachat' => $ARR_LF['C_NUMEROINTERNE_RACHAT']]);
				if (!empty($ARR_RA['idrachat'])) {
					$NEW_ROW['idrachat'] = (int)$ARR_RA['idrachat'];
				} else {
					break;
				}
				// Materiel
				$arr_b = $BASE_SYNC->t_bien->findOne(['N_ID' => $ROW['N_SOURCE_BIEN_ID']]);
				$test  = $APP_MATERIEL->query_one(['codeMateriel' => $arr_b['C_IDENTIFIANTFABRICANT_BIENIMMA']]);
				if (empty($test['idmateriel'])) {
					$NEW_ROW['idmateriel'] = (int)$test['idmateriel'];
				}

				$idrachat = $APP_RACHAT_LGN->create_update(['codeRachat_ligne' => $ROW['N_ID']], $NEW_ROW);

				break;
			case "t_cptcpta":  //   n'est pas le t_partie.C_CODE_ORG
				break;
				// clients :
				if (empty($ROW['C_NUMERO_CPTCPTA']) && empty($ROW['N_APRCOLLECCLIENTPRIO_CPTCPTA_ID'])) break;
				// t_cptcpta.C_NUMERO_CPTCPTA  = C0000322  => donnéees clients
				// C_INTITULE_CPTCPTA => nopmCLient
				// echo substr($ROW['C_NUMERO_CPTCPTA'],0,1).' '.$ROW['C_NUMERO_CPTCPTA'].' '.$ROW['C_INTITULE_CPTCPTA']."<br>";
				if (substr($ROW['C_NUMERO_CPTCPTA'], 0, 1) != '3' && substr($ROW['C_NUMERO_CPTCPTA'], 0, 1) != '9' && substr($ROW['C_NUMERO_CPTCPTA'], 0, 1) != 'C') break;
				$APP_CLI                            = new App('client');
				$vars_cli                           = ['N_ID' => $ROW['N_ID'], 'nomClient' => $ROW['C_INTITULE_CPTCPTA'], 'codeClient' => $ROW['C_NUMERO_CPTCPTA']];
				$vars_cli['dateCreationClient']     = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
				$vars_cli['dateModificationClient'] = date('Y-m-d', strtotime($ROW['T_UPDATE_DATE']));
				//

				break;
			//
			case "t_site": // site.
				// N_CORRESPA_LIEUFONCTIONEMPLACEMENT_ID
				$NEW_ROW  = [];
				$APP_SITE = new App('site');

				$ARR_ADR   = $BASE_SYNC->t_adrmedia->findOne(['N_ID' => $ROW['N_APRADRCOURRIER_ADRMEDIA_ID']]);
				$ARR_VILLE = $BASE_SYNC->t_subadmin->findOne(['N_ID' => $ARR_ADR['N_INDIQ_SUBADMIN_ID']]);
				$ARR_CP    = $BASE_SYNC->t_codepost->findOne(['N_ID' => $ARR_ADR['N_INDIQ_CODEPOST_ID']]);
				//
				$NEW_ROW['N_ID']           = $ROW['N_ID'];
				$NEW_ROW['codeSite']       = $ROW['N_ID'];
				$NEW_ROW['nomSite']        = empty($ROW['C_NOM_SITE']) ? $ARR_VILLE['C_NOM_SUBADMIN'] : $ROW['C_NOM_SITE'];
				$NEW_ROW['villeSite']      = $ARR_VILLE['C_NOM_SUBADMIN'];
				$NEW_ROW['codePostalSite'] = $ARR_CP['C_CODE_CODEPOST'];
				$NEW_ROW['cpSite']         = $ARR_CP['C_CODE_CODEPOST'];
				$NEW_ROW['adresseSite']    = $ARR_ADR['C_ADRESSEPOSTALE1_ADRMEDIA'];
				$NEW_ROW['adresse2Site']   = $ARR_ADR['C_ADRESSEPOSTALE2_ADRMEDIA'];
				$NEW_ROW['adresse3Site']   = $ARR_ADR['C_ADRESSEPOSTALE3_ADRMEDIA'] . ' ' . $ARR_ADR['C_ADRESSEPOSTALE4_ADRMEDIA'];;
				$NEW_ROW['faxSite']        = $ARR_ADR['C_FAX_ADRMEDIA'];
				$NEW_ROW['emailSite']      = $ARR_ADR['C_MAIL1_ADRMEDIA'];
				$NEW_ROW['email2Site']     = $ARR_ADR['C_MAIL2_ADRMEDIA'];
				$NEW_ROW['mobileSite']     = $ARR_ADR['C_MOBILE1_ADRMEDIA'];
				$NEW_ROW['mobile2Site']    = $ARR_ADR['C_MOBILE2_ADRMEDIA'];
				$NEW_ROW['telephoneSite']  = $ARR_ADR['C_TELEPHONE1_ADRMEDIA'];
				$NEW_ROW['telephone2Site'] = $ARR_ADR['C_TELEPHONE2_ADRMEDIA'];
				//
				$idsite = $APP_SITE->create_update(['N_ID' => $NEW_ROW['N_ID']], $NEW_ROW);
				break;

			/*case "t_infocontact":
				// N_CONCERNE_PERS_ID
				// N_DONNE_MANIERECONTACTER_ID
				// N_SONTCELLES_ORG_ID
				// B_PRIVILEGIE_CONTACTDSORG_INFOCONTACT_IDX

				$NEW_ROW = [ ];
				$APP_CON = new App('contact');
				$APP_CON_TY = new App('contact_type');
				$APP_CLI = new App('client');

				$ARR_PARTIE_ORG = $BASE_SYNC->t_partie->findOne([ 'N_ID' => $ROW['N_SONTCELLES_ORG_ID'] ]);
				$ARR_PARTIE_PERS     = $BASE_SYNC->t_partie->findOne([ 'N_ID' => $ROW['N_CONCERNE_PERS_ID'] ]);
				$ARR_LF         = $BASE_SYNC->t_lieufonction->findOne([ 'N_ID' => $ARR_PARTIE_PERS['N_CORRESPA_LIEUFONCTIONENTITEPHY_ID'] ]);

				$codeClient = $ARR_PARTIE_ORG['C_CODE_ORG'];
				$arrclient  = $APP_CLI->findOne([ 'codeClient' => $codeClient ]);
				if ( empty($arrclient['idclient']) ) {
					break;
				}

				$NEW_ROW['idclient'] = (int)$arrclient['idclient'];
				if(!empty($arrclient['idagent'] )) $NEW_ROW['idagent']  = (int)$arrclient['idagent'];
				//
				if(!empty($ROW['N_CONCERNE_PERS_ID'])){ // contact presonne
					// C_NOMDENAISSANCE_PERS => contact_type
					if(!empty($ARR_PARTIE_PERS['C_NOMDENAISSANCE_PERS'] )){
						$NEW_ROW_TY['codeContact_type'] = strtolower($ARR_PARTIE_PERS['C_NOMDENAISSANCE_PERS']);
						$NEW_ROW_TY['nomContact_type'] = $ARR_PARTIE_PERS['C_NOMDENAISSANCE_PERS'];
						$idcontact_type = $APP_CON_TY->create_update([ 'codeContact_type' => $NEW_ROW_TY['codeContact_type'] ] , $NEW_ROW_TY);
						$NEW_ROW['idcontact_type']      = (int)$idcontact_type;
					}

					$NEW_ROW['nomContact']          = $ARR_PARTIE_PERS['C_NOM_PERS'];
					$NEW_ROW['prenomContact']       = strtolower($ARR_PARTIE_PERS['C_PRENOM_PERS']);
					$NEW_ROW['codeContact']         = $codeContact = $ARR_PARTIE_PERS['N_ID'];

					$APP_CON->create_update([ 'codeContact' => $codeContact ] , $NEW_ROW);

				}

                // B_PRIVILEGIE_CONTACTDSORG_INFOCONTACT_IDX = oui => adresse principale en passant par t_manierecontacter
				if($ROW['B_PRIVILEGIE_CONTACTDSORG_INFOCONTACT_IDX']=='Oui' && empty($ROW['N_CONCERNE_PERS_ID'])){
					$ARRMANIERE = $BASE_SYNC->t_manierecontacter->findOne(['N_ID'=>$ROW['N_DONNE_MANIERECONTACTER_ID']]); // N_ID = $ROW['N_DONNE_MANIERECONTACTER_ID']
					$ARRADRMEDIA = $BASE_SYNC->t_adrmedia->findOne(['N_ID'=>$ARRMANIERE['N_UTILISE_ADRMEDIA_ID']]);// $ARRMANIERE['N_UTILISE_ADRMEDIA_ID']
					$ARR_VILLE  = $BASE_SYNC->t_subadmin->findOne([ 'N_ID' => $ARRADRMEDIA['N_INDIQ_SUBADMIN_ID'] ]);
					$ARR_CP     = $BASE_SYNC->t_codepost->findOne([ 'N_ID' => $ARRADRMEDIA['N_INDIQ_CODEPOST_ID'] ]);

					$NEW_ROW['villeClient']      = $ARR_VILLE['C_NOM_SUBADMIN'];
					$NEW_ROW['codePostalClient'] = $ARR_CP['C_CODE_CODEPOST'];
					$NEW_ROW['codePostalClient'] = $ARR_CP['C_CODE_CODEPOST'];
					$NEW_ROW['adresseClient']    = $ARRADRMEDIA['C_ADRESSEPOSTALE1_ADRMEDIA'];
					$NEW_ROW['adresse2Client']   = $ARRADRMEDIA['C_ADRESSEPOSTALE2_ADRMEDIA'];
					$NEW_ROW['adresse3Client']   = $ARRADRMEDIA['C_ADRESSEPOSTALE3_ADRMEDIA'] . ' ' . $ARRADRMEDIA['C_ADRESSEPOSTALE4_ADRMEDIA'];;
					$NEW_ROW['faxClient']        = $ARRADRMEDIA['C_FAX_ADRMEDIA'];
					$NEW_ROW['emailClient']      = $ARRADRMEDIA['C_MAIL1_ADRMEDIA'];
					$NEW_ROW['email2Client']     = $ARRADRMEDIA['C_MAIL2_ADRMEDIA'];
					$NEW_ROW['mobileClient']     = $ARRADRMEDIA['C_MOBILE1_ADRMEDIA'];
					$NEW_ROW['mobile2Client']    = $ARRADRMEDIA['C_MOBILE2_ADRMEDIA'];
					$NEW_ROW['telephoneClient']  = $ARRADRMEDIA['C_TELEPHONE1_ADRMEDIA'];
					$NEW_ROW['telephone2Client'] = $ARRADRMEDIA['C_TELEPHONE2_ADRMEDIA'];

					$APP_CLI->update([ 'idclient' => $NEW_ROW['idclient'] ] , $NEW_ROW);
				}
				break;*/

			case "t_cptacces": // AGENT

				// N_APPARTIENT_PARTIE_ID => t_partie.N_ID ;
				// C_IDENTIFIANT_CPTACCES => codeAgent => loginAgent ....

				$NEW_ROW    = [];
				$APP_AG     = new App('agent');
				$ARR_PARTIE = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_APPARTIENT_PARTIE_ID']]);

				$NEW_ROW['codeAgent']             = $ROW['C_IDENTIFIANT_CPTACCES'];
				$NEW_ROW['nomAgent']              = $ARR_PARTIE['C_NOM_PERS'];
				$NEW_ROW['prenomAgent']           = $ARR_PARTIE['C_PRENOM_PERS'];
				$NEW_ROW['dateCreationAgent']     = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['dateModificationAgent'] = date('Y-m-d', strtotime($ROW['T_UPDATE_DATE']));
				//
				$test = $APP_AG->query(['codeAgent' => $ROW['C_IDENTIFIANT_CPTACCES']]);

				//
				return;
				if ($test->count() == 0):
					// création
					$idagent            = (int)$APP_AG->getNext('idagent');
					$NEW_ROW['idagent'] = $idagent;
					$APP_AG->insert($NEW_ROW);
				else:
					$arr_c              = $test->getNext();
					$idagent            = (int)$arr_c['idagent'];
					$NEW_ROW['idagent'] = $idagent;
				endif;

				break;

			case "t_adrmedia":
				// t_adrmedia.N_ESTADRCOURRIER_SITE_ID = t_site.N_ID ; liaison site adresse.
				// C_ADRESSEPOSTALE1_ADRMEDIA 234 /
				// C_FAX_ADRMEDIA
				// C_MAIL1_ADRMEDIA C_MAIL2_ADRMEDIA
				// C_MOBILE1_ADRMEDIA 2
				// C_TELEPHONE1_ADRMEDIA 2
				// C_WEB_ADRMEDIA
				// N_ID

				if (empty($ROW['N_ESTADRCOURRIER_SITE_ID'])) {
					$test_site = $BASE_SYNC->t_site->findOne(['N_ID' => $ROW['N_ESTADRCOURRIER_SITE_ID']]);
					if (!empty($test_site['N_ID'])) {
						do_artis_rows('t_site', $test_site);
					}
				}

				// $ROW['N_ESTUTILISEPAR_MANIERECONTACTER_ID'];

				//
				$test_infoc = $BASE_SYNC->t_infocontact->findOne(['N_DONNE_MANIERECONTACTER_ID' => $ROW['N_ESTUTILISEPAR_MANIERECONTACTER_ID']]);
				if (!empty($test_infoc['N_ID'])) {
					do_artis_rows('t_infocontact', $test_infoc);
				}

				break;
			case "t_lieufonction":
				// t_lieufonction.C_CODE_LIEUFONCTION = t_cptcpta.C_NUMERO_CPTCPTA ||   t_lieufonction.N_CORRESPA_ORG_ID = t_partie.N_ID   =  t_cptcpta.N_COMPTABILISETRANSACTIONS_PARTIE_ID

				break;
			case "t_classif":
				$NEW_ROW          = [];
				$ARR_CATEG        = $BASE_SYNC->t_categ->findOne(['N_APPARTIENTA_CLASSIF_ID' => $ROW['N_ID']]);
				$ARR_CATEG_PROFIL = $BASE_SYNC->t_categprofilclt->findOne(['N_SOURCE_CATEGGEN_ID' => $ARR_CATEG['N_ID']]);
				$ARR_RELATION     = $BASE_SYNC->t_relationclientfournisseur->findOne(['N_APRPROFILCLIENT_PROFILCLT_ID' => $ARR_CATEG_PROFIL['N_TARGET_PROFILCLT_ID']]);
				//	vardump($ARR_CATEG);
				break;
			case "t_categprofilclt": // N_SOURCE_ORG = entite ; N_TARGET_ORG = client
				//  t_relationclientfournisseur.N_APRPROFILCLIENT_PROFILCLT_ID = t_categprofilclt.N_TARGET_PROFILCLT_ID
				//  t_categ.N_ID = t_categprofilclt.N_SOURCE_CATEGGEN_ID
				//  t_categ.N_APPARTIENTA_CLASSIF_ID = t_classif.N_ID
				$APP_CL = new App('client');
				$APP_EN = new App('entite');

				$NEW_ROW      = [];
				$ARR_RELATION = $BASE_SYNC->t_relationclientfournisseur->findOne(['N_APRPROFILCLIENT_PROFILCLT_ID' => $ROW['N_TARGET_PROFILCLT_ID']]);
				$ARR_ENTITE   = $BASE_SYNC->t_partie->findOne(['N_ID' => $ARR_RELATION['N_SOURCE_ORG_ID']]); // C_CODE_ORG
				$ARR_CLIENT   = $BASE_SYNC->t_partie->findOne(['N_ID' => $ARR_RELATION['N_TARGET_ORG_ID']]);  // C_CODE_ORG
				//
				$arrcl  = $APP_CL->findOne(['codeClient' => $ARR_CLIENT['C_CODE_ORG']]);
				$arrent = $APP_EN->findOne(['codeEntite' => $ARR_ENTITE['C_CODE_ORG']]);

				$idclient = (int)$arrcl['idclient'];
				$identite = (int)$arrent['identite'];

				if (empty($idclient) || empty($identite)) break;
				// echo $ROW['N_SOURCE_CATEGGEN_ID']."--";
				$ARR_CATEG = $BASE_SYNC->t_categ->findOne(['N_ID' => $ROW['N_SOURCE_CATEGGEN_ID']]);
				// echo $ARR_CATEG['C_NOM_CATEG']."--";
				$ARR_CLASSIF = $BASE_SYNC->t_classif->findone(['N_ID' => $ARR_CATEG['N_APPARTIENTA_CLASSIF_ID']]); // C_NOM_CLASSIF in 'ACTIVITE CLIENT','AGENCES','CATEGORIES CLIENTS','COMMERCIAL'
				// echo $ARR_CLASSIF['C_NOM_CLASSIF']."--";
				// $ARR            = $BASE_SYNC->t_categprofilclt->findone(['N_TARGET_PROFILCLT_ID'=>$ARR_RELATION['N_APRPROFILCLIENT_PROFILCLT_ID']]);

				$NOM  = empty($ARR_CATEG['C_NOM_CATEG']) ? $ARR_CATEG['C_ABREV_CATEGGEN'] : $ARR_CATEG['C_NOM_CATEG'];
				$CODE = $ARR_CATEG['C_ABREV_CATEGGEN'];
				// client => entirte
				$NEW_ROW['identite'] = $identite;

				switch ($ARR_CLASSIF['C_NOM_CLASSIF']):
					case "AGENCES":
						$APP_TMP             = new App('agence');
						$ARR_TMP             = $APP_TMP->findOne(['codeAgence' => $CODE]);
						$NEW_ROW['idagence'] = $APP_TMP->create_update(['codeAgence' => $CODE], ['nomAgence' => $NOM]);
						$APP_CL->update(['idclient' => $idclient], $NEW_ROW);
						break;
					case "ACTIVITE CLIENT": // client_type
						$APP_TMP                  = new App('client_type');
						$ARR_TMP                  = $APP_TMP->findOne(['codeClient_type' => $CODE]);
						$NEW_ROW['idclient_type'] = $APP_TMP->create_update(['codeClient_type' => $CODE], ['nomClient_type' => $NOM]);
						$APP_CL->update(['idclient' => $idclient], $NEW_ROW);
						break;
					case "CATEGORIES CLIENTS":
						$APP_TMP = new App('client_categorie');
						$ARR_TMP = $APP_TMP->findOne(['codeClient_categorie' => $CODE]);
						if (!empty($ARR_TMP['idclient_categorie'])) {
							$NEW_ROW['idclient_categorie'] = (int)$ARR_TMP['idclient_categorie'];
							$APP_CL->update(['idclient' => $idclient], $NEW_ROW);
						}
						break;
					case "COMMERCIAL":
						$APP_TMP = new App('agent');
						switch ($CODE) :
							case 'NR':  // NICE REPROMEL  => new
								$codeAgent   = 'RNICE';
								$nomAgent    = 'REPROMEL';
								$prenomAgent = 'NICE';
								break;
							case 'MT':  // MURET Thierry  => new
								$codeAgent   = 'TMURET';
								$nomAgent    = 'MURET';
								$prenomAgent = 'Thierry';
								break;
							case 'DT':  // DURET Thomas  => new
								$codeAgent   = 'TDURET';
								$nomAgent    = 'DURET';
								$prenomAgent = 'Thomas';
								break;
							case 'DI':  // DELLERIE Isabelle => new
								$codeAgent   = 'IDELLERIE';
								$nomAgent    = 'DELLERIE';
								$prenomAgent = 'Isabelle';
								break;
							case 'DG':  // DEMOUX Guillaume => new
								$codeAgent   = 'GDEMOUX';
								$nomAgent    = 'DEMOUX';
								$prenomAgent = 'Guillaume';
								break;
							///////////////////////////////////////////////////////////////////////////
							case 'TT':  // THEYSSIER Thierry
								$codeAgent = 'TTHEYSSIER';
								break;
							case 'TB':  // BEURIER THIERRY
								$codeAgent = 'TBEURIER';
								break;
							case 'KP':  // PLANUS KARINE
								$codeAgent = 'KPLANUS';
								break;

							case 'FC':  // CALDAROLA FREDERIQUE
								$codeAgent = 'FCALDAROLA';
								break;
							case 'MD':  // MAROSELLI Daniele
								$codeAgent = 'DMAROSELLI';
								break;
							case 'JPF':  // FRONTONI JEAN-PIERRE
								$codeAgent = 'JFRONTONI';
								break;
							case 'HP':  // HOLME Peter
								$codeAgent = 'PHOLME';
								break;
							case 'HJ':  // HEBRARD Joel
								$codeAgent = 'JHEBRARD';
								break;
							case 'GS':  // GUERPILLON STEPHANE
								$codeAgent = 'SGUERPILLON';
								break;
							case 'FDRAHI':  // DRAHI FIONA
								$codeAgent = 'FDRAHI';
								break;
							case 'FA':  // FALLETA Antoine
								$codeAgent = 'AFALLETTA';
								break;
							case 'DS':  // DI BARI Sophie
								$codeAgent = 'SDIBARI';
								break;

						endswitch;

						if (!empty($codeAgent)) {
							$ARR_AGENT = $APP_TMP->findOne(['codeAgent' => $codeAgent]);
							if (!empty($ARR_AGENT['idagent'])) {
								$NEW_ROW['idagent'] = (int)$ARR_AGENT['idagent'];
								$APP_CL->update(['idclient' => $idclient], $NEW_ROW);
							} else {
								$NEW_ROW['idagent'] = $APP_TMP->create_update(['codeAgent' => $codeAgent], ['nomAgent' => $nomAgent, 'prenomAgent' => $prenomAgent]);
								$APP_CL->update(['idclient' => $idclient], $NEW_ROW);
							}
						}
						break;

				endswitch;

				break;
			case "t_partie": // clients , agents , org  ... tout le monde !
				// t_partie.N_ID  =  t_cptcpta.N_COMPTABILISETRANSACTIONS_PARTIE_ID  avec B_PRINCIPAL_CPTCOMPTABILITEAUXILIAIRE_IDX = 'Oui'  monitor T_COMPUTED_UPDATE_DATE
				// t_partie.C_CODE_ORG = C0000322  => donnéees clients

				$NEW_ROW = [];
				if ($ROW['C_TYPE_IDX'] == 'com.artis.business.parties.OrgInt'):
					$APP_ENT = new App('entite');

					$NEW_ROW['codeEntite'] = $ROW['C_CODE_ORG'];
					$NEW_ROW['nomEntite']  = $ROW['C_RAISONSOCIALE_ORG'];

					$arr_test = $APP_ENT->findOne(['codeEntite' => $ROW['C_CODE_ORG']]);

					if (empty($arr_test['identite'])):
						$identite            = (int)$APP->getNext('identite');
						$NEW_ROW['identite'] = $identite;

						$APP_ENT->insert($NEW_ROW);
					else:
						$identite            = (int)$arr_test['identite'];
						$NEW_ROW['identite'] = $identite;
						//
						$APP_ENT->update(['identite' => $identite], $NEW_ROW);
					endif;

				endif;

				if ($ROW['C_TYPE_IDX'] == 'com.artis.business.parties.OrgExterne'):  // N_INFO_ORG_USERAREATYPE_ID VALUE = t_categ.N_ID (C_NOM_CATEG VALUE) => categ // t_categ.C_ABREV_CATEGGEN

					// break;

					$start = substr($ROW['C_CODE_ORG'], 0, 1);

					if (($start != '3') && ($start != '9') && ($start != '1') && ($start != 'C')) {
						break;
					}

					$APP_CLI    = new App('client');
					$APP_CLICAT = new App('client_categorie');

					$NEW_ROW = [];

					$arr_s    = $BASE_SYNC->t_classiforga->findOne(['N_SOURCE_ORG_ID' => $ROW['N_ID']]); // => N_TARGET_CLASSIF_ID
					$arr_ss   = $BASE_SYNC->t_classif->findOne(['N_ID' => $arr_s['N_TARGET_CLASSIF_ID']]); // => N_TARGET_CLASSIF_ID
					$arr_test = $APP_CLI->findOne(['codeClient' => $ROW['C_CODE_ORG']]);

					if (!empty($arr_s['C_NOM_CATEG_VALUE'])): // N_INFO_ORG_USERAREATYPE_ID VALUE

						$NEW_ROW['nomClient_categorie']  = $arr_s['C_NOM_CATEG_VALUE'];
						$NEW_ROW['codeClient_categorie'] = $arr_s['C_ABREV_CATEGGEN'];
						//
						$NEW_ROW['idclient_categorie'] = (int)$APP_CLICAT->create_update(['codeClient_categorie' => $arr_s['C_ABREV_CATEGGEN']], $NEW_ROW);
					endif;

					if (empty($arr_test['idclient'])) :// client artis only
						//
						$NEW_ROW['nomClient']  = $ROW['C_RAISONSOCIALE_ORG'];
						$NEW_ROW['codeClient'] = $ROW['C_CODE_ORG'];

						$NEW_ROW['idclient'] = (int)$APP_CLI->create_update(['codeClient' => $ROW['C_CODE_ORG']], $NEW_ROW);
					// echo $msg = $ROW['C_RAISONSOCIALE_ORG'] . ' ' . $ROW['C_CODE_ORG'] . ' <bold>' . $arr_s['C_NOM_CATEG_VALUE'] . '</bold>-' . $ROW['N_INFO_ORG_USERAREATYPE_ID'] . '-' . $arr_test['nomClient'] . '<br>';
					else:
						$APP_CLI->update(['idclient' => (int)$arr_test['idclient']], ['idclient_categorie' => (int)$NEW_ROW['idclient_categorie']]);
					endif;

				endif;
				break;

			case "t_infocontact":
				$NEW_ROW = [];

				$APP_CLI = new App('client');

				if ($ROW['B_PRIVILEGIE_CONTACTDSORG_INFOCONTACT_IDX'] == 'Oui' && !empty($ROW['N_SONTCELLES_ORG_ID'])) {  // adrsse societe + mail + ...

					$arr_m   = $BASE_SYNC->t_manierecontacter->findOne(['N_FIGUREDS_INFOCONTACT_ID' => $ROW['N_ID']]);
					$ARR_ADR = $BASE_SYNC->t_adrmedia->findOne(['N_ID' => $arr_m['N_UTILISE_ADRMEDIA_ID']]);

					$ARR_SITE  = $BASE_SYNC->t_site->findOne(['N_ID' => $ARR_ADR['N_ESTADRCOURRIER_SITE_ID']]);
					$ARR_VILLE = $BASE_SYNC->t_subadmin->findOne(['N_ID' => $ARR_ADR['N_INDIQ_SUBADMIN_ID']]);
					$ARR_CP    = $BASE_SYNC->t_codepost->findOne(['N_ID' => $ARR_ADR['N_INDIQ_CODEPOST_ID']]);
					//
					$NEW_ROW['villeClient']    = $ARR_VILLE['C_NOM_SUBADMIN'];
					$NEW_ROW['cpClient']       = $ARR_CP['C_CODE_CODEPOST'];
					$NEW_ROW['adresseClient']  = $ARR_ADR['C_ADRESSEPOSTALE1_ADRMEDIA'];
					$NEW_ROW['adresse2Client'] = $ARR_ADR['C_ADRESSEPOSTALE2_ADRMEDIA'];
					$NEW_ROW['adresse3Client'] = $ARR_ADR['C_ADRESSEPOSTALE3_ADRMEDIA'] . ' ' . $ARR_ADR['C_ADRESSEPOSTALE4_ADRMEDIA'];;
					$NEW_ROW['faxClient']        = $ARR_ADR['C_FAX_ADRMEDIA'];
					$NEW_ROW['emailClient']      = $ARR_ADR['C_MAIL1_ADRMEDIA'];
					$NEW_ROW['email2Client']     = $ARR_ADR['C_MAIL2_ADRMEDIA'];
					$NEW_ROW['mobileClient']     = $ARR_ADR['C_MOBILE1_ADRMEDIA'];
					$NEW_ROW['mobile2Client']    = $ARR_ADR['C_MOBILE2_ADRMEDIA'];
					$NEW_ROW['telephoneClient']  = $ARR_ADR['C_TELEPHONE1_ADRMEDIA'];
					$NEW_ROW['telephone2Client'] = $ARR_ADR['C_TELEPHONE2_ADRMEDIA'];
					// client
					$arr_partie = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_SONTCELLES_ORG_ID']]); // => N_TARGET_CLASSIF_ID
					$codeClient = $arr_partie['C_CODE_ORG'];
					$arr_cli    = $APP_CLI->findOne(['codeClient' => $codeClient]);
					if (!empty($arr_cli['idclient'])) {
						// vardump($NEW_ROW);
						$APP_CLI->update(['idclient' => (int)$arr_cli['idclient']], $NEW_ROW);
					}
				}
				break;
			case "t_servclt": // OK VALID  // ligne de contrat, finalement, non // t_couvbienparserv
				$NEW_ROW = [];
				do_artis_index('t_moduleservclt', 'N_ID');
				do_artis_index('t_moduleservclt', 'N_ESTCOUVERTPAR_MODULESERVCLT_ID');

				$APP_CTR         = new App('contrat');
				$APP_CTRLGN      = new App('contrat_ligne');
				$t_moduleservclt = $BASE_SYNC->t_moduleservclt;
				$t_solservclt    = $BASE_SYNC->t_solservclt;

				// 'C_CODE_SERVCLT' , C_COMMENTAIRES_SERVCLT N_ESTCOUVERTPAR_MODULESERVCLT_ID

				$arr_moduleservclt = $t_moduleservclt->findOne(['N_ID' => $ROW['N_ESTCOUVERTPAR_MODULESERVCLT_ID'], 'D_DATEDEFIN_MODULESERVCLT' => ['$gte' => date('Y-m-d')]]);
				$arr_servclt       = $t_solservclt->findOne(['N_ID' => $arr_moduleservclt['N_ESTCOMPRISDS_SOLSERVCLT_ID']]);
				$codeContrat       = $arr_servclt['C_CODE_SOLSERVCLT'];

				$arrcontrat = $APP_CTR->findOne(['codeContrat' => $codeContrat]);
				$idcontrat  = (int)$arrcontrat['idcontrat'];

				if (empty($idcontrat)) {
					break;
				}

				$NEW_ROW['idcontrat']              = $idcontrat;
				$NEW_ROW['codeContrat_ligne']      = $ROW['C_CODE_SERVCLT'];
				$NEW_ROW['nomContrat_ligne']       = $ROW['C_COMMENTAIRES_SERVCLT'];
				$NEW_ROW['dateDebutContrat_ligne'] = $arr_moduleservclt['D_DATEDEDEBUT_MODULESERVCLT'];
				$NEW_ROW['dateFinContrat_ligne']   = $arr_moduleservclt['D_DATEDEFIN_MODULESERVCLT'];

				$arrcontrat_ligne = $APP_CTRLGN->findOne(['idcontrat' => $idcontrat, 'codeContrat_ligne' => $ROW['C_CODE_SERVCLT']]);

				if (empty($arrcontrat_ligne['idcontrat_ligne'])):
					$idcontrat_ligne            = (int)$APP->getNext('idcontrat_ligne');
					$NEW_ROW['idcontrat_ligne'] = $idcontrat_ligne;

					$APP_CTRLGN->insert($NEW_ROW);
				else:
					$idcontrat_ligne            = (int)$arrcontrat_ligne['idcontrat_ligne'];
					$NEW_ROW['idcontrat_ligne'] = $idcontrat_ligne;
					//
					$APP_CTRLGN->update(['idcontrat_ligne' => $idcontrat_ligne], $NEW_ROW);
				endif;

				break;
			case "t_statutsolservclt":
				$APP_STCTR = new App('contrat_statut');
				$APP_CTR   = new App('contrat');
				// statut  N_UTILISE_STATUTSOLSERVCLT_ID
				// $ARR_STATUT = $BASE_SYNC->t_statutsolservclt->findOne(['N_ID'=>$ROW['N_UTILISE_STATUTSOLSERVCLT_ID']]);

				$NEW_ROW['codeContrat_statut'] = niceUrl($ROW['C_STATUTVAL_STATUTSOLSERVCLT_IDX']);
				$NEW_ROW['nomContrat_statut']  = $ROW['C_STATUTVAL_STATUTSOLSERVCLT_IDX'];

				$idcontrat_statut = $APP_STCTR->create_update(['codeContrat_statut' => $NEW_ROW['codeContrat_statut']], $NEW_ROW);
				// mise à jour contrat ss_clt
				$ARR_SERV = $BASE_SYNC->t_solservclt->findOne(['N_ID' => $ROW['N_SEREFEREA_SOLSERVCLT_ID']]);
				if (empty($ARR_SERV['N_ID'])) break;
				$codeContrat = $ARR_SERV['C_CODE_SOLSERVCLT'];
				$ARR_CTR     = $APP_CTR->findOne(['codeContrat' => $codeContrat]);
				if (!empty($ARR_CTR['idcontrat'])) {
					$APP_CTR->update(['idcontrat' => (int)$ARR_CTR['idcontrat']], ['idcontrat_statut' => $idcontrat_statut]);
				}

				break;
			case "t_solservclt":  // OK VALID // contrat => trouver client, lieu, materiel ... // => contrat n'a pas de site.
				// test date fin module service client
				$ARRMODULE = $BASE_SYNC->t_moduleservclt->findOne(['N_ESTCOMPRISDS_SOLSERVCLT_ID' => $ROW['N_ID'], 'D_DATEDEFIN_MODULESERVCLT' => ['$gte' => date('Y-m-d')]]); // donnera la fin du contrat
				if (empty($ARRMODULE['N_ID'])) break;
				//
				$NEW_ROW = [];
				// N_ESTSIGNEEPAR_COLLABO_ID
				// N_ESTCONCLUECLIENT_LIEUFONCTION_ID
				// t_solservclt => wazaa
				// contrat // D_DATEDEDEBUT_SOLSERVCLT // D_DATEDEFININITIALE_SOLSERVCLT // R_DUREEINITIALENB_SOLSERVCLT // C_LIBCOURT_SOLSERVCLT nom  // C_COMMENTAIRES_SOLSERVCLT commentaires
				$APP_CTR    = new App('contrat');
				$APP_CTR_TY = new App('contrat_type');
				$APP_CLI    = new App('client');
				$APP_MAT    = new App('materiel');
				$APP_SITE   = new App('site');
				$APP_AGENT  = new App('agent');

				// test statut service client
				$ARRSTATMODULE = $BASE_SYNC->t_statutsolservclt->findOne(['N_SEREFEREA_SOLSERVCLT_ID' => $ROW['N_ID']]); // donnera la fin du contrat
				if ($ARRSTATMODULE['C_STATUTVAL_STATUTSOLSERVCLT_IDX'] == 'Fermée') break;
				// Ne pas prendre contrats passés
				// if (strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']) < time()) break;

				//	$test = $APP_CTR->find([ 'codeContrat' => $ROW['C_CODE_SOLSERVCLT'] ]);
				// T_lieufonction
				//
				$NEW_ROW['dateDebutContrat']   = date('Y-m-d', strtotime($ROW['D_DATEDEDEBUT_SOLSERVCLT']));
				$NEW_ROW['dateFinContrat']     = date('Y-m-d', strtotime($ARRMODULE['D_DATEDEFIN_MODULESERVCLT']));
				$NEW_ROW['dureeContrat']       = $ROW['R_DUREEINITIALENB_SOLSERVCLT'];
				$NEW_ROW['nomContrat']         = $ROW['C_CODE_SOLSERVCLT'] . ' ' . $ROW['C_LIBCOURT_SOLSERVCLT'];
				$NEW_ROW['codeContrat']        = $ROW['C_CODE_SOLSERVCLT'];
				$NEW_ROW['commentaireContrat'] = $ROW['C_COMMENTAIRES_SOLSERVCLT'];

				// idagent
				$test_sa = $BASE_SYNC->t_partie->findOne(['N_ID' => $ROW['N_ESTSIGNEEPAR_COLLABO_ID']]);
				if (!empty($test_sa['N_ID'])) {
					$codeAgent = $test_sa['C_MATRICULE_COLLABO'];
					if (!empty($codeAgent)) {
						$ARR_AG = $APP_AGENT->findOne(['codeAgent' => $codeAgent]);
						if (!empty($ARR_AG['idagent'])) {
							$NEW_ROW['idagent'] = $idagent = (int)$ARR_AG['idagent'];
						}
					}
				}
				// statut  N_UTILISE_STATUTSOLSERVCLT_ID
				$ARR_STATUT = $BASE_SYNC->t_statutsolservclt->findOne(['N_ID' => $ROW['N_UTILISE_STATUTSOLSERVCLT_ID']]);

				// Type
				if ($ROW['C_LIBCOURT_SOLSERVCLT'] != 'false' && !empty($ROW['C_LIBCOURT_SOLSERVCLT'])) {
					if (empty($ROW['C_LIBCOURT_SOLSERVCLT'])) {
						$ROW['C_LIBCOURT_SOLSERVCLT'] = 'sans-contrat';
					}
					$NEW_ROW['idcontrat_type'] = (int)$APP_CTR_TY->create_update(['codeContrat_type' => niceUrl($ROW['C_LIBCOURT_SOLSERVCLT'])], ['nomContrat_type' => $ROW['C_LIBCOURT_SOLSERVCLT']]);
				}

				// adresse : t_site.N_APRADRCOURRIER_ADRMEDIA_ID = t_adrmedia.N_ID
				if ($ROW['N_ESTCONCLUECLIENT_LIEUFONCTION_ID']) {
					$arr_lf        = $BASE_SYNC->t_lieufonction->findOne(['N_ID' => $ROW['N_ESTCONCLUECLIENT_LIEUFONCTION_ID']]); // => t_lieufonction
					$t_partieN_ID  = $arr_lf['N_CORRESPA_ORG_ID'];   // t_partie.N_ID
					$t_siteN_ID    = $arr_lf['N_CORRESPA_SITE_ID'];
					$t_partie_code = $arr_lf['C_CODE_LIEUFONCTION'];
					$arr_partie    = $BASE_SYNC->t_partie->findOne(['N_ID' => $t_partieN_ID]); // => t_lieufonction
					//
					$ARR_CLI = $APP_CLI->query_one(['codeClient' => $t_partie_code]);
					if (empty($ARR_CLI['idclient'])) {
						return ['error', 'Pas de client pour solution de service. code client : ' . $t_partie_code];
						break;
					}
					$idclient                       = (int)$ARR_CLI['idclient'];
					$NEW_ROW['idclient']            = $idclient;
					$NEW_ROW['nomContrat']          = $ROW['C_CODE_SOLSERVCLT'] . ' ' . strtoupper(substr(str_replace(' ', '', $ARR_CLI['nomClient']), 0, 12)) . ' ' . $ROW['C_LIBCOURT_SOLSERVCLT'];
					$NEW_ROW['aa_partie_n_id']      = $t_partieN_ID;
					$NEW_ROW['aa_partie_code']      = $t_partie_code;
					$NEW_ROW['aa_partie_site_n_id'] = $t_siteN_ID;

					$APP_CTR->create_update(['codeContrat' => $NEW_ROW['codeContrat']], $NEW_ROW);
				}
				// agent dans client ?
				if (!empty($idclient) && !empty($idagent)) {
					$APP_CLI->update(['idclient' => $idclient], ['idagent' => $idagent]);
				}
				// idcontrat dans table materiel .... A faire ou pas ?

				break;
			case "t_couvbienparserv": // OK VALID   => rajouter idclient dans materiel => contrat_ligne !!! acti oui / non
				// if($ROW['B_ACTIF_COUVBIENPARSERV_IDX']=='Non') return (['error','no']);
				$NEW_ROW = [];
				if ($ROW['N_SOURCE_BIEN_ID'] == 64886) {
					// echo "64886 <br>";
				}
				// do_artis_index('t_couvbienparserv','N_ID');

				$APP_CTR    = new App('contrat');
				$APP_CTRLGN = new App('contrat_ligne');
				$APP_CLI    = new App('client');
				$APP_MAT    = new App('materiel');
				$APP_FIN    = new App('financement');

				//$ARR_COUV = $BASE_SYNC->t_couvbienparserv->findOne(['N_SOURCE_BIEN_ID' => $ROW['N_ID'],'B_ACTIF_COUVBIENPARSERV_IDX'=>'Oui']);
				//if (!empty($ARR_COUV['N_ID'])) {
				$ARR_SERV = $BASE_SYNC->t_servclt->findOne(['N_ID' => $ROW['N_TARGET_SERVCLT_ID']]);
				// couvert par module ?
				if (!empty($ARR_SERV['N_ESTCOUVERTPAR_MODULESERVCLT_ID'])) {
					//echo "1";
					$ARR_MODULE = $BASE_SYNC->t_moduleservclt->findOne(['N_ID' => $ARR_SERV['N_ESTCOUVERTPAR_MODULESERVCLT_ID'], 'D_DATEDEFIN_MODULESERVCLT' => ['$gte' => date('Y-m-d')]]);
					if (!empty($ARR_MODULE['N_ID'])) {

						$RS_SOLSERVCLT  = $BASE_SYNC->t_solservclt->find(['N_ID' => $ARR_MODULE['N_ESTCOMPRISDS_SOLSERVCLT_ID']])->sort(['D_DATEDEFININITIALE_SOLSERVCLT' => -1]); // , 'D_DATEDEFININITIALE_SOLSERVCLT' => ['$gte' => date('Y-m-d')] D_DATEDEFININITIALE_SOLSERVCLT
						$ARR_SOLSERVCLT = $RS_SOLSERVCLT->getNext();
						if (!empty($ARR_SOLSERVCLT['N_ID'])) {
							//echo '4 ';
							$ARR_B   = $BASE_SYNC->t_bien->findOne(['N_ID' => $ROW['N_SOURCE_BIEN_ID']]);
							$ARR_MAT = $APP_MAT->findOne(['codeMateriel' => $ARR_B['C_IDENTIFIANTFABRICANT_BIENIMMA']]);
							$ARR_CON = $APP_CTR->findOne(['codeContrat' => $ARR_SOLSERVCLT['C_CODE_SOLSERVCLT']]);
							if (!empty($ARR_MAT['idmateriel']) && !empty($ARR_CON['idcontrat'])) {
								//echo "5 ";
								$NEW_ROW['idcontrat'] = (int)$ARR_CON['idcontrat'];
								if (!empty($ARR_CON['idclient'])) $NEW_ROW['idclient'] = (int)$ARR_CON['idclient'];
								if (!empty($ARR_MAT['idfinancement'])) {
									$ARR_FIN = $APP_FIN->findOne(['idfinancement' => (int)$ARR_MAT['idfinancement']]);
									if (!empty($ARR_FIN['idleaser'])) {
										$NEW_ROW['idleaser'] = (int)$ARR_FIN['idleaser'];
										$APP_CTR->update(['idcontrat' => (int)$ARR_CON['idcontrat']], ['idleaser' => (int)$ARR_FIN['idleaser']]);
										// vardump($NEW_ROW);vardump($ARR_MAT);exit;
									}
								}
								// contrat et client dans le materiel
								$APP_MAT->update(['idmateriel' => (int)$ARR_MAT['idmateriel']], $NEW_ROW);

								//
								$arrcontrat_ligne = $APP_CTRLGN->findOne(['idcontrat' => (int)$NEW_ROW['idcontrat'], 'codeContrat_ligne' => $ARR_SERV['C_CODE_SERVCLT']]);
								if (!empty($arrcontrat_ligne['idcontrat_ligne'])) {

									// $APP_CTRLGN->update(['idcontrat_ligne' => (int)$arrcontrat_ligne['idcontrat_ligne']], ['estCouvertContrat_ligne' => $ROW['B_ACTIF_COUVBIENPARSERV_IDX']]);
								}

							}
						}
					}
				} else {
					// t_bienpossedeparorg
					// $ARR_POSS = $BASE_SYNC->t_bienpossedeparorg->findOne(['N_ID' => $ROW['N_SOURCE_BIEN_ID']]);
				}
				//}
				break;
			case "t_biengereparorg":

				break;
			case "t_classiforg": // agent, categorie,type, agence : classification et appartenance des clients ( N_SOURCE_ORG_ID )
				$APP_CLT     = new App('client_type');
				$APP_CLT_CAT = new App('client_categorie');
				$APP_AGENT   = new App('agent');

				$NEW_ROW = [];
				// CODE CLASSIF
				$arr_classif = $BASE_SYNC->t_classif->findOne(['N_ID' => $ROW['N_TARGET_CLASSIF_ID']]);
				switch ($arr_classif['C_IDENTIFIANTTECHNIQUE_CLASSIF']):
					case "COMMERCIAL":

						break;
					case "AGENCES":

						break;
					case "ACTIVITE_CLIENT":

						break;
					case "CATEGORIE_CLIENT":

						break;
				endswitch;
				break;
			case "t_categ":
				$NEW_ROW = [];

				$APP_CLT     = new App('client_type');
				$APP_CLT_CAT = new App('client_categorie');

				$arr_classif = $BASE_SYNC->t_classif->findOne(['N_ID' => $ROW['N_APPARTIENTA_CLASSIF_ID']]);

				if ($arr_classif['C_IDENTIFIANTTECHNIQUE_CLASSIF'] == 'ACTIVITE_CLIENT') {// activite = client_type
					//
					$NEW_ROW['codeClient_type'] = $ROW['C_ABREV_CATEGGEN'];
					$NEW_ROW['nomClient_type']  = $ROW['C_NOM_CATEG'];

					$ARR_TEST = $APP_CLT->findOne(['codeClient_type' => $ROW['C_ABREV_CATEGGEN']]);

					if (empty($ARR_TEST['idclient_type'])):
						$idclient_type            = (int)$APP->getNext('idclient_type');
						$NEW_ROW['idclient_type'] = $idclient_type;
						$APP_CLT->insert($NEW_ROW);
					else:
						$NEW_ROW['idclient_type'] = $idclient_type = (int)$ARR_TEST['idclient_type'];
						//
						$APP_CLT->update(['idclient_type' => $idclient_type], $NEW_ROW);
					endif;

				}

				if ($arr_classif['C_IDENTIFIANTTECHNIQUE_CLASSIF'] == 'CATEGORIE_CLIENT') {//client_categorie
					//

					$NEW_ROW['codeClient_categorie'] = $ROW['C_ABREV_CATEGGEN'];
					$NEW_ROW['nomClient_categorie']  = $ROW['C_NOM_CATEG'];

					$ARR_TEST = $APP_CLT_CAT->findOne(['codeClient_categorie' => $ROW['C_ABREV_CATEGGEN']]);

					if (empty($ARR_TEST['idclient_categorie'])):
						$idclient_categorie            = (int)$APP->getNext('idclient_categorie');
						$NEW_ROW['idclient_categorie'] = $idclient_categorie;
						$APP_CLT_CAT->insert($NEW_ROW);
					else:
						$NEW_ROW['idclient_categorie'] = $idclient_categorie = (int)$ARR_TEST['idclient_categorie'];
						//
						$APP_CLT_CAT->update(['idclient_categorie' => $idclient_categorie], $NEW_ROW);
					endif;
				}
				break;
			case"t_valori": // relevés compteurs => par date pout table materiel_compteur  // => VMM !!!
				// R_VAL_VALORI
				// N_DONNEMES_GRANDEUR_ID => lien grandeur pour numserie materiel
				// C_ORIGINEDELAVAL_VALORI_IDX  => commentaire
				// C_COMMENTAIRESVALID_VALORI   => commentaires
				// T_CREATE_DATE
				// N_ID
//vardump($ROW);

				$NEW_ROW = [];

				$APP_MAT     = new App('materiel');
				$APP_MAT_CPT = new App('materiel_compteur');
				$APP_MAT_VOL = new App('materiel_volume');

				//
				$NEW_ROW['N_ID']                          = $ROW['N_ID'];
				$NEW_ROW['valeurMateriel_compteur']       = (int)$ROW['R_VAL_VALORI'];
				$NEW_ROW['commentaireMateriel_compteur']  = $ROW['C_COMMENTAIRESVALID_VALORI'];
				$NEW_ROW['dateCreationMateriel_compteur'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['dateMateriel_compteur']         = date('Y-m-d', strtotime($ROW['T_RELEVEELE_VALORI']));

				// MATOS +  COMPTEUR (NB / COUL )
				$arr_grd  = $BASE_SYNC->t_grandeur->findOne(['N_ID' => $ROW['N_DONNEMES_GRANDEUR_ID']]);
				$arr_bien = $BASE_SYNC->t_bien->findOne(['N_ID' => $arr_grd['N_DEPEND_BIEN_ID']]);
				// => num, code du bien, sync idae
				$numserie = $arr_bien['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				//
				$arr_mat = $APP_MAT->findOne(['codeMateriel' => $numserie]);
				if (empty($arr_mat['idmateriel'])) {
					break;
				}
				// vardump($arr_mat);
				$NEW_ROW['idmateriel']            = (int)$arr_mat['idmateriel'];
				$NEW_ROW['nomMateriel_compteur']  = $arr_grd['C_NOM_GRANDEUR'];
				$NEW_ROW['codeMateriel_compteur'] = $arr_grd['C_ABREVIATION_GRANDEUR'];

				$arr_mat_vpt = $APP_MAT_CPT->findOne(['N_ID' => $ROW['N_ID']]);
				if (empty($arr_mat_vpt['N_ID'])) {
					$APP_MAT_CPT->insert(['N_ID' => $ROW['N_ID']] + $NEW_ROW);
					break;
				}
				// break;
				// $APP_MAT_CPT->create_update(['N_ID' => $ROW['N_ID']], $NEW_ROW);

				// si dernier compteur => dans materiel champ : compteur NB + compteur Coul
				$rsm = $APP_MAT_CPT->find(['idmateriel' => $NEW_ROW['idmateriel']])->sort(['dateMateriel_compteur' => -1]);
				$arm = $rsm->getNext();
				//vardump($arr_grd['C_ABREVIATION_GRANDEUR']);

				switch ($arr_grd['C_ABREVIATION_GRANDEUR']):
					case "NB" :
						$field = "compteurNBMateriel";
						break;
					case "COUL" :
						$field = "compteurCouleurMateriel";
						break;
					case "CPTTOT" :
						$field = "compteurTotalMateriel";
						break;
					case "BK" :
						$field = "compteurBKMateriel";
						break;
					case "01" :
						$field = "compteurNBMateriel";
						break;
					case "02" :
						$field = "compteurCouleurMateriel";
						break;
					default :
						$field = "compteurNBMateriel";
						break;
				endswitch;
				if (empty($field)) break;
				//
				//echo $field.' '.$arm['valeurMateriel_compteur'] .'!='. $ROW['R_VAL_VALORI'];
				//$APP_MAT->find(['idmateriel'=>$NEW_ROW['idmateriel']]);
				if ($arm['valeurMateriel_compteur'] != $arr_mat[$field]) $APP_MAT->create_update(['idmateriel' => $NEW_ROW['idmateriel']], [$field => (int)$ROW['R_VAL_VALORI']]);
				break;
			case "t_volumemoyenmensuel": // => t_grandeur => t_bien

				break;
				$NEW_ROW = [];

				$APP_MAT     = new App('materiel');
				$APP_MAT_GRD = new App('materiel_compteur');
				$APP_MAT_VOL = new App('materiel_volume');

				$arr_1    = $BASE_SYNC->t_grandeur->findOne(['N_ID' => $ROW['N_PERMETESTIMER_GRANDEUR_ID']]);
				$arr_bien = $BASE_SYNC->t_bien->findOne(['N_ID' => $arr_1['N_DEPEND_BIEN_ID']]);
				// => num, code du bien, sync idae
				$numserie = $arr_bien['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				//
				$arr_mat = $APP_MAT->findOne(['codeMateriel' => $numserie]);
				if (empty($arr_mat['idmateriel'])) {
					break;
				}

				$NEW_ROW['N_ID']                        = $ROW['N_ID'];
				$NEW_ROW['idmateriel']                  = $idmateriel = (int)$arr_mat['idmateriel'];
				$NEW_ROW['dateCreationMateriel_volume'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
				$NEW_ROW['dateMateriel_volume']         = date('Y-m-d', strtotime($ROW['D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL']));
				$NEW_ROW['nomMateriel_volume']          = $ROW['C_ORIGINEVALESTIMEE_VOLUMEMOYENMENSUEL_IDX'] . ' ' . $arr_1['C_ABREVIATION_GRANDEUR'] . ' ' . fonctionsProduction::mois_short_Date_fr($NEW_ROW['dateMateriel_volume']);
				$NEW_ROW['quantiteMateriel_volume']     = (int)$ROW['R_VALVMM_VOLUMEMOYENMENSUEL'];
				$NEW_ROW['valeurMateriel_volume']       = (int)$ROW['R_VALVMM_VOLUMEMOYENMENSUEL'];
				$NEW_ROW['codeMateriel_volume']         = $arr_1['C_ABREVIATION_GRANDEUR'];

				// cherchons la vmm
				$test_grd = $APP_MAT_VOL->findOne(['N_ID' => $ROW['N_ID']]);
				if (empty($test_grd['idmateriel'])) {
					$NEW_ROW['idmateriel_volume'] = (int)$APP->getNext('idmateriel_volume');
					$APP_MAT_VOL->insert($NEW_ROW);
				} else {
					$APP_MAT_VOL->update(['N_ID' => $ROW['N_ID'], 'idmateriel' => $idmateriel], $NEW_ROW);
				}

				switch ($arr_1['C_ABREVIATION_GRANDEUR']): // pour le materiel // a finir
					case "NB" :
						$field = "vmmNBMateriel";
						break;
					default :
						$field = "vmmCouleurMateriel";
						break;
				endswitch;

				$rsm = $APP_MAT_VOL->find(['idmateriel' => $NEW_ROW['idmateriel']])->sort(['dateMateriel_volume' => -1]);
				$arm = $rsm->getNext();
				if ($arm['valeurMateriel_volume'] != $ROW['R_VALVMM_VOLUMEMOYENMENSUEL']) $APP_MAT->create_update(['idmateriel' => $NEW_ROW['idmateriel']], [$field => (int)$ROW['R_VALVMM_VOLUMEMOYENMENSUEL']]);

				break;

			case "t_grandeur":
				// => t_grandeur => t_bien => table de liaison.
				/*$NEW_ROW = array();

				$APP_MAT     = new App('materiel');
				$APP_MAT_GRD = new App('materiel_compteur');
				$APP_MAT_VOL = new App('materiel_volume');

				$arr_1    = $BASE_SYNC->t_grandeur->findOne([ 'N_ID' => $ROW['N_PERMETESTIMER_GRANDEUR_ID'] ]);
				$arr_bien = $BASE_SYNC->t_bien->findOne([ 'N_ID' => $arr_1['N_DEPEND_BIEN_ID'] ]);
				// => num, code du bien, sync idae
				$numserie = $arr_bien['C_IDENTIFIANTFABRICANT_BIENIMMA'];
				//
				$arr_mat = $APP_MAT->findOne([ 'codeMateriel' => $numserie ]);
				if ( empty($arr_mat['idmateriel']) ) {
					break;
				}

				$NEW_ROW['N_ID']                        = $ROW['N_ID'];
				$NEW_ROW['idmateriel']                  = $idmateriel = (int)$arr_mat['idmateriel'];
				$NEW_ROW['dateCreationMateriel_volume'] = $ROW['T_CREATE_DATE'];
				$NEW_ROW['dateDebutMateriel_volume']    = $ROW['D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL'];
				$NEW_ROW['quantiteMateriel_volume']     = (int)$ROW['R_VALVMM_VOLUMEMOYENMENSUEL'];

				// cherchons la vmm
				$test_grd = $APP_MAT_VOL->findOne([ 'N_ID' => $ROW['N_ID'] ]);
				if ( empty($test_grd['idmateriel']) ) {
					$NEW_ROW['idmateriel_volume'] = (int)$APP->getNext('idmateriel_volume');
					$APP_MAT_VOL->insert($NEW_ROW);
				} else {
					$APP_MAT_VOL->update([ 'N_ID' => $ROW['N_ID'] , 'idmateriel' => $idmateriel ] , $NEW_ROW);
				}*/
				break;
		endswitch;
	}