<?   
class PersonneHasEmail
{
	var $conn = null;
	function PersonneHasEmail()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createPersonneHasEmail($params)
	{ 
		$this->conn->AutoExecute("personne_has_email", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updatePersonneHasEmail($params)
	{
		$this->conn->AutoExecute($rs, $params, "UPDATE", "idpersonne_has_email = ".$idpersonne_has_email); 
	}
	
	function truncate()
	{ 
		$sql="truncate table  personne_has_email "; 
		return $this->conn->Execute($sql); 	
	}
	
	function deletePersonneHasEmail($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="delete from  personne_has_email where idpersonne_has_email = $idpersonne_has_email"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOnePersonneHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_personne_has_email where 1 " ;
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
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlIn($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotIn($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlIn($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotIn($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlIn($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotIn($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlIn($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotIn($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlIn($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotIn($noordreEmail) ; }
			if(!empty($idpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlIn($idpersonne_has_email) ; }
			if(!empty($noidpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlNotIn($noidpersonne_has_email) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlIn($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotIn($noemail_idemail) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOnePersonneHasEmail($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_personne_has_email where 1 " ;
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
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlSearch($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotSearch($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlSearch($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotSearch($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlSearch($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotSearch($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlSearch($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotSearch($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlSearch($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotSearch($noordreEmail) ; }
			if(!empty($idpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlSearch($idpersonne_has_email) ; }
			if(!empty($noidpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlNotSearch($noidpersonne_has_email) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlSearch($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotSearch($noemail_idemail) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllPersonneHasEmail($params)
	{
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from  personne_has_email"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOnePersonneHasEmail($name="idvue_personne_has_email",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_personne_has_email , idvue_personne_has_email FROM vue_personne_has_email WHERE  1 ";  
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
			if(!empty($idemail)){ $sql .= " AND idemail ".sqlIn($idemail) ; }
			if(!empty($noidemail)){ $sql .= " AND idemail ".sqlNotIn($noidemail) ; }
			if(!empty($emailEmail)){ $sql .= " AND emailEmail ".sqlIn($emailEmail) ; }
			if(!empty($noemailEmail)){ $sql .= " AND emailEmail ".sqlNotIn($noemailEmail) ; }
			if(!empty($commentaireEmail)){ $sql .= " AND commentaireEmail ".sqlIn($commentaireEmail) ; }
			if(!empty($nocommentaireEmail)){ $sql .= " AND commentaireEmail ".sqlNotIn($nocommentaireEmail) ; }
			if(!empty($principalEmail)){ $sql .= " AND principalEmail ".sqlIn($principalEmail) ; }
			if(!empty($noprincipalEmail)){ $sql .= " AND principalEmail ".sqlNotIn($noprincipalEmail) ; }
			if(!empty($ordreEmail)){ $sql .= " AND ordreEmail ".sqlIn($ordreEmail) ; }
			if(!empty($noordreEmail)){ $sql .= " AND ordreEmail ".sqlNotIn($noordreEmail) ; }
			if(!empty($idpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlIn($idpersonne_has_email) ; }
			if(!empty($noidpersonne_has_email)){ $sql .= " AND idpersonne_has_email ".sqlNotIn($noidpersonne_has_email) ; }
			if(!empty($email_idemail)){ $sql .= " AND email_idemail ".sqlIn($email_idemail) ; }
			if(!empty($noemail_idemail)){ $sql .= " AND email_idemail ".sqlNotIn($noemail_idemail) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_personne_has_email ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectPersonneHasEmail($name="idvue_personne_has_email",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_personne_has_email , idvue_personne_has_email FROM vue_personne_has_email ORDER BY nomVue_personne_has_email ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassPersonneHasEmail = new PersonneHasEmail(); ?>