<?   
class TypeLocalisation
{
	var $conn = null;
	function TypeLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeLocalisation($params){ 
		$this->conn->AutoExecute("type_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_localisation", $params, "UPDATE", "idtype_localisation = ".$idtype_localisation); 
	}
	
	function deleteTypeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_localisation WHERE idtype_localisation = $idtype_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_localisation where 1 " ;
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlIn($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotIn($noidtype_localisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_localisation where 1 " ;
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlSearch($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotSearch($noidtype_localisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlSearch($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotSearch($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeLocalisation(){ 
		$sql="SELECT * FROM  type_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeLocalisation($name="idtype_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_localisation , idtype_localisation FROM WHERE 1 ";  
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlIn($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotIn($noidtype_localisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeLocalisation($name="idtype_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_localisation , idtype_localisation FROM type_localisation ORDER BY nomType_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeLocalisation = new TypeLocalisation(); ?>