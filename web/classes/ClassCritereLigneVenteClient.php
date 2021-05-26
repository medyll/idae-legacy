<?   
class CritereLigneVenteClient
{
	var $conn = null;
	function CritereLigneVenteClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCritereLigneVenteClient($params){ 
		$this->conn->AutoExecute("critere_ligne_vente_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCritereLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("critere_ligne_vente_client", $params, "UPDATE", "idcritere_ligne_vente_client = ".$idcritere_ligne_vente_client); 
	}
	
	function deleteCritereLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  critere_ligne_vente_client WHERE idcritere_ligne_vente_client = $idcritere_ligne_vente_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneCritereLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from critere_ligne_vente_client where 1 " ;
			if(!empty($idcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlIn($idcritere_ligne_vente_client) ; }
			if(!empty($noidcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlNotIn($noidcritere_ligne_vente_client) ; }
			if(!empty($ligne_vente_client_idligne_vente_client)){ $sql .= " AND ligne_vente_client_idligne_vente_client ".sqlIn($ligne_vente_client_idligne_vente_client) ; }
			if(!empty($noligne_vente_client_idligne_vente_client)){ $sql .= " AND ligne_vente_client_idligne_vente_client ".sqlNotIn($noligne_vente_client_idligne_vente_client) ; }
			if(!empty($type_critere_ligne_vente_idtype_critere_ligne_vente)){ $sql .= " AND type_critere_ligne_vente_idtype_critere_ligne_vente ".sqlIn($type_critere_ligne_vente_idtype_critere_ligne_vente) ; }
			if(!empty($notype_critere_ligne_vente_idtype_critere_ligne_vente)){ $sql .= " AND type_critere_ligne_vente_idtype_critere_ligne_vente ".sqlNotIn($notype_critere_ligne_vente_idtype_critere_ligne_vente) ; }
			if(!empty($valeurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlIn($valeurCritere_ligne_vente_client) ; }
			if(!empty($novaleurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlNotIn($novaleurCritere_ligne_vente_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllCritereLigneVenteClient(){ 
		$sql="SELECT * FROM  critere_ligne_vente_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCritereLigneVenteClient($name="idcritere_ligne_vente_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCritere_ligne_vente_client , idcritere_ligne_vente_client FROM WHERE 1 ";  
			if(!empty($idcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlIn($idcritere_ligne_vente_client) ; }
			if(!empty($noidcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlNotIn($noidcritere_ligne_vente_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($ligne_vente_client_idligne_vente_client)){ $sql .= " AND ligne_vente_client_idligne_vente_client ".sqlIn($ligne_vente_client_idligne_vente_client) ; }
			if(!empty($noligne_vente_client_idligne_vente_client)){ $sql .= " AND ligne_vente_client_idligne_vente_client ".sqlNotIn($noligne_vente_client_idligne_vente_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($type_critere_ligne_vente_idtype_critere_ligne_vente)){ $sql .= " AND type_critere_ligne_vente_idtype_critere_ligne_vente ".sqlIn($type_critere_ligne_vente_idtype_critere_ligne_vente) ; }
			if(!empty($notype_critere_ligne_vente_idtype_critere_ligne_vente)){ $sql .= " AND type_critere_ligne_vente_idtype_critere_ligne_vente ".sqlNotIn($notype_critere_ligne_vente_idtype_critere_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($valeurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlIn($valeurCritere_ligne_vente_client) ; }
			if(!empty($novaleurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlNotIn($novaleurCritere_ligne_vente_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomCritere_ligne_vente_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCritereLigneVenteClient($name="idcritere_ligne_vente_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCritere_ligne_vente_client , idcritere_ligne_vente_client FROM critere_ligne_vente_client ORDER BY nomCritere_ligne_vente_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCritereLigneVenteClient = new CritereLigneVenteClient(); ?>