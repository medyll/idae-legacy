<?
include_once ('conf.inc.php');
$APP = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

ini_set('display_errors',55);

$ClassClient = new Client(); 
$ClassTache = new Tache();  
$ClassTacheHasSociete = new TacheHasSociete();  
$ClassStatutTache = new StatutTache();  
$ClassTypeTache = new TypeTache(); 

set_time_limit(0);

$rsLoop = $BASE_SYNC->t_partie -> find(array('C_TYPE_IDX' => 'com.artis.business.parties.OrgExterne'));
	while ($arr_loop = $rsLoop -> getNext()) {
		if(str_find($arr_loop['C_CODE_ORG'],'XXX')) continue;
	//	echo is_numeric(substr($arr_loop['C_CODE_ORG'],0,1)); echo "<br>";
		$start = substr($arr_loop['C_CODE_ORG'],0,1);
		if(( $start != 'C') &&   !is_numeric($start)) continue;
		$rstest = $ClassClient -> getOneClient(array('numeroClient'=>$arr_loop['C_CODE_ORG']));

		if($rstest->recordCount()==0){
			echo $arr_loop['C_CODE_ORG'].";".$arr_loop['C_RAISONSOCIALE_ORG']."<br>";
		}

	}
echo "done";
	exit;
	// CLIENT
echo $table_name = 'client';
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassClient -> getOneClient(array('estClientClient'=>1,'groupBy' => 'idclient','sortBy'=>'entite_identite asc, nomClient asc'));
echo $rs->recordcount();
echo "<br>";
	$done = 0;
	$empty = 0;
	echo  "idclient;nomClient;codeClient<br>";
