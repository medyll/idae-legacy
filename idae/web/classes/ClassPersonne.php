<?   
class Personne
{
	var $conn = null;
	function Personne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createPersonne($params){ 
		$this->conn->AutoExecute("personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updatePersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("personne", $params, "UPDATE", "idpersonne = ".$idpersonne); 
	}
	
	function deletePersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  personne WHERE idpersonne = $idpersonne"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE personne"; 
		return $this->conn->Execute($sql); 	
	}
	function getOnePersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from personne where 1 " ;
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOnePersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from personne where 1 " ;
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllPersonne(){ 
		$sql="SELECT * FROM  personne"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOnePersonne($name="idpersonne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomPersonne , idpersonne FROM WHERE 1 ";  
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomPersonne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectPersonne($name="idpersonne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomPersonne , idpersonne FROM personne ORDER BY nomPersonne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassPersonne = new Personne(); ?>