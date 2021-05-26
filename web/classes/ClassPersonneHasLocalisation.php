<?   
class PersonneHasLocalisation
{
	var $conn = null;
	function PersonneHasLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createPersonneHasLocalisation($params){ 
		$this->conn->AutoExecute("personne_has_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updatePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("personne_has_localisation", $params, "UPDATE", "idpersonne_has_localisation = ".$idpersonne_has_localisation); 
	}
	
	function truncate(){ 
		$sql="TRUNCATE TABLE  personne_has_localisation "; 
		return $this->conn->Execute($sql); 	
	}
	
	function deletePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  personne_has_localisation WHERE idpersonne_has_localisation = $idpersonne_has_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOnePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = empty($all)? '*' : $all ;
		$sql="select ".$all." from vue_search_personne_localisation where 1 " ;
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
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlIn($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotIn($noemailEmail) ; }
			if(!empty($commentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlIn($commentaireSociete_has_personne) ; }
			if(!empty($nocommentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlNotIn($nocommentaireSociete_has_personne) ; }
			if(!empty($fonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlIn($fonctionSociete_has_personne) ; }
			if(!empty($nofonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlNotIn($nofonctionSociete_has_personne) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($idpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlIn($idpersonne_has_localisation) ; }
			if(!empty($noidpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlNotIn($noidpersonne_has_localisation) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlIn($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotIn($noidemail) ; }
			if(!empty($idsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlIn($idsociete_has_personne) ; }
			if(!empty($noidsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlNotIn($noidsociete_has_personne) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOnePersonneHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_search_personne_localisation where 1 " ;
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
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlSearch($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotSearch($nocommentaireTelfax) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlSearch($numeroTelfax,'numeroTelfax') ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotSearch($nonumeroTelfax) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlSearch($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotSearch($nocommentaireAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlSearch($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotSearch($nopays) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlSearch($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotSearch($novilleAdresse) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlSearch($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotSearch($nocodePostalAdresse) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlSearch($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotSearch($noadresse2) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlSearch($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotSearch($noadresse1) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlSearch($emailEmail,'emailEmail') ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotSearch($noemailEmail) ; }
			if(!empty($commentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlSearch($commentaireSociete_has_personne) ; }
			if(!empty($nocommentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlNotSearch($nocommentaireSociete_has_personne) ; }
			if(!empty($fonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlSearch($fonctionSociete_has_personne) ; }
			if(!empty($nofonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlNotSearch($nofonctionSociete_has_personne) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($idpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlSearch($idpersonne_has_localisation) ; }
			if(!empty($noidpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlNotSearch($noidpersonne_has_localisation) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlSearch($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotSearch($noidemail) ; }
			if(!empty($idsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlSearch($idsociete_has_personne) ; }
			if(!empty($noidsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlNotSearch($noidsociete_has_personne) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlSearch($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotSearch($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlSearch($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotSearch($notype_telfax_idtype_telfax) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlSearch($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotSearch($noidadresse) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllPersonneHasLocalisation(){ 
		$sql="SELECT * FROM  personne_has_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOnePersonneHasLocalisation($name="idpersonne_has_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPersonne_has_localisation , idpersonne_has_localisation FROM personne_has_localisation WHERE  1 ";  
			if(!empty($idpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlIn($idpersonne_has_localisation) ; }
			if(!empty($noidpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlNotIn($noidpersonne_has_localisation) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomPersonne_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectPersonneHasLocalisation($name="idpersonne_has_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomPersonne_has_localisation , idpersonne_has_localisation FROM personne_has_localisation ORDER BY nomPersonne_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassPersonneHasLocalisation = new PersonneHasLocalisation(); ?>