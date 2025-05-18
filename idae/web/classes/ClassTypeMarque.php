<?   
class TypeMarque
{
	var $conn = null;
	function TypeMarque(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeMarque($params){ 
		$this->conn->AutoExecute("type_marque", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_marque", $params, "UPDATE", "idtype_marque = ".$idtype_marque); 
	}
	
	function deleteTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_marque WHERE idtype_marque = $idtype_marque"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_marque where 1 " ;
			if(!empty($idtype_marque)){ $sql .= " AND idtype_marque ".sqlIn($idtype_marque) ; }
			if(!empty($noidtype_marque)){ $sql .= " AND idtype_marque ".sqlNotIn($noidtype_marque) ; }
			if(!empty($nomType_marque)){ $sql .= " AND nomType_marque ".sqlIn($nomType_marque) ; }
			if(!empty($nonomType_marque)){ $sql .= " AND nomType_marque ".sqlNotIn($nonomType_marque) ; }
			if(!empty($commentaireType_marque)){ $sql .= " AND commentaireType_marque ".sqlIn($commentaireType_marque) ; }
			if(!empty($nocommentaireType_marque)){ $sql .= " AND commentaireType_marque ".sqlNotIn($nocommentaireType_marque) ; }
			if(!empty($ordreType_marque)){ $sql .= " AND ordreType_marque ".sqlIn($ordreType_marque) ; }
			if(!empty($noordreType_marque)){ $sql .= " AND ordreType_marque ".sqlNotIn($noordreType_marque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllTypeMarque(){ 
		$sql="SELECT * FROM  type_marque"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeMarque($name="idtype_marque",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_marque , idtype_marque FROM WHERE 1 ";  
			if(!empty($idtype_marque)){ $sql .= " AND idtype_marque ".sqlIn($idtype_marque) ; }
			if(!empty($noidtype_marque)){ $sql .= " AND idtype_marque ".sqlNotIn($noidtype_marque) ; }
			if(!empty($nomType_marque)){ $sql .= " AND nomType_marque ".sqlIn($nomType_marque) ; }
			if(!empty($nonomType_marque)){ $sql .= " AND nomType_marque ".sqlNotIn($nonomType_marque) ; }
			if(!empty($commentaireType_marque)){ $sql .= " AND commentaireType_marque ".sqlIn($commentaireType_marque) ; }
			if(!empty($nocommentaireType_marque)){ $sql .= " AND commentaireType_marque ".sqlNotIn($nocommentaireType_marque) ; }
			if(!empty($ordreType_marque)){ $sql .= " AND ordreType_marque ".sqlIn($ordreType_marque) ; }
			if(!empty($noordreType_marque)){ $sql .= " AND ordreType_marque ".sqlNotIn($noordreType_marque) ; }
		$sql .=" ORDER BY nomType_marque ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeMarque($name="idtype_marque",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_marque , idtype_marque FROM type_marque ORDER BY nomType_marque ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeMarque = new TypeMarque(); ?>