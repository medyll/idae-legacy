<?   
class IdaePrestataire
{
	var $conn = null;
	function IdaePrestataire(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createIdaePrestataire($params){ 
		$this->conn->AutoExecute("prestataires", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaePrestataire($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("prestataires", $params, "UPDATE", "idprestataires = ".$idprestataires); 
	}
	
	function deleteIdaePrestataire($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  prestataires WHERE idprestataires = $idprestataires"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaePrestataire($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from prestataires where 1 " ;
			if(!empty($idprestataires)){ $sql .= " AND idprestataires ".sqlIn($idprestataires) ; }
			if(!empty($noidprestataires)){ $sql .= " AND idprestataires ".sqlNotIn($noidprestataires) ; }
			if(!empty($nom)){ $sql .= " AND nom ".sqlIn($nom) ; }
			if(!empty($nonom)){ $sql .= " AND nom ".sqlNotIn($nonom) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaePrestataire($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from prestataires where 1 " ;
			if(!empty($idprestataires)){ $sql .= " AND idprestataires ".sqlSearch($idprestataires) ; }
			if(!empty($noidprestataires)){ $sql .= " AND idprestataires ".sqlNotSearch($noidprestataires) ; }
			if(!empty($nom)){ $sql .= " AND nom ".sqlSearch($nom) ; }
			if(!empty($nonom)){ $sql .= " AND nom ".sqlNotSearch($nonom) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaePrestataire(){ 
		$sql="SELECT * FROM  prestataires"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaePrestataire($name="idprestataires",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPrestataires , idprestataires FROM prestataires WHERE  1 ";  
			if(!empty($idprestataires)){ $sql .= " AND idprestataires ".sqlIn($idprestataires) ; }
			if(!empty($noidprestataires)){ $sql .= " AND idprestataires ".sqlNotIn($noidprestataires) ; }
			if(!empty($nom)){ $sql .= " AND nom ".sqlIn($nom) ; }
			if(!empty($nonom)){ $sql .= " AND nom ".sqlNotIn($nonom) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomPrestataires ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaePrestataire($name="idprestataires",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomPrestataires , idprestataires FROM prestataires ORDER BY nomPrestataires ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaePrestataire = new IdaePrestataire(); ?>