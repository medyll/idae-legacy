<?   
include_once(dctAdodb."/adodb.inc.php"); 
class ClientIsSociete
{
	var $conn = null;
	function ClientIsSociete()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createClientIsSociete($params)
	{ 
		$this->conn->AutoExecute("client_is_societe", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateClientIsSociete($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("client_is_societe", $params, "UPDATE", "idclient_is_societe = ".$idclient_is_societe); 
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE client_is_societe"; 
		$this->conn->Execute($sql); 	
	}
	function deleteClientIsSociete($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  client_is_societe where idclient_is_societe = $idclient_is_societe"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneClientIsSociete($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from client_is_societe where 1 " ;
			if(!empty($client_idclient)){ $sql .= " AND client_idclient= '$client_idclient'" ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete= '$societe_idsociete'" ; }
			
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllClientIsSociete()
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  client_is_societe"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassClientIsSociete = new ClientIsSociete(); ?>