<? 
include_once(dctAdodb."/adodb.inc.php");   
class AgentHasGroupeAgent
{
	var $conn = null;
	function AgentHasGroupeAgent()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasGroupeAgent($params)
	{ 
		$this->conn->AutoExecute("agent_has_groupe_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasGroupeAgent($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_groupe_agent", $params, "UPDATE", "idagent_has_groupe_agent = ".$idagent_has_groupe_agent); 
	}
	
	function truncate()
	{ 
		$sql="truncate  table  agent_has_groupe_agent "; 
		
		return $this->conn->Execute($sql); 	
	}
	
	function deleteAgentHasGroupeAgent($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  agent_has_groupe_agent where agent_idagent = $agent_idagent and groupe_agent_idgroupe_agent = $groupe_agent_idgroupe_agent"; 
		
		return $this->conn->Execute($sql); 	
	}
	
	function getOneAgentHasGroupeAgent($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from agent_has_groupe_agent where 1 " ;
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent= '$agent_idagent'" ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent= '$groupe_agent_idgroupe_agent'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllAgentHasGroupeAgent()
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  agent_has_groupe_agent"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassAgentHasGroupeAgent = new AgentHasGroupeAgent(); ?>