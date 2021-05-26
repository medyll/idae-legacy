<?   
class Fetes
{
	var $conn = null; 
	function Fetes(){ 
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
	
	function createFetes($params){ 
		$this->conn->AutoExecute("fetes", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateFetes($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("fetes", $params, "UPDATE", "idfetes = ".$idfetes); 
	}
	
	function deleteFetes($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  fetes WHERE idfetes = $idfetes"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneFetes($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from fetes where 1 " ;
			$sql .= $this->getParseSelect("fet_mois",$params);
			$sql .= $this->getParseSelect("fet_jour",$params);
			$sql .= $this->getParseSelect("fet_nom",$params);
			$sql .= $this->getParseSelect("idfetes",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneFetes($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from fetes where 1 " ;
		$sql .= $this->getParseSearch("fet_mois",$params);
		$sql .= $this->getParseSearch("fet_jour",$params);
		$sql .= $this->getParseSearch("fet_nom",$params);
		$sql .= $this->getParseSearch("idfetes",$params);
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllFetes(){ 
		$sql="SELECT * FROM  fetes"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneFetes($name="idfetes",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomFetes , idfetes FROM WHERE 1 "; $sql = "";
	
		$sql .= $this->getParseSelect("fet_mois",$params);
		$sql .= $this->getParseSelect("fet_jour",$params);
		$sql .= $this->getParseSelect("fet_nom",$params);
		$sql .= $this->getParseSelect("idfetes",$params);
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomFetes ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectFetes($name="idfetes",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomFetes , idfetes FROM fetes ORDER BY nomFetes ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassFetes = new Fetes(); ?>