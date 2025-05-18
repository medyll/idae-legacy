<?   
class TypeTelFax
{
	var $conn = null;
	function TypeTelFax(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeTelFax($params){ 
		$this->conn->AutoExecute("type_telfax", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeTelFax($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_telfax", $params, "UPDATE", "idtype_telfax = ".$idtype_telfax); 
	}
	
	function deleteTypeTelFax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_telfax WHERE idtype_telfax = $idtype_telfax"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeTelFax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_telfax where 1 " ;
			if(!empty($idtype_telfax)){ $sql .= " AND idtype_telfax ".sqlIn($idtype_telfax) ; }
			if(!empty($noidtype_telfax)){ $sql .= " AND idtype_telfax ".sqlNotIn($noidtype_telfax) ; }
			if(!empty($nomType_telfax)){ $sql .= " AND nomType_telfax ".sqlIn($nomType_telfax) ; }
			if(!empty($nonomType_telfax)){ $sql .= " AND nomType_telfax ".sqlNotIn($nonomType_telfax) ; }
			
			if(!empty($codeType_telfax)){ $sql .= " AND codeType_telfax ".sqlIn($codeType_telfax) ; }
			if(!empty($nocodeType_telfax)){ $sql .= " AND codeType_telfax ".sqlNotIn($nocodeType_telfax) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeTelFax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_telfax where 1 " ;
			if(!empty($idtype_telfax)){ $sql .= " AND idtype_telfax ".sqlSearch($idtype_telfax) ; }
			if(!empty($noidtype_telfax)){ $sql .= " AND idtype_telfax ".sqlNotSearch($noidtype_telfax) ; }
			if(!empty($nomType_telfax)){ $sql .= " AND nomType_telfax ".sqlSearch($nomType_telfax) ; }
			if(!empty($nonomType_telfax)){ $sql .= " AND nomType_telfax ".sqlNotSearch($nonomType_telfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeTelFax(){ 
		$sql="SELECT * FROM  type_telfax"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeTelFax($name="idtype_telfax",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_telfax , idtype_telfax FROM type_telfax WHERE 1 ";  
			if(!empty($idtype_telfax)){ $sql .= " AND idtype_telfax ".sqlIn($idtype_telfax) ; }
			if(!empty($noidtype_telfax)){ $sql .= " AND idtype_telfax ".sqlNotIn($noidtype_telfax) ; }
			if(!empty($nomType_telfax)){ $sql .= " AND nomType_telfax ".sqlIn($nomType_telfax) ; }
			if(!empty($nonomType_telfax)){ $sql .= " AND nomType_telfax ".sqlNotIn($nonomType_telfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_telfax ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeTelFax($name="idtype_telfax",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_telfax , idtype_telfax FROM type_telfax ORDER BY nomType_telfax Desc";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeTelFax = new TypeTelFax(); ?>