<?   
class LigneLocationClientHasAgent
{
	var $conn = null; 
	function LigneLocationClientHasAgent(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getParseSelect($field,$params){  
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			if(is_array($params[$field])){ /*var_dump($params);$params = array_filter($params[$field]);*/  }
		}
		$sql = "";
		if(!empty($params[$field])) 		$sql .=  " AND $field ".sqlIn($params[$field]);
		if(!empty($params["no".$field]))		$sql .=  " AND $field ".sqlNotIn($params["no".$field]); 
		if(!empty($params["lt_".$field])) 		$sql .=  " AND $field <   ".$params["lt_".$field];
		if(!empty($params["lte_".$field]))		$sql .=  " AND $field <=  ".$params["lte_".$field];
		if(!empty($params["gt_".$field]))		$sql .=  " AND $field >   ".$params["gt_".$field];
		if(!empty($params["gte_".$field]))		$sql .=  " AND $field >=  ".$params["gte_".$field];
		if(!empty($params["or_".$field]))		$sql .=  " OR $field =  ".$params["or_".$field];
		if(!empty($params["nor_".$field]))		$sql .=  " OR $field <>  ".$params["nor_".$field];
		if(!empty($params["isnull_".$field]))	$sql .=  " AND $field IS NULL " ;
		return $sql;				
	}
	
