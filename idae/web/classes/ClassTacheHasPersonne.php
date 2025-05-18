<?   
class TacheHasPersonne
{
	var $conn = null;
	function TacheHasPersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTacheHasPersonne($params){ 
		$this->conn->AutoExecute("tache_has_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTacheHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("tache_has_personne", $params, "UPDATE", "idtache_has_personne = ".$idtache_has_personne); 
	}
	
	function deleteTacheHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  tache_has_personne WHERE idtache_has_personne = $idtache_has_personne"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TACBLE  tache_has_personne "; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTacheHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_tache_has_personne where 1 " ;
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
			if(!empty($idtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlIn($idtache_has_personne) ; }
			if(!empty($noidtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlNotIn($noidtache_has_personne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTacheHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_tache_has_personne where 1 " ;
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
			if(!empty($idtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlSearch($idtache_has_personne) ; }
			if(!empty($noidtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlNotSearch($noidtache_has_personne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTacheHasPersonne(){ 
		$sql="SELECT * FROM  vue_tache_has_personne"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTacheHasPersonne($name="idvue_tache_has_personne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPersonne , idvue_tache_has_personne FROM vue_tache_has_personne WHERE  1 ";  
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
			if(!empty($idtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlIn($idtache_has_personne) ; }
			if(!empty($noidtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlNotIn($noidtache_has_personne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_tache_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTacheHasPersonne($name="idtache_has_personne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomTache_has_personne , idtache_has_personne FROM tache_has_personne ORDER BY nomTache_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTacheHasPersonne = new TacheHasPersonne(); ?>