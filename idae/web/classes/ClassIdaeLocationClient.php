<?   
class IdaeLocationClient
{
	var $conn = null;
	function IdaeLocationClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLocationClient($params){ 
		$this->conn->AutoExecute("vue_parcs", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_parcs", $params, "UPDATE", "idvue_parcs = ".$idvue_parcs); 
	}
	
	function deleteIdaeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_parcs WHERE idvue_parcs = $idvue_parcs"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_parcs where 1 " ;
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlIn($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotIn($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($designation)){ $sql .= " AND designation ".sqlIn($designation) ; }
			if(!empty($nodesignation)){ $sql .= " AND designation ".sqlNotIn($nodesignation) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlIn($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotIn($nofils) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlIn($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotIn($notypeContrat) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlIn($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotIn($nodateCreationLocation_client) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlIn($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotIn($noleaser) ; }
			if(!empty($prestataire)){ $sql .= " AND prestataire ".sqlIn($prestataire) ; }
			if(!empty($noprestataire)){ $sql .= " AND prestataire ".sqlNotIn($noprestataire) ; }
			if(!empty($loyer_trim)){ $sql .= " AND loyer_trim ".sqlIn($loyer_trim) ; }
			if(!empty($noloyer_trim)){ $sql .= " AND loyer_trim ".sqlNotIn($noloyer_trim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_parcs where 1 " ;
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlSearch($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotSearch($noidlocation_client) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlSearch($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotSearch($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($designation)){ $sql .= " AND designation ".sqlSearch($designation) ; }
			if(!empty($nodesignation)){ $sql .= " AND designation ".sqlNotSearch($nodesignation) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlSearch($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotSearch($nofils) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlSearch($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotSearch($notypeContrat) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlSearch($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotSearch($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlSearch($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotSearch($nodateCreationLocation_client) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlSearch($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotSearch($nodureeEnTrim) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlSearch($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotSearch($noleaser) ; }
			if(!empty($prestataire)){ $sql .= " AND prestataire ".sqlSearch($prestataire) ; }
			if(!empty($noprestataire)){ $sql .= " AND prestataire ".sqlNotSearch($noprestataire) ; }
			if(!empty($loyer_trim)){ $sql .= " AND loyer_trim ".sqlSearch($loyer_trim) ; }
			if(!empty($noloyer_trim)){ $sql .= " AND loyer_trim ".sqlNotSearch($noloyer_trim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLocationClient(){ 
		$sql="SELECT * FROM  vue_parcs"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLocationClient($name="idvue_parcs",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_parcs , idvue_parcs FROM vue_parcs WHERE  1 ";  
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlIn($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotIn($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($designation)){ $sql .= " AND designation ".sqlIn($designation) ; }
			if(!empty($nodesignation)){ $sql .= " AND designation ".sqlNotIn($nodesignation) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlIn($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotIn($nofils) ; }
			if(!empty($typeContrat)){ $sql .= " AND typeContrat ".sqlIn($typeContrat) ; }
			if(!empty($notypeContrat)){ $sql .= " AND typeContrat ".sqlNotIn($notypeContrat) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlIn($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotIn($nodateCreationLocation_client) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlIn($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotIn($noleaser) ; }
			if(!empty($prestataire)){ $sql .= " AND prestataire ".sqlIn($prestataire) ; }
			if(!empty($noprestataire)){ $sql .= " AND prestataire ".sqlNotIn($noprestataire) ; }
			if(!empty($loyer_trim)){ $sql .= " AND loyer_trim ".sqlIn($loyer_trim) ; }
			if(!empty($noloyer_trim)){ $sql .= " AND loyer_trim ".sqlNotIn($noloyer_trim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_parcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLocationClient($name="idvue_parcs",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_parcs , idvue_parcs FROM vue_parcs ORDER BY nomVue_parcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLocationClient = new IdaeLocationClient(); ?>