<?   
class IdaePersonneHasLocalisation
{
	var $conn = null;
	function IdaePersonneHasLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaePersonneHasLocalisation($params){ 
		$this->conn->AutoExecute("personnes_has_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("personnes_has_localisation", $params, "UPDATE", "idpersonnes_has_localisation = ".$idpersonnes_has_localisation); 
	}
	
	function deleteIdaePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  personnes_has_localisation WHERE idpersonnes_has_localisation = $idpersonnes_has_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from personnes_has_localisation where 1 " ;
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlIn($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotIn($nopersonnes_idpersonnes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from personnes_has_localisation where 1 " ;
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlSearch($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotSearch($nopersonnes_idpersonnes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaePersonneHasLocalisation(){ 
		$sql="SELECT * FROM  personnes_has_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaePersonneHasLocalisation($name="idpersonnes_has_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPersonnes_has_localisation , idpersonnes_has_localisation FROM personnes_has_localisation WHERE  1 ";  
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlIn($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotIn($nopersonnes_idpersonnes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomPersonnes_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaePersonneHasLocalisation($name="idpersonnes_has_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomPersonnes_has_localisation , idpersonnes_has_localisation FROM personnes_has_localisation ORDER BY nomPersonnes_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaePersonneHasLocalisation = new IdaePersonneHasLocalisation(); ?>