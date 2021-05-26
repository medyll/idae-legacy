<?   
class MarqueHasTypeMarque
{
	var $conn = null;
	function MarqueHasTypeMarque(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createMarqueHasTypeMarque($params){ 
		$this->conn->AutoExecute("marque_has_type_marque", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateMarqueHasTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("marque_has_type_marque", $params, "UPDATE", "idmarque_has_type_marque = ".$idmarque_has_type_marque); 
	}
	
	function deleteMarqueHasTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  marque_has_type_marque WHERE idmarque_has_type_marque = $idmarque_has_type_marque"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneMarqueHasTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from marque_has_type_marque where 1 " ;
			if(!empty($idmarque_has_type_marque)){ $sql .= " AND idmarque_has_type_marque ".sqlIn($idmarque_has_type_marque) ; }
			if(!empty($noidmarque_has_type_marque)){ $sql .= " AND idmarque_has_type_marque ".sqlNotIn($noidmarque_has_type_marque) ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlIn($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotIn($nomarque_idmarque) ; }
			if(!empty($type_marque_idtype_marque)){ $sql .= " AND type_marque_idtype_marque ".sqlIn($type_marque_idtype_marque) ; }
			if(!empty($notype_marque_idtype_marque)){ $sql .= " AND type_marque_idtype_marque ".sqlNotIn($notype_marque_idtype_marque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllMarqueHasTypeMarque(){ 
		$sql="SELECT * FROM  marque_has_type_marque"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneMarqueHasTypeMarque($name="idmarque_has_type_marque",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomMarque_has_type_marque , idmarque_has_type_marque FROM WHERE 1 ";  
			if(!empty($idmarque_has_type_marque)){ $sql .= " AND idmarque_has_type_marque ".sqlIn($idmarque_has_type_marque) ; }
			if(!empty($noidmarque_has_type_marque)){ $sql .= " AND idmarque_has_type_marque ".sqlNotIn($noidmarque_has_type_marque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlIn($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotIn($nomarque_idmarque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($type_marque_idtype_marque)){ $sql .= " AND type_marque_idtype_marque ".sqlIn($type_marque_idtype_marque) ; }
			if(!empty($notype_marque_idtype_marque)){ $sql .= " AND type_marque_idtype_marque ".sqlNotIn($notype_marque_idtype_marque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomMarque_has_type_marque ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectMarqueHasTypeMarque($name="idmarque_has_type_marque",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomMarque_has_type_marque , idmarque_has_type_marque FROM marque_has_type_marque ORDER BY nomMarque_has_type_marque ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassMarqueHasTypeMarque = new MarqueHasTypeMarque(); ?>