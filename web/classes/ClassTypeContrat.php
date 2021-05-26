<?   
class TypeContrat
{
	var $conn = null;
	function TypeContrat(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeContrat($params){ 
		$this->conn->AutoExecute("type_contrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_contrat", $params, "UPDATE", "idtype_contrat = ".$idtype_contrat); 
	}
	
	function deleteTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_contrat WHERE idtype_contrat = $idtype_contrat"; 
		return $this->conn->Execute($sql); 	
	}
	
	function truncate(){ 	
		$sql="TRUNCATE TABLE type_contrat "; 
		$this->conn->Execute($sql); 	
	}
	function getOneTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from type_contrat where 1 " ;
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlIn($idtype_contrat) ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotIn($noidtype_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlIn($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotIn($nonomType_contrat) ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlIn($ordreType_contrat) ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotIn($noordreType_contrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotIn($nocodeType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_contrat where 1 " ;
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlSearch($idtype_contrat) ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotSearch($noidtype_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlSearch($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotSearch($nonomType_contrat) ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlSearch($ordreType_contrat) ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotSearch($noordreType_contrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlSearch($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotSearch($nocodeType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeContrat(){ 
		$sql="SELECT * FROM  type_contrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeContrat($name="idtype_contrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_contrat , idtype_contrat FROM type_contrat WHERE  1 ";  
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlIn($idtype_contrat) ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotIn($noidtype_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlIn($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotIn($nonomType_contrat) ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlIn($ordreType_contrat) ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotIn($noordreType_contrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotIn($nocodeType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeContrat($name="idtype_contrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_contrat , idtype_contrat FROM type_contrat ORDER BY nomType_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeContrat = new TypeContrat(); ?>