while ($arr = $rs -> fetchRow()) { // le code est dans t_partie ? goon
	// $arr = fonctionsProduction::cleanPostMongo($arr, true)
	if(empty($arr['nomSociete'])) continue;
	$idsociete = (int)$arr['idsociete'] ;
	$arr['nomClient'] = $arr['nomSociete'];
	$arr['idae_idclient'] = (int)$arr['idclient'];
	$idclient  = (int)$arr['idclient'] ;
	//
	unset($arr['nomSociete'], $arr['societe_idsociete']);
	flush();
	ob_flush();
	//
	$ct 		= $BASE_SYNC->t_partie -> find(array('C_CODE_ORG' => $arr['numeroClient']))->count();
	$arr_cl 	= $BASE_SYNC->t_partie -> findOne(array('C_CODE_ORG' => $arr['numeroClient']));
	// Si code artis et code artis != codeClient => update.
	if($ct!=0 && !empty($arr['numeroClient'])){

		$done++;
		//
		$arr['N_ID'] = $arr_cl['N_ID'];

		$app_cli -> update(array('idclient' => $idclient), array('$set' => $arr), array('upsert' => true));
		// recuperation taches
		$rs_tache = $ClassTacheHasSociete -> getOneTacheHasSociete(array('idsociete'=>$idsociete,'groupBy'=>'idtache'));
		// echo $rs_tache->recordcount();
		while ($arr_tache = $rs_tache -> fetchRow()) {
			$idtache =(int)$arr_tache['idtache'];
			if(!in_array($arr_tache['type_tache_idtype_tache'],array(7,8,9,10,11,12))) continue; 
			// idtache , objetTache , commentaireTache , dateCreationTache , dateDebutTache , dateFinTache , heureDebutTache , heureFinTache
			// statut_tache_idstatut_tache // type_tache_idtype_tache
			$heuredeb = ($arr_tache['heureDebutTache']=="24:00:00")? 'AM' : $arr_tache['heureDebutTache'] ;
			$heuredeb = ($heuredeb=='01:00:00')? 'PM' : $heuredeb ;
			if($heuredeb=='AM' || $heuredeb=='PM' ){
				$arr_tache['heureFinTache'] = '';
			}
			
			
			$in_tache = array('idclient'=>$idclient,'idtache'=>$idtache,'nomTache'=>$arr_tache['objetTache'],
				'descriptionTache'=>utf8_encode($arr_tache['commentaireTache']),
				'dateCreationTache'=>$arr_tache['dateCreationTache'],'dateDebutTache'=>$arr_tache['dateDebutTache'],'dateFinTache'=>$arr_tache['dateFinTache'],
				'heureDebutTache'=>$heuredeb,'heureFinTache'=>$arr_tache['heureFinTache'],
				'idtache_statut'=>(int)$arr_tache['statut_tache_idstatut_tache'],'idtache_type'=>(int)$arr_tache['type_tache_idtype_tache'],
				'nomClient'=> $arr['nomClient']
			);
			$app_tache -> update(array('idtache' => $idtache), array('$set' => $in_tache), array('upsert' => true));
		}
		if(!empty($arr_cl['codeClient'])){
		//	echo  $arr_cl['idclient'].': '.$arr['nomClient'].' idae : "'.$arr['numeroClient'].'" '.', artis : "'.$arr_cl['codeClient'].'" '.$ct."<br>";
		}
	}else{

		// on cherche par le nom !!!
		$arr_cl 	= $BASE_SYNC->t_partie -> findOne(array('C_RAISONSOCIALE_ORG' => trim($arr['nomClient'])));
		if(!empty($arr_cl['C_RAISONSOCIALE_ORG'])){
			$arr['N_ID'] = $arr_cl['N_ID'];

			$app_cli -> update(array('idclient' => $idclient), array('$set' => $arr), array('upsert' => true));

			$done++;
		}else{
			// echo "entite".$arr['entite_identite']." idae ". $arr['idclient'].' : '.$arr['nomClient'].' code idae '.$arr['numeroClient']."<br>";
			echo  $arr['idclient'].';'.$arr['nomClient'].";;<br>";
			$empty++;
		}
	}
}

 

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
$ClassSocieteHasLocalisation = new SocieteHasLocalisation();
$rsSocieteHasLocalisation = $ClassSocieteHasLocalisation->getOneSocieteHasLocalisation(array('groupBy'=>'idadresse'));
$app_cli = $APP -> plug('sitebase_base', 'client');
$app_adr = $APP -> plug('sitebase_base', 'adresse');
while($arrL = $rsSocieteHasLocalisation->fetchRow()){
	$arr 	= 	mysqlToMongo($arrL, 0);
	// recuperer idclient
	$idsociete = (int)$arr['idsociete'];
	$one = $app_cli->findOne(['idsociete'=>$idsociete]);
	$arr['idclient'] = (int)$one['idclient'];
	unset($arr['idtype_localisation'],$arr['migrateTypeLocalisation'],$arr['nomSociete'],$arr['idsociete'],$arr['siretSociete'],$arr['sirenSociete'],$arr['idtelfax'],$arr['idtype_telfax'],$arr['idtelfax'],$arr['numeroTelfax'],$arr['commentaireTelfax'],$arr['idsociete_has_localisation'],$arr['idagent']);
	//
	$arr['nomAdresse'] = $arr['adresse1'].' '.$arr['villeAdresse'];
	$app_adr -> update(array('idadresse' => $arr['idadresse']), array('$set' => $arr), array('upsert' => true));
}

// adresse et phone des contacts => dans le contact ou pas ?
$ClassPersonneHasLocalisation = new PersonneHasLocalisation();
$rsPersonneHasLocalisation = $ClassPersonneHasLocalisation->getOnePersonneHasLocalisation(array('groupBy'=>'idadresse'));
$app_con = $APP -> plug('sitebase_base', 'contact');
while($arrL = $rsPersonneHasLocalisation->fetchRow()){
	$arrL 	= 	mysqlToMongo($arrL, 0);
	$arr['adresseContact'] = $arrL['adresse1'];
	$arr['adresse2Contact'] = $arrL['adresse2'];
	$arr['codePostalContact'] = $arrL['codePostalAdresse'];
	$arr['villeContact'] = $arrL['villeAdresse'];
	$arr['paysContact'] = $arrL['paysAdresse']; 
	$app_con -> update(array('idcontact' => $arrL['idpersonne']), array('$set' => $arr), array('upsert' => true));
}
exit;
// BIG DATA
$to_index = ['client','entite','groupe_agent', 'fournisseur','type_fournisseur'  ,'materiel', 'type_tache','coupon','coupon_ligne', 'type_suivi', 'statut_tache', 'contrat','produit', 'materiel', 'gamme_produit', 'categorie_produit', 'marque', 'adresse','localisation','location_client','ligne_location_client'];
// $to_index = ['ligne_location_client'];
 
