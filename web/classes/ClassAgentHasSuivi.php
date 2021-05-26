<? 
include_once(dctAdodb."/adodb.inc.php");   
class AgentHasSuivi
{
	var $conn = null;
	function AgentHasSuivi()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasSuivi($params)
	{ 
		$this->conn->AutoExecute("agent_has_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasSuivi($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_suivi", $params, "UPDATE", "idagent_has_suivi = ".$idagent_has_suivi); 
	}
	
	function deleteAgentHasSuivi($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  agent_has_suivi where idagent_has_suivi = $idagent_has_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneAgentHasSuivi($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from agent_has_suivi where 1 " ;
			if(!empty($idagent_has_suivi)){ $sql .= " AND idagent_has_suivi= '$idagent_has_suivi'" ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent= '$agent_idagent'" ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi= '$suivi_idsuivi'" ; }
			if(!empty($proprietaireAgent_has_suivi)){ $sql .= " AND proprietaireAgent_has_suivi= '$proprietaireAgent_has_suivi'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllAgentHasSuivi()
	{
		$sql="select * from  agent_has_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassAgentHasSuivi = new AgentHasSuivi(); ?>