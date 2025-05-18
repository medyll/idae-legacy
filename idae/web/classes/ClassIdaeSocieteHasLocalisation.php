<?   
class IdaeSocieteHasLocalisation
{
	var $conn = null;
	function IdaeSocieteHasLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeSocieteHasLocalisation($params){ 
		$this->conn->AutoExecute("societe_has_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("societe_has_localisation", $params, "UPDATE", "idsociete_has_localisation = ".$idsociete_has_localisation); 
	}
	
	function deleteIdaeSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  societe_has_localisation WHERE idsociete_has_localisation = $idsociete_has_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from societe_has_localisation where 1 " ;
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from societe_has_localisation where 1 " ;
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeSocieteHasLocalisation(){ 
		$sql="SELECT * FROM  societe_has_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeSocieteHasLocalisation($name="idsociete_has_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSociete_has_localisation , idsociete_has_localisation FROM societe_has_localisation WHERE  1 ";  
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSociete_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeSocieteHasLocalisation($name="idsociete_has_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSociete_has_localisation , idsociete_has_localisation FROM societe_has_localisation ORDER BY nomSociete_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeSocieteHasLocalisation = new IdaeSocieteHasLocalisation(); ?>