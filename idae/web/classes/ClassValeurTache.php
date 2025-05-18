<?   
class ValeurTache
{
	var $conn = null; 
	function ValeurTache(){ 
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
	
	function createValeurTache($params){ 
		$this->conn->AutoExecute("valeur_tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateValeurTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("valeur_tache", $params, "UPDATE", "idvaleur_tache = ".$idvaleur_tache); 
	}
	
	function deleteValeurTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  valeur_tache WHERE idvaleur_tache = $idvaleur_tache"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneValeurTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from valeur_tache where 1 " ;
			$sql .= $this->getParseSelect("idvaleur_tache",$params);
			$sql .= $this->getParseSelect("type_suivi_idtype_suivi",$params);
			$sql .= $this->getParseSelect("nomValeur_tache",$params);
			$sql .= $this->getParseSelect("ordreValeur_tache",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneValeurTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from valeur_tache where 1 " ;
		$sql .= $this->getParseSearch("idvaleur_tache",$params);
		$sql .= $this->getParseSearch("type_suivi_idtype_suivi",$params);
		$sql .= $this->getParseSearch("nomValeur_tache",$params);
		$sql .= $this->getParseSearch("ordreValeur_tache",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllValeurTache(){ 
		$sql="SELECT * FROM  valeur_tache"; 

		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneValeurTache($name="idvaleur_tache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from valeur_tache where 1 " ;
		$sql .= $this->getParseSelect("idvaleur_tache",$params);
		$sql .= $this->getParseSelect("type_suivi_idtype_suivi",$params);
		$sql .= $this->getParseSelect("nomValeur_tache",$params);
		$sql .= $this->getParseSelect("ordreValeur_tache",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomValeur_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectValeurTache($name="idvaleur_tache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomValeur_tache , idvaleur_tache FROM valeur_tache ORDER BY nomValeur_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassValeurTache = new ValeurTache(); ?>