foreach ($to_index as $key => $table_name) {
	skelMdl::send_cmd('act_notify' , array( 'msg'    =>  'Migration en cours '.$table_name , session_id()));
	$id = 'id' . $table_name;
	$arr_name = explode('_', $table_name);
	if (sizeof($arr_name) == 2) {
		$ClassName = ucfirst($arr_name[0]) . ucfirst($arr_name[1]);
	} elseif (sizeof($arr_name) == 3) {
		$ClassName = ucfirst($arr_name[0]) . ucfirst($arr_name[1]) . ucfirst($arr_name[2]);
	}else {
		$ClassName = ucfirst($table_name);
	}
	// MYSQL
	$ClassB = new $ClassName();
	$method = 'getOne' . $ClassName;
	$rs = $ClassB -> $method(array('groupBy' => $id));
	// MONGODB
	$app_cli = $APP -> plug('sitebase_base', $table_name);	
	echo "<br>".$table_name . ' ' . $rs -> recordcount() ; 
	flush();
	ob_flush();
	while ($arr = $rs -> fetchRow()) {
		$arr 	= 	mysqlToMongo($arr, 0);
		$arr 	= 	fonctionsProduction::cleanPostMongo($arr, true); 
		//
		echo ' .';
		$arr['commentaire'.ucfirst($table_name)] = utf8_encode($arr['commentaire'.ucfirst($table_name)]);
		flush();
		ob_flush(); 
		$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
	}
}
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




exit ;

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
$ClassTache = new Tache();
;
$ClassTacheHasMateriel = new TacheHasMateriel();
$ClassAgentHasTache = new AgentHasTache();
$ClassCritereLigneLocation = new CritereLigneLocation();
$ClassLigneLocationClientHasCritereLigneLocation = new LigneLocationClientHasCritereLigneLocation();
$ClassStatutTache = new StatutTache();
$ClassTypeTache = new TypeTache();

// CLIENT
echo $table_name = 'client';
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassClient -> getOneClient(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr['nomClient'] = $arr['nomSociete'];
	unset($arr['nomSociete'], $arr['societe_idsociete']);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));

}



// FOURNISSEUR
echo $table_name = 'fournisseur';
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassFournisseur -> getOneFournisseur(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case('fournisseur_type', $arr);
	//$arr['idtype_fournisseur'] = $arr['idfournisseur_type'];$arr['codeType_fournisseur'] = $arr['codeFournisseur_type'];
	$arr['nomFournisseur'] = $arr['nomSociete'];
	unset($arr['idfournisseur_type'], $arr['codeFournisseur_type'], $arr['nomFournisseur_type'], $arr['nomFournisseur']);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
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
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassProduit -> getOneProduit(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case($table_name, $arr);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
}

// PRODUIT GAMME
echo $table_name = 'gamme_produit';
$app_cli = $APP -> plug('sitebase_base', 'produit_gamme');
$id = 'id' . $table_name;
$rs = $ClassGammeProduit -> getOneGammeProduit(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case($table_name, $arr);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
}

// PRODUIT CATEGORIE
echo $table_name = 'categorie_produit';
$app_cli = $APP -> plug('sitebase_base', 'produit_categorie');
$id = 'id' . $table_name;
$rs = $ClassCategorieProduit -> getOneCategorieProduit(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case($table_name, $arr);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
}

// MARQUE
echo $table_name = 'marque';
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassMarque -> getOneMarque(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case($table_name, $arr);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
}

// MATERIEL
echo $table_name = 'materiel';
$app_cli = $APP -> plug('sitebase_base', $table_name);
$id = 'id' . $table_name;
$rs = $ClassMateriel -> getOneMateriel(array('groupBy' => $id));
while ($arr = $rs -> fetchRow()) {
	$arr = mysqlToMongo($arr, 0);
	$arr = fonctionsProduction::cleanPostMongo($arr, true);
	$arr = invert_case($table_name, $arr);
	echo '. ';
	flush();
	ob_flush();
	$app_cli -> update(array($id => $arr[$id]), array('$set' => $arr), array('upsert' => true));
}

function invert_case($table, $arr) {
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