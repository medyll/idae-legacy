<?   
class GroupeAgentHasFils
{
	var $conn = null;
	function GroupeAgentHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createGroupeAgentHasFils($params){ 
		$this->conn->AutoExecute("groupe_agent_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateGroupeAgentHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("groupe_agent_has_fils", $params, "UPDATE", "idgroupe_agent_has_fils = ".$idgroupe_agent_has_fils); 
	}
	
	function deleteGroupeAgentHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  groupe_agent_has_fils WHERE idgroupe_agent_has_fils = $idgroupe_agent_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneGroupeAgentHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from groupe_agent_has_fils where 1 " ;
			if(!empty($idgroupe_agent_has_fils)){ $sql .= " AND idgroupe_agent_has_fils ".sqlIn($idgroupe_agent_has_fils) ; }
			if(!empty($noidgroupe_agent_has_fils)){ $sql .= " AND idgroupe_agent_has_fils ".sqlNotIn($noidgroupe_agent_has_fils) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($idfilsgroupe_agent)){ $sql .= " AND idfilsgroupe_agent ".sqlIn($idfilsgroupe_agent) ; }
			if(!empty($noidfilsgroupe_agent)){ $sql .= " AND idfilsgroupe_agent ".sqlNotIn($noidfilsgroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllGroupeAgentHasFils(){ 
		$sql="SELECT * FROM  groupe_agent_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneGroupeAgentHasFils($name="idgroupe_agent_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomGroupe_agent_has_fils , idgroupe_agent_has_fils FROM WHERE 1 ";  
			if(!empty($idgroupe_agent_has_fils)){ $sql .= " AND idgroupe_agent_has_fils ".sqlIn($idgroupe_agent_has_fils) ; }
			if(!empty($noidgroupe_agent_has_fils)){ $sql .= " AND idgroupe_agent_has_fils ".sqlNotIn($noidgroupe_agent_has_fils) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($idfilsgroupe_agent)){ $sql .= " AND idfilsgroupe_agent ".sqlIn($idfilsgroupe_agent) ; }
			if(!empty($noidfilsgroupe_agent)){ $sql .= " AND idfilsgroupe_agent ".sqlNotIn($noidfilsgroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomGroupe_agent_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectGroupeAgentHasFils($name="idgroupe_agent_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomGroupe_agent_has_fils , idgroupe_agent_has_fils FROM groupe_agent_has_fils ORDER BY nomGroupe_agent_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassGroupeAgentHasFils = new GroupeAgentHasFils(); ?>