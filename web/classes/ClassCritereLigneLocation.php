<?   
class CritereLigneLocation
{
	var $conn = null;
	function CritereLigneLocation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCritereLigneLocation($params){ 
		$this->conn->AutoExecute("critere_ligne_location", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("critere_ligne_location", $params, "UPDATE", "idcritere_ligne_location = ".$idcritere_ligne_location); 
	}
	
	function deleteCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  critere_ligne_location WHERE idcritere_ligne_location = $idcritere_ligne_location"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE  critere_ligne_location "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from critere_ligne_location where 1 " ;
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlIn($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotIn($noidcritere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from critere_ligne_location where 1 " ;
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlSearch($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotSearch($noidcritere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlSearch($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotSearch($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlSearch($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotSearch($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlSearch($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotSearch($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlSearch($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotSearch($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlSearch($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotSearch($noordreCritere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllCritereLigneLocation(){ 
		$sql="SELECT * FROM  critere_ligne_location"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCritereLigneLocation($name="idcritere_ligne_location",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCritere_ligne_location , idcritere_ligne_location FROM critere_ligne_location WHERE  1 ";  
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlIn($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotIn($noidcritere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCritere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCritereLigneLocation($name="idcritere_ligne_location",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCritere_ligne_location , idcritere_ligne_location FROM critere_ligne_location ORDER BY nomCritere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCritereLigneLocation = new CritereLigneLocation(); ?>