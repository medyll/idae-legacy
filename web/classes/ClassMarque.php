<?   
class Marque
{
	var $conn = null;
	function Marque(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createMarque($params){ 
		$this->conn->AutoExecute("marque", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateMarque($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("marque", $params, "UPDATE", "idmarque = ".$idmarque); 
	}
	function truncate(){
		$sql="TRUNCATE TABLE  marque"; 
		return $this->conn->Execute($sql); 	
	}
	function deleteMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  marque WHERE idmarque = $idmarque"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneMarque($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from marque where 1 " ;
			if(!empty($idmarque)){ $sql .= " AND idmarque ".sqlIn($idmarque) ; }
			if(!empty($noidmarque)){ $sql .= " AND idmarque ".sqlNotIn($noidmarque) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($commentaireMarque)){ $sql .= " AND commentaireMarque ".sqlIn($commentaireMarque) ; }
			if(!empty($nocommentaireMarque)){ $sql .= " AND commentaireMarque ".sqlNotIn($nocommentaireMarque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllMarque(){ 
		$sql="SELECT * FROM  marque"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneMarque($name="idmarque",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomMarque , idmarque FROM marque WHERE 1 ";  
			if(!empty($idmarque)){ $sql .= " AND idmarque ".sqlIn($idmarque) ; }
			if(!empty($noidmarque)){ $sql .= " AND idmarque ".sqlNotIn($noidmarque) ; } 
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($commentaireMarque)){ $sql .= " AND commentaireMarque ".sqlIn($commentaireMarque) ; }
			if(!empty($nocommentaireMarque)){ $sql .= " AND commentaireMarque ".sqlNotIn($nocommentaireMarque) ; }
		$sql .=" ORDER BY nomMarque ";
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectMarque($name="idmarque",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomMarque , idmarque FROM marque ORDER BY nomMarque ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassMarque = new Marque(); ?>