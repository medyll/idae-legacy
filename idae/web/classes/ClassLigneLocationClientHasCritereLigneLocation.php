<?   
class LigneLocationClientHasCritereLigneLocation
{
	var $conn = null;
	function LigneLocationClientHasCritereLigneLocation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLigneLocationClientHasCritereLigneLocation($params){ 
		$this->conn->AutoExecute("ligne_location_client_has_critere_ligne_location", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneLocationClientHasCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_location_client_has_critere_ligne_location", $params, "UPDATE", "idligne_location_client_has_critere_ligne_location = ".$idligne_location_client_has_critere_ligne_location); 
	}
	
	function deleteLigneLocationClientHasCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_location_client_has_critere_ligne_location WHERE idligne_location_client_has_critere_ligne_location = $idligne_location_client_has_critere_ligne_location"; 
		return $this->conn->Execute($sql); 	
	}
	
	function truncate(){ 	
		$sql="TRUNCATE TABLE  ligne_location_client_has_critere_ligne_location "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLigneLocationClientHasCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from ligne_location_client_has_critere_ligne_location where 1 " ;
			if(!empty($idligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlIn($idligne_location_client_has_critere_ligne_location) ; }
			if(!empty($noidligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlNotIn($noidligne_location_client_has_critere_ligne_location) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlIn($critere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotIn($nocritere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneLocationClientHasCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from ligne_location_client_has_critere_ligne_location where 1 " ;
			if(!empty($idligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlSearch($idligne_location_client_has_critere_ligne_location) ; }
			if(!empty($noidligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlNotSearch($noidligne_location_client_has_critere_ligne_location) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlSearch($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotSearch($noligne_location_client_idligne_location_client) ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlSearch($critere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotSearch($nocritere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlSearch($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotSearch($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneLocationClientHasCritereLigneLocation(){ 
		$sql="SELECT * FROM  ligne_location_client_has_critere_ligne_location"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneLocationClientHasCritereLigneLocation($name="idligne_location_client_has_critere_ligne_location",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_location_client_has_critere_ligne_location , idligne_location_client_has_critere_ligne_location FROM ligne_location_client_has_critere_ligne_location WHERE  1 ";  
			if(!empty($idligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlIn($idligne_location_client_has_critere_ligne_location) ; }
			if(!empty($noidligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlNotIn($noidligne_location_client_has_critere_ligne_location) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlIn($critere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotIn($nocritere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_location_client_has_critere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneLocationClientHasCritereLigneLocation($name="idligne_location_client_has_critere_ligne_location",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_location_client_has_critere_ligne_location , idligne_location_client_has_critere_ligne_location FROM ligne_location_client_has_critere_ligne_location ORDER BY nomLigne_location_client_has_critere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}

}
$ClassLigneLocationClientHasCritereLigneLocation = new LigneLocationClientHasCritereLigneLocation(); ?>