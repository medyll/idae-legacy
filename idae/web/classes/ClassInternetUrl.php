<? 
include_once(dctAdodb."/adodb.inc.php");   
class InternetUrl
{
	var $conn = null;
	function InternetUrl()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createInternetUrl($params)
	{ 
		$this->conn->AutoExecute("internet_url", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("internet_url", $params, "UPDATE", "idinternet_url = ".$idinternet_url); 
	}
	
	function deleteInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  internet_url where idinternet_url = $idinternet_url"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneInternetUrl($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from internet_url where 1 " ;
			if(!empty($idinternet_url)){ $sql .= " AND idinternet_url= '$idinternet_url'" ; }
			if(!empty($urlInternet_url)){ $sql .= " AND urlInternet_url= '$urlInternet_url'" ; }
			if(!empty($commentaireInterner_url)){ $sql .= " AND commentaireInterner_url= '$commentaireInterner_url'" ; }
			if(!empty($ordreInternet_url)){ $sql .= " AND ordreInternet_url= '$ordreInternet_url'" ; }
			if(!empty($principaleInternet_url)){ $sql .= " AND principaleInternet_url= '$principaleInternet_url'" ; }
			if(!empty($textInternet_url)){ $sql .= " AND textInternet_url= '$textInternet_url'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllInternetUrl()
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  internet_url"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassInternetUrl = new InternetUrl(); ?>