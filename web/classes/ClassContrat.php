<?   
class Contrat
{
	var $conn = null;
	function Contrat(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createContrat($params){ 
		$this->conn->AutoExecute("contrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("contrat", $params, "UPDATE", "idcontrat = ".$idcontrat); 
	}
	
	function deleteContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  contrat WHERE idcontrat = $idcontrat"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE contrat "; 
		$this->conn->Execute($sql); 	
	}
	function getOneContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_contrat where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlIn($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotIn($noidcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($notype_contrat_idtype_contrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlIn($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotIn($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlIn($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotIn($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlIn($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotIn($nodateClotureContrat) ; }
			if(!empty($idcontrat_old)){ $sql .= " AND idcontrat_old ".sqlIn($idcontrat_old) ; }
			if(!empty($noidcontrat_old)){ $sql .= " AND idcontrat_old ".sqlNotIn($noidcontrat_old) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotIn($nocodeType_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlIn($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotIn($nonomType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_contrat where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlSearch($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotSearch($noidcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlSearch($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotSearch($notype_contrat_idtype_contrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlSearch($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotSearch($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlSearch($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotSearch($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlSearch($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotSearch($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlSearch($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotSearch($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlSearch($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotSearch($nodateClotureContrat) ; }
			if(!empty($idcontrat_old)){ $sql .= " AND idcontrat_old ".sqlSearch($idcontrat_old) ; }
			if(!empty($noidcontrat_old)){ $sql .= " AND idcontrat_old ".sqlNotSearch($noidcontrat_old) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlSearch($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotSearch($nocodeType_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlSearch($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotSearch($nonomType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllContrat(){ 
		$sql="SELECT * FROM  contrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneContrat($name="idcontrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomContrat , idcontrat FROM contrat WHERE  1 ";  
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlIn($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotIn($noidcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($notype_contrat_idtype_contrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlIn($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotIn($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlIn($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotIn($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlIn($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotIn($nodateClotureContrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomContrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectContrat($name="idcontrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomContrat , idcontrat FROM contrat ORDER BY nomContrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassContrat = new Contrat(); ?>