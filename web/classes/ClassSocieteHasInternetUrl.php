<? 
include_once(dctAdodb."/adodb.inc.php");   
class SocieteHasInternetUrl
{
	var $conn = null;
	function SocieteHasInternetUrl()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSocieteHasInternetUrl($params)
	{ 
		$this->conn->AutoExecute("societe_has_internet_url", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSocieteHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("societe_has_internet_url", $params, "UPDATE", "idsociete_has_internet_url = ".$idsociete_has_internet_url); 
	}
	
	function deleteSocieteHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  societe_has_internet_url where idsociete_has_internet_url = $idsociete_has_internet_url"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSocieteHasInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from societe_has_internet_url where 1 " ;
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete= '$societe_idsociete'" ; }
			if(!empty($internet_url_idinternet_url)){ $sql .= " AND internet_url_idinternet_url= '$internet_url_idinternet_url'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllSocieteHasInternetUrl()
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  societe_has_internet_url"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassSocieteHasInternetUrl = new SocieteHasInternetUrl(); ?>