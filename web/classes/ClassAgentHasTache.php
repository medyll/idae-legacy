<?   
class AgentHasTache
{
	var $conn = null;
	function AgentHasTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasTache($params){ 
		$this->conn->AutoExecute("agent_has_tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_tache", $params, "UPDATE", "idagent_has_tache = ".$idagent_has_tache); 
	}
	
	function deleteAgentHasTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent_has_tache WHERE idagent_has_tache = $idagent_has_tache"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE agent_has_tache"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneAgentHasTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent_has_tache where 1 " ;
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlIn($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotIn($noidagent_has_tache) ; }
			if(!empty($lt_idagent_has_tache)){ $sql .= " AND idagent_has_tache < '".$lt_idagent_has_tache."'" ; }
			if(!empty($gt_idagent_has_tache)){ $sql .= " AND idagent_has_tache > '".$gt_idagent_has_tache."'" ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($lt_tache_idtache)){ $sql .= " AND tache_idtache < '".$lt_tache_idtache."'" ; }
			if(!empty($gt_tache_idtache)){ $sql .= " AND tache_idtache > '".$gt_tache_idtache."'" ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlIn($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotIn($noproprietaireAgent_has_tache) ; }
			if(!empty($lt_proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache < '".$lt_proprietaireAgent_has_tache."'" ; }
			if(!empty($gt_proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache > '".$gt_proprietaireAgent_has_tache."'" ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($lt_idagent)){ $sql .= " AND idagent < '".$lt_idagent."'" ; }
			if(!empty($gt_idagent)){ $sql .= " AND idagent > '".$gt_idagent."'" ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($lt_personne_idpersonne)){ $sql .= " AND personne_idpersonne < '".$lt_personne_idpersonne."'" ; }
			if(!empty($gt_personne_idpersonne)){ $sql .= " AND personne_idpersonne > '".$gt_personne_idpersonne."'" ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($lt_estActifAgent)){ $sql .= " AND estActifAgent < '".$lt_estActifAgent."'" ; }
			if(!empty($gt_estActifAgent)){ $sql .= " AND estActifAgent > '".$gt_estActifAgent."'" ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($lt_dateDebutAgent)){ $sql .= " AND dateDebutAgent < '".$lt_dateDebutAgent."'" ; }
			if(!empty($gt_dateDebutAgent)){ $sql .= " AND dateDebutAgent > '".$gt_dateDebutAgent."'" ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($noidpersonne) ; }
			if(!empty($lt_idpersonne)){ $sql .= " AND idpersonne < '".$lt_idpersonne."'" ; }
			if(!empty($gt_idpersonne)){ $sql .= " AND idpersonne > '".$gt_idpersonne."'" ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlIn($langue_idlangue) ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotIn($nolangue_idlangue) ; }
			if(!empty($lt_langue_idlangue)){ $sql .= " AND langue_idlangue < '".$lt_langue_idlangue."'" ; }
			if(!empty($gt_langue_idlangue)){ $sql .= " AND langue_idlangue > '".$gt_langue_idlangue."'" ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($lt_nomPersonne)){ $sql .= " AND nomPersonne < '".$lt_nomPersonne."'" ; }
			if(!empty($gt_nomPersonne)){ $sql .= " AND nomPersonne > '".$gt_nomPersonne."'" ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($lt_prenomPersonne)){ $sql .= " AND prenomPersonne < '".$lt_prenomPersonne."'" ; }
			if(!empty($gt_prenomPersonne)){ $sql .= " AND prenomPersonne > '".$gt_prenomPersonne."'" ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlIn($prenom2Personne) ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotIn($noprenom2Personne) ; }
			if(!empty($lt_prenom2Personne)){ $sql .= " AND prenom2Personne < '".$lt_prenom2Personne."'" ; }
			if(!empty($gt_prenom2Personne)){ $sql .= " AND prenom2Personne > '".$gt_prenom2Personne."'" ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlIn($sexePersonne) ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotIn($nosexePersonne) ; }
			if(!empty($lt_sexePersonne)){ $sql .= " AND sexePersonne < '".$lt_sexePersonne."'" ; }
			if(!empty($gt_sexePersonne)){ $sql .= " AND sexePersonne > '".$gt_sexePersonne."'" ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlIn($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotIn($nonomJeuneFillePersonne) ; }
			if(!empty($lt_nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne < '".$lt_nomJeuneFillePersonne."'" ; }
			if(!empty($gt_nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne > '".$gt_nomJeuneFillePersonne."'" ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlIn($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotIn($noetatCivilPersonne) ; }
			if(!empty($lt_etatCivilPersonne)){ $sql .= " AND etatCivilPersonne < '".$lt_etatCivilPersonne."'" ; }
			if(!empty($gt_etatCivilPersonne)){ $sql .= " AND etatCivilPersonne > '".$gt_etatCivilPersonne."'" ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlIn($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotIn($noregimeMatrimonialPersonne) ; }
			if(!empty($lt_regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne < '".$lt_regimeMatrimonialPersonne."'" ; }
			if(!empty($gt_regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne > '".$gt_regimeMatrimonialPersonne."'" ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlIn($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotIn($nodateNaissancePersonne) ; }
			if(!empty($lt_dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne < '".$lt_dateNaissancePersonne."'" ; }
			if(!empty($gt_dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne > '".$gt_dateNaissancePersonne."'" ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlIn($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotIn($nocommentairePersonne) ; }
			if(!empty($lt_commentairePersonne)){ $sql .= " AND commentairePersonne < '".$lt_commentairePersonne."'" ; }
			if(!empty($gt_commentairePersonne)){ $sql .= " AND commentairePersonne > '".$gt_commentairePersonne."'" ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($lt_villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne < '".$lt_villeNaissancePersonne."'" ; }
			if(!empty($gt_villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne > '".$gt_villeNaissancePersonne."'" ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlIn($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotIn($nophotoPersonne) ; }
			if(!empty($lt_photoPersonne)){ $sql .= " AND photoPersonne < '".$lt_photoPersonne."'" ; }
			if(!empty($gt_photoPersonne)){ $sql .= " AND photoPersonne > '".$gt_photoPersonne."'" ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlIn($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotIn($nopaysNaissancePerspnne) ; }
			if(!empty($lt_paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne < '".$lt_paysNaissancePerspnne."'" ; }
			if(!empty($gt_paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne > '".$gt_paysNaissancePerspnne."'" ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($lt_idtache)){ $sql .= " AND idtache < '".$lt_idtache."'" ; }
			if(!empty($gt_idtache)){ $sql .= " AND idtache > '".$gt_idtache."'" ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($lt_suivi_idsuivi)){ $sql .= " AND suivi_idsuivi < '".$lt_suivi_idsuivi."'" ; }
			if(!empty($gt_suivi_idsuivi)){ $sql .= " AND suivi_idsuivi > '".$gt_suivi_idsuivi."'" ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($lt_statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache < '".$lt_statut_tache_idstatut_tache."'" ; }
			if(!empty($gt_statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache > '".$gt_statut_tache_idstatut_tache."'" ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($lt_type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache < '".$lt_type_tache_idtype_tache."'" ; }
			if(!empty($gt_type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache > '".$gt_type_tache_idtype_tache."'" ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($lt_objetTache)){ $sql .= " AND objetTache < '".$lt_objetTache."'" ; }
			if(!empty($gt_objetTache)){ $sql .= " AND objetTache > '".$gt_objetTache."'" ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($lt_dateCreationTache)){ $sql .= " AND dateCreationTache < '".$lt_dateCreationTache."'" ; }
			if(!empty($gt_dateCreationTache)){ $sql .= " AND dateCreationTache > '".$gt_dateCreationTache."'" ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($lt_heureDebutTache)){ $sql .= " AND heureDebutTache < '".$lt_heureDebutTache."'" ; }
			if(!empty($gt_heureDebutTache)){ $sql .= " AND heureDebutTache > '".$gt_heureDebutTache."'" ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlIn($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotIn($noheureCreationTache) ; }
			if(!empty($lt_heureCreationTache)){ $sql .= " AND heureCreationTache < '".$lt_heureCreationTache."'" ; }
			if(!empty($gt_heureCreationTache)){ $sql .= " AND heureCreationTache > '".$gt_heureCreationTache."'" ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($lt_dateDebutTache)){ $sql .= " AND dateDebutTache < '".$lt_dateDebutTache."'" ; }
			if(!empty($gt_dateDebutTache)){ $sql .= " AND dateDebutTache > '".$gt_dateDebutTache."'" ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($lt_heureFinTache)){ $sql .= " AND heureFinTache < '".$lt_heureFinTache."'" ; }
			if(!empty($gt_heureFinTache)){ $sql .= " AND heureFinTache > '".$gt_heureFinTache."'" ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($lt_dateFinTache)){ $sql .= " AND dateFinTache < '".$lt_dateFinTache."'" ; }
			if(!empty($gt_dateFinTache)){ $sql .= " AND dateFinTache > '".$gt_dateFinTache."'" ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($lt_commentaireTache)){ $sql .= " AND commentaireTache < '".$lt_commentaireTache."'" ; }
			if(!empty($gt_commentaireTache)){ $sql .= " AND commentaireTache > '".$gt_commentaireTache."'" ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($lt_valeurTache)){ $sql .= " AND valeurTache < '".$lt_valeurTache."'" ; }
			if(!empty($gt_valeurTache)){ $sql .= " AND valeurTache > '".$gt_valeurTache."'" ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($lt_resultatTache)){ $sql .= " AND resultatTache < '".$lt_resultatTache."'" ; }
			if(!empty($gt_resultatTache)){ $sql .= " AND resultatTache > '".$gt_resultatTache."'" ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($lt_migrateidtache)){ $sql .= " AND migrateidtache < '".$lt_migrateidtache."'" ; }
			if(!empty($gt_migrateidtache)){ $sql .= " AND migrateidtache > '".$gt_migrateidtache."'" ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlIn($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotIn($nomigrateidtacheTech) ; }
			if(!empty($lt_migrateidtacheTech)){ $sql .= " AND migrateidtacheTech < '".$lt_migrateidtacheTech."'" ; }
			if(!empty($gt_migrateidtacheTech)){ $sql .= " AND migrateidtacheTech > '".$gt_migrateidtacheTech."'" ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($lt_idagent_writer)){ $sql .= " AND idagent_writer < '".$lt_idagent_writer."'" ; }
			if(!empty($gt_idagent_writer)){ $sql .= " AND idagent_writer > '".$gt_idagent_writer."'" ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($lt_type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi < '".$lt_type_suivi_idtype_suivi."'" ; }
			if(!empty($gt_type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi > '".$gt_type_suivi_idtype_suivi."'" ; } 
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($lt_codeType_suivi)){ $sql .= " AND codeType_suivi < '".$lt_codeType_suivi."'" ; }
			if(!empty($gt_codeType_suivi)){ $sql .= " AND codeType_suivi > '".$gt_codeType_suivi."'" ; } 
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlIn($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotIn($nocodeStatut_tache) ; }
			if(!empty($lt_codeStatut_tache)){ $sql .= " AND codeStatut_tache < '".$lt_codeStatut_tache."'" ; }
			if(!empty($gt_codeStatut_tache)){ $sql .= " AND codeStatut_tache > '".$gt_codeStatut_tache."'" ; } 
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAgentHasTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent_has_tache where 1 " ;
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlSearch($idagent_has_tache,"idagent_has_tache") ; }
		if(!empty($lt_idagent_has_tache)){ $sql .= " AND idagent_has_tache < '".$lt_idagent_has_tache."'" ; }
		if(!empty($gt_idagent_has_tache)){ $sql .= " AND idagent_has_tache > '".$gt_idagent_has_tache."'" ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotSearch($noidagent_has_tache) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlSearch($tache_idtache,"tache_idtache") ; }
		if(!empty($lt_tache_idtache)){ $sql .= " AND tache_idtache < '".$lt_tache_idtache."'" ; }
		if(!empty($gt_tache_idtache)){ $sql .= " AND tache_idtache > '".$gt_tache_idtache."'" ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotSearch($notache_idtache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent,"agent_idagent") ; }
		if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
		if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlSearch($proprietaireAgent_has_tache,"proprietaireAgent_has_tache") ; }
		if(!empty($lt_proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache < '".$lt_proprietaireAgent_has_tache."'" ; }
		if(!empty($gt_proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache > '".$gt_proprietaireAgent_has_tache."'" ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotSearch($noproprietaireAgent_has_tache) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent,"idagent") ; }
		if(!empty($lt_idagent)){ $sql .= " AND idagent < '".$lt_idagent."'" ; }
		if(!empty($gt_idagent)){ $sql .= " AND idagent > '".$gt_idagent."'" ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne,"personne_idpersonne") ; }
		if(!empty($lt_personne_idpersonne)){ $sql .= " AND personne_idpersonne < '".$lt_personne_idpersonne."'" ; }
		if(!empty($gt_personne_idpersonne)){ $sql .= " AND personne_idpersonne > '".$gt_personne_idpersonne."'" ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlSearch($estActifAgent,"estActifAgent") ; }
		if(!empty($lt_estActifAgent)){ $sql .= " AND estActifAgent < '".$lt_estActifAgent."'" ; }
		if(!empty($gt_estActifAgent)){ $sql .= " AND estActifAgent > '".$gt_estActifAgent."'" ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotSearch($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlSearch($dateDebutAgent,"dateDebutAgent") ; }
		if(!empty($lt_dateDebutAgent)){ $sql .= " AND dateDebutAgent < '".$lt_dateDebutAgent."'" ; }
		if(!empty($gt_dateDebutAgent)){ $sql .= " AND dateDebutAgent > '".$gt_dateDebutAgent."'" ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotSearch($nodateDebutAgent) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlSearch($idpersonne,"idpersonne") ; }
		if(!empty($lt_idpersonne)){ $sql .= " AND idpersonne < '".$lt_idpersonne."'" ; }
		if(!empty($gt_idpersonne)){ $sql .= " AND idpersonne > '".$gt_idpersonne."'" ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotSearch($noidpersonne) ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlSearch($langue_idlangue,"langue_idlangue") ; }
		if(!empty($lt_langue_idlangue)){ $sql .= " AND langue_idlangue < '".$lt_langue_idlangue."'" ; }
		if(!empty($gt_langue_idlangue)){ $sql .= " AND langue_idlangue > '".$gt_langue_idlangue."'" ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotSearch($nolangue_idlangue) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlSearch($nomPersonne,"nomPersonne") ; }
		if(!empty($lt_nomPersonne)){ $sql .= " AND nomPersonne < '".$lt_nomPersonne."'" ; }
		if(!empty($gt_nomPersonne)){ $sql .= " AND nomPersonne > '".$gt_nomPersonne."'" ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotSearch($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlSearch($prenomPersonne,"prenomPersonne") ; }
		if(!empty($lt_prenomPersonne)){ $sql .= " AND prenomPersonne < '".$lt_prenomPersonne."'" ; }
		if(!empty($gt_prenomPersonne)){ $sql .= " AND prenomPersonne > '".$gt_prenomPersonne."'" ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotSearch($noprenomPersonne) ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlSearch($prenom2Personne,"prenom2Personne") ; }
		if(!empty($lt_prenom2Personne)){ $sql .= " AND prenom2Personne < '".$lt_prenom2Personne."'" ; }
		if(!empty($gt_prenom2Personne)){ $sql .= " AND prenom2Personne > '".$gt_prenom2Personne."'" ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotSearch($noprenom2Personne) ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlSearch($sexePersonne,"sexePersonne") ; }
		if(!empty($lt_sexePersonne)){ $sql .= " AND sexePersonne < '".$lt_sexePersonne."'" ; }
		if(!empty($gt_sexePersonne)){ $sql .= " AND sexePersonne > '".$gt_sexePersonne."'" ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotSearch($nosexePersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlSearch($nomJeuneFillePersonne,"nomJeuneFillePersonne") ; }
		if(!empty($lt_nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne < '".$lt_nomJeuneFillePersonne."'" ; }
		if(!empty($gt_nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne > '".$gt_nomJeuneFillePersonne."'" ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotSearch($nonomJeuneFillePersonne) ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlSearch($etatCivilPersonne,"etatCivilPersonne") ; }
		if(!empty($lt_etatCivilPersonne)){ $sql .= " AND etatCivilPersonne < '".$lt_etatCivilPersonne."'" ; }
		if(!empty($gt_etatCivilPersonne)){ $sql .= " AND etatCivilPersonne > '".$gt_etatCivilPersonne."'" ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotSearch($noetatCivilPersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlSearch($regimeMatrimonialPersonne,"regimeMatrimonialPersonne") ; }
		if(!empty($lt_regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne < '".$lt_regimeMatrimonialPersonne."'" ; }
		if(!empty($gt_regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne > '".$gt_regimeMatrimonialPersonne."'" ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotSearch($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlSearch($dateNaissancePersonne,"dateNaissancePersonne") ; }
		if(!empty($lt_dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne < '".$lt_dateNaissancePersonne."'" ; }
		if(!empty($gt_dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne > '".$gt_dateNaissancePersonne."'" ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotSearch($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlSearch($commentairePersonne,"commentairePersonne") ; }
		if(!empty($lt_commentairePersonne)){ $sql .= " AND commentairePersonne < '".$lt_commentairePersonne."'" ; }
		if(!empty($gt_commentairePersonne)){ $sql .= " AND commentairePersonne > '".$gt_commentairePersonne."'" ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotSearch($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlSearch($villeNaissancePersonne,"villeNaissancePersonne") ; }
		if(!empty($lt_villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne < '".$lt_villeNaissancePersonne."'" ; }
		if(!empty($gt_villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne > '".$gt_villeNaissancePersonne."'" ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotSearch($novilleNaissancePersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlSearch($photoPersonne,"photoPersonne") ; }
		if(!empty($lt_photoPersonne)){ $sql .= " AND photoPersonne < '".$lt_photoPersonne."'" ; }
		if(!empty($gt_photoPersonne)){ $sql .= " AND photoPersonne > '".$gt_photoPersonne."'" ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotSearch($nophotoPersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlSearch($paysNaissancePerspnne,"paysNaissancePerspnne") ; }
		if(!empty($lt_paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne < '".$lt_paysNaissancePerspnne."'" ; }
		if(!empty($gt_paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne > '".$gt_paysNaissancePerspnne."'" ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotSearch($nopaysNaissancePerspnne) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlSearch($idtache,"idtache") ; }
		if(!empty($lt_idtache)){ $sql .= " AND idtache < '".$lt_idtache."'" ; }
		if(!empty($gt_idtache)){ $sql .= " AND idtache > '".$gt_idtache."'" ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotSearch($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlSearch($suivi_idsuivi,"suivi_idsuivi") ; }
		if(!empty($lt_suivi_idsuivi)){ $sql .= " AND suivi_idsuivi < '".$lt_suivi_idsuivi."'" ; }
		if(!empty($gt_suivi_idsuivi)){ $sql .= " AND suivi_idsuivi > '".$gt_suivi_idsuivi."'" ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotSearch($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlSearch($statut_tache_idstatut_tache,"statut_tache_idstatut_tache") ; }
		if(!empty($lt_statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache < '".$lt_statut_tache_idstatut_tache."'" ; }
		if(!empty($gt_statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache > '".$gt_statut_tache_idstatut_tache."'" ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotSearch($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlSearch($type_tache_idtype_tache,"type_tache_idtype_tache") ; }
		if(!empty($lt_type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache < '".$lt_type_tache_idtype_tache."'" ; }
		if(!empty($gt_type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache > '".$gt_type_tache_idtype_tache."'" ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotSearch($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlSearch($objetTache,"objetTache") ; }
		if(!empty($lt_objetTache)){ $sql .= " AND objetTache < '".$lt_objetTache."'" ; }
		if(!empty($gt_objetTache)){ $sql .= " AND objetTache > '".$gt_objetTache."'" ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotSearch($noobjetTache) ; }
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlSearch($dateCreationTache,"dateCreationTache") ; }
		if(!empty($lt_dateCreationTache)){ $sql .= " AND dateCreationTache < '".$lt_dateCreationTache."'" ; }
		if(!empty($gt_dateCreationTache)){ $sql .= " AND dateCreationTache > '".$gt_dateCreationTache."'" ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotSearch($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlSearch($heureDebutTache,"heureDebutTache") ; }
		if(!empty($lt_heureDebutTache)){ $sql .= " AND heureDebutTache < '".$lt_heureDebutTache."'" ; }
		if(!empty($gt_heureDebutTache)){ $sql .= " AND heureDebutTache > '".$gt_heureDebutTache."'" ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotSearch($noheureDebutTache) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlSearch($heureCreationTache,"heureCreationTache") ; }
		if(!empty($lt_heureCreationTache)){ $sql .= " AND heureCreationTache < '".$lt_heureCreationTache."'" ; }
		if(!empty($gt_heureCreationTache)){ $sql .= " AND heureCreationTache > '".$gt_heureCreationTache."'" ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotSearch($noheureCreationTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlSearch($dateDebutTache,"dateDebutTache") ; }
		if(!empty($lt_dateDebutTache)){ $sql .= " AND dateDebutTache < '".$lt_dateDebutTache."'" ; }
		if(!empty($gt_dateDebutTache)){ $sql .= " AND dateDebutTache > '".$gt_dateDebutTache."'" ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotSearch($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlSearch($heureFinTache,"heureFinTache") ; }
		if(!empty($lt_heureFinTache)){ $sql .= " AND heureFinTache < '".$lt_heureFinTache."'" ; }
		if(!empty($gt_heureFinTache)){ $sql .= " AND heureFinTache > '".$gt_heureFinTache."'" ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotSearch($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlSearch($dateFinTache,"dateFinTache") ; }
		if(!empty($lt_dateFinTache)){ $sql .= " AND dateFinTache < '".$lt_dateFinTache."'" ; }
		if(!empty($gt_dateFinTache)){ $sql .= " AND dateFinTache > '".$gt_dateFinTache."'" ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotSearch($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlSearch($commentaireTache,"commentaireTache") ; }
		if(!empty($lt_commentaireTache)){ $sql .= " AND commentaireTache < '".$lt_commentaireTache."'" ; }
		if(!empty($gt_commentaireTache)){ $sql .= " AND commentaireTache > '".$gt_commentaireTache."'" ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotSearch($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlSearch($valeurTache,"valeurTache") ; }
		if(!empty($lt_valeurTache)){ $sql .= " AND valeurTache < '".$lt_valeurTache."'" ; }
		if(!empty($gt_valeurTache)){ $sql .= " AND valeurTache > '".$gt_valeurTache."'" ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotSearch($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlSearch($resultatTache,"resultatTache") ; }
		if(!empty($lt_resultatTache)){ $sql .= " AND resultatTache < '".$lt_resultatTache."'" ; }
		if(!empty($gt_resultatTache)){ $sql .= " AND resultatTache > '".$gt_resultatTache."'" ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotSearch($noresultatTache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlSearch($migrateidtache,"migrateidtache") ; }
		if(!empty($lt_migrateidtache)){ $sql .= " AND migrateidtache < '".$lt_migrateidtache."'" ; }
		if(!empty($gt_migrateidtache)){ $sql .= " AND migrateidtache > '".$gt_migrateidtache."'" ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotSearch($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlSearch($migrateidtacheTech,"migrateidtacheTech") ; }
		if(!empty($lt_migrateidtacheTech)){ $sql .= " AND migrateidtacheTech < '".$lt_migrateidtacheTech."'" ; }
		if(!empty($gt_migrateidtacheTech)){ $sql .= " AND migrateidtacheTech > '".$gt_migrateidtacheTech."'" ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotSearch($nomigrateidtacheTech) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlSearch($idagent_writer,"idagent_writer") ; }
		if(!empty($lt_idagent_writer)){ $sql .= " AND idagent_writer < '".$lt_idagent_writer."'" ; }
		if(!empty($gt_idagent_writer)){ $sql .= " AND idagent_writer > '".$gt_idagent_writer."'" ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotSearch($noidagent_writer) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi,"type_suivi_idtype_suivi") ; }
		if(!empty($lt_type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi < '".$lt_type_suivi_idtype_suivi."'" ; }
		if(!empty($gt_type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi > '".$gt_type_suivi_idtype_suivi."'" ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAgentHasTache(){ 
		$sql="SELECT * FROM  agent_has_tache"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAgentHasTache($name="idagent_has_tache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAgent_has_tache , idagent_has_tache FROM agent_has_tache WHERE  1 ";  
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlIn($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotIn($noidagent_has_tache) ; }
			if(!empty($tache_idtache)){ $sql .= " AND tache_idtache ".sqlIn($tache_idtache) ; }
			if(!empty($notache_idtache)){ $sql .= " AND tache_idtache ".sqlNotIn($notache_idtache) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlIn($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotIn($noproprietaireAgent_has_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAgent_has_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAgentHasTache($name="idagent_has_tache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAgent_has_tache , idagent_has_tache FROM agent_has_tache ORDER BY nomAgent_has_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAgentHasTache = new AgentHasTache(); ?>