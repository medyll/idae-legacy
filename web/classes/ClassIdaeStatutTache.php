<?   
class IdaeStatutTache
{
	var $conn = null;
	function IdaeStatutTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeStatutTache($params){ 
		$this->conn->AutoExecute("statustaches", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("statustaches", $params, "UPDATE", "idstatustaches = ".$idstatustaches); 
	}
	
	function deleteIdaeStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  statustaches WHERE idstatustaches = $idstatustaches"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from statustaches where 1 " ;
			if(!empty($idstatus)){ $sql .= " AND idstatus ".sqlIn($idstatus) ; }
			if(!empty($noidstatus)){ $sql .= " AND idstatus ".sqlNotIn($noidstatus) ; }
			if(!empty($statusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlIn($statusTaches_2) ; }
			if(!empty($nostatusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlNotIn($nostatusTaches_2) ; }
			if(!empty($valeurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlIn($valeurStatusTaches) ; }
			if(!empty($novaleurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlNotIn($novaleurStatusTaches) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from statustaches where 1 " ;
			if(!empty($idstatus)){ $sql .= " AND idstatus ".sqlSearch($idstatus) ; }
			if(!empty($noidstatus)){ $sql .= " AND idstatus ".sqlNotSearch($noidstatus) ; }
			if(!empty($statusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlSearch($statusTaches_2) ; }
			if(!empty($nostatusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlNotSearch($nostatusTaches_2) ; }
			if(!empty($valeurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlSearch($valeurStatusTaches) ; }
			if(!empty($novaleurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlNotSearch($novaleurStatusTaches) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeStatutTache(){ 
		$sql="SELECT * FROM  statustaches"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeStatutTache($name="idstatustaches",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomStatustaches , idstatustaches FROM statustaches WHERE  1 ";  
			if(!empty($idstatus)){ $sql .= " AND idstatus ".sqlIn($idstatus) ; }
			if(!empty($noidstatus)){ $sql .= " AND idstatus ".sqlNotIn($noidstatus) ; }
			if(!empty($statusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlIn($statusTaches_2) ; }
			if(!empty($nostatusTaches_2)){ $sql .= " AND statusTaches_2 ".sqlNotIn($nostatusTaches_2) ; }
			if(!empty($valeurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlIn($valeurStatusTaches) ; }
			if(!empty($novaleurStatusTaches)){ $sql .= " AND valeurStatusTaches ".sqlNotIn($novaleurStatusTaches) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomStatustaches ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeStatutTache($name="idstatustaches",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomStatustaches , idstatustaches FROM statustaches ORDER BY nomStatustaches ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeStatutTache = new IdaeStatutTache(); ?>