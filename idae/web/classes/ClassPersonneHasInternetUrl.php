<? 
include_once(dctAdodb."/adodb.inc.php");   
class PersonneHasInternetUrl
{
	var $conn = null;
	function PersonneHasInternetUrl()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createPersonneHasInternetUrl($params)
	{ 
		$this->conn->AutoExecute("personne_has_internet_url", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updatePersonneHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("personne_has_internet_url", $params, "UPDATE", "idpersonne_has_internet_url = ".$idpersonne_has_internet_url); 
	}
	
	function deletePersonneHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  personne_has_internet_url where idpersonne_has_internet_url = $idpersonne_has_internet_url"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOnePersonneHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from personne_has_internet_url where 1 " ;
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne= '$personne_idpersonne'" ; }
			if(!empty($internet_url_idinternet_url)){ $sql .= " AND internet_url_idinternet_url= '$internet_url_idinternet_url'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllPersonneHasInternetUrl()
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  personne_has_internet_url"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassPersonneHasInternetUrl = new PersonneHasInternetUrl(); ?>