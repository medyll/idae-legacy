<?   
class GroupeAgentHasDroit
{
	var $conn = null;
	function GroupeAgentHasDroit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createGroupeAgentHasDroit($params){ 
		$this->conn->AutoExecute("groupe_agent_has_droit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateGroupeAgentHasDroit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("groupe_agent_has_droit", $params, "UPDATE", "idgroupe_agent_has_droit = ".$idgroupe_agent_has_droit); 
	}
	
	function deleteGroupeAgentHasDroit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  groupe_agent_has_droit WHERE idgroupe_agent_has_droit = $idgroupe_agent_has_droit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneGroupeAgentHasDroit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from groupe_agent_has_droit where 1 " ;
			if(!empty($idgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlIn($idgroupe_agent_has_droit) ; }
			if(!empty($noidgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlNotIn($noidgroupe_agent_has_droit) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($droit_iddroit)){ $sql .= " AND droit_iddroit ".sqlIn($droit_iddroit) ; }
			if(!empty($nodroit_iddroit)){ $sql .= " AND droit_iddroit ".sqlNotIn($nodroit_iddroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneGroupeAgentHasDroit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from groupe_agent_has_droit where 1 " ;
			if(!empty($idgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlSearch($idgroupe_agent_has_droit) ; }
			if(!empty($noidgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlNotSearch($noidgroupe_agent_has_droit) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlSearch($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotSearch($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($droit_iddroit)){ $sql .= " AND droit_iddroit ".sqlSearch($droit_iddroit) ; }
			if(!empty($nodroit_iddroit)){ $sql .= " AND droit_iddroit ".sqlNotSearch($nodroit_iddroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllGroupeAgentHasDroit(){ 
		$sql="SELECT * FROM  groupe_agent_has_droit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneGroupeAgentHasDroit($name="idgroupe_agent_has_droit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomGroupe_agent_has_droit , idgroupe_agent_has_droit FROM groupe_agent_has_droit WHERE  1 ";  
			if(!empty($idgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlIn($idgroupe_agent_has_droit) ; }
			if(!empty($noidgroupe_agent_has_droit)){ $sql .= " AND idgroupe_agent_has_droit ".sqlNotIn($noidgroupe_agent_has_droit) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($droit_iddroit)){ $sql .= " AND droit_iddroit ".sqlIn($droit_iddroit) ; }
			if(!empty($nodroit_iddroit)){ $sql .= " AND droit_iddroit ".sqlNotIn($nodroit_iddroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomGroupe_agent_has_droit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectGroupeAgentHasDroit($name="idgroupe_agent_has_droit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomGroupe_agent_has_droit , idgroupe_agent_has_droit FROM groupe_agent_has_droit ORDER BY nomGroupe_agent_has_droit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassGroupeAgentHasDroit = new GroupeAgentHasDroit(); ?>