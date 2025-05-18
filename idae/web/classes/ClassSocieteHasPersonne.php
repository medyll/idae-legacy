<?   
class SocieteHasPersonne
{
	var $conn = null;
	function SocieteHasPersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSocieteHasPersonne($params){ 
		$this->conn->AutoExecute("societe_has_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("societe_has_personne", $params, "UPDATE", "idsociete_has_personne = ".$idsociete_has_personne); 
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE  societe_has_personne "; 
		return $this->conn->Execute($sql); 	
	}
	function deleteSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  societe_has_personne WHERE idsociete_has_personne = $idsociete_has_personne"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_societe_has_personne where 1 " ;
			if(!empty($idsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlIn($idsociete_has_personne) ; }
			if(!empty($noidsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlNotIn($noidsociete_has_personne) ; }
			if(!empty($fonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlIn($fonctionSociete_has_personne) ; }
			if(!empty($nofonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlNotIn($nofonctionSociete_has_personne) ; }
			if(!empty($commentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlIn($commentaireSociete_has_personne) ; }
			if(!empty($nocommentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlNotIn($nocommentaireSociete_has_personne) ; }
			if(!empty($principaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlIn($principaleSocieteHasPersonne) ; }
			if(!empty($noprincipaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlNotIn($noprincipaleSocieteHasPersonne) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
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
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlIn($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotIn($noetatCivilPersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlIn($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotIn($nophotoPersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlIn($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotIn($nonomJeuneFillePersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlIn($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotIn($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlIn($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotIn($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlIn($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotIn($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlIn($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotIn($nopaysNaissancePerspnne) ; }
			
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$orNomPersonne = 'AND';
		if(!empty($prenomPersonne) && !empty($nomPersonne) && !empty($nomSociete)){
			$orNomPersonne = 'OR';
			}
		$sql="select * from vue_societe_has_personne where 1 " ;
			if(!empty($idsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlSearch($idsociete_has_personne) ; }
			if(!empty($noidsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlNotSearch($noidsociete_has_personne) ; }
			if(!empty($fonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlSearch($fonctionSociete_has_personne) ; }
			if(!empty($nofonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlNotSearch($nofonctionSociete_has_personne) ; }
			if(!empty($commentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlSearch($commentaireSociete_has_personne) ; }
			if(!empty($nocommentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlNotSearch($nocommentaireSociete_has_personne) ; }
			if(!empty($principaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlSearch($principaleSocieteHasPersonne) ; }
			if(!empty($noprincipaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlNotSearch($noprincipaleSocieteHasPersonne) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlSearch($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotSearch($noidpersonne) ; }
			if(!empty($langue_idlangue)){ $sql .= " AND langue_idlangue ".sqlSearch($langue_idlangue) ; }
			if(!empty($nolangue_idlangue)){ $sql .= " AND langue_idlangue ".sqlNotSearch($nolangue_idlangue) ; }
			if(!empty($nomPersonne)){ $sql .= $orNomPersonne."   nomPersonne ".sqlSearch($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotSearch($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " OR prenomPersonne ".sqlSearch($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotSearch($noprenomPersonne) ; }
			if(!empty($prenom2Personne)){ $sql .= " AND prenom2Personne ".sqlSearch($prenom2Personne) ; }
			if(!empty($noprenom2Personne)){ $sql .= " AND prenom2Personne ".sqlNotSearch($noprenom2Personne) ; }
			if(!empty($sexePersonne)){ $sql .= " AND sexePersonne ".sqlSearch($sexePersonne) ; }
			if(!empty($nosexePersonne)){ $sql .= " AND sexePersonne ".sqlNotSearch($nosexePersonne) ; }
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlSearch($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotSearch($noetatCivilPersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlSearch($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotSearch($nophotoPersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlSearch($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotSearch($nonomJeuneFillePersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlSearch($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotSearch($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlSearch($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotSearch($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlSearch($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotSearch($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlSearch($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotSearch($novilleNaissancePersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlSearch($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotSearch($nopaysNaissancePerspnne) ; }
			
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllSocieteHasPersonne(){ 
		$sql="SELECT * FROM  vue_societe_has_personne"; 
		return $this->conn->Execute($sql) ;	
	}
	
	function getSelectOneSocieteHasPersonne($name="idpersonne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPersonne , idpersonne FROM vue_societe_has_personne WHERE  1 ";  
			if(!empty($idsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlIn($idsociete_has_personne) ; }
			if(!empty($noidsociete_has_personne)){ $sql .= " AND idsociete_has_personne ".sqlNotIn($noidsociete_has_personne) ; }
			if(!empty($fonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlIn($fonctionSociete_has_personne) ; }
			if(!empty($nofonctionSociete_has_personne)){ $sql .= " AND fonctionSociete_has_personne ".sqlNotIn($nofonctionSociete_has_personne) ; }
			if(!empty($commentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlIn($commentaireSociete_has_personne) ; }
			if(!empty($nocommentaireSociete_has_personne)){ $sql .= " AND commentaireSociete_has_personne ".sqlNotIn($nocommentaireSociete_has_personne) ; }
			if(!empty($principaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlIn($principaleSocieteHasPersonne) ; }
			if(!empty($noprincipaleSocieteHasPersonne)){ $sql .= " AND principaleSocieteHasPersonne ".sqlNotIn($noprincipaleSocieteHasPersonne) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
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
			if(!empty($etatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlIn($etatCivilPersonne) ; }
			if(!empty($noetatCivilPersonne)){ $sql .= " AND etatCivilPersonne ".sqlNotIn($noetatCivilPersonne) ; }
			if(!empty($photoPersonne)){ $sql .= " AND photoPersonne ".sqlIn($photoPersonne) ; }
			if(!empty($nophotoPersonne)){ $sql .= " AND photoPersonne ".sqlNotIn($nophotoPersonne) ; }
			if(!empty($nomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlIn($nomJeuneFillePersonne) ; }
			if(!empty($nonomJeuneFillePersonne)){ $sql .= " AND nomJeuneFillePersonne ".sqlNotIn($nonomJeuneFillePersonne) ; }
			if(!empty($regimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlIn($regimeMatrimonialPersonne) ; }
			if(!empty($noregimeMatrimonialPersonne)){ $sql .= " AND regimeMatrimonialPersonne ".sqlNotIn($noregimeMatrimonialPersonne) ; }
			if(!empty($dateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlIn($dateNaissancePersonne) ; }
			if(!empty($nodateNaissancePersonne)){ $sql .= " AND dateNaissancePersonne ".sqlNotIn($nodateNaissancePersonne) ; }
			if(!empty($commentairePersonne)){ $sql .= " AND commentairePersonne ".sqlIn($commentairePersonne) ; }
			if(!empty($nocommentairePersonne)){ $sql .= " AND commentairePersonne ".sqlNotIn($nocommentairePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($paysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlIn($paysNaissancePerspnne) ; }
			if(!empty($nopaysNaissancePerspnne)){ $sql .= " AND paysNaissancePerspnne ".sqlNotIn($nopaysNaissancePerspnne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomPersonne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSocieteHasPersonne($name="idsociete_has_personne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSociete_has_personne , idsociete_has_personne FROM societe_has_personne ORDER BY nomSociete_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSocieteHasPersonne = new SocieteHasPersonne(); ?>