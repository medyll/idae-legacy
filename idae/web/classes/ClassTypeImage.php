<?   
class TypeImage
{
	var $conn = null;
	function TypeImage(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeImage($params){ 
		$this->conn->AutoExecute("type_image", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeImage($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_image", $params, "UPDATE", "idtype_image = ".$idtype_image); 
	}
	
	function deleteTypeImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_image WHERE idtype_image = $idtype_image"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_image where 1 " ;
			if(!empty($idtype_image)){ $sql .= " AND idtype_image ".sqlIn($idtype_image) ; }
			if(!empty($noidtype_image)){ $sql .= " AND idtype_image ".sqlNotIn($noidtype_image) ; }
			if(!empty($nomType_image)){ $sql .= " AND nomType_image ".sqlIn($nomType_image) ; }
			if(!empty($nonomType_image)){ $sql .= " AND nomType_image ".sqlNotIn($nonomType_image) ; }
			if(!empty($largeurType_image)){ $sql .= " AND largeurType_image ".sqlIn($largeurType_image) ; }
			if(!empty($nolargeurType_image)){ $sql .= " AND largeurType_image ".sqlNotIn($nolargeurType_image) ; }
			if(!empty($longueurType_image)){ $sql .= " AND longueurType_image ".sqlIn($longueurType_image) ; }
			if(!empty($nolongueurType_image)){ $sql .= " AND longueurType_image ".sqlNotIn($nolongueurType_image) ; }
			if(!empty($codeType_image)){ $sql .= " AND codeType_image ".sqlIn($codeType_image) ; }
			if(!empty($nocodeType_image)){ $sql .= " AND codeType_image ".sqlNotIn($nocodeType_image) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_image where 1 " ;
			if(!empty($idtype_image)){ $sql .= " AND idtype_image ".sqlSearch($idtype_image) ; }
			if(!empty($noidtype_image)){ $sql .= " AND idtype_image ".sqlNotSearch($noidtype_image) ; }
			if(!empty($nomType_image)){ $sql .= " AND nomType_image ".sqlSearch($nomType_image) ; }
			if(!empty($nonomType_image)){ $sql .= " AND nomType_image ".sqlNotSearch($nonomType_image) ; }
			if(!empty($largeurType_image)){ $sql .= " AND largeurType_image ".sqlSearch($largeurType_image) ; }
			if(!empty($nolargeurType_image)){ $sql .= " AND largeurType_image ".sqlNotSearch($nolargeurType_image) ; }
			if(!empty($longueurType_image)){ $sql .= " AND longueurType_image ".sqlSearch($longueurType_image) ; }
			if(!empty($nolongueurType_image)){ $sql .= " AND longueurType_image ".sqlNotSearch($nolongueurType_image) ; }
			if(!empty($codeType_image)){ $sql .= " AND codeType_image ".sqlSearch($codeType_image) ; }
			if(!empty($nocodeType_image)){ $sql .= " AND codeType_image ".sqlNotSearch($nocodeType_image) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeImage(){ 
		$sql="SELECT * FROM  type_image"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeImage($name="idtype_image",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_image , idtype_image FROM WHERE 1 ";  
			if(!empty($idtype_image)){ $sql .= " AND idtype_image ".sqlIn($idtype_image) ; }
			if(!empty($noidtype_image)){ $sql .= " AND idtype_image ".sqlNotIn($noidtype_image) ; }
			if(!empty($nomType_image)){ $sql .= " AND nomType_image ".sqlIn($nomType_image) ; }
			if(!empty($nonomType_image)){ $sql .= " AND nomType_image ".sqlNotIn($nonomType_image) ; }
			if(!empty($largeurType_image)){ $sql .= " AND largeurType_image ".sqlIn($largeurType_image) ; }
			if(!empty($nolargeurType_image)){ $sql .= " AND largeurType_image ".sqlNotIn($nolargeurType_image) ; }
			if(!empty($longueurType_image)){ $sql .= " AND longueurType_image ".sqlIn($longueurType_image) ; }
			if(!empty($nolongueurType_image)){ $sql .= " AND longueurType_image ".sqlNotIn($nolongueurType_image) ; }
			if(!empty($codeType_image)){ $sql .= " AND codeType_image ".sqlIn($codeType_image) ; }
			if(!empty($nocodeType_image)){ $sql .= " AND codeType_image ".sqlNotIn($nocodeType_image) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_image ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeImage($name="idtype_image",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_image , idtype_image FROM type_image ORDER BY nomType_image ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeImage = new TypeImage(); ?>