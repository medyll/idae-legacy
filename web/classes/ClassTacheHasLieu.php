<? 
include_once(dctAdodb."/adodb.inc.php");   
class TacheHasLieu
{
	var $conn = null;
	function TacheHasLieu()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTacheHasLieu($params)
	{ 
		$this->conn->AutoExecute("tache_has_lieu", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTacheHasLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("tache_has_lieu", $params, "UPDATE", "idtache_has_lieu = ".$idtache_has_lieu); 
	}
	
	function deleteTacheHasLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  tache_has_lieu where idtache_has_lieu = $idtache_has_lieu"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTacheHasLieu($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from tache_has_lieu where 1 " ;
			if(!empty($idtache_has_lieu)){ $sql .= " AND idtache_has_lieu= '$idtache_has_lieu'" ; }
			if(!empty($lieu_idlieu)){ $sql .= " AND lieu_idlieu= '$lieu_idlieu'" ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache= '$tache_idtache'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllTacheHasLieu()
	{	
		$sql="select * from  tache_has_lieu"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassTacheHasLieu = new TacheHasLieu(); ?>