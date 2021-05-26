<?   
class ContratHasCritereContrat
{
	var $conn = null; 
	function ContratHasCritereContrat(){ 
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
	
	function createContratHasCritereContrat($params){ 
		$this->conn->AutoExecute("contrat_has_critere_contrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateContratHasCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("contrat_has_critere_contrat", $params, "UPDATE", "idcontrat_has_critere_contrat = ".$idcontrat_has_critere_contrat); 
	}
	
	function truncate(){ 
		$sql="truncate table contrat_has_critere_contrat  "; 
		return $this->conn->Execute($sql); 	
	}
	
	function deleteContratHasCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  contrat_has_critere_contrat WHERE idcontrat_has_critere_contrat = $idcontrat_has_critere_contrat"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneContratHasCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_contrat_has_critere_contrat where 1 " ;
			$sql .= $this->getParseSelect("idcontrat",$params);
			$sql .= $this->getParseSelect("numContrat",$params);
			$sql .= $this->getParseSelect("dureeContrat",$params);
			$sql .= $this->getParseSelect("dateDebutContrat",$params);
			$sql .= $this->getParseSelect("dateFinContrat",$params);
			$sql .= $this->getParseSelect("dateClotureContrat",$params);
			$sql .= $this->getParseSelect("idcontrat_has_critere_contrat",$params);
			$sql .= $this->getParseSelect("contrat_idcontrat",$params);
			$sql .= $this->getParseSelect("valeurContrat_has_critere_contrat",$params);
			$sql .= $this->getParseSelect("idcritere_contrat",$params);
			$sql .= $this->getParseSelect("critere_contrat_idcritere_contrat",$params);
			$sql .= $this->getParseSelect("ordreCritere_contrat",$params);
			$sql .= $this->getParseSelect("codeCritere_contrat",$params);
			$sql .= $this->getParseSelect("uniteCritere_contrat",$params);
			$sql .= $this->getParseSelect("nomCritere_contrat",$params);
			$sql .= $this->getParseSelect("typeChampCritere_contrat",$params);
			$sql .= $this->getParseSelect("typeSaisieCritereContrat",$params);
			$sql .= $this->getParseSelect("idcontrat_old",$params);
			$sql .= $this->getParseSelect("type_contrat_idtype_contrat",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneContratHasCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_contrat_has_critere_contrat where 1 " ;
		$sql .= $this->getParseSearch("idcontrat",$params);
		$sql .= $this->getParseSearch("numContrat",$params);
		$sql .= $this->getParseSearch("dureeContrat",$params);
		$sql .= $this->getParseSearch("dateDebutContrat",$params);
		$sql .= $this->getParseSearch("dateFinContrat",$params);
		$sql .= $this->getParseSearch("dateClotureContrat",$params);
		$sql .= $this->getParseSearch("idcontrat_has_critere_contrat",$params);
		$sql .= $this->getParseSearch("contrat_idcontrat",$params);
		$sql .= $this->getParseSearch("valeurContrat_has_critere_contrat",$params);
		$sql .= $this->getParseSearch("idcritere_contrat",$params);
		$sql .= $this->getParseSearch("critere_contrat_idcritere_contrat",$params);
		$sql .= $this->getParseSearch("ordreCritere_contrat",$params);
		$sql .= $this->getParseSearch("codeCritere_contrat",$params);
		$sql .= $this->getParseSearch("uniteCritere_contrat",$params);
		$sql .= $this->getParseSearch("nomCritere_contrat",$params);
		$sql .= $this->getParseSearch("typeChampCritere_contrat",$params);
		$sql .= $this->getParseSearch("typeSaisieCritereContrat",$params);
		$sql .= $this->getParseSearch("idcontrat_old",$params);
		$sql .= $this->getParseSearch("type_contrat_idtype_contrat",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllContratHasCritereContrat(){ 
		$sql="SELECT * FROM  contrat_has_critere_contrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneContratHasCritereContrat($name="idcontrat_has_critere_contrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomContrat_has_critere_contrat , idcontrat_has_critere_contrat FROM WHERE 1 "; $sql = "";
	
		$sql .= $this->getParseSelect("idcontrat_has_critere_contrat",$params);
		$sql .= $this->getParseSelect("contrat_idcontrat",$params);
		$sql .= $this->getParseSelect("critere_contrat_idcritere_contrat",$params);
		$sql .= $this->getParseSelect("valeurContrat_has_critere_contrat",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomContrat_has_critere_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectContratHasCritereContrat($name="idcontrat_has_critere_contrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomContrat_has_critere_contrat , idcontrat_has_critere_contrat FROM contrat_has_critere_contrat ORDER BY nomContrat_has_critere_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassContratHasCritereContrat = new ContratHasCritereContrat(); ?>