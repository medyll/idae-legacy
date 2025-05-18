<?   
class Date
{
	var $conn = null;
	function Date(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createDate($params){ 
		$this->conn->AutoExecute("date", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateDate($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("date", $params, "UPDATE", "iddate = ".$iddate); 
	}
	
	function deleteDate($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  date WHERE iddate = $iddate"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneDate($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from date where 1 " ;
			if(!empty($iddate)){ $sql .= " AND iddate ".sqlIn($iddate) ; }
			if(!empty($noiddate)){ $sql .= " AND iddate ".sqlNotIn($noiddate) ; }
			if(!empty($dateCreationDate)){ $sql .= " AND dateCreationDate ".sqlIn($dateCreationDate) ; }
			if(!empty($nodateCreationDate)){ $sql .= " AND dateCreationDate ".sqlNotIn($nodateCreationDate) ; }
			if(!empty($dateDebutDate)){ $sql .= " AND dateDebutDate ".sqlIn($dateDebutDate) ; }
			if(!empty($nodateDebutDate)){ $sql .= " AND dateDebutDate ".sqlNotIn($nodateDebutDate) ; }
			if(!empty($dateClotureDate)){ $sql .= " AND dateClotureDate ".sqlIn($dateClotureDate) ; }
			if(!empty($nodateClotureDate)){ $sql .= " AND dateClotureDate ".sqlNotIn($nodateClotureDate) ; }
			if(!empty($dateFinDate)){ $sql .= " AND dateFinDate ".sqlIn($dateFinDate) ; }
			if(!empty($nodateFinDate)){ $sql .= " AND dateFinDate ".sqlNotIn($nodateFinDate) ; }
			if(!empty($commentaireDate)){ $sql .= " AND commentaireDate ".sqlIn($commentaireDate) ; }
			if(!empty($nocommentaireDate)){ $sql .= " AND commentaireDate ".sqlNotIn($nocommentaireDate) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllDate(){ 
		$sql="SELECT * FROM  date"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneDate($name="iddate",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomDate , iddate FROM WHERE 1 ";  
			if(!empty($iddate)){ $sql .= " AND iddate ".sqlIn($iddate) ; }
			if(!empty($noiddate)){ $sql .= " AND iddate ".sqlNotIn($noiddate) ; } 
			if(!empty($dateCreationDate)){ $sql .= " AND dateCreationDate ".sqlIn($dateCreationDate) ; }
			if(!empty($nodateCreationDate)){ $sql .= " AND dateCreationDate ".sqlNotIn($nodateCreationDate) ; } 
			if(!empty($dateDebutDate)){ $sql .= " AND dateDebutDate ".sqlIn($dateDebutDate) ; }
			if(!empty($nodateDebutDate)){ $sql .= " AND dateDebutDate ".sqlNotIn($nodateDebutDate) ; } 
			if(!empty($dateClotureDate)){ $sql .= " AND dateClotureDate ".sqlIn($dateClotureDate) ; }
			if(!empty($nodateClotureDate)){ $sql .= " AND dateClotureDate ".sqlNotIn($nodateClotureDate) ; } 
			if(!empty($dateFinDate)){ $sql .= " AND dateFinDate ".sqlIn($dateFinDate) ; }
			if(!empty($nodateFinDate)){ $sql .= " AND dateFinDate ".sqlNotIn($nodateFinDate) ; } 
			if(!empty($commentaireDate)){ $sql .= " AND commentaireDate ".sqlIn($commentaireDate) ; }
			if(!empty($nocommentaireDate)){ $sql .= " AND commentaireDate ".sqlNotIn($nocommentaireDate) ; }
		$sql .=" ORDER BY nomDate ";
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectDate($name="iddate",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomDate , iddate FROM date ORDER BY nomDate ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassDate = new Date(); ?>