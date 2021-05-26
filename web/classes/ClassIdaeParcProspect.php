<?   
class IdaeParcProspect
{
	var $conn = null;
	function IdaeParcProspect(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeParcProspect($params){ 
		$this->conn->AutoExecute("parc_prospect", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("parc_prospect", $params, "UPDATE", "idparc_prospect = ".$idparc_prospect); 
	}
	
	function deleteIdaeParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  parc_prospect WHERE idparc_prospect = $idparc_prospect"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_parc_prospect where 1 " ;
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlIn($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotIn($noidparc_prospect) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlIn($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotIn($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($prestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlIn($prestataires_idprestataires) ; }
			if(!empty($noprestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlNotIn($noprestataires_idprestataires) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
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

	function searchOneIdaeParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from parc_prospect where 1 " ;
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlSearch($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotSearch($noidparc_prospect) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlSearch($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotSearch($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($prestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlSearch($prestataires_idprestataires) ; }
			if(!empty($noprestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlNotSearch($noprestataires_idprestataires) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlSearch($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotSearch($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlSearch($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotSearch($nodureeEnTrim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeParcProspect(){ 
		$sql="SELECT * FROM  parc_prospect"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeParcProspect($name="idparc_prospect",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomParc_prospect , idparc_prospect FROM parc_prospect WHERE  1 ";  
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlIn($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotIn($noidparc_prospect) ; }
			if(!empty($client_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlIn($client_conseillers_idConseiller) ; }
			if(!empty($noclient_conseillers_idConseiller)){ $sql .= " AND client_conseillers_idConseiller ".sqlNotIn($noclient_conseillers_idConseiller) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($prestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlIn($prestataires_idprestataires) ; }
			if(!empty($noprestataires_idprestataires)){ $sql .= " AND prestataires_idprestataires ".sqlNotIn($noprestataires_idprestataires) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomParc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeParcProspect($name="idparc_prospect",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomParc_prospect , idparc_prospect FROM parc_prospect ORDER BY nomParc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeParcProspect = new IdaeParcProspect(); ?>