<?
	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);

	$ClassClient               = new Client();
	$ClassFournisseur          = new Fournisseur();
	$ClassAgentHasClientClient = new AgentHasClient();
	$ClassTache                = new Tache();
	$ClassTacheHasSociete      = new TacheHasSociete();
	$ClassStatutTache          = new StatutTache();
	$ClassTypeTache            = new TypeTache();
	$ClassTache                = new Tache();
	$ClassAgentHasTache        = new AgentHasTache();

	set_time_limit(0);

	$app_tache        = new App('tache');
	$app_contact      = new App('contact');
	$app_contact_type = new App('contact_type');
	$app_prestataire  = new App('prestataire');

	if (empty($_POST['run'])):
		?>
		<div style="width:350px;overflow:hidden;">
			<br>
			<br>
			<br>
			<div class="flex_h flex_align_middle">
				<div style="width:90px;text-align:center">
					<i class="fa fa-download fa-2x"></i></div>
				<div class="texterouge">
					Importation clients prospects et taches depuis idae
				</div>
			</div>
			<div class="padding margin">
				<progress value="0" id="auto_first_job" style="display:none;"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<br>
			<br>
			<br>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('customer/<?= CUSTOMERNAME ?>/app_admin/app_migration_client_prospect','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst"></div>
		</div>
		<?
		return;
	endif;

	// $ClassFournisseur->getSelectOneFournisseur("search[fournisseur_idfournisseur]","",true,array('codeType_fournisseur'=>'PREST'))
	$rsF = $ClassFournisseur->getOneFournisseur(['codeType_fournisseur' => 'PREST']);
	$i   = 0;
	while ($arr = $rsF->fetchRow()) {
		$i++;
		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
		                                   'progress_value'   => $i,
		                                   'progress_max'     => $rsF->recordCount(),
		                                   'progress_message' => $arr['nomSociete']], session_id());
		$ins = ['nomPrestataire' => strtolower($arr['nomSociete'])];
		$app_prestataire->create_update($ins);
	}

	// CLIENT dans ARTIS !!!

	$TYPE = 'client';

	$table_name = ($TYPE == 'client') ? 'client' : 'prospect';
	$app_cli    = $APP->plug('sitebase_base', $table_name);
	$id         = 'id' . $table_name;

	if ($TYPE == 'client') {
		$rs = $ClassClient->getOneClient([/*'numeroClient'=>'CSEM',*/
			'estClientClient' => 1, 'groupBy' => 'idclient', 'sortBy' => 'entite_identite asc, nomClient asc']);
	} else {
		// PROSPECTS :
		$rs = $ClassClient->getOneClient(['noestClientClient' => 1, 'groupBy' => 'idclient', 'sortBy' => 'entite_identite asc, nomClient asc']);
	}

	$done  = 0;
	$empty = 0;
	$i     = 0;
	//	echo  "Nom client idae; code depuis artys;\r\n";
	while ($arr = $rs->fetchRow()) { // le code est dans t_partie ? goon
		// continue;
		$i++;
		foreach ($arr as $kk => $vv):
			if (is_numeric($kk)) unset($arr[$kk]);
		endforeach;

		$idclient        = (int)$arr['idclient'];
		$idsociete       = (int)$arr['idsociete'];
		$arr['idclient'] = (int)$arr['idclient'];

		if (empty($arr['nomSociete'])) continue;

		$rsA = $ClassAgentHasClientClient->getOneAgentHasClient(['client_idclient' => $idclient, 'nocodeGroupe_agent' => 'TECH']);

		$arr['nomClient']     = $arr['nomSociete'];
		$arr['codeClient']    = $arr['numeroClient'];
		$arr['idae_idclient'] = (int)$arr['idclient'];
		$arr['identite']      = (int)$arr['identite'];
		$arr['idagent']       = (int)$rsA->fields['agent_idagent'];
		//
		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
		                                   'progress_value'   => $i,
		                                   'progress_max'     => $rs->recordCount(),
		                                   'progress_message' => $arr['nomClient']], session_id());
		//
		unset($arr['nomSociete'], $arr['societe_idsociete']);
		flush();
		ob_flush();
		//
		$ct     = $BASE_SYNC->t_partie->find(['C_CODE_ORG' => $arr['numeroClient']])->count();
		$arr_cl = $BASE_SYNC->t_partie->findOne(['C_CODE_ORG' => $arr['numeroClient']]);

		//
		if ($ct != 0 && !empty($arr['numeroClient'])) {
			if ($TYPE == 'client') {
				$arr_new_cli  = $app_cli->findOne(['codeClient' => $arr['codeClient']]);
				$new_idclient = (int)$arr_new_cli['idclient'];
				if (empty($new_idclient)) {
					echo $arr['nomClient'] . ';codeClient ' . $arr['numeroClient'] . ';<br>';
					continue;
				}
				echo $arr['nomClient'] . ';codeClient ' . $arr['numeroClient'] . ';<br>';
				$done++;
			}

			flush();
			ob_flush();

			//
			$arr['N_ID'] = $arr_cl['N_ID'];

			// recuperation taches
			$rs_tache = $ClassTacheHasSociete->getOneTacheHasSociete(['idsociete' => $idsociete, 'groupBy' => 'idtache']);
			echo ' taches => ' . $rs_tache->recordcount() . '<br>';
			while ($arr_tache = $rs_tache->fetchRow()) {
				$arr_tache_tmp = $ClassAgentHasTache->getOneAgentHasTache(['tache_idtache' => $arr_tache['idtache']]);

				$idtache = (int)$arr_tache['idtache'];
				if (!in_array($arr_tache['type_tache_idtype_tache'], [7, 8, 9, 10, 11, 12])) continue;

				$heuredeb = ($arr_tache['heureDebutTache'] == "24:00:00") ? 'PM' : $arr_tache['heureDebutTache'];
				$heuredeb = ($heuredeb == '01:00:00') ? 'AM' : $heuredeb;
				$heuredeb = (empty($heuredeb)) ? 'PM' : $heuredeb;
				// echo $idtache.' '.$heuredeb.' '.$arr_tache['objetTache'].'<br>';
				if ($heuredeb == 'AM' || $heuredeb == 'PM') {
					$arr_tache['heureFinTache'] = '';
				}
				$codeTache = (string)$arr_tache['idtache'];
				$in_tache  = ['idagent'           => (int)$arr_tache_tmp->fields['idagent'], 'idclient' => $new_idclient, 'codeTache' => $codeTache, 'nomTache' => $arr_tache['objetTache'],
				              'descriptionTache'  => utf8_encode($arr_tache['commentaireTache']), 'resultatTache' => utf8_encode($arr_tache['resultatTache']),
				              'dateCreationTache' => $arr_tache['dateCreationTache'], 'dateDebutTache' => $arr_tache['dateDebutTache'], 'dateFinTache' => $arr_tache['dateFinTache'],
				              'heureDebutTache'   => $heuredeb, 'heureFinTache' => $arr_tache['heureFinTache'],
				              'idtache_statut'    => (int)$arr_tache['statut_tache_idstatut_tache'], 'idtache_type' => (int)$arr_tache['type_tache_idtype_tache'],
				              'nomClient'         => $arr['nomClient']
				];

				$ClassTacheHasPersonne = new TacheHasPersonne();
				$rs_pers               = $ClassTacheHasPersonne->getOneTacheHasPersonne(['idtache' => $idtache, 'groupBy' => 'idpersonne']);

				if (!empty($rs_pers->fields['idpersonne'])):
					$in_tache['idcontact'] = (int)$app_contact->create_update(['codeContact' => (string)$rs_pers->fields['idpersonne']], ['nomContact' => utf8_encode($rs_pers->fields['nomPersonne'])]);
					echo 'contact [' . $in_tache['idcontact'] . '] ';
				endif;

				$app_tache->create_update(['codeTache' => $codeTache], $in_tache);

			}

			// CONTACTS
			$ClassSocieteHasPersonne = new SocieteHasPersonne();//$APP -> plug('sitebase_base', 'contact');
			$rs_contact              = $ClassSocieteHasPersonne->getOneSocieteHasPersonne(['idsociete' => $idsociete, 'groupBy' => 'idpersonne']);

			echo ' contacts => ' . $rs_contact->recordcount() . '<br>';

			while ($arr2 = $rs_contact->fetchRow()) {
				$arr2 = mysqlToMongo($arr2, 0);
				$arr2 = fonctionsProduction::cleanPostMongo($arr2, true);
				// echo $arr2['nomPersonne'].' '.$arr2['prenomPersonne'].'<br>';
				//

				$out                          = [];
				$out['codeContact']           = (string)$arr2['idpersonne'];
				$out['idclient']              = (int)$new_idclient;
				$out['idagent']               = (int)$arr['idagent'];
				$out['etatCivilContact']      = utf8_encode($arr2['etatCivilPersonne']);
				$out['commentaireContact']    = utf8_encode($arr2['commentairePersonne']);
				$out['nomContact']            = ucfirst(utf8_encode(strtolower($arr2['nomPersonne'])));
				$out['prenomContact']         = utf8_encode(strtolower($arr2['prenomPersonne']));
				$fonctionSociete_has_personne = utf8_encode(strtolower($arr2['fonctionSociete_has_personne']));
				if (!empty($fonctionSociete_has_personne)) {
					$out['idcontact_type'] = $app_contact_type->create_update(['codeContact_type' => trim(strtoupper($fonctionSociete_has_personne))], ['nomContact_type' => strtolower($fonctionSociete_has_personne)]);
				}
				//
				$ClassPersonneHasLocalisation = new PersonneHasLocalisation();

				$rsPersonneHasLocalisation = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'groupBy' => 'type_telfax_idtype_telfax']);

				// echo "personne ".$arr2['idpersonne'].' '.$arr2['nomPersonne'];
				while ($arrL = $rsPersonneHasLocalisation->fetchRow()) {
					if ($rsPersonneHasLocalisation->recordCount() > 2) {
						// vardump($arrL);
						// echo ' <br>'.$arrL['type_telfax_idtype_telfax'].' '.$arrL['commentaireTelfax'].' '.' '.$arrL['numeroTelfax'].' <br>';
					}
					// echo 'Telephones :  <br>'.$arrL['nomTypeTelfax'].' ['.$arrL['commentaireTelfax'].'] = '.maskTel($arrL['numeroTelfax']).' <br>';
					$out['adresseContact']    = utf8_encode($arrL['adresse1']);
					$out['adresse2Contact']   = utf8_encode($arrL['adresse2']);
					$out['codePostalContact'] = utf8_encode($arrL['codePostalAdresse']);
					$out['villeContact']      = utf8_encode($arrL['villeAdresse']);
					$out['paysContact']       = utf8_encode($arrL['paysAdresse']);
					$out['emailContact']      = utf8_encode($arrL['emailEmail']);
					$out['idagent']           = (int)$arr['idagent'];
					$arrL['numeroTelfax']     = cleanTel($arrL['numeroTelfax']);

					switch ($arrL['type_telfax_idtype_telfax']):
						case "1": // Tel si pas de type 4 et 3 => telephone principal, sinon tel2
							$rstest = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'type_telfax_idtype_telfax' => [4, 3]]);

							if ($rstest->RecordCount() == 0) {
								$out['telephoneContact'] = $arrL['numeroTelfax'];
							} else {
								$out['telephone2Contact'] = $arrL['numeroTelfax'];
							}

							break;
						case "4": // Tel si pas de type 1 et 3 => telephone principal, sinon tel2
							$rstest = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'type_telfax_idtype_telfax' => [1, 3]]);
							if ($rstest->RecordCount() == 0) {
								$out['telephoneContact'] = $arrL['numeroTelfax'];
							} else {
								$out['telephone2Contact'] = $arrL['numeroTelfax'];
							}
							break;
						case "3": // bureau
							$rstest = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'type_telfax_idtype_telfax' => [1, 4]]);
							if ($rstest->RecordCount() == 0) {
								$out['telephoneContact'] = $arrL['numeroTelfax'];
							} else {
								$out['telephone2Contact'] = $arrL['numeroTelfax'];
							}
							$out['telephoneContact'] = $arrL['numeroTelfax'];
							break;
						case "2": // fax
							$out['faxContact'] = $arrL['numeroTelfax'];
							break;
						case "5": // Mobile
							$out['mobileContact'] = $arrL['numeroTelfax'];
							break;
						default:
							echo "not";
							break;

					endswitch;

				}

				$app_contact->create_update(['codeContact' => $out['codeContact']], $out);
			}

		} else {
			$empty++;
		}
	}

	echo "<br>Done => " . $done;
	echo "<br>Vides => " . $empty;

	exit;
	// FIN CLIENT

	$i = 0;
	//
	$APP_PR = new App('prospect');
	// PROSPECTS :
	$rs = $ClassClient->getOneClient(['estProspectClient' => 1, 'groupBy' => 'idclient', 'sortBy' => 'entite_identite asc, nomClient asc']);
	while ($arr = $rs->fetchRow()) { //
		$i++;
		if (empty($arr['nomSociete'])) continue;
		$idclient  = (int)$arr['idclient'];
		$idsociete = (int)$arr['idsociete'];
		echo "<br>-" . $idsociete . ' ' . $arr['nomSociete'];
		//
		//
		$rsA = $ClassAgentHasClientClient->getOneAgentHasClient(['client_idclient' => $idclient, 'nocodeGroupe_agent' => 'TECH']);
		// PROSPECT
		$out_p['nomProspect']  = $arr['nomSociete'];
		$out_p['codeProspect'] = (string)$arr['idclient'];
		$out_p['identite']     = (int)$arr['identite'];
		$out_p['idagent']      = (int)$rsA->fields['agent_idagent'];
		// PROSPECT LOCALISATION
		$ClassSocieteHasLocalisation = new SocieteHasLocalisation();
		$rsLoca                      = $ClassSocieteHasLocalisation->getOneSocieteHasLocalisation(['idsociete' => $idsociete, 'groupBy' => 'localisation_idlocalisation']);
		$arrLoca                     = $rsLoca->fetchRow();
		$out_p['adresseProspect']    = $arrLoca['adresse1'];
		$out_p['adresse2Prospect']   = $arrLoca['adresse2'];
		$out_p['cpProspect']         = $arrLoca['codePostalAdresse'];
		$out_p['villeProspect']      = $arrLoca['villeAdresse'];
		$out_p['paysProspect']       = $arrLoca['paysAdresse'];

		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
		                                   'progress_value'   => $i,
		                                   'progress_max'     => $rs->recordCount(),
		                                   'progress_message' => $arr['nomSociete']], session_id());

		$idprospect = $APP_PR->create_update(['codeProspect' => (string)$idclient], $out_p);

		$rs_tache = $ClassTacheHasSociete->getOneTacheHasSociete(['idsociete' => $idsociete, 'groupBy' => 'idtache']);
		//  echo ' taches => ' . $rs_tache->recordcount();
		while ($arr_tache = $rs_tache->fetchRow()) {
			echo ' type ' . $arr_tache['type_tache_idtype_tache'];
			if (!in_array($arr_tache['type_tache_idtype_tache'], [1, 4, 7, 8, 9, 10, 11, 12])) continue;
			$arr_tache_tmp = $ClassAgentHasTache->getOneAgentHasTache(['tache_idtache' => $arr_tache['idtache']]);
			$heuredeb      = ($arr_tache['heureDebutTache'] == "24:00:00") ? 'PM' : $arr_tache['heureDebutTache'];
			$heuredeb      = ($heuredeb == '01:00:00') ? 'AM' : $heuredeb;
			$heuredeb      = (empty($heuredeb)) ? 'PM' : $heuredeb;

			if ($heuredeb == 'AM' || $heuredeb == 'PM') {
				$arr_tache['heureFinTache'] = '';
			}

			$codeTache             = (string)$arr_tache['idtache'];
			$in_tache              = ['idagent'           => (int)$arr_tache_tmp->fields['idagent'], 'idprospect' => $idprospect, 'codeTache' => $codeTache,
			                          'nomTache'          => $arr_tache['objetTache'], 'descriptionTache' => utf8_encode($arr_tache['commentaireTache']), 'resultatTache' => utf8_encode($arr_tache['resultatTache']),
			                          'dateCreationTache' => $arr_tache['dateCreationTache'], 'dateDebutTache' => $arr_tache['dateDebutTache'],
			                          'dateFinTache'      => $arr_tache['dateFinTache'], 'heureDebutTache' => $heuredeb, 'heureFinTache' => $arr_tache['heureFinTache'],
			                          'idtache_statut'    => (int)$arr_tache['statut_tache_idstatut_tache'], 'idtache_type' => (int)$arr_tache['type_tache_idtype_tache'],
			                          'nomProspect'       => $arr['nomClient']
			];
			$ClassTacheHasPersonne = new TacheHasPersonne();
			$rs_pers               = $ClassTacheHasPersonne->getOneTacheHasPersonne(['idtache' => $arr_tache['idtache'], 'groupBy' => 'idpersonne']);

			if (!empty($rs_pers->fields['idpersonne'])):
				$in_tache['idcontact'] = (int)$app_contact->create_update(['codeContact' => (string)$rs_pers->fields['idpersonne']], ['nomContact' => utf8_encode($rs_pers->fields['nomPersonne'])]);
			endif;

			$app_tache->create_update(['codeTache' => $codeTache], $in_tache);
		}
		// CONTACTS
		$ClassSocieteHasPersonne = new SocieteHasPersonne();
		$rs_contact              = $ClassSocieteHasPersonne->getOneSocieteHasPersonne(['idsociete' => $idsociete, 'groupBy' => 'idpersonne']);
		// echo ' contacts => ' . $rs_contact->recordcount();
		while ($arr2 = $rs_contact->fetchRow()) {
			$out                          = [];
			$out['codeContact']           = (string)$arr2['idpersonne'];
			$out['idprospect']            = (int)$idprospect;
			$out['idagent']               = $out_p['idagent'];
			$out['etatCivilContact']      = utf8_encode($arr2['etatCivilPersonne']);
			$out['commentaireContact']    = utf8_encode($arr2['commentairePersonne']);
			$out['nomContact']            = ucfirst(utf8_encode(strtolower($arr2['nomPersonne'])));
			$out['prenomContact']         = utf8_encode(strtolower($arr2['prenomPersonne']));
			$fonctionSociete_has_personne = utf8_encode(strtolower($arr2['fonctionSociete_has_personne']));
			if (!empty($fonctionSociete_has_personne)) {
				$out['idcontact_type'] = $app_contact_type->create_update(['codeContact_type' => trim(strtoupper($fonctionSociete_has_personne))], ['nomContact_type' => strtolower($fonctionSociete_has_personne)]);
			}
			//
			$ClassPersonneHasLocalisation = new PersonneHasLocalisation();

			$rsPersonneHasLocalisation = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'groupBy' => 'type_telfax_idtype_telfax']);

			while ($arrL = $rsPersonneHasLocalisation->fetchRow()) {
				$out['adresseContact']    = utf8_encode($arrL['adresse1']);
				$out['adresse2Contact']   = utf8_encode($arrL['adresse2']);
				$out['codePostalContact'] = utf8_encode($arrL['codePostalAdresse']);
				$out['villeContact']      = utf8_encode($arrL['villeAdresse']);
				$out['paysContact']       = utf8_encode($arrL['paysAdresse']);
				$out['emailContact']      = utf8_encode($arrL['emailEmail']);
				$out['idagent']           = (int)$arr['idagent'];
				$arrL['numeroTelfax']     = cleanTel(utf8_encode($arrL['numeroTelfax']));

				switch ($arrL['type_telfax_idtype_telfax']):
					case "1": // Tel si pas de type 4 et 3 => telephone principal, sinon tel2
						$rstest = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'type_telfax_idtype_telfax' => [4, 3]]);

						if ($rstest->RecordCount() == 0) {
							$out['telephoneContact'] = $arrL['numeroTelfax'];
						} else {
							$out['telephone2Contact'] = $arrL['numeroTelfax'];
						}

						break;
					case "4":  // Tel si pas de type 4 et 3 => telephone principal, sinon tel2
						$rstest = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'type_telfax_idtype_telfax' => [1, 3]]);

						if ($rstest->RecordCount() == 0) {
							$out['telephoneContact'] = $arrL['numeroTelfax'];
						} else {
							$out['telephone2Contact'] = $arrL['numeroTelfax'];
						}

						break;
					case "3":
						$out['telephone2Contact'] = $arrL['numeroTelfax'];
						break;
					case "2":
						$out['faxContact'] = $arrL['numeroTelfax'];
						break;
					case "5":
						$out['mobileContact'] = $arrL['numeroTelfax'];
						break;

				endswitch;

			}

			flush();
			buffer_flush();
			$app_contact->create_update(['codeContact' => $out['codeContact']], $out);
		}
	}

	// SET_NEXT idcontact

	exit;

	// Adresse et telephone des sociétés => LOCALISATION remplacé par ADRESSE !
	$ClassSocieteHasLocalisation = new SocieteHasLocalisation();
	$rsSocieteHasLocalisation    = $ClassSocieteHasLocalisation->getOneSocieteHasLocalisation(['groupBy' => 'idadresse']);
	$app_cli                     = $APP->plug('sitebase_base', 'client');
	$app_adr                     = $APP->plug('sitebase_base', 'adresse');
	while ($arrL = $rsSocieteHasLocalisation->fetchRow()) {
		$arr = mysqlToMongo($arrL, 0);
		// recuperer idclient
		$idsociete       = (int)$arr['idsociete'];
		$one             = $app_cli->findOne(['idsociete' => $idsociete]);
		$arr['idclient'] = (int)$one['idclient'];
		unset($arr['idtype_localisation'], $arr['migrateTypeLocalisation'], $arr['nomSociete'], $arr['idsociete'], $arr['siretSociete'], $arr['sirenSociete'], $arr['idtelfax'], $arr['idtype_telfax'], $arr['idtelfax'], $arr['numeroTelfax'], $arr['commentaireTelfax'], $arr['idsociete_has_localisation'], $arr['idagent']);
		//
		$arr['nomAdresse'] = $arr['adresse1'] . ' ' . $arr['villeAdresse'];
		$app_adr->update(['idadresse' => $arr['idadresse']], ['$set' => $arr], ['upsert' => true]);
	}

	// adresse et phone des contacts => dans le contact ou pas ?

	exit;
	// BIG DATA
	$to_index = ['client', 'entite', 'groupe_agent', 'fournisseur', 'type_fournisseur', 'materiel', 'type_tache', 'coupon', 'coupon_ligne', 'type_suivi', 'statut_tache', 'contrat', 'produit', 'materiel', 'gamme_produit', 'categorie_produit', 'marque', 'adresse', 'localisation', 'location_client', 'ligne_location_client'];
	// $to_index = ['ligne_location_client'];

	foreach ($to_index as $key => $table_name) {
		skelMdl::send_cmd('act_notify', ['msg' => 'Migration en cours ' . $table_name, session_id()]);
		$id       = 'id' . $table_name;
		$arr_name = explode('_', $table_name);
		if (sizeof($arr_name) == 2) {
			$ClassName = ucfirst($arr_name[0]) . ucfirst($arr_name[1]);
		} elseif (sizeof($arr_name) == 3) {
			$ClassName = ucfirst($arr_name[0]) . ucfirst($arr_name[1]) . ucfirst($arr_name[2]);
		} else {
			$ClassName = ucfirst($table_name);
		}
		// MYSQL
		$ClassB = new $ClassName();
		$method = 'getOne' . $ClassName;
		$rs     = $ClassB->$method(['groupBy' => $id]);
		// MONGODB
		$app_cli = $APP->plug('sitebase_base', $table_name);
		echo "<br>" . $table_name . ' ' . $rs->recordcount();
		flush();
		ob_flush();
		while ($arr = $rs->fetchRow()) {
			$arr = mysqlToMongo($arr, 0);
			$arr = fonctionsProduction::cleanPostMongo($arr, true);
			//
			echo ' .';
			$arr['commentaire' . ucfirst($table_name)] = utf8_encode($arr['commentaire' . ucfirst($table_name)]);
			flush();
			ob_flush();
			$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
		}
	}

	exit;

	// $ClassImport = new Import();
	$ClassPersonne                 = new Personne();
	$ClassClient                   = new Client();
	$ClassSociete                  = new Societe();
	$ClassFournisseur              = new Fournisseur();
	$ClassSuivi                    = new Suivi();
	$ClassClientIsSociete          = new ClientIsSociete();
	$ClassEmail                    = new Email();
	$ClassSocieteHasEmail          = new SocieteHasEmail();
	$ClassAdresse                  = new Adresse();
	$ClassTelFax                   = new TelFax();
	$ClassSocieteHasLocalisation   = new SocieteHasLocalisation();
	$ClassInternetUrl              = new InternetUrl();
	$ClassSocieteHasInternetUrl    = new SocieteHasInternetUrl();
	$ClassSuiviHasSociete          = new SuiviHasSociete();
	$ClassAgentHasClient           = new AgentHasClient();
	$ClassAgent                    = new Agent();
	$ClassLocalisation             = new Localisation();
	$ClassLocationClient           = new LocationClient();
	$ClassSocieteHasPersonne       = new SocieteHasPersonne();
	$ClassPersonneHasLocalisation  = new PersonneHasLocalisation();
	$ClassContrat                  = new Contrat();
	$ClassContratHasCritereContrat = new ContratHasCritereContrat();
	$ClassContratHasProduit        = new ContratHasProduit();
	$ClassLocationClientHasContrat = new LocationClientHasContrat();
	$ClassCategorieProduit         = new CategorieProduit();
	$ClassMarque                   = new Marque();
	$ClassCategorieProuit          = new CategorieProduit();
	$ClassProduit                  = new Produit();
	$ClassGammeProduit             = new GammeProduit();
	$ClassMateriel                 = new Materiel();
	$ClassLigneLocationClient      = new LigneLocationClient();
	$ClassTache                    = new Tache();;
	$ClassTacheHasMateriel                           = new TacheHasMateriel();
	$ClassAgentHasTache                              = new AgentHasTache();
	$ClassCritereLigneLocation                       = new CritereLigneLocation();
	$ClassLigneLocationClientHasCritereLigneLocation = new LigneLocationClientHasCritereLigneLocation();
	$ClassStatutTache                                = new StatutTache();
	$ClassTypeTache                                  = new TypeTache();

	// CLIENT
	echo $table_name = 'client';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id      = 'id' . $table_name;
	$rs      = $ClassClient->getOneClient(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr              = mysqlToMongo($arr, 0);
		$arr              = fonctionsProduction::cleanPostMongo($arr, true);
		$arr['nomClient'] = $arr['nomSociete'];
		unset($arr['nomSociete'], $arr['societe_idsociete']);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);

	}

	// FOURNISSEUR
	echo $table_name = 'fournisseur';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id      = 'id' . $table_name;
	$rs      = $ClassFournisseur->getOneFournisseur(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case('fournisseur_type', $arr);
		//$arr['idtype_fournisseur'] = $arr['idfournisseur_type'];$arr['codeType_fournisseur'] = $arr['codeFournisseur_type'];
		$arr['nomFournisseur'] = $arr['nomSociete'];
		unset($arr['idfournisseur_type'], $arr['codeFournisseur_type'], $arr['nomFournisseur_type'], $arr['nomFournisseur']);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// TACHE

	/*
	 echo  $table_name = 'tache';
	 $app_cli = $APP->plug('sitebase_base', $table_name);
	 $id = 'id'.$table_name;
	 $rs = $ClassTache->getOneTache(array('groupBy'=>$id));
	 while ($arr = $rs->fetchRow()) {
	 $arr = mysqlToMongo($arr,0);
	 $arr = fonctionsProduction::cleanPostMongo($arr , true);
	 $arr =  invert_case('type_tache',$arr);
	 $arr =  invert_case('statut_tache',$arr);
	 $arr =  invert_case('type_suivi',$arr);
	 $arr =  invert_case('type_fournisseur',$arr);

	 unset($arr['idstatut_tache'] ,$arr['idtype_tache'],$arr['nomFournisseur_type'],$arr['nomFournisseur']);
	 echo '. '; flush(); ob_flush();
	 $app_cli->update(array($id =>$arr[$id]),array('$set'=>$arr),array('upsert'=>true));
	 }  */

	// PRODUIT
	echo $table_name = 'produit';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id      = 'id' . $table_name;
	$rs      = $ClassProduit->getOneProduit(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// PRODUIT GAMME
	echo $table_name = 'gamme_produit';
	$app_cli = $APP->plug('sitebase_base', 'produit_gamme');
	$id      = 'id' . $table_name;
	$rs      = $ClassGammeProduit->getOneGammeProduit(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// PRODUIT CATEGORIE
	echo $table_name = 'categorie_produit';
	$app_cli = $APP->plug('sitebase_base', 'produit_categorie');
	$id      = 'id' . $table_name;
	$rs      = $ClassCategorieProduit->getOneCategorieProduit(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// MARQUE
	echo $table_name = 'marque';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id      = 'id' . $table_name;
	$rs      = $ClassMarque->getOneMarque(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// MATERIEL
	echo $table_name = 'materiel';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id      = 'id' . $table_name;
	$rs      = $ClassMateriel->getOneMateriel(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	function invert_case($table, $arr) {
		return $arr;
		$arrexpl = explode('_', $table);
		if (sizeof($arrexpl) != 2)
			return $arr;
		$new_table = $arrexpl[1] . '_' . $arrexpl[0];
		$arr_repl  = ['id', 'code', 'nom', 'valeur', 'ordre', 'commentaire', 'resultat'];
		foreach ($arr_repl as $key => $value) {

			$new_field = $key . $new_table;

			if (!empty($arr[$key . ucfirst($table)])) :
				$arr[$key . ucfirst($new_table)] = $arr[$key . ucfirst($table)];
				unset($arr[$key . ucfirst($table)]);
			endif;
			if (!empty($arr[$key . $table])) :
				$arr[$key . $new_table] = $arr[$key . $table];
				unset($arr[$key . $table]);
			endif;

		}

		return $arr;
	}

?>
FIN