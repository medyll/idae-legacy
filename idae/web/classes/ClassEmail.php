<?   
class Email
{
	var $conn = null;
	function Email(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createEmail($params){ 
		$this->conn->AutoExecute("email", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateEmail($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("email", $params, "UPDATE", "idemail = ".$idemail); 
	}
	
	function deleteEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  email WHERE idemail = $idemail"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="truncate table  email"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from email where 1 " ;
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlIn($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotIn($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlIn($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotIn($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlIn($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotIn($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlIn($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotIn($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlIn($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotIn($noordreEmail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from email where 1 " ;
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlSearch($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotSearch($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlSearch($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotSearch($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlSearch($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotSearch($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlSearch($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotSearch($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlSearch($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotSearch($noordreEmail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllEmail(){ 
		$sql="SELECT * FROM  email"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneEmail($name="idemail",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomEmail , idemail FROM email WHERE  1 ";  
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlIn($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotIn($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlIn($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotIn($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlIn($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotIn($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlIn($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotIn($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlIn($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotIn($noordreEmail) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomEmail ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectEmail($name="idemail",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomEmail , idemail FROM email ORDER BY nomEmail ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassEmail = new Email(); ?>