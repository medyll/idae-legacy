<? 
include_once(dctAdodb."/adodb.inc.php");   
class Lieu
{
	var $conn = null;
	function Lieu()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLieu($params)
	{ 
		$this->conn->AutoExecute("lieu", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("lieu", $params, "UPDATE", "idlieu = ".$idlieu); 
	}
	
	function deleteLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  lieu where idlieu = $idlieu"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from lieu where 1 " ;
			if(!empty($idlieu)){ $sql .= " AND idlieu= '$idlieu'" ; }
			if(!empty($dateCreationLieu)){ $sql .= " AND dateCreationLieu= '$dateCreationLieu'" ; }
			if(!empty($dateFinLieu)){ $sql .= " AND dateFinLieu= '$dateFinLieu'" ; }
			if(!empty($dateDebutLieu)){ $sql .= " AND dateDebutLieu= '$dateDebutLieu'" ; }
			if(!empty($nomLieu)){ $sql .= " AND nomLieu like '%$nomLieu%'" ; }
			if(!empty($commentaireLieu)){ $sql .= " AND commentaireLieu= '$commentaireLieu'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllLieu()
	{	
		$sql="select * from  lieu"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassLieu = new Lieu(); ?>