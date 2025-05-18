<?   
class EtatClient
{
	var $conn = null;
	function EtatClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createEtatClient($params){ 
		$this->conn->AutoExecute("etat_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateEtatClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("etat_client", $params, "UPDATE", "idetat_client = ".$idetat_client); 
	}
	
	function deleteEtatClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  etat_client WHERE idetat_client = $idetat_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneEtatClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_client_has_etat_client where 1 " ;
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlIn($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotIn($nocommentaireClient) ; }
			if(!empty($loginClient)){ $sql .= " AND loginClient ".sqlIn($loginClient) ; }
			if(!empty($nologinClient)){ $sql .= " AND loginClient ".sqlNotIn($nologinClient) ; }
			if(!empty($passwordClient)){ $sql .= " AND passwordClient ".sqlIn($passwordClient) ; }
			if(!empty($nopasswordClient)){ $sql .= " AND passwordClient ".sqlNotIn($nopasswordClient) ; }
			if(!empty($idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlIn($idclient_has_etat_client) ; }
			if(!empty($noidclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlNotIn($noidclient_has_etat_client) ; }
			if(!empty($etat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlIn($etat_client_idetat_client) ; }
			if(!empty($noetat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlNotIn($noetat_client_idetat_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($ordreClient_has_etat_client)){ $sql .= " AND ordreClient_has_etat_client ".sqlIn($ordreClient_has_etat_client) ; }
			if(!empty($noordreClient_has_etat_client)){ $sql .= " AND ordreClient_has_etat_client ".sqlNotIn($noordreClient_has_etat_client) ; }
			if(!empty($dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlIn($dateClient_has_etat_client) ; }
			if(!empty($nodateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlNotIn($nodateClient_has_etat_client) ; }
			if(!empty($idetat_client)){ $sql .= " AND idetat_client ".sqlIn($idetat_client) ; }
			if(!empty($noidetat_client)){ $sql .= " AND idetat_client ".sqlNotIn($noidetat_client) ; }
			if(!empty($nomEtat_client)){ $sql .= " AND nomEtat_client ".sqlIn($nomEtat_client) ; }
			if(!empty($nonomEtat_client)){ $sql .= " AND nomEtat_client ".sqlNotIn($nonomEtat_client) ; }
			if(!empty($ordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlIn($ordreEtat_client) ; }
			if(!empty($noordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlNotIn($noordreEtat_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneEtatClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_client_has_etat_client where 1 " ;
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlSearch($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotSearch($nonumeroClient) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlSearch($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotSearch($noidclient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlSearch($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotSearch($nodateCreationClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlSearch($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotSearch($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlSearch($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotSearch($noestClientClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlSearch($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotSearch($noestSuspectClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlSearch($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotSearch($nocommentaireClient) ; }
			if(!empty($loginClient)){ $sql .= " AND loginClient ".sqlSearch($loginClient) ; }
			if(!empty($nologinClient)){ $sql .= " AND loginClient ".sqlNotSearch($nologinClient) ; }
			if(!empty($passwordClient)){ $sql .= " AND passwordClient ".sqlSearch($passwordClient) ; }
			if(!empty($nopasswordClient)){ $sql .= " AND passwordClient ".sqlNotSearch($nopasswordClient) ; }
			if(!empty($idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlSearch($idclient_has_etat_client) ; }
			if(!empty($noidclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlNotSearch($noidclient_has_etat_client) ; }
			if(!empty($etat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlSearch($etat_client_idetat_client) ; }
			if(!empty($noetat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlNotSearch($noetat_client_idetat_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($ordreClient_has_etat_client)){ $sql .= " AND ordreClient_has_etat_client ".sqlSearch($ordreClient_has_etat_client) ; }
			if(!empty($noordreClient_has_etat_client)){ $sql .= " AND ordreClient_has_etat_client ".sqlNotSearch($noordreClient_has_etat_client) ; }
			if(!empty($dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlSearch($dateClient_has_etat_client) ; }
			if(!empty($nodateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlNotSearch($nodateClient_has_etat_client) ; }
			if(!empty($idetat_client)){ $sql .= " AND idetat_client ".sqlSearch($idetat_client) ; }
			if(!empty($noidetat_client)){ $sql .= " AND idetat_client ".sqlNotSearch($noidetat_client) ; }
			if(!empty($nomEtat_client)){ $sql .= " AND nomEtat_client ".sqlSearch($nomEtat_client) ; }
			if(!empty($nonomEtat_client)){ $sql .= " AND nomEtat_client ".sqlNotSearch($nonomEtat_client) ; }
			if(!empty($ordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlSearch($ordreEtat_client) ; }
			if(!empty($noordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlNotSearch($noordreEtat_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllEtatClient(){ 
		$sql="SELECT * FROM  etat_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneEtatClient($name="idetat_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomEtat_client , idetat_client FROM etat_client WHERE  1 ";  
			if(!empty($idetat_client)){ $sql .= " AND idetat_client ".sqlIn($idetat_client) ; }
			if(!empty($noidetat_client)){ $sql .= " AND idetat_client ".sqlNotIn($noidetat_client) ; }
			if(!empty($nomEtat_client)){ $sql .= " AND nomEtat_client ".sqlIn($nomEtat_client) ; }
			if(!empty($nonomEtat_client)){ $sql .= " AND nomEtat_client ".sqlNotIn($nonomEtat_client) ; }
			if(!empty($ordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlIn($ordreEtat_client) ; }
			if(!empty($noordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlNotIn($noordreEtat_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomEtat_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectEtatClient($name="idetat_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomEtat_client , idetat_client FROM etat_client ORDER BY nomEtat_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassEtatClient = new EtatClient(); ?>