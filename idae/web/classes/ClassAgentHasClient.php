<?   
class AgentHasClient
{
	var $conn = null;
	function AgentHasClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasClient($params){ 
		$this->conn->AutoExecute("agent_has_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_client", $params, "UPDATE", "idagent_has_client = ".$idagent_has_client); 
	}
	
	function deleteAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent_has_client WHERE idagent_has_client = $idagent_has_client"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE agent_has_client"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent_has_client where 1 " ;
			if(!empty($idagent_has_client)){ $sql .= " AND idagent_has_client ".sqlIn($idagent_has_client) ; }
			if(!empty($noidagent_has_client)){ $sql .= " AND idagent_has_client ".sqlNotIn($noidagent_has_client) ; }
			if(!empty($lt_idagent_has_client)){ $sql .= " AND idagent_has_client < '".$lt_idagent_has_client."'" ; }
			if(!empty($gt_idagent_has_client)){ $sql .= " AND idagent_has_client > '".$gt_idagent_has_client."'" ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($lt_nomPersonne)){ $sql .= " AND nomPersonne < '".$lt_nomPersonne."'" ; }
			if(!empty($gt_nomPersonne)){ $sql .= " AND nomPersonne > '".$gt_nomPersonne."'" ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($lt_prenomPersonne)){ $sql .= " AND prenomPersonne < '".$lt_prenomPersonne."'" ; }
			if(!empty($gt_prenomPersonne)){ $sql .= " AND prenomPersonne > '".$gt_prenomPersonne."'" ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($lt_estClientClient)){ $sql .= " AND estClientClient < '".$lt_estClientClient."'" ; }
			if(!empty($gt_estClientClient)){ $sql .= " AND estClientClient > '".$gt_estClientClient."'" ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; }
			if(!empty($lt_estProspectClient)){ $sql .= " AND estProspectClient < '".$lt_estProspectClient."'" ; }
			if(!empty($gt_estProspectClient)){ $sql .= " AND estProspectClient > '".$gt_estProspectClient."'" ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; }
			if(!empty($lt_estSuspectClient)){ $sql .= " AND estSuspectClient < '".$lt_estSuspectClient."'" ; }
			if(!empty($gt_estSuspectClient)){ $sql .= " AND estSuspectClient > '".$gt_estSuspectClient."'" ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; }
			if(!empty($lt_numeroClient)){ $sql .= " AND numeroClient < '".$lt_numeroClient."'" ; }
			if(!empty($gt_numeroClient)){ $sql .= " AND numeroClient > '".$gt_numeroClient."'" ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; }
			if(!empty($lt_dateCreationClient)){ $sql .= " AND dateCreationClient < '".$lt_dateCreationClient."'" ; }
			if(!empty($gt_dateCreationClient)){ $sql .= " AND dateCreationClient > '".$gt_dateCreationClient."'" ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlIn($nomGroupe_agent) ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlNotIn($nonomGroupe_agent) ; }
			if(!empty($lt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent < '".$lt_nomGroupe_agent."'" ; }
			if(!empty($gt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent > '".$gt_nomGroupe_agent."'" ; }
			if(!empty($idgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlIn($idgroupe_agent) ; }
			if(!empty($noidgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlNotIn($noidgroupe_agent) ; }
			if(!empty($lt_idgroupe_agent)){ $sql .= " AND idgroupe_agent < '".$lt_idgroupe_agent."'" ; }
			if(!empty($gt_idgroupe_agent)){ $sql .= " AND idgroupe_agent > '".$gt_idgroupe_agent."'" ; }
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			if(!empty($lt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent < '".$lt_codeGroupe_agent."'" ; }
			if(!empty($gt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent > '".$gt_codeGroupe_agent."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAgentHasClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent_has_client where 1 " ;
			if(!empty($idagent_has_client)){ $sql .= " AND idagent_has_client ".sqlSearch($idagent_has_client,"idagent_has_client") ; }
			if(!empty($lt_idagent_has_client)){ $sql .= " AND idagent_has_client < '".$lt_idagent_has_client."'" ; }
			if(!empty($gt_idagent_has_client)){ $sql .= " AND idagent_has_client > '".$gt_idagent_has_client."'" ; }
			if(!empty($noidagent_has_client)){ $sql .= " AND idagent_has_client ".sqlNotSearch($noidagent_has_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient,"client_idclient") ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent,"agent_idagent") ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlSearch($nomPersonne,"nomPersonne") ; }
			if(!empty($lt_nomPersonne)){ $sql .= " AND nomPersonne < '".$lt_nomPersonne."'" ; }
			if(!empty($gt_nomPersonne)){ $sql .= " AND nomPersonne > '".$gt_nomPersonne."'" ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotSearch($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlSearch($prenomPersonne,"prenomPersonne") ; }
			if(!empty($lt_prenomPersonne)){ $sql .= " AND prenomPersonne < '".$lt_prenomPersonne."'" ; }
			if(!empty($gt_prenomPersonne)){ $sql .= " AND prenomPersonne > '".$gt_prenomPersonne."'" ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotSearch($noprenomPersonne) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlSearch($estClientClient,"estClientClient") ; }
			if(!empty($lt_estClientClient)){ $sql .= " AND estClientClient < '".$lt_estClientClient."'" ; }
			if(!empty($gt_estClientClient)){ $sql .= " AND estClientClient > '".$gt_estClientClient."'" ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotSearch($noestClientClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlSearch($estProspectClient,"estProspectClient") ; }
			if(!empty($lt_estProspectClient)){ $sql .= " AND estProspectClient < '".$lt_estProspectClient."'" ; }
			if(!empty($gt_estProspectClient)){ $sql .= " AND estProspectClient > '".$gt_estProspectClient."'" ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotSearch($noestProspectClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlSearch($estSuspectClient,"estSuspectClient") ; }
			if(!empty($lt_estSuspectClient)){ $sql .= " AND estSuspectClient < '".$lt_estSuspectClient."'" ; }
			if(!empty($gt_estSuspectClient)){ $sql .= " AND estSuspectClient > '".$gt_estSuspectClient."'" ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotSearch($noestSuspectClient) ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlSearch($numeroClient,"numeroClient") ; }
			if(!empty($lt_numeroClient)){ $sql .= " AND numeroClient < '".$lt_numeroClient."'" ; }
			if(!empty($gt_numeroClient)){ $sql .= " AND numeroClient > '".$gt_numeroClient."'" ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotSearch($nonumeroClient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlSearch($dateCreationClient,"dateCreationClient") ; }
			if(!empty($lt_dateCreationClient)){ $sql .= " AND dateCreationClient < '".$lt_dateCreationClient."'" ; }
			if(!empty($gt_dateCreationClient)){ $sql .= " AND dateCreationClient > '".$gt_dateCreationClient."'" ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotSearch($nodateCreationClient) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete,"nomSociete") ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlSearch($nomGroupe_agent,"nomGroupe_agent") ; }
			if(!empty($lt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent < '".$lt_nomGroupe_agent."'" ; }
			if(!empty($gt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent > '".$gt_nomGroupe_agent."'" ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlNotSearch($nonomGroupe_agent) ; }
			if(!empty($idgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlSearch($idgroupe_agent,"idgroupe_agent") ; }
			if(!empty($lt_idgroupe_agent)){ $sql .= " AND idgroupe_agent < '".$lt_idgroupe_agent."'" ; }
			if(!empty($gt_idgroupe_agent)){ $sql .= " AND idgroupe_agent > '".$gt_idgroupe_agent."'" ; }
			if(!empty($noidgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlNotSearch($noidgroupe_agent) ; }
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlSearch($codeGroupe_agent,"codeGroupe_agent") ; }
			if(!empty($lt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent < '".$lt_codeGroupe_agent."'" ; }
			if(!empty($gt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent > '".$gt_codeGroupe_agent."'" ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotSearch($nocodeGroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAgentHasClient(){ 
		$sql="SELECT * FROM  agent_has_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAgentHasClient($name="idagent_has_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAgent_has_client , idagent_has_client FROM agent_has_client WHERE  1 ";  
			if(!empty($idagent_has_client)){ $sql .= " AND idagent_has_client ".sqlIn($idagent_has_client) ; }
			if(!empty($noidagent_has_client)){ $sql .= " AND idagent_has_client ".sqlNotIn($noidagent_has_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAgent_has_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAgentHasClient($name="idagent_has_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAgent_has_client , idagent_has_client FROM agent_has_client ORDER BY nomAgent_has_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAgentHasClient = new AgentHasClient(); ?>