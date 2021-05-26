<?   
class TachePlanning
{
	var $conn = null;
	function TachePlanning(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTachePlanning($params){ 
		$this->conn->AutoExecute("vue_tache_planning", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTachePlanning($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_tache_planning", $params, "UPDATE", "idvue_tache_planning = ".$idvue_tache_planning); 
	}
	
	function deleteTachePlanning($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_tache_planning WHERE idvue_tache_planning = $idvue_tache_planning"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTachePlanning($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_tache_planning where 1 " ;
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlIn($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotIn($noidagent_has_tache) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlIn($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotIn($noproprietaireAgent_has_tache) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlIn($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotIn($noheureCreationTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlIn($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotIn($nomigrateidtacheTech) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTachePlanning($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_tache_planning where 1 " ;
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlSearch($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotSearch($noidagent_has_tache) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlSearch($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotSearch($notache_idtache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlSearch($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotSearch($noproprietaireAgent_has_tache) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlSearch($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotSearch($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlSearch($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotSearch($nodateDebutAgent) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlSearch($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotSearch($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlSearch($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotSearch($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlSearch($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotSearch($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlSearch($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotSearch($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlSearch($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotSearch($noobjetTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlSearch($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotSearch($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlSearch($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotSearch($noheureDebutTache) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlSearch($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotSearch($noheureCreationTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlSearch($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotSearch($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlSearch($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotSearch($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlSearch($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotSearch($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlSearch($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotSearch($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlSearch($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotSearch($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlSearch($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotSearch($noresultatTache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlSearch($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotSearch($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlSearch($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotSearch($nomigrateidtacheTech) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlSearch($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotSearch($noidagent_writer) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTachePlanning(){ 
		$sql="SELECT * FROM  vue_tache_planning"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTachePlanning($name="idvue_tache_planning",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_tache_planning , idvue_tache_planning FROM vue_tache_planning WHERE  1 ";  
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlIn($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotIn($noidagent_has_tache) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlIn($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotIn($noproprietaireAgent_has_tache) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlIn($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotIn($noheureCreationTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlIn($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotIn($nomigrateidtacheTech) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_tache_planning ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTachePlanning($name="idvue_tache_planning",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_tache_planning , idvue_tache_planning FROM vue_tache_planning ORDER BY nomVue_tache_planning ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTachePlanning = new TachePlanning(); ?>