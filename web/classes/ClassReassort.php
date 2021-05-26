<?   
class Reassort
{
	var $conn = null;
	function Reassort(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createReassort($params){ 
		$this->conn->AutoExecute("reassort", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateReassort($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("reassort", $params, "UPDATE", "idreassort = ".$idreassort); 
	}
	
	function deleteReassort($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  reassort WHERE idreassort = $idreassort"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneReassort($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from reassort where 1 " ;
			if(!empty($idreassort)){ $sql .= " AND idreassort ".sqlIn($idreassort) ; }
			if(!empty($noidreassort)){ $sql .= " AND idreassort ".sqlNotIn($noidreassort) ; }
			if(!empty($nomReassort)){ $sql .= " AND nomReassort ".sqlIn($nomReassort) ; }
			if(!empty($nonomReassort)){ $sql .= " AND nomReassort ".sqlNotIn($nonomReassort) ; }
			if(!empty($commentaireReassort)){ $sql .= " AND commentaireReassort ".sqlIn($commentaireReassort) ; }
			if(!empty($nocommentaireReassort)){ $sql .= " AND commentaireReassort ".sqlNotIn($nocommentaireReassort) ; }
			if(!empty($dateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlIn($dateCreationReassort) ; }
			if(!empty($nodateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlNotIn($nodateCreationReassort) ; }
			if(!empty($estActifReassort)){ $sql .= " AND estActifReassort ".sqlIn($estActifReassort) ; }
			if(!empty($noestActifReassort)){ $sql .= " AND estActifReassort ".sqlNotIn($noestActifReassort) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneReassort($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from reassort where 1 " ;
			if(!empty($idreassort)){ $sql .= " AND idreassort ".sqlSearch($idreassort) ; }
			if(!empty($noidreassort)){ $sql .= " AND idreassort ".sqlNotSearch($noidreassort) ; }
			if(!empty($nomReassort)){ $sql .= " AND nomReassort ".sqlSearch($nomReassort) ; }
			if(!empty($nonomReassort)){ $sql .= " AND nomReassort ".sqlNotSearch($nonomReassort) ; }
			if(!empty($commentaireReassort)){ $sql .= " AND commentaireReassort ".sqlSearch($commentaireReassort) ; }
			if(!empty($nocommentaireReassort)){ $sql .= " AND commentaireReassort ".sqlNotSearch($nocommentaireReassort) ; }
			if(!empty($dateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlSearch($dateCreationReassort) ; }
			if(!empty($nodateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlNotSearch($nodateCreationReassort) ; }
			if(!empty($estActifReassort)){ $sql .= " AND estActifReassort ".sqlSearch($estActifReassort) ; }
			if(!empty($noestActifReassort)){ $sql .= " AND estActifReassort ".sqlNotSearch($noestActifReassort) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllReassort(){ 
		$sql="SELECT * FROM  reassort"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneReassort($name="idreassort",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomReassort , idreassort FROM reassort WHERE  1 ";  
			if(!empty($idreassort)){ $sql .= " AND idreassort ".sqlIn($idreassort) ; }
			if(!empty($noidreassort)){ $sql .= " AND idreassort ".sqlNotIn($noidreassort) ; }
			if(!empty($nomReassort)){ $sql .= " AND nomReassort ".sqlIn($nomReassort) ; }
			if(!empty($nonomReassort)){ $sql .= " AND nomReassort ".sqlNotIn($nonomReassort) ; }
			if(!empty($commentaireReassort)){ $sql .= " AND commentaireReassort ".sqlIn($commentaireReassort) ; }
			if(!empty($nocommentaireReassort)){ $sql .= " AND commentaireReassort ".sqlNotIn($nocommentaireReassort) ; }
			if(!empty($dateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlIn($dateCreationReassort) ; }
			if(!empty($nodateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlNotIn($nodateCreationReassort) ; }
			if(!empty($estActifReassort)){ $sql .= " AND estActifReassort ".sqlIn($estActifReassort) ; }
			if(!empty($noestActifReassort)){ $sql .= " AND estActifReassort ".sqlNotIn($noestActifReassort) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomReassort ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectReassort($name="idreassort",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomReassort , idreassort FROM reassort ORDER BY nomReassort ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassReassort = new Reassort(); ?>