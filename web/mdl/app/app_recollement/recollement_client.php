<?
	include_once('conf.inc.php');
	$APP = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);

	$ClassClient = new Client();
	$ClassAgentHasClientClient = new AgentHasClient();
	$ClassTache = new Tache();
	$ClassTacheHasSociete = new TacheHasSociete();
	$ClassStatutTache = new StatutTache();
	$ClassTypeTache = new TypeTache();

	set_time_limit(0);

	$app_tache = new App('tache');
	$app_contact = new App('contact');
	/*
	// TACHE STATUT
	echo $table_name = 'statut_tache';
	$app_cli = $APP -> plug('sitebase_base', 'tache_statut');
	$id = 'id' . $table_name;
	$rs = $ClassStatutTache -> getOneStatutTache(array('groupBy' => $id));
	while ($arr = $rs -> fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
	}

	// TACHE TYPE
	echo $table_name = 'type_tache';
	$app_cli = $APP -> plug('sitebase_base', 'tache_type');
	$id = 'id' . $table_name;
	$rs = $ClassTypeTache -> getOneTypeTache(array('groupBy' => $id, 'debug' => 1));
	while ($arr = $rs -> fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
	}*/


	// CLIENT dans ARTIS !!!

	$table_name = 'client';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id = 'id' . $table_name;
	$rs = $ClassClient->getOneClient(['estClientClient' => 1, 'groupBy' => 'idclient','sortBy' => 'entite_identite asc, nomClient asc']);

	$done = 0;
	$empty = 0;
	echo  "Nom client idae; code depuis artys;\r\n";
	while ($arr = $rs->fetchRow()) { // le code est dans t_partie ? goon

		foreach ($arr as $kk => $vv):
			if (is_numeric($kk)) unset($arr[ $kk ]);
		endforeach;

		$idclient = (int)$arr['idclient'];
		$idsociete = (int)$arr['idsociete'];
		$arr['idclient'] = (int)$arr['idclient'];

		if (empty($arr['nomSociete'])) continue;

		$rsA = $ClassAgentHasClientClient->getOneAgentHasClient(['client_idclient' => $idclient, 'nocodeGroupe_agent' => 'TECH']);

		$arr['nomClient']       = $arr['nomSociete'];
		$arr['codeClient']      = $arr['numeroClient'];
		$arr['idae_idclient']   = (int)$arr['idclient'];
		$arr['identite']        = (int)$arr['identite'];
		$arr['idagent']         = (int)$rsA->fields['agent_idagent'];
		//
		unset($arr['nomSociete'], $arr['societe_idsociete']);
		flush();
		ob_flush();
		//
		$ct = $BASE_SYNC->t_partie->find(['C_CODE_ORG' => $arr['numeroClient']])->count();
		$arr_cl = $BASE_SYNC->t_partie->findOne(['C_CODE_ORG' => $arr['numeroClient']]);
		//
		flush();
		ob_flush();
		/*if ( empty($arr['numeroClient'])) {echo $arr['nomClient'].";;<br>";continue;}
		continue;*/
		//
		if ($ct != 0 && !empty($arr['numeroClient'])) {
			$arr_new_cli    = $app_cli -> findOne(array('codeClient' => $arr['codeClient']));
			$new_idclient   = (int)$arr_new_cli['idclient'];
			if(empty($new_idclient)) {echo $arr['nomClient'].';codeClient '.$arr['numeroClient'].';<br>';continue;}
			continue;
			echo $arr['nomClient'].' '.$new_idclient.'<br>';
			$done++;

			flush();
			ob_flush();

			//
			$arr['N_ID'] = $arr_cl['N_ID'];

			// $app_cli -> update(array('idclient' => $idclient), array('$set' => $arr), array('upsert' => true));

			// recuperation taches
			$rs_tache = $ClassTacheHasSociete->getOneTacheHasSociete(['idsociete' => $idsociete, 'groupBy' => 'idtache']);
			echo ' taches => ' . $rs_tache->recordcount().'<br>';
			while ($arr_tache = $rs_tache->fetchRow()) {
				$idtache = (int)$arr_tache['idtache'];
				if (!in_array($arr_tache['type_tache_idtype_tache'], [7, 8, 9, 10, 11, 12])) continue;
				echo $arr_tache['objetTache'].' ; ';

				$heuredeb = ($arr_tache['heureDebutTache'] == "24:00:00") ? 'AM' : $arr_tache['heureDebutTache'];
				$heuredeb = ($heuredeb == '01:00:00') ? 'PM' : $heuredeb;
				if ($heuredeb == 'AM' || $heuredeb == 'PM') {
					$arr_tache['heureFinTache'] = '';
				}
				$codeTache  = (string)$arr_tache['idtache'];
				$in_tache = ['idagent'           => $arr['idagent'], 'idclient' => $new_idclient, 'codeTache' => $codeTache, 'nomTache' => $arr_tache['objetTache'],
				             'descriptionTache'  => utf8_encode($arr_tache['commentaireTache']),'resultatTache'  => utf8_encode($arr_tache['commentaireTache']),
				             'dateCreationTache' => $arr_tache['dateCreationTache'], 'dateDebutTache' => $arr_tache['dateDebutTache'], 'dateFinTache' => $arr_tache['dateFinTache'],
				             'heureDebutTache'   => $heuredeb, 'heureFinTache' => $arr_tache['heureFinTache'],
				             'idtache_statut'    => (int)$arr_tache['statut_tache_idstatut_tache'], 'idtache_type' => (int)$arr_tache['type_tache_idtype_tache'],
				             'nomClient'         => $arr['nomClient']
				];

				$ClassTacheHasPersonne = new TacheHasPersonne();
				$rs_pers = $ClassTacheHasPersonne->getOneTacheHasPersonne(['idtache' => $idtache, 'groupBy' => 'idpersonne']);

				if(!empty($rs_pers->fields['idpersonne'])):
					$in_tache['idcontact'] = (int)$rs_pers->fields['idpersonne'];
					echo 'contact ['.$in_tache['idcontact'].'] ';
				endif;

				$app_tache->create_update(['codeTache' => $codeTache], $in_tache);

			}
			echo "<br>";
			// CONTACTS
			$ClassSocieteHasPersonne = new SocieteHasPersonne();
			$app_contact = new App('contact');//$APP -> plug('sitebase_base', 'contact');
			$rs_contact = $ClassSocieteHasPersonne->getOneSocieteHasPersonne(['idsociete' => $idsociete, 'groupBy' => 'idpersonne']);

			echo ' contacts => ' . $rs_contact->recordcount().'<br>';

			while ($arr2 = $rs_contact->fetchRow()) {
				$arr2 = mysqlToMongo($arr2, 0);
				$arr2 = fonctionsProduction::cleanPostMongo($arr2, true);
				echo $arr2['nomPersonne'].' '.$out['prenomContact'].'<br>';
				//
				flush();
				ob_flush();
				$out = [];
				$out['etatCivilContact'] = utf8_encode($arr2['etatCivilPersonne']);
				$out['commentaireContact'] = utf8_encode($arr2['commentairePersonne']);
				$out['nomContact'] = ucfirst(utf8_encode(strtolower($arr2['nomPersonne'])));
				$out['prenomContact'] = utf8_encode(strtolower($arr2['prenomPersonne']));
				$out['codeContact'] = $arr2['idpersonne'];
				$out['idcontact'] = (int)$arr2['idpersonne'];
				$out['idclient'] = (int)$new_idclient;
				$out['idagent'] = (int)$arr['idagent'];

				//
				$ClassPersonneHasLocalisation = new PersonneHasLocalisation();

				$rsPersonneHasLocalisation = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['idpersonne' => $arr2['idpersonne'], 'groupBy' => 'type_telfax_idtype_telfax']);

				while ($arrL = $rsPersonneHasLocalisation->fetchRow()) {
					if ($rsPersonneHasLocalisation->recordCount() > 2) {
						// vardump($arrL);
						// echo ' <br>'.$arrL['type_telfax_idtype_telfax'].' '.$arrL['commentaireTelfax'].' '.' '.$arrL['numeroTelfax'].' <br>';
					}
					$out['adresseContact'] = utf8_encode($arrL['adresse1']);
					$out['adresse2Contact'] = utf8_encode($arrL['adresse2']);
					$out['codePostalContact'] = utf8_encode($arrL['codePostalAdresse']);
					$out['villeContact'] = utf8_encode($arrL['villeAdresse']);
					$out['paysContact'] = utf8_encode($arrL['paysAdresse']);
					$out['emailContact'] = utf8_encode($arrL['emailEmail']);
					$out['idagent'] = (int)$arr['idagent'];
					$arrL['numeroTelfax'] = cleanTel($arrL['numeroTelfax']);

					switch ($arrL['type_telfax_idtype_telfax']):
						case "1":
							$out['telephoneContact'] = $arrL['numeroTelfax'];
							break;
						case "4":
							$out['telephone2Contact'] = $arrL['numeroTelfax'];
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

				$app_contact->create_update(['codeContact' => $out['codeContact']], $out);
			}
			if (!empty($arr_cl['codeClient'])) {

			}
			echo "<hr>";
		} else {
			// on cherche par le nom !!!
			$arr_cl = $BASE_SYNC->t_partie->findOne(['C_RAISONSOCIALE_ORG' => trim($arr['nomClient'])]);
			if (!empty($arr_cl['C_RAISONSOCIALE_ORG'])) {
				$arr['N_ID'] = $arr_cl['N_ID'];
				// $app_cli -> update(array('idclient' => $$new_idclient), array('$set' => $arr), array('upsert' => true));
				$done++;
			} else {

				$empty++;
			}
		}
	}


?>