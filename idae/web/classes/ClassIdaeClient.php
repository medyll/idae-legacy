<?   
class IdaeClient
{
	var $conn = null;
	function IdaeClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeClient($params){ 
		$this->conn->AutoExecute("vue_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_client", $params, "UPDATE", "idvue_client = ".$idvue_client); 
	}
	
	function deleteIdaeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_client WHERE idvue_client = $idvue_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_client where 1 " ;
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlIn($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotIn($nocommentaireClient) ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_client where 1 " ;
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlSearch($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotSearch($noidclient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlSearch($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotSearch($nodateCreationClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlSearch($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotSearch($noestSuspectClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlSearch($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotSearch($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlSearch($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotSearch($noestClientClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlSearch($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotSearch($nocommentaireClient) ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlSearch($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotSearch($nonumeroClient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeClient(){ 
		$sql="SELECT * FROM  vue_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeClient($name="idvue_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_client , idvue_client FROM vue_client WHERE  1 ";  
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlIn($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotIn($nocommentaireClient) ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeClient($name="idvue_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_client , idvue_client FROM vue_client ORDER BY nomVue_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeClient = new IdaeClient(); ?>