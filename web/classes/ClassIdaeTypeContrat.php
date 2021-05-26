<?   
class IdaeTypeContrat
{
	var $conn = null;
	function IdaeTypeContrat(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeTypeContrat($params){ 
		$this->conn->AutoExecute("typecontrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("typecontrat", $params, "UPDATE", "idtypeContrat = ".$idtypeContrat); 
	}
	
	function deleteIdaeTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  typecontrat WHERE idtypeContrat = $idtypeContrat"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from typecontrat where 1 " ;
			if(!empty($idtypeContrat)){ $sql .= " AND idtypeContrat ".sqlIn($idtypeContrat) ; }
			if(!empty($noidtypeContrat)){ $sql .= " AND idtypeContrat ".sqlNotIn($noidtypeContrat) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlIn($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotIn($notypeContrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from typecontrat where 1 " ;
			if(!empty($idtypeContrat)){ $sql .= " AND idtypeContrat ".sqlSearch($idtypeContrat) ; }
			if(!empty($noidtypeContrat)){ $sql .= " AND idtypeContrat ".sqlNotSearch($noidtypeContrat) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlSearch($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotSearch($notypeContrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeTypeContrat(){ 
		$sql="SELECT * FROM  typecontrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeTypeContrat($name="idtypecontrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomTypecontrat , idtypecontrat FROM typecontrat WHERE  1 ";  
			if(!empty($idtypeContrat)){ $sql .= " AND idtypeContrat ".sqlIn($idtypeContrat) ; }
			if(!empty($noidtypeContrat)){ $sql .= " AND idtypeContrat ".sqlNotIn($noidtypeContrat) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlIn($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotIn($notypeContrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomTypecontrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeTypeContrat($name="idtypecontrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomTypecontrat , idtypecontrat FROM typecontrat ORDER BY nomTypecontrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeTypeContrat = new IdaeTypeContrat(); ?>