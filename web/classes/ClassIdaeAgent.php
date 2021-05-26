<?   
class IdaeAgent
{
	var $conn = null;
	function IdaeAgent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeAgent($params){ 
		$this->conn->AutoExecute("vue_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_agent", $params, "UPDATE", "idvue_agent = ".$idvue_agent); 
	}
	
	function deleteIdaeAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_agent WHERE idvue_agent = $idvue_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlIn($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotIn($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlIn($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotIn($nopasswordAgent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlSearch($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotSearch($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlSearch($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotSearch($nopasswordAgent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeAgent(){ 
		$sql="SELECT * FROM  vue_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeAgent($name="idvue_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_agent , idvue_agent FROM vue_agent WHERE  1 ";  
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlIn($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotIn($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlIn($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotIn($nopasswordAgent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeAgent($name="idvue_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_agent , idvue_agent FROM vue_agent ORDER BY nomVue_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeAgent = new IdaeAgent(); ?>