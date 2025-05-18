<?   
class TacheHasMateriel
{
	var $conn = null; 
	function TacheHasMateriel(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getParseSelect($field,$params){  
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			//if(is_array($params[$field])){$params = array_filter($params[$field]); }
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
		return $sql;				
	}
	
	function getParseSearch($field,$params){ 
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			//if(is_array($params[$field])){$params = array_filter($params[$field]); }
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
		return $sql;				
	}
	
	function createTacheHasMateriel($params){ 
		$this->conn->AutoExecute("tache_has_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTacheHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("tache_has_materiel", $params, "UPDATE", "idtache_has_materiel = ".$idtache_has_materiel); 
	}
	
	function deleteTacheHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  tache_has_materiel WHERE idtache_has_materiel = $idtache_has_materiel"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTacheHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_tache_has_materiel where 1 " ;
			$sql .= $this->getParseSelect("idtache",$params);
			$sql .= $this->getParseSelect("suivi_idsuivi",$params);
			$sql .= $this->getParseSelect("statut_tache_idstatut_tache",$params);
			$sql .= $this->getParseSelect("type_tache_idtype_tache",$params);
			$sql .= $this->getParseSelect("objetTache",$params);
			$sql .= $this->getParseSelect("dateCreationTache",$params);
			$sql .= $this->getParseSelect("heureDebutTache",$params);
			$sql .= $this->getParseSelect("heureCreationTache",$params);
			$sql .= $this->getParseSelect("dateDebutTache",$params);
			$sql .= $this->getParseSelect("heureFinTache",$params);
			$sql .= $this->getParseSelect("dateFinTache",$params);
			$sql .= $this->getParseSelect("commentaireTache",$params);
			$sql .= $this->getParseSelect("valeurTache",$params);
			$sql .= $this->getParseSelect("resultatTache",$params);
			$sql .= $this->getParseSelect("migrateidtache",$params);
			$sql .= $this->getParseSelect("migrateidtacheTech",$params);
			$sql .= $this->getParseSelect("idagent_writer",$params);
			$sql .= $this->getParseSelect("idmateriel",$params);
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
			$sql .= $this->getParseSelect("materiel_idmateriel",$params);
			$sql .= $this->getParseSelect("tache_idtache",$params);
			$sql .= $this->getParseSelect("nomProduit",$params);
			$sql .= $this->getParseSelect("idproduit",$params);
			$sql .= $this->getParseSelect("gamme_produit_idgamme_produit",$params);
			$sql .= $this->getParseSelect("marque_idmarque",$params);
			$sql .= $this->getParseSelect("referenceInterneProduit",$params);
			$sql .= $this->getParseSelect("pahtProduit",$params);
			$sql .= $this->getParseSelect("pvhtProduit",$params);
			$sql .= $this->getParseSelect("commentaireProduit",$params);
			$sql .= $this->getParseSelect("estActifProduit",$params);
			$sql .= $this->getParseSelect("qteAlerteStockProduit",$params);
			$sql .= $this->getParseSelect("nomStatut_tache",$params);
			$sql .= $this->getParseSelect("valeurStatut_tache",$params);
			$sql .= $this->getParseSelect("ordreStatut_tache",$params);
			$sql .= $this->getParseSelect("codeStatut_tache",$params);
			$sql .= $this->getParseSelect("codeType_tache",$params);
			$sql .= $this->getParseSelect("nomType_tache",$params);
			$sql .= $this->getParseSelect("ordreType_tache",$params);
			$sql .= $this->getParseSelect("type_suivi_idtype_suivi",$params);
			$sql .= $this->getParseSelect("codeType_suivi",$params);
			$sql .= $this->getParseSelect("idtype_suivi",$params);
			$sql .= $this->getParseSelect("nomType_suivi",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTacheHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_tache_has_materiel where 1 " ;
		$sql .= $this->getParseSearch("idtache",$params);
		$sql .= $this->getParseSearch("suivi_idsuivi",$params);
		$sql .= $this->getParseSearch("statut_tache_idstatut_tache",$params);
		$sql .= $this->getParseSearch("type_tache_idtype_tache",$params);
		$sql .= $this->getParseSearch("objetTache",$params);
		$sql .= $this->getParseSearch("dateCreationTache",$params);
		$sql .= $this->getParseSearch("heureDebutTache",$params);
		$sql .= $this->getParseSearch("heureCreationTache",$params);
		$sql .= $this->getParseSearch("dateDebutTache",$params);
		$sql .= $this->getParseSearch("heureFinTache",$params);
		$sql .= $this->getParseSearch("dateFinTache",$params);
		$sql .= $this->getParseSearch("commentaireTache",$params);
		$sql .= $this->getParseSearch("valeurTache",$params);
		$sql .= $this->getParseSearch("resultatTache",$params);
		$sql .= $this->getParseSearch("migrateidtache",$params);
		$sql .= $this->getParseSearch("migrateidtacheTech",$params);
		$sql .= $this->getParseSearch("idagent_writer",$params);
		$sql .= $this->getParseSearch("idmateriel",$params);
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
		$sql .= $this->getParseSearch("materiel_idmateriel",$params);
		$sql .= $this->getParseSearch("tache_idtache",$params);
		$sql .= $this->getParseSearch("nomProduit",$params);
		$sql .= $this->getParseSearch("idproduit",$params);
		$sql .= $this->getParseSearch("gamme_produit_idgamme_produit",$params);
		$sql .= $this->getParseSearch("marque_idmarque",$params);
		$sql .= $this->getParseSearch("referenceInterneProduit",$params);
		$sql .= $this->getParseSearch("pahtProduit",$params);
		$sql .= $this->getParseSearch("pvhtProduit",$params);
		$sql .= $this->getParseSearch("commentaireProduit",$params);
		$sql .= $this->getParseSearch("estActifProduit",$params);
		$sql .= $this->getParseSearch("qteAlerteStockProduit",$params);
		$sql .= $this->getParseSearch("nomStatut_tache",$params);
		$sql .= $this->getParseSearch("valeurStatut_tache",$params);
		$sql .= $this->getParseSearch("ordreStatut_tache",$params);
		$sql .= $this->getParseSearch("codeStatut_tache",$params);
		$sql .= $this->getParseSearch("codeType_tache",$params);
		$sql .= $this->getParseSearch("nomType_tache",$params);
		$sql .= $this->getParseSearch("ordreType_tache",$params);
		$sql .= $this->getParseSearch("type_suivi_idtype_suivi",$params);
		$sql .= $this->getParseSearch("codeType_suivi",$params);
		$sql .= $this->getParseSearch("idtype_suivi",$params);
		$sql .= $this->getParseSearch("nomType_suivi",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTacheHasMateriel(){ 
		$sql="SELECT * FROM  tache_has_materiel"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTacheHasMateriel($name="idtache_has_materiel",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomTache_Has_Materiel , idtache_has_materiel FROM WHERE 1 ";  
	
		$sql .= $this->getParseSelect("idtache_has_materiel",$params);
		$sql .= $this->getParseSelect("materiel_idmateriel",$params);
		$sql .= $this->getParseSelect("tache_idtache",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomTache_Has_Materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTacheHasMateriel($name="idtache_has_materiel",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomTache_Has_Materiel , idtache_has_materiel FROM tache_has_materiel ORDER BY nomTache_Has_Materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTacheHasMateriel = new TacheHasMateriel(); ?>