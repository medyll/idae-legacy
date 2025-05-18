<?
	include_once('conf.inc.php');
	$APP = new App();
	ini_set('display_errors',55);

	echo "red";
	exit;
	// $APP->plug_base('sitebase_base')->drop();
	$APP->plug_base('sitebase_base')->entite->drop();
	$APP->plug_base('sitebase_base')->prospect->drop();
	$APP->plug_base('sitebase_base')->tache->drop();

	$app_tache = $APP -> plug('sitebase_base', 'tache');

	$ClassClient = new Client();
	$ClassTache = new Tache();
	$ClassTacheHasSociete = new TacheHasSociete();
	$ClassAgentHasTache = new AgentHasTache();
	$ClassStatutTache = new StatutTache();
	$ClassTypeTache = new TypeTache();


	$to_index = ['entite','contrat', 'contrat_ligne', 'adresse', 'client', 'client_type', 'client_categorie', 'contact', 'fournisseur', 'type_fournisseur', 'type_suivi', 'type_fournisseur', 'agent', 'materiel', 'materiel_compteur', 'tache', 'tache_type', 'tache_statut', 'produit', 'produit_gamme', 'produit_categorie', 'marque'];
	foreach ($to_index as $key => $table_name) {
		$uc_table_name = ucfirst($table_name);
		$uc = ucfirst($table_name);
		$app_cli = $APP->plug('sitebase_base', $table_name);
		$app_cli->createIndex(['id' . $table_name => 1]);
		//
		$arr_fields = ['nom', 'prenom', 'code', 'ordre', 'numero', 'reference', 'valeur', 'dateDebut', 'dateFin', 'heureDebut', 'heureFin'];
		foreach ($arr_fields as $key2 => $dafield) {
			$app_cli->createIndex([$dafield . $uc => 1]);
			$app_cli->createIndex([$dafield . $uc => -1]);
		}
	}

	set_time_limit(0);

	// ENTITE
	$ClassEntite = new Entite();
	$rs = $ClassEntite->getOneEntite(['groupBy' => 'identite', 'sortBy' => 'identite']);
	$app_cli = $APP->plug('sitebase_base', 'entite');
	while ($arr = $rs->fetchRow()) {
		unset($arr['_id']);
		$arr['identite'] = (int)$arr['identite'];
		$arr = mysqlToMongo($arr, 0);
		//$arr = fonctionsProduction::cleanPostMongo($arr, true);
		echo $arr['codeEntite'];
		$app_cli->update(['identite' => $arr['identite']], ['$set' => $arr], ['upsert' => true]);
		$APP->setNext('identite', (int)$arr['identite']);
	}

	// AGENT
	$ClassAgent = new Agent();
	echo $table_name = 'agent';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$app_groupe_agent = $APP->plug('sitebase_base', 'agent_groupe');
	$id = 'id' . $table_name;
	$rs = $ClassAgent->getOneAgent(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arrExp['idagent'] = (int)$arr['idagent'];
		$arrExp['codeAgent'] = $arr['codeAgent'];
		$arrExp['loginAgent'] = $arr['loginAgent'];
		$arrExp['passwordAgent'] = $arr['passwordAgent'];
		$arrExp['estActifAgent'] = $arr['estActifAgent'];
		$arrExp['nomAgent'] = $arr['nomPersonne'];
		$arrExp['prenomAgent'] = $arr['prenomPersonne'];
		$arrExp['etatCivilAgent'] = utf8_encode($arr['etatCivilPersonne']);
		unset($arr['nomPersonne'], $arr['idpersonne'], $arr['prenomPersonne'], $arr['etatCivilPersonne'], $arr['idlangue']);

		flush();
		ob_flush();
		// Le groupe de l'agent
		if (!empty($arr['idgroupe_agent'])):
			$arrExpG['nomAgent_groupe'] = $arrExp['nomAgent_groupe'] = $arr['nomGroupe_agent'];
			$arrExpG['codeAgent_groupe'] = $arrExp['codeAgent_groupe'] = $arr['codeGroupe_agent'];
			$arrExpG['idagent_groupe'] = $arrExp['idagent_groupe'] = (int)$arr['idgroupe_agent'];
			//
			$app_groupe_agent->update(['idagent_groupe' => $arrExpG['idagent_groupe']], ['$set' => $arrExpG], ['upsert' => true]);
		endif;
		//
		$app_cli->update([$id => $arr[$id]], ['$set' => $arrExp], ['upsert' => true]);

	}

	// PROSPECTS ET LEUR TACHES
	$ClassClient = new AgentHasClient();
	$table_name = 'prospect';
	$app_cli = $APP->plug('sitebase_base', 'prospect');
	$id = 'id' . $table_name;
	//
	$rs = $ClassClient->getOneAgentHasClient(['estProspectClient' => 1, 'groupBy' => 'client_idclient', 'sortBy' => 'client_idclient']);
	while ($arr = $rs->fetchRow()) {
		$arr = fonctionsProduction::cleanPostMongo($arr, false);
		$arr['idagent'] = (int)$arr['agent_idagent'];
		$arr['idprospect'] = (int)$arr['client_idclient'];
		$arr['idetat_prospect'] = (int)$arr['etat_prospect_idetat_prospect'];
		$arr['nomClient'] = $arr['nomSociete'];
		$arr['codeProspect'] = empty($arr['numeroClient']) ? $arr['idclient'] : $arr['numeroClient'] ;
		$idsociete = (int)$arr['idsociete'];
		unset($arr['nomSociete'], $arr['societe_idsociete'], $arr['estClientClient'], $arr['idagent_has_prospect'], $arr['prospect_idprospect'], $arr['nomGroupe_agent'], $arr['idgroupe_agent'], $arr['codeGroupe_agent'], $arr['numeroProspect']);
		flush();
		ob_flush();
		//
		$out = [];
		foreach ($arr as $key => $value) {
			$key_new = str_replace('client', 'prospect', $key);
			$key_new = str_replace('Client', 'Prospect', $key_new);
			$out[$key_new] = utf8_encode($value);
		}
		unset($arr['nomSociete'], $arr['societe_idsociete'], $arr['estClientClient'], $arr['idagent_has_prospect'], $arr['prospect_idprospect'], $arr['nomGroupe_agent'], $arr['idgroupe_agent'], $arr['codeGroupe_agent'], $arr['numeroProspect']);

		//
		$arr_avoid = ['entite_identite', 'estProspectProspect', 'estSuspectProspect', 'agent_idagent', 'idsociete', 'arretProspect', 'etat_prospect_idetat_prospect', 'nomPersonne', 'prenomPersonne'];
		foreach ($arr_avoid as $key => $value) {
			unset($out[$value]);
		}
		$out['idprospect'] = $idprospect = (int)$arr['client_idclient'];
		$out['descriptionProspect'] = $out['commentaireProspect'];

		$app_cli->update(['idprospect' => (int)$out['idprospect']], ['$set' => $out], ['upsert' => true]);
		$APP->setNext('idprospect', (int)$arr['idclient']);
		//
		// recuperation taches prospects
		echo ". ";
		$rs_tache = $ClassTacheHasSociete->getOneTacheHasSociete(['idsociete' => $idsociete, 'groupBy' => 'idtache']);
		echo $rs_tache->recordcount();
		while ($arr_tache = $rs_tache->fetchRow()) {
			$idtache = (int)$arr_tache['idtache'];
			if (!in_array($arr_tache['type_tache_idtype_tache'], [7, 8, 9, 10, 11, 12])) continue;
			// idtache , objetTache , commentaireTache , dateCreationTache , dateDebutTache , dateFinTache , heureDebutTache , heureFinTache
			// statut_tache_idstatut_tache // type_tache_idtype_tache
			$heuredeb = ($arr_tache['heureDebutTache'] == "24:00:00") ? 'AM' : $arr_tache['heureDebutTache'];
			$heuredeb = ($heuredeb == '01:00:00') ? 'PM' : $heuredeb;
			if ($heuredeb == 'AM' || $heuredeb == 'PM') {
				$arr_tache['heureFinTache'] = '';
			}
			$rs_tache_det = $ClassAgentHasTache->getOneAgentHasTache(['idtache' => $idtache]);
			if(empty($rs_tache_det->fields['idagent'])) continue;
			//
			$in_tache = ['idprospect'          => $idprospect, 'idtache' => $idtache, 'nomTache' => $arr_tache['objetTache'],
			             'descriptionTache'  => utf8_encode($arr_tache['commentaireTache']),
			             'dateCreationTache' => $arr_tache['dateCreationTache'], 'dateDebutTache' => $arr_tache['dateDebutTache'], 'dateFinTache' => $arr_tache['dateFinTache'],
			             'heureDebutTache'   => $heuredeb, 'heureFinTache' => $arr_tache['heureFinTache'],
			             'idtache_statut'    => (int)$arr_tache['statut_tache_idstatut_tache'], 'idtache_type' => (int)$arr_tache['type_tache_idtype_tache'],
			             'nomProspect'         => $arr['nomProspect']
			];
			$in_tache['idagent'] = (int)$rs_tache_det->fields['idagent'] ;
			$app_tache->update(['idtache' => $idtache], ['$set' => $in_tache], ['upsert' => true]);

			flush();
			ob_flush();
		}

	}
	// recollement agent / tache

	echo "DONE";
	echo "done";die();

	// LigneParcProspect RESSOURCES PROSPECTS : idproduit, idprospect,qte, valeur, date
	$app_res = new App('prospect_ressource');
	$app_plug_res = $APP->plug('sitebase_base', 'prospect_ressource');

	/*$out = [];
	$ClassLigneParcProspect = new LigneParcProspect();
	$ClassProduit = new Produit();
	$rs = $ClassLigneParcProspect->getOneLigneParcProspect(['groupBy' => 'idligne_parc_prospect']);
	echo "<hr>";
	while ($arr = $rs->fetchRow()) {
		$arr = fonctionsProduction::cleanPostMongo($arr, false);
		// idparc => idressource , duree_location => dureeRessource, machine =>  ?
		$out['idprospect'] = (int)$arr['idclient'];
		$out['idprospect_ressource'] = (int)$arr['idligne_parc_prospect'];
		if (!empty($arr['dateDebutContrat']) && $arr['dateDebutContrat'] != '0000-00-00') {
			$out['dateDebutProspect_ressource'] = $arr['dateDebutContrat'];
			$out['dureeProspect_ressource'] = (int)$arr['duree_location'];
			$deb = new DateTime($out['dateDebutProspect_ressource']);
			$fin = $deb->modify('+ ' . ($arr['duree_location'] * 3) . ' month');
			$out['dateFinProspect_ressource'] = $fin->format('Y-m-d');
		}
		$out['quantiteProspect_ressource'] = (int)$arr['quantite'];
		$arr['machine'] = mysql_escape_string(utf8_encode($arr['machine']));
		// on devine le produit
		$rsP = $ClassProduit->getOneProduit(['debug' => true, 'nomProduit' => $arr['machine']]);
		if ($rsP->recordCount() != 0) {
			$out['idproduit'] = (int)$rsP->fields['idproduit'];
			$out['nomProduit'] = $rsP->fields['nomProduit'];
		} else {
			$out['nomProduit'] = $arr['machine'];
		}

		//
		echo "<hr>";
		//
		$APP->setNext('idprospect_ressource', (int)$out['idprospect_ressource']);
		//
		$app_plug_res->update(['idprospect_ressource' => (int)$out['idprospect_ressource']], ['$set' => $out], ['upsert' => true]);
		$app_res->consolidate_scheme($out['idprospect_ressource']);
	}*/
	//

	// MASSY
	$app_ag = $APP->plug('sitebase_base', 'agent');
	$app_ag->update([], ['$rename' => ['nomPersonne' => 'nomAgent', 'prenomPersonne' => 'prenomAgent', 'etatCivilPersonne' => 'etatCivilAgent']], ['multiple' => 1]);
	// CLIENT
	$app_ag = $APP->plug('sitebase_base', 'client');
	$app_ag->update([], ['$rename' => ['nomSociete' => 'nomClient']], ['multiple' => 1]);
	$app_ag->update(['estClientClient' => 1], ['$set' => ['idclient_type' => 1]], ['multiple' => 1]);
	$app_ag->update(['estProspectClient' => 1], ['$set' => ['idclient_type' => 2]], ['multiple' => 1]);
	$app_ag->update(['estPerduClient' => 1], ['$set' => ['idclient_type' => 3]], ['multiple' => 1]);
	$app_ag->update(['estSuspectClient' => 1], ['$set' => ['idclient_type' => 4]], ['multiple' => 1]);

	// FOURN
	$app_ag = $APP->plug('sitebase_base', 'fournisseur');
	$app_ag->update([], ['$rename' => ['nomSociete' => 'nomFournisseur']], ['multiple' => 1]);

	// MATERIEL
	$app_ag = $APP->plug('sitebase_base', 'ligne_location_client');
	$app_ag->update([], ['$rename' => ['nomProduit' => 'nomLigne_location_client']], ['multiple' => 1]);

	// LOCATION CLIENT
	// $app_ag = $APP -> plug('sitebase_base', 'location_client');
	// $app_ag->update( [],['$rename'=>['numeroLocation_client'=>'nomLocation_client']],['multiple'=>1]);



	// CONTACT depuis societe has personne
	/*
	$ClassSocieteHasPersonne = new SocieteHasPersonne();
	$app_contact = $APP -> plug('sitebase_base', 'contact');
	$rs_contact = $ClassSocieteHasPersonne  -> getOneSocieteHasPersonne(['groupBy'=>'idpersonne']);
	while ($arr = $rs_contact -> fetchRow()) {
			$arr 	= 	mysqlToMongo($arr, 0);
			$arr 	= 	fonctionsProduction::cleanPostMongo($arr, true);
			//
			flush();
			ob_flush();
			$arr['etatCivilPersonne'] = utf8_encode($arr['etatCivilPersonne']);
			$arr['commentairePersonne'] = utf8_encode($arr['commentairePersonne']);
			$arr['commentaireSociete'] = utf8_encode($arr['commentaireSociete']);
			$arr['idcontact'] = (int)$arr['idpersonne'];
			 //
			$app_contact -> update(array('idpersonne' => $arr['idpersonne']), array('$set' => $arr), array('upsert' => true));
		}
	$app_contact -> update([], array('$rename' => ['nomPersonne'=>'nomContact','prenomPersonne'=>'prenomContact']), array('multiple' => 1));


	$ARR_SCH = $APP->get_schemes();
	// LES TYPES A TRADUIRE
	$to_rename = ['type_fournisseur','type_tache','type_location_client','type_telfax','type_suivi','type_localisation','type_contrat'];
	foreach ($to_rename as $key => $table_name) {
		$arrexpl = explode('_', $table_name);
		// if (sizeof($arrexpl) != 2) return $arr;
		$new_table 		= $arrexpl[1] . '_' . $arrexpl[0];
		$old_name_id 	= 'id'.$table_name;
		$name_id 		= 'id'.$new_table;
		$old_name_name 	= 'nom'.ucfirst($table_name);
		$name_name 		= 'nom'.ucfirst($new_table);
		// MASS rename here => dans toutes les tables
		foreach ($ARR_SCH as $key2 => $arr_sh):
			$base            = $arr_sh['base'];
			$table           = $arr_sh['collection'];
			$dq = $APP->plug($base, $table);
			$dq -> update([],['$rename'=>[$old_name_id=>$name_id,$old_name_name=>$name_name]],['multiple'=>1]);
		endforeach ;
	}*/

	// Adresse et telephone des sociétés => LOCALISATION remplacé par ADRESSE !
	/*$ClassSocieteHasLocalisation = new SocieteHasLocalisation();
	$rsSocieteHasLocalisation = $ClassSocieteHasLocalisation->getOneSocieteHasLocalisation(['groupBy' => 'idadresse']);
	$app_cli = $APP->plug('sitebase_base', 'client');
	$app_adr = $APP->plug('sitebase_base', 'adresse');
	while ($arrL = $rsSocieteHasLocalisation->fetchRow()) {
		$arr = mysqlToMongo($arrL, 0);
		// recuperer idclient
		$idsociete = (int)$arr['idsociete'];
		$one = $app_cli->findOne(['idsociete' => $idsociete]);
		$arr['idclient'] = (int)$one['idclient'];
		unset($arr['idtype_localisation'], $arr['migrateTypeLocalisation'], $arr['nomSociete'], $arr['idsociete'], $arr['siretSociete'], $arr['sirenSociete'], $arr['idtelfax'], $arr['idtype_telfax'], $arr['idtelfax'], $arr['numeroTelfax'], $arr['commentaireTelfax'], $arr['idsociete_has_localisation'], $arr['idagent']);
		//
		$arr['nomAdresse'] = $arr['adresse1'] . ' ' . $arr['villeAdresse'];
		$app_adr->update(['idadresse' => $arr['idadresse']], ['$set' => $arr], ['upsert' => true]);
	}*/

	// adresse et phone des contacts => dans le contact ou pas ?
	/*$ClassPersonneHasLocalisation = new PersonneHasLocalisation();
	$rsPersonneHasLocalisation = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(['groupBy' => 'idadresse']);
	$app_con = $APP->plug('sitebase_base', 'contact');
	while ($arrL = $rsPersonneHasLocalisation->fetchRow()) {
		$arrL = mysqlToMongo($arrL, 0);
		$arr['adresseContact'] = $arrL['adresse1'];
		$arr['adresse2Contact'] = $arrL['adresse2'];
		$arr['codePostalContact'] = $arrL['codePostalAdresse'];
		$arr['villeContact'] = $arrL['villeAdresse'];
		$arr['paysContact'] = $arrL['paysAdresse'];
		$app_con->update(['idcontact' => $arrL['idpersonne']], ['$set' => $arr], ['upsert' => true]);
	}*/
	// exit;
	// BIG DATA
	// $to_index = ['client', 'entite', 'groupe_agent', 'fournisseur', 'type_fournisseur', 'materiel', 'type_tache', 'coupon', 'coupon_ligne', 'type_suivi', 'statut_tache', 'contrat', 'produit', 'materiel', 'gamme_produit', 'categorie_produit', 'marque', 'adresse', 'localisation', 'location_client', 'ligne_location_client'];
	$to_index = ['groupe_agent','type_tache', 'statut_tache'];

	foreach ($to_index as $key => $table_name) {
		skelMdl::send_cmd('act_notify', ['msg' => 'Migration en cours ' . $table_name, session_id()]);
		$id = 'id' . $table_name;
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
		$rs = $ClassB->$method(['groupBy' => $id]);
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
	// renaming
	/*
	$to_rename = ['type_fournisseur','groupe_agent','type_tache','statut_tache','gamme_produit','categorie_produit','marque'];
	foreach ($to_rename as $key => $table_name) {
		$app_cli = $APP -> plug('sitebase_base', $table_name);
		//
		$arrexpl = explode('_', $table_name);
		if (sizeof($arrexpl) != 2) return $arr;
		$new_table = $arrexpl[1] . '_' . $arrexpl[0];
		//
		$arr_fields = ['nom', 'prenom', 'code', 'ordre', 'numero', 'reference', 'resultat','valeur', 'dateDebut', 'dateFin', 'heureDebut', 'heureFin','type'];
		//
		//	$app_cli->update([],['$rename'=> ['id'.$table_name => 'id'.$new_table]]);
		foreach ($arr_fields as $key2 => $dafield) {
			// $app_cli -> createIndex(array($dafield . $uc => 1));
			// $app_cli -> createIndex(array($dafield . $uc => -1));
			// $app_cli -> update([],['$rename'=> [$dafield.ucfirst($table_name) => $dafield.ucfirst($new_table)] ]);
		}



	}*/

	exit;

	// $ClassImport = new Import();
	$ClassPersonne = new Personne();
	$ClassClient = new Client();
	$ClassSociete = new Societe();
	$ClassFournisseur = new Fournisseur();
	$ClassSuivi = new Suivi();
	$ClassClientIsSociete = new ClientIsSociete();
	$ClassEmail = new Email();
	$ClassSocieteHasEmail = new SocieteHasEmail();
	$ClassAdresse = new Adresse();
	$ClassTelFax = new TelFax();
	$ClassSocieteHasLocalisation = new SocieteHasLocalisation();
	$ClassInternetUrl = new InternetUrl();
	$ClassSocieteHasInternetUrl = new SocieteHasInternetUrl();
	$ClassSuiviHasSociete = new SuiviHasSociete();
	$ClassAgentHasClient = new AgentHasClient();
	$ClassAgent = new Agent();
	$ClassLocalisation = new Localisation();
	$ClassLocationClient = new LocationClient();
	$ClassSocieteHasPersonne = new SocieteHasPersonne();
	$ClassPersonneHasLocalisation = new PersonneHasLocalisation();
	$ClassContrat = new Contrat();
	$ClassContratHasCritereContrat = new ContratHasCritereContrat();
	$ClassContratHasProduit = new ContratHasProduit();
	$ClassLocationClientHasContrat = new LocationClientHasContrat();
	$ClassCategorieProduit = new CategorieProduit();
	$ClassMarque = new Marque();
	$ClassCategorieProuit = new CategorieProduit();
	$ClassProduit = new Produit();
	$ClassGammeProduit = new GammeProduit();
	$ClassMateriel = new Materiel();
	$ClassLigneLocationClient = new LigneLocationClient();
	$ClassTache = new Tache();;
	$ClassTacheHasMateriel = new TacheHasMateriel();
	$ClassAgentHasTache = new AgentHasTache();
	$ClassCritereLigneLocation = new CritereLigneLocation();
	$ClassLigneLocationClientHasCritereLigneLocation = new LigneLocationClientHasCritereLigneLocation();
	$ClassStatutTache = new StatutTache();
	$ClassTypeTache = new TypeTache();

	// CLIENT
	echo $table_name = 'client';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id = 'id' . $table_name;
	$rs = $ClassClient->getOneClient(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
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
	$id = 'id' . $table_name;
	$rs = $ClassFournisseur->getOneFournisseur(['groupBy' => $id]);
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

	// TACHE STATUT
	echo $table_name = 'statut_tache';
	$app_cli = $APP->plug('sitebase_base', 'tache_statut');
	$id = 'id' . $table_name;
	$rs = $ClassStatutTache->getOneStatutTache(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// TACHE TYPE
	echo $table_name = 'type_tache';
	$app_cli = $APP->plug('sitebase_base', 'tache_type');
	$id = 'id' . $table_name;
	$rs = $ClassTypeTache->getOneTypeTache(['groupBy' => $id, 'debug' => 1]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	// PRODUIT
	echo $table_name = 'produit';
	$app_cli = $APP->plug('sitebase_base', $table_name);
	$id = 'id' . $table_name;
	$rs = $ClassProduit->getOneProduit(['groupBy' => $id]);
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
	$id = 'id' . $table_name;
	$rs = $ClassGammeProduit->getOneGammeProduit(['groupBy' => $id]);
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
	$id = 'id' . $table_name;
	$rs = $ClassCategorieProduit->getOneCategorieProduit(['groupBy' => $id]);
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
	$id = 'id' . $table_name;
	$rs = $ClassMarque->getOneMarque(['groupBy' => $id]);
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
	$id = 'id' . $table_name;
	$rs = $ClassMateriel->getOneMateriel(['groupBy' => $id]);
	while ($arr = $rs->fetchRow()) {
		$arr = mysqlToMongo($arr, 0);
		$arr = fonctionsProduction::cleanPostMongo($arr, true);
		$arr = invert_case($table_name, $arr);
		echo '. ';
		flush();
		ob_flush();
		$app_cli->update([$id => $arr[$id]], ['$set' => $arr], ['upsert' => true]);
	}

	function invert_case($table, $arr)
	{
		return $arr;
		$arrexpl = explode('_', $table);
		if (sizeof($arrexpl) != 2)
			return $arr;
		$new_table = $arrexpl[1] . '_' . $arrexpl[0];
		$arr_repl = ['id', 'code', 'nom', 'valeur', 'ordre', 'commentaire', 'resultat'];
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