<? 
include_once(dctAdodb."/adodb.inc.php");   
class SuiviHasPersonne
{
	var $conn = null;
	function SuiviHasPersonne()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSuiviHasPersonne($params)
	{ 
		$this->conn->AutoExecute("suivi_has_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSuiviHasPersonne($params)
	{
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("suivi_has_personne", $params, "UPDATE", "idsuivi_has_personne = ".$idsuivi_has_personne); 
	}
	
	function deleteSuiviHasPersonne($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  suivi_has_personne where idsuivi_has_personne = $idsuivi_has_personne"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSuiviHasPersonne($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from suivi_has_personne where 1 " ;
			if(!empty($idsuivi_has_personne)){ $sql .= " AND idsuivi_has_personne= '$idsuivi_has_personne'" ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi= '$suivi_idsuivi'" ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne= '$personne_idpersonne'" ; }
			if(!empty($principaleSuivi_has_personne)){ $sql .= " AND principaleSuivi_has_personne= '$principaleSuivi_has_personne'" ; }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllSuiviHasPersonne()
	{ 
		$sql="select * from  suivi_has_personne"; 
		return $this->conn->Execute($sql) ;	
	}
}
$ClassSuiviHasPersonne = new SuiviHasPersonne(); ?>