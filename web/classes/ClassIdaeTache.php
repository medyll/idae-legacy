<?   
class IdaeTache
{
	var $conn = null;
	function IdaeTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeTache($params){ 
		$this->conn->AutoExecute("vue_tache_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_tache_suivi", $params, "UPDATE", "idvue_tache_suivi = ".$idvue_tache_suivi); 
	}
	
	function deleteIdaeTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_tache_suivi WHERE idvue_tache_suivi = $idvue_tache_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_tache_suivi where 1 " ;
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($satut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlIn($satut_tache_idstatut_tache) ; }
			if(!empty($nosatut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlNotIn($nosatut_tache_idstatut_tache) ; }
			if(!empty($recurrence)){ $sql .= " AND recurrence ".sqlIn($recurrence) ; }
			if(!empty($norecurrence)){ $sql .= " AND recurrence ".sqlNotIn($norecurrence) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($idTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlIn($idTypeTacheResult) ; }
			if(!empty($noidTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlNotIn($noidTypeTacheResult) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlIn($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotIn($noidTachePere) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_tache_suivi where 1 " ;
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlSearch($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotSearch($nosuivi_idsuivi) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlSearch($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotSearch($nomigrateidtache) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlSearch($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotSearch($noidtache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlSearch($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotSearch($nocommentaireTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlSearch($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotSearch($nodateCreationTache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlSearch($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotSearch($nodateDebutTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlSearch($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotSearch($nodateFinTache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlSearch($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotSearch($noobjetTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlSearch($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotSearch($noheureDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlSearch($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotSearch($noheureFinTache) ; }
			if(!empty($satut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlSearch($satut_tache_idstatut_tache) ; }
			if(!empty($nosatut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlNotSearch($nosatut_tache_idstatut_tache) ; }
			if(!empty($recurrence)){ $sql .= " AND recurrence ".sqlSearch($recurrence) ; }
			if(!empty($norecurrence)){ $sql .= " AND recurrence ".sqlNotSearch($norecurrence) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlSearch($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotSearch($novaleurTache) ; }
			if(!empty($idTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlSearch($idTypeTacheResult) ; }
			if(!empty($noidTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlNotSearch($noidTypeTacheResult) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlSearch($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotSearch($noidTachePere) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlSearch($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotSearch($notype_tache_idtype_tache) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeTache(){ 
		$sql="SELECT * FROM  vue_tache_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeTache($name="idvue_tache_suivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_tache_suivi , idvue_tache_suivi FROM vue_tache_suivi WHERE  1 ";  
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($satut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlIn($satut_tache_idstatut_tache) ; }
			if(!empty($nosatut_tache_idstatut_tache)){ $sql .= " AND satut_tache_idstatut_tache ".sqlNotIn($nosatut_tache_idstatut_tache) ; }
			if(!empty($recurrence)){ $sql .= " AND recurrence ".sqlIn($recurrence) ; }
			if(!empty($norecurrence)){ $sql .= " AND recurrence ".sqlNotIn($norecurrence) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($idTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlIn($idTypeTacheResult) ; }
			if(!empty($noidTypeTacheResult)){ $sql .= " AND idTypeTacheResult ".sqlNotIn($noidTypeTacheResult) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlIn($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotIn($noidTachePere) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_tache_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeTache($name="idvue_tache_suivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_tache_suivi , idvue_tache_suivi FROM vue_tache_suivi ORDER BY nomVue_tache_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeTache = new IdaeTache(); ?>