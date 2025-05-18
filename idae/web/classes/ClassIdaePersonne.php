<?   
class IdaePersonne
{
	var $conn = null;
	function IdaePersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaePersonne($params){ 
		$this->conn->AutoExecute("vue_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaePersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_personne", $params, "UPDATE", "idvue_personne = ".$idvue_personne); 
	}
	
	function deleteIdaePersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_personne WHERE idvue_personne = $idvue_personne"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaePersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_personne where 1 " ;
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($noidpersonne) ; }
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
			if(!empty($paysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlIn($paysNaissancePersonne) ; }
			if(!empty($nopaysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlNotIn($nopaysNaissancePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaePersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_personne where 1 " ;
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlSearch($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotSearch($noidpersonne) ; }
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
			if(!empty($paysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlSearch($paysNaissancePersonne) ; }
			if(!empty($nopaysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlNotSearch($nopaysNaissancePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlSearch($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotSearch($novilleNaissancePersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaePersonne(){ 
		$sql="SELECT * FROM  vue_personne"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaePersonne($name="idvue_personne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_personne , idvue_personne FROM vue_personne WHERE  1 ";  
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($noidpersonne) ; }
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
			if(!empty($paysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlIn($paysNaissancePersonne) ; }
			if(!empty($nopaysNaissancePersonne)){ $sql .= " AND paysNaissancePersonne ".sqlNotIn($nopaysNaissancePersonne) ; }
			if(!empty($villeNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlIn($villeNaissancePersonne) ; }
			if(!empty($novilleNaissancePersonne)){ $sql .= " AND villeNaissancePersonne ".sqlNotIn($novilleNaissancePersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaePersonne($name="idvue_personne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_personne , idvue_personne FROM vue_personne ORDER BY nomVue_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaePersonne = new IdaePersonne(); ?>