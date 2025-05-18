<?   
class Livraison
{
	var $conn = null;
	function Livraison(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLivraison($params){ 
		$this->conn->AutoExecute("livraison", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLivraison($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("livraison", $params, "UPDATE", "idlivraison = ".$idlivraison); 
	}
	
	function deleteLivraison($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  livraison WHERE idlivraison = $idlivraison"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){
		$sql="TRUNCATE TABLE  livraison "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from livraison where 1 " ;
			if(!empty($idlivraison)){ $sql .= " AND idlivraison ".sqlIn($idlivraison) ; }
			if(!empty($noidlivraison)){ $sql .= " AND idlivraison ".sqlNotIn($noidlivraison) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($dateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlIn($dateCreationLivraison) ; }
			if(!empty($nodateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlNotIn($nodateCreationLivraison) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlIn($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotIn($nodateLivraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from livraison where 1 " ;
			if(!empty($idlivraison)){ $sql .= " AND idlivraison ".sqlSearch($idlivraison) ; }
			if(!empty($noidlivraison)){ $sql .= " AND idlivraison ".sqlNotSearch($noidlivraison) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlSearch($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotSearch($notache_idtache) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlSearch($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotSearch($noligne_location_client_idligne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($dateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlSearch($dateCreationLivraison) ; }
			if(!empty($nodateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlNotSearch($nodateCreationLivraison) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlSearch($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotSearch($nodateLivraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLivraison(){ 
		$sql="SELECT * FROM  livraison"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLivraison($name="idlivraison",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLivraison , idlivraison FROM livraison WHERE  1 ";  
			if(!empty($idlivraison)){ $sql .= " AND idlivraison ".sqlIn($idlivraison) ; }
			if(!empty($noidlivraison)){ $sql .= " AND idlivraison ".sqlNotIn($noidlivraison) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($dateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlIn($dateCreationLivraison) ; }
			if(!empty($nodateCreationLivraison)){ $sql .= " AND dateCreationLivraison ".sqlNotIn($nodateCreationLivraison) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlIn($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotIn($nodateLivraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLivraison ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLivraison($name="idlivraison",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLivraison , idlivraison FROM livraison ORDER BY nomLivraison ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLivraison = new Livraison(); ?>