	function getParseSearch($field,$params){ 
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			if(is_array($params[$field])){$params = array_filter($params[$field]);echo "boooo"; }
		}
		$sql = "";
		if(!empty($params[$field]))	$sql .=  " AND $field ".sqlSearch($params[$field],$field) ;
		if(!empty($params["no".$field]))	$sql .=  " AND $field ".sqlNotSearch($params["no".$field],$field) ; 
		if(!empty($params["lt_".$field]))	$sql .=  " AND $field <   ".$params["lt_".$field] ;
		if(!empty($params["lte_".$field]))	$sql .=  " AND $field <=  ".$params["lte_".$field] ;
		if(!empty($params["gt_".$field]))	$sql .=  " AND $field >   ".$params["gt_".$field] ;
		if(!empty($params["gte_".$field]))	$sql .=  " AND $field >=  ".$params["gte_".$field] ;
		if(!empty($params["or_".$field]))	$sql .=  " OR $field =  ".$params["or_".$field] ;
		if(!empty($params["nor_".$field]))	$sql .=  " OR $field <>  ".$params["nor_".$field] ;
		if(!empty($params["isnull_".$field]))	$sql .=  " AND $field IS NULL " ;
		return $sql;				
	}
	
	function truncate(){ 
		$sql = "truncate table ligne_location_client_has_agent";
		return $this->conn->Execute($sql); 					
	}
	
	function createLigneLocationClientHasAgent($params){ 
		$this->conn->AutoExecute("ligne_location_client_has_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneLocationClientHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_location_client_has_agent", $params, "UPDATE", "idligne_location_client_has_agent = ".$idligne_location_client_has_agent); 
	}
	
	function deleteLigneLocationClientHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_location_client_has_agent WHERE idligne_location_client_has_agent = $idligne_location_client_has_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneLigneLocationClientHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_ligne_location_client_has_agent where 1 " ;
			$sql .= $this->getParseSelect("idagent",$params);
			$sql .= $this->getParseSelect("personne_idpersonne",$params);
			$sql .= $this->getParseSelect("loginAgent",$params);
			$sql .= $this->getParseSelect("passwordAgent",$params);
			$sql .= $this->getParseSelect("dateCreationAgent",$params);
			$sql .= $this->getParseSelect("dateFinAgent",$params);
			$sql .= $this->getParseSelect("estActifAgent",$params);
			$sql .= $this->getParseSelect("dateDebutAgent",$params);
			$sql .= $this->getParseSelect("idligne_location_client_has_agent",$params);
			$sql .= $this->getParseSelect("ligne_location_client_idligne_location_client",$params);
			$sql .= $this->getParseSelect("agent_id_agent",$params);
			$sql .= $this->getParseSelect("nomPersonne",$params);
			$sql .= $this->getParseSelect("prenomPersonne",$params);
			$sql .= $this->getParseSelect("sexePersonne",$params);
			$sql .= $this->getParseSelect("idligne_location_client",$params);
			$sql .= $this->getParseSelect("localisation_idlocalisation",$params);
			$sql .= $this->getParseSelect("materiel_idmateriel",$params);
			$sql .= $this->getParseSelect("location_client_idlocation_client",$params);
			$sql .= $this->getParseSelect("dateCreationLigne_location_client",$params);
			$sql .= $this->getParseSelect("dateDebutLigne_location_client",$params);
			$sql .= $this->getParseSelect("dateClotureLigne_location_client",$params);
			$sql .= $this->getParseSelect("dateFinLigne_location_client",$params);
			$sql .= $this->getParseSelect("dureeLigne_location_client",$params);
			$sql .= $this->getParseSelect("uniteDureeLigne_location_client",$params);
			$sql .= $this->getParseSelect("pvhtLigne_location_client",$params);
			$sql .= $this->getParseSelect("idlocation_client",$params);
			$sql .= $this->getParseSelect("type_location_client_idtype_location_client",$params);
			$sql .= $this->getParseSelect("client_idclient",$params);
			$sql .= $this->getParseSelect("numeroLocation_client",$params);
			$sql .= $this->getParseSelect("dateCreationLocation_client",$params);
			$sql .= $this->getParseSelect("dateDebutLocation_client",$params);
			$sql .= $this->getParseSelect("dateFinLocation_client",$params);
			$sql .= $this->getParseSelect("dateClotureLocation_client",$params);
			$sql .= $this->getParseSelect("dureeTrimLocation_client",$params);
			$sql .= $this->getParseSelect("loyerTrimLocation_client",$params);
			$sql .= $this->getParseSelect("idmateriel",$params);
			$sql .= $this->getParseSelect("stock_materiel_idstock_materiel",$params);
			$sql .= $this->getParseSelect("produit_idproduit",$params);
			$sql .= $this->getParseSelect("numeroSerieInterneMateriel",$params);
			$sql .= $this->getParseSelect("numeroSerieExterneMateriel",$params);
			$sql .= $this->getParseSelect("dateCreationMateriel",$params);
			$sql .= $this->getParseSelect("dateAcquisitionMateriel",$params);
			$sql .= $this->getParseSelect("dateFinMateriel",$params);
			$sql .= $this->getParseSelect("estActifMateriel",$params);
			$sql .= $this->getParseSelect("pahtMateriel",$params);
			$sql .= $this->getParseSelect("pvhtMateriel",$params);
			$sql .= $this->getParseSelect("commentaireMateriel",$params);
			$sql .= $this->getParseSelect("idproduit",$params);
			$sql .= $this->getParseSelect("gamme_produit_idgamme_produit",$params);
			$sql .= $this->getParseSelect("marque_idmarque",$params);
			$sql .= $this->getParseSelect("referenceInterneProduit",$params);
			$sql .= $this->getParseSelect("nomProduit",$params);
			$sql .= $this->getParseSelect("pahtProduit",$params);
			$sql .= $this->getParseSelect("pvhtProduit",$params);
			$sql .= $this->getParseSelect("commentaireProduit",$params);
			$sql .= $this->getParseSelect("estActifProduit",$params);
			$sql .= $this->getParseSelect("qteAlerteStockProduit",$params);
			$sql .= $this->getParseSelect("idsociete",$params);
			$sql .= $this->getParseSelect("nomSociete",$params);
			$sql .= $this->getParseSelect("siretSociete",$params);
			$sql .= $this->getParseSelect("sirenSociete",$params);
			$sql .= $this->getParseSelect("apeSociete",$params);
			$sql .= $this->getParseSelect("tvaIntraSociete",$params);
			$sql .= $this->getParseSelect("formeJuridiqueSociete",$params);
			$sql .= $this->getParseSelect("capitalSociete",$params);
			$sql .= $this->getParseSelect("deviseCapitalSociete",$params);
			$sql .= $this->getParseSelect("deviseFacturationSociete",$params);
			$sql .= $this->getParseSelect("activiteSociete",$params);
			$sql .= $this->getParseSelect("chiffreAffaireSociete",$params);
			$sql .= $this->getParseSelect("nbSalarieSociete",$params);
			$sql .= $this->getParseSelect("beneficeSocitete",$params);
			$sql .= $this->getParseSelect("estFacturableSociete",$params);
			$sql .= $this->getParseSelect("idclient_is_societe",$params);
			$sql .= $this->getParseSelect("societe_idsociete",$params);
			$sql .= $this->getParseSelect("idadresse",$params);
			$sql .= $this->getParseSelect("adresse1",$params);
			$sql .= $this->getParseSelect("adresse2",$params);
			$sql .= $this->getParseSelect("codePostalAdresse",$params);
			$sql .= $this->getParseSelect("villeAdresse",$params);
			$sql .= $this->getParseSelect("pays",$params);
			$sql .= $this->getParseSelect("commentaireAdresse",$params);
			$sql .= $this->getParseSelect("groupe_agent_idgroupe_agent",$params);
			$sql .= $this->getParseSelect("categorie_produit_idcategorie_produit",$params);
			$sql .= $this->getParseSelect("idcategorie_produit",$params);
			$sql .= $this->getParseSelect("nomCategorie_produit",$params);
			$sql .= $this->getParseSelect("codeCategorie_produit",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneLocationClientHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_ligne_location_client_has_agent where 1 " ;
		$sql .= $this->getParseSearch("idagent",$params);
		$sql .= $this->getParseSearch("personne_idpersonne",$params);
		$sql .= $this->getParseSearch("loginAgent",$params);
		$sql .= $this->getParseSearch("passwordAgent",$params);
		$sql .= $this->getParseSearch("dateCreationAgent",$params);
		$sql .= $this->getParseSearch("dateFinAgent",$params);
		$sql .= $this->getParseSearch("estActifAgent",$params);
		$sql .= $this->getParseSearch("dateDebutAgent",$params);
		$sql .= $this->getParseSearch("idligne_location_client_has_agent",$params);
		$sql .= $this->getParseSearch("ligne_location_client_idligne_location_client",$params);
		$sql .= $this->getParseSearch("agent_id_agent",$params);
		$sql .= $this->getParseSearch("nomPersonne",$params);
		$sql .= $this->getParseSearch("prenomPersonne",$params);
		$sql .= $this->getParseSearch("sexePersonne",$params);
		$sql .= $this->getParseSearch("idligne_location_client",$params);
		$sql .= $this->getParseSearch("localisation_idlocalisation",$params);
		$sql .= $this->getParseSearch("materiel_idmateriel",$params);
		$sql .= $this->getParseSearch("location_client_idlocation_client",$params);
		$sql .= $this->getParseSearch("dateCreationLigne_location_client",$params);
		$sql .= $this->getParseSearch("dateDebutLigne_location_client",$params);
		$sql .= $this->getParseSearch("dateClotureLigne_location_client",$params);
		$sql .= $this->getParseSearch("dateFinLigne_location_client",$params);
		$sql .= $this->getParseSearch("dureeLigne_location_client",$params);
		$sql .= $this->getParseSearch("uniteDureeLigne_location_client",$params);
		$sql .= $this->getParseSearch("pvhtLigne_location_client",$params);
		$sql .= $this->getParseSearch("idlocation_client",$params);
		$sql .= $this->getParseSearch("type_location_client_idtype_location_client",$params);
		$sql .= $this->getParseSearch("client_idclient",$params);
		$sql .= $this->getParseSearch("numeroLocation_client",$params);
		$sql .= $this->getParseSearch("dateCreationLocation_client",$params);
		$sql .= $this->getParseSearch("dateDebutLocation_client",$params);
		$sql .= $this->getParseSearch("dateFinLocation_client",$params);
		$sql .= $this->getParseSearch("dateClotureLocation_client",$params);
		$sql .= $this->getParseSearch("dureeTrimLocation_client",$params);
		$sql .= $this->getParseSearch("loyerTrimLocation_client",$params);
		$sql .= $this->getParseSearch("idmateriel",$params);
		$sql .= $this->getParseSearch("stock_materiel_idstock_materiel",$params);
		$sql .= $this->getParseSearch("produit_idproduit",$params);
		$sql .= $this->getParseSearch("numeroSerieInterneMateriel",$params);
		$sql .= $this->getParseSearch("numeroSerieExterneMateriel",$params);
		$sql .= $this->getParseSearch("dateCreationMateriel",$params);
		$sql .= $this->getParseSearch("dateAcquisitionMateriel",$params);
		$sql .= $this->getParseSearch("dateFinMateriel",$params);
		$sql .= $this->getParseSearch("estActifMateriel",$params);
		$sql .= $this->getParseSearch("pahtMateriel",$params);
		$sql .= $this->getParseSearch("pvhtMateriel",$params);
		$sql .= $this->getParseSearch("commentaireMateriel",$params);
		$sql .= $this->getParseSearch("idproduit",$params);
		$sql .= $this->getParseSearch("gamme_produit_idgamme_produit",$params);
		$sql .= $this->getParseSearch("marque_idmarque",$params);
		$sql .= $this->getParseSearch("referenceInterneProduit",$params);
		$sql .= $this->getParseSearch("nomProduit",$params);
		$sql .= $this->getParseSearch("pahtProduit",$params);
		$sql .= $this->getParseSearch("pvhtProduit",$params);
		$sql .= $this->getParseSearch("commentaireProduit",$params);
		$sql .= $this->getParseSearch("estActifProduit",$params);
		$sql .= $this->getParseSearch("qteAlerteStockProduit",$params);
		$sql .= $this->getParseSearch("idsociete",$params);
		$sql .= $this->getParseSearch("nomSociete",$params);
		$sql .= $this->getParseSearch("siretSociete",$params);
		$sql .= $this->getParseSearch("sirenSociete",$params);
		$sql .= $this->getParseSearch("apeSociete",$params);
		$sql .= $this->getParseSearch("tvaIntraSociete",$params);
		$sql .= $this->getParseSearch("formeJuridiqueSociete",$params);
		$sql .= $this->getParseSearch("capitalSociete",$params);
		$sql .= $this->getParseSearch("deviseCapitalSociete",$params);
		$sql .= $this->getParseSearch("deviseFacturationSociete",$params);
		$sql .= $this->getParseSearch("activiteSociete",$params);
		$sql .= $this->getParseSearch("chiffreAffaireSociete",$params);
		$sql .= $this->getParseSearch("nbSalarieSociete",$params);
		$sql .= $this->getParseSearch("beneficeSocitete",$params);
		$sql .= $this->getParseSearch("estFacturableSociete",$params);
		$sql .= $this->getParseSearch("idclient_is_societe",$params);
		$sql .= $this->getParseSearch("societe_idsociete",$params);
		$sql .= $this->getParseSearch("idadresse",$params);
		$sql .= $this->getParseSearch("adresse1",$params);
		$sql .= $this->getParseSearch("adresse2",$params);
		$sql .= $this->getParseSearch("codePostalAdresse",$params);
		$sql .= $this->getParseSearch("villeAdresse",$params);
		$sql .= $this->getParseSearch("pays",$params);
		$sql .= $this->getParseSearch("commentaireAdresse",$params);
		$sql .= $this->getParseSearch("groupe_agent_idgroupe_agent",$params);
		$sql .= $this->getParseSearch("categorie_produit_idcategorie_produit",$params);
		$sql .= $this->getParseSearch("idcategorie_produit",$params);
		$sql .= $this->getParseSearch("nomCategorie_produit",$params);
		$sql .= $this->getParseSearch("codeCategorie_produit",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneLocationClientHasAgent(){ 
		$sql="SELECT * FROM  ligne_location_client_has_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneLocationClientHasAgent($name="idligne_location_client_has_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_location_client_has_agent , idligne_location_client_has_agent FROM WHERE 1 "; $sql = "";
	
		$sql .= $this->getParseSelect("idligne_location_client_has_agent",$params);
		$sql .= $this->getParseSelect("ligne_location_client_idligne_location_client",$params);
		$sql .= $this->getParseSelect("agent_id_agent",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_location_client_has_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneLocationClientHasAgent($name="idligne_location_client_has_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_location_client_has_agent , idligne_location_client_has_agent FROM ligne_location_client_has_agent ORDER BY nomLigne_location_client_has_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLigneLocationClientHasAgent = new LigneLocationClientHasAgent(); ?>