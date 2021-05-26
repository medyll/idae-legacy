<?   
class CritereContrat
{
	var $conn = null;
	function CritereContrat(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCritereContrat($params){ 
		$this->conn->AutoExecute("critere_contrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("critere_contrat", $params, "UPDATE", "idcritere_contrat = ".$idcritere_contrat); 
	}
	
	function deleteCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  critere_contrat WHERE idcritere_contrat = $idcritere_contrat"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE critere_contrat "; 
		$this->conn->Execute($sql); 	
	}
	function getOneCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from critere_contrat where 1 " ;
			if(!empty($idcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlIn($idcritere_contrat) ; }
			if(!empty($noidcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlNotIn($noidcritere_contrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($type_contrat_idtype_contrat) ; }
			if(!empty($nomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlIn($nomCritere_contrat) ; }
			if(!empty($nonomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlNotIn($nonomCritere_contrat) ; }
			if(!empty($uniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlIn($uniteCritere_contrat) ; }
			if(!empty($nouniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlNotIn($nouniteCritere_contrat) ; }
			if(!empty($codeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlIn($codeCritere_contrat) ; }
			if(!empty($nocodeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlNotIn($nocodeCritere_contrat) ; }
			if(!empty($ordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlIn($ordreCritere_contrat) ; }
			if(!empty($noordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlNotIn($noordreCritere_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneCritereContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from critere_contrat where 1 " ;
			if(!empty($idcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlSearch($idcritere_contrat) ; }
			if(!empty($noidcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlNotSearch($noidcritere_contrat) ; }
			if(!empty($type_contrat_id_type_contrat)){ $sql .= " AND type_contrat_id_type_contrat ".sqlSearch($type_contrat_id_type_contrat) ; }
			if(!empty($notype_contrat_id_type_contrat)){ $sql .= " AND type_contrat_id_type_contrat ".sqlNotSearch($notype_contrat_id_type_contrat) ; }
			if(!empty($nomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlSearch($nomCritere_contrat) ; }
			if(!empty($nonomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlNotSearch($nonomCritere_contrat) ; }
			if(!empty($uniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlSearch($uniteCritere_contrat) ; }
			if(!empty($nouniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlNotSearch($nouniteCritere_contrat) ; }
			if(!empty($codeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlSearch($codeCritere_contrat) ; }
			if(!empty($nocodeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlNotSearch($nocodeCritere_contrat) ; }
			if(!empty($ordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlSearch($ordreCritere_contrat) ; }
			if(!empty($noordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlNotSearch($noordreCritere_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllCritereContrat(){ 
		$sql="SELECT * FROM  critere_contrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCritereContrat($name="idcritere_contrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCritere_contrat , idcritere_contrat FROM critere_contrat WHERE  1 ";  
			if(!empty($idcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlIn($idcritere_contrat) ; }
			if(!empty($noidcritere_contrat)){ $sql .= " AND idcritere_contrat ".sqlNotIn($noidcritere_contrat) ; }
			if(!empty($type_contrat_id_type_contrat)){ $sql .= " AND type_contrat_id_type_contrat ".sqlIn($type_contrat_id_type_contrat) ; }
			if(!empty($notype_contrat_id_type_contrat)){ $sql .= " AND type_contrat_id_type_contrat ".sqlNotIn($notype_contrat_id_type_contrat) ; }
			if(!empty($nomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlIn($nomCritere_contrat) ; }
			if(!empty($nonomCritere_contrat)){ $sql .= " AND nomCritere_contrat ".sqlNotIn($nonomCritere_contrat) ; }
			if(!empty($uniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlIn($uniteCritere_contrat) ; }
			if(!empty($nouniteCritere_contrat)){ $sql .= " AND uniteCritere_contrat ".sqlNotIn($nouniteCritere_contrat) ; }
			if(!empty($codeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlIn($codeCritere_contrat) ; }
			if(!empty($nocodeCritere_contrat)){ $sql .= " AND codeCritere_contrat ".sqlNotIn($nocodeCritere_contrat) ; }
			if(!empty($ordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlIn($ordreCritere_contrat) ; }
			if(!empty($noordreCritere_contrat)){ $sql .= " AND ordreCritere_contrat ".sqlNotIn($noordreCritere_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCritere_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCritereContrat($name="idcritere_contrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCritere_contrat , idcritere_contrat FROM critere_contrat ORDER BY nomCritere_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCritereContrat = new CritereContrat(); ?>