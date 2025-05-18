<?   
class LigneLocationClientHasPersonne
{
	var $conn = null;
	function LigneLocationClientHasPersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLigneLocationClientHasPersonne($params){ 
		$this->conn->AutoExecute("ligne_location_client_has_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneLocationClientHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_location_client_has_personne", $params, "UPDATE", "idligne_location_client_has_personne = ".$idligne_location_client_has_personne); 
	}
	
	function deleteLigneLocationClientHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_location_client_has_personne WHERE idligne_location_client_has_personne = $idligne_location_client_has_personne"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE  ligne_location_client_has_personne "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLigneLocationClientHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_ligne_localisation_has_personne where 1 " ;
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
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlIn($idligne_location_client_has_personne) ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotIn($noidligne_location_client_has_personne) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneLocationClientHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_localisation_has_personne where 1 " ;
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
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlSearch($idligne_location_client_has_personne) ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotSearch($noidligne_location_client_has_personne) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlSearch($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotSearch($noligne_location_client_idligne_location_client) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneLocationClientHasPersonne(){ 
		$sql="SELECT * FROM  ligne_location_client_has_personne"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneLocationClientHasPersonne($name="idligne_location_client_has_personne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_location_client_has_personne , idligne_location_client_has_personne FROM ligne_location_client_has_personne WHERE  1 ";  
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlIn($idligne_location_client_has_personne) ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotIn($noidligne_location_client_has_personne) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_location_client_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneLocationClientHasPersonne($name="idligne_location_client_has_personne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_location_client_has_personne , idligne_location_client_has_personne FROM ligne_location_client_has_personne ORDER BY nomLigne_location_client_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLigneLocationClientHasPersonne = new LigneLocationClientHasPersonne(); ?>