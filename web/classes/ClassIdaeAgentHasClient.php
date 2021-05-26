<?   
class IdaeAgentHasClient
{
	var $conn = null;
	function IdaeAgentHasClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeAgentHasClient($params){ 
		$this->conn->AutoExecute("vue_agent_has_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_agent_has_client", $params, "UPDATE", "idvue_agent_has_client = ".$idvue_agent_has_client); 
	}
	
	function deleteIdaeAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_agent_has_client WHERE idvue_agent_has_client = $idvue_agent_has_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent_has_client where 1 " ;
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent_has_client where 1 " ;
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeAgentHasClient(){ 
		$sql="SELECT * FROM  vue_agent_has_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeAgentHasClient($name="idvue_agent_has_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_agent_has_client , idvue_agent_has_client FROM vue_agent_has_client WHERE  1 ";  
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_agent_has_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeAgentHasClient($name="idvue_agent_has_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_agent_has_client , idvue_agent_has_client FROM vue_agent_has_client ORDER BY nomVue_agent_has_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeAgentHasClient = new IdaeAgentHasClient(); ?>