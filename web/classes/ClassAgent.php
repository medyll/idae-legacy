<?   
class Agent
{
	var $conn = null;
	function Agent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgent($params){ 
		$this->conn->AutoExecute("agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent", $params, "UPDATE", "idagent = ".$idagent); 
	}
	
	function deleteAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent WHERE idagent = $idagent"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE agent"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent where 1 " ;
			if(!empty($idagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlIn($idagent_has_groupe_agent) ; }
			if(!empty($noidagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlNotIn($noidagent_has_groupe_agent) ; }
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlIn($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotIn($noidagent_has_secteur) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlIn($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotIn($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlIn($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotIn($nopasswordAgent) ; }
			if(!empty($dateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlIn($dateCreationAgent) ; }
			if(!empty($nodateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlNotIn($nodateCreationAgent) ; }
			if(!empty($dateFinAgent)){ $sql .= " AND dateFinAgent ".sqlIn($dateFinAgent) ; }
			if(!empty($nodateFinAgent)){ $sql .= " AND dateFinAgent ".sqlNotIn($nodateFinAgent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($noidpersonne) ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlIn($langue_idlangue) ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotIn($nolangue_idlangue) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " OR prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlIn($prenom2Personne) ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotIn($noprenom2Personne) ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlIn($sexePersonne) ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotIn($nosexePersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlIn($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotIn($nonomJeuneFillePersonne) ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlIn($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotIn($noetatCivilPersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlIn($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotIn($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlIn($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotIn($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlIn($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotIn($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlIn($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotIn($nophotoPersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlIn($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotIn($nopaysNaissancePerspnne) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent where 1 " ;
			if(!empty($idagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlSearch($idagent_has_groupe_agent) ; }
			if(!empty($noidagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlNotSearch($noidagent_has_groupe_agent) ; }
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlSearch($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotSearch($noidagent_has_secteur) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlSearch($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotSearch($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlSearch($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotSearch($nopasswordAgent) ; }
			if(!empty($dateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlSearch($dateCreationAgent) ; }
			if(!empty($nodateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlNotSearch($nodateCreationAgent) ; }
			if(!empty($dateFinAgent)){ $sql .= " AND dateFinAgent ".sqlSearch($dateFinAgent) ; }
			if(!empty($nodateFinAgent)){ $sql .= " AND dateFinAgent ".sqlNotSearch($nodateFinAgent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlSearch($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotSearch($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlSearch($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotSearch($nodateDebutAgent) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlSearch($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotSearch($noidpersonne) ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlSearch($langue_idlangue) ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotSearch($nolangue_idlangue) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlSearch($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotSearch($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlSearch($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotSearch($noprenomPersonne) ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlSearch($prenom2Personne) ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotSearch($noprenom2Personne) ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlSearch($sexePersonne) ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotSearch($nosexePersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlSearch($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotSearch($nonomJeuneFillePersonne) ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlSearch($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotSearch($noetatCivilPersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlSearch($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotSearch($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlSearch($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotSearch($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlSearch($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotSearch($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlSearch($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotSearch($novilleNaissancePersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlSearch($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotSearch($nophotoPersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlSearch($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotSearch($nopaysNaissancePerspnne) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlSearch($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotSearch($nogroupe_agent_idgroupe_agent) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlSearch($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotSearch($nosecteur_idsecteur) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlSearch($nomGroupe_agent) ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotSearch($nonomGroupe_agent) ; }
			
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAgent(){ 
		$sql="SELECT * FROM  agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAgent($name="idagent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT concat(nomPersonne , ' ' , prenomPersonne)  , idagent FROM vue_agent WHERE  1 ";  
			if(!empty($idagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlIn($idagent_has_groupe_agent) ; }
			if(!empty($noidagent_has_groupe_agent)){ $sql .= " AND idagent_has_groupe_agent ".sqlNotIn($noidagent_has_groupe_agent) ; }
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlIn($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotIn($noidagent_has_secteur) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlIn($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotIn($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlIn($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotIn($nopasswordAgent) ; }
			if(!empty($dateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlIn($dateCreationAgent) ; }
			if(!empty($nodateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlNotIn($nodateCreationAgent) ; }
			if(!empty($dateFinAgent)){ $sql .= " AND dateFinAgent ".sqlIn($dateFinAgent) ; }
			if(!empty($nodateFinAgent)){ $sql .= " AND dateFinAgent ".sqlNotIn($nodateFinAgent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($noidpersonne) ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlIn($langue_idlangue) ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotIn($nolangue_idlangue) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlIn($prenom2Personne) ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotIn($noprenom2Personne) ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlIn($sexePersonne) ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotIn($nosexePersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlIn($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotIn($nonomJeuneFillePersonne) ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlIn($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotIn($noetatCivilPersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlIn($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotIn($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlIn($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotIn($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlIn($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotIn($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlIn($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotIn($nophotoPersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlIn($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotIn($nopaysNaissancePerspnne) ; }
			if(!empty($groupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlIn($groupe_agent_idgroupe_agent) ; }
			if(!empty($nogroupe_agent_idgroupe_agent)){ $sql .= " AND groupe_agent_idgroupe_agent ".sqlNotIn($nogroupe_agent_idgroupe_agent) ; }
			
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } else{ $sql .= " group by idagent " ; }
		$sql .=" ORDER BY nomPersonne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAgent($name="idvue_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomPersonne , idagent FROM vue_agent ORDER BY nomPersonne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAgent = new Agent(); ?>