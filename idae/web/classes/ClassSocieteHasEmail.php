<?   
class SocieteHasEmail
{
	var $conn = null;
	function SocieteHasEmail(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSocieteHasEmail($params){ 
		$this->conn->AutoExecute("societe_has_email", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSocieteHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("societe_has_email", $params, "UPDATE", "idsociete_has_email = ".$idsociete_has_email); 
	}
	
	function deleteSocieteHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  societe_has_email WHERE idsociete_has_email = $idsociete_has_email"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSocieteHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from societe_has_email where 1 " ;
			if(!empty($idsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlIn($idsociete_has_email) ; }
			if(!empty($noidsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlNotIn($noidsociete_has_email) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlIn($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotIn($noemail_idemail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSocieteHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from societe_has_email where 1 " ;
			if(!empty($idsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlSearch($idsociete_has_email) ; }
			if(!empty($noidsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlNotSearch($noidsociete_has_email) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlSearch($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotSearch($noemail_idemail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSocieteHasEmail(){ 
		$sql="SELECT * FROM  societe_has_email"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSocieteHasEmail($name="idsociete_has_email",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSociete_has_email , idsociete_has_email FROM WHERE 1 ";  
			if(!empty($idsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlIn($idsociete_has_email) ; }
			if(!empty($noidsociete_has_email)){ $sql .= " AND idsociete_has_email ".sqlNotIn($noidsociete_has_email) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlIn($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotIn($noemail_idemail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSociete_has_email ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSocieteHasEmail($name="idsociete_has_email",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSociete_has_email , idsociete_has_email FROM societe_has_email ORDER BY nomSociete_has_email ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSocieteHasEmail = new SocieteHasEmail(); ?>