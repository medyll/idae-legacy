<?   
class ContratHasFournisseur
{
	var $conn = null; 
	function ContratHasFournisseur(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getParseSelect($field,$params){ 
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			if(is_array($params[$field])){$params = array_filter($params[$field]); }
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
			if(is_array($params[$field])){$params = array_filter($params[$field]); }
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
	
	function createContratHasFournisseur($params){ 
		$this->conn->AutoExecute("contrat_has_fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateContratHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("contrat_has_fournisseur", $params, "UPDATE", "idcontrat_has_fournisseur = ".$idcontrat_has_fournisseur); 
	}
	function truncate(){ 
		$sql="truncate table contrat_has_fournisseur "; 
		return $this->conn->Execute($sql); 	
	}
	function deleteContratHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  contrat_has_fournisseur WHERE idcontrat_has_fournisseur = $idcontrat_has_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneContratHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_contrat_has_fournisseur where 1 " ;
			$sql .= $this->getParseSelect("idcontrat_has_fournisseur",$params);
			$sql .= $this->getParseSelect("contrat_idcontrat",$params);
			$sql .= $this->getParseSelect("idcontrat_old",$params);
			$sql .= $this->getParseSelect("dateClotureContrat",$params);
			$sql .= $this->getParseSelect("dateFinContrat",$params);
			$sql .= $this->getParseSelect("dateDebutContrat",$params);
			$sql .= $this->getParseSelect("dureeContrat",$params);
			$sql .= $this->getParseSelect("numContrat",$params);
			$sql .= $this->getParseSelect("type_contrat_idtype_contrat",$params);
			$sql .= $this->getParseSelect("dateCreationFournisseur",$params);
			$sql .= $this->getParseSelect("commentaireFournisseur",$params);
			$sql .= $this->getParseSelect("fournisseur_idfournisseur",$params);
			$sql .= $this->getParseSelect("societe_idsociete",$params);
			$sql .= $this->getParseSelect("nomSociete",$params);
			$sql .= $this->getParseSelect("nomType_contrat",$params);
			$sql .= $this->getParseSelect("codeType_contrat",$params);
			$sql .= $this->getParseSelect("ordreType_contrat",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneContratHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_contrat_has_fournisseur where 1 " ;
		$sql .= $this->getParseSearch("idcontrat_has_fournisseur",$params);
		$sql .= $this->getParseSearch("contrat_idcontrat",$params);
		$sql .= $this->getParseSearch("idcontrat_old",$params);
		$sql .= $this->getParseSearch("dateClotureContrat",$params);
		$sql .= $this->getParseSearch("dateFinContrat",$params);
		$sql .= $this->getParseSearch("dateDebutContrat",$params);
		$sql .= $this->getParseSearch("dureeContrat",$params);
		$sql .= $this->getParseSearch("numContrat",$params);
		$sql .= $this->getParseSearch("type_contrat_idtype_contrat",$params);
		$sql .= $this->getParseSearch("dateCreationFournisseur",$params);
		$sql .= $this->getParseSearch("commentaireFournisseur",$params);
		$sql .= $this->getParseSearch("fournisseur_idfournisseur",$params);
		$sql .= $this->getParseSearch("societe_idsociete",$params);
		$sql .= $this->getParseSearch("nomSociete",$params);
		$sql .= $this->getParseSearch("nomType_contrat",$params);
		$sql .= $this->getParseSearch("codeType_contrat",$params);
		$sql .= $this->getParseSearch("ordreType_contrat",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllContratHasFournisseur(){ 
		$sql="SELECT * FROM  contrat_has_fournisseur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneContratHasFournisseur($name="idcontrat_has_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomContrat_has_fournisseur , idcontrat_has_fournisseur FROM WHERE 1 "; $sql = "";
	
		$sql .= $this->getParseSelect("idcontrat_has_fournisseur",$params);
		$sql .= $this->getParseSelect("contrat_idcontrat",$params);
		$sql .= $this->getParseSelect("fournisseur_idfournisseur",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomContrat_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectContratHasFournisseur($name="idcontrat_has_fournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomContrat_has_fournisseur , idcontrat_has_fournisseur FROM contrat_has_fournisseur ORDER BY nomContrat_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassContratHasFournisseur = new ContratHasFournisseur(); ?>