<?   
class IdaeParcs
{
	var $conn = null;
	function IdaeParcs(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeParcs($params){ 
		$this->conn->AutoExecute("parcs", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeParcs($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("parcs", $params, "UPDATE", "idparcs = ".$idparcs); 
	}
	
	function deleteIdaeParcs($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  parcs WHERE idparcs = $idparcs"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeParcs($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from parcs where 1 " ;
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlIn($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotIn($noidparcs) ; }
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
			if(!empty($dossier)){ $sql .= " AND dossier ".sqlIn($dossier) ; }
			if(!empty($nodossier)){ $sql .= " AND dossier ".sqlNotIn($nodossier) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlIn($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotIn($nocreerLe) ; }
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
	function searchOneIdaeParcs($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from parcs where 1 " ;
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlSearch($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotSearch($noidparcs) ; }
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
			if(!empty($dossier)){ $sql .= " AND dossier ".sqlSearch($dossier) ; }
			if(!empty($nodossier)){ $sql .= " AND dossier ".sqlNotSearch($nodossier) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlSearch($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotSearch($nocreerLe) ; }
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
	function getAllIdaeParcs(){ 
		$sql="SELECT * FROM  parcs"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeParcs($name="idparcs",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomParcs , idparcs FROM parcs WHERE  1 ";  
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlIn($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotIn($noidparcs) ; }
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
			if(!empty($dossier)){ $sql .= " AND dossier ".sqlIn($dossier) ; }
			if(!empty($nodossier)){ $sql .= " AND dossier ".sqlNotIn($nodossier) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlIn($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotIn($nocreerLe) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlIn($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotIn($noleaser) ; }
			if(!empty($prestataire)){ $sql .= " AND prestataire ".sqlIn($prestataire) ; }
			if(!empty($noprestataire)){ $sql .= " AND prestataire ".sqlNotIn($noprestataire) ; }
			if(!empty($loyer_trim)){ $sql .= " AND loyer_trim ".sqlIn($loyer_trim) ; }
			if(!empty($noloyer_trim)){ $sql .= " AND loyer_trim ".sqlNotIn($noloyer_trim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomParcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeParcs($name="idparcs",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomParcs , idparcs FROM parcs ORDER BY nomParcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeParcs = new IdaeParcs(); ?>