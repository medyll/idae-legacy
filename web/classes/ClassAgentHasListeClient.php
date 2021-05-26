<?   
class AgentHasListeClient
{
	var $conn = null;
	function AgentHasListeClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasListeClient($params){ 
		$this->conn->AutoExecute("agent_has_liste_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasListeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_liste_client", $params, "UPDATE", "idagent_has_liste_client = ".$idagent_has_liste_client); 
	}
	
	function deleteAgentHasListeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent_has_liste_client WHERE idagent_has_liste_client = $idagent_has_liste_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneAgentHasListeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from agent_has_liste_client where 1 " ;
			if(!empty($idagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlIn($idagent_has_liste_client) ; }
			if(!empty($noidagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlNotIn($noidagent_has_liste_client) ; }
			if(!empty($liste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlIn($liste_client_idliste_client) ; }
			if(!empty($noliste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlNotIn($noliste_client_idliste_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAgentHasListeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from agent_has_liste_client where 1 " ;
			if(!empty($idagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlSearch($idagent_has_liste_client) ; }
			if(!empty($noidagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlNotSearch($noidagent_has_liste_client) ; }
			if(!empty($liste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlSearch($liste_client_idliste_client) ; }
			if(!empty($noliste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlNotSearch($noliste_client_idliste_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAgentHasListeClient(){ 
		$sql="SELECT * FROM  agent_has_liste_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAgentHasListeClient($name="idagent_has_liste_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAgent_has_liste_client , idagent_has_liste_client FROM agent_has_liste_client WHERE  1 ";  
			if(!empty($idagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlIn($idagent_has_liste_client) ; }
			if(!empty($noidagent_has_liste_client)){ $sql .= " AND idagent_has_liste_client ".sqlNotIn($noidagent_has_liste_client) ; }
			if(!empty($liste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlIn($liste_client_idliste_client) ; }
			if(!empty($noliste_client_idliste_client)){ $sql .= " AND liste_client_idliste_client ".sqlNotIn($noliste_client_idliste_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAgent_has_liste_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAgentHasListeClient($name="idagent_has_liste_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAgent_has_liste_client , idagent_has_liste_client FROM agent_has_liste_client ORDER BY nomAgent_has_liste_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAgentHasListeClient = new AgentHasListeClient(); ?>