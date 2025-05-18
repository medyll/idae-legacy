<?   
class Compteur
{
	var $conn = null;
	function Compteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCompteur($params){ 
		$this->conn->AutoExecute("compteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("compteur", $params, "UPDATE", "idcompteur = ".$idcompteur); 
	}
	
	function deleteCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  compteur WHERE idcompteur = $idcompteur"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE compteur "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from compteur where 1 " ;
			if(!empty($idcompteur)){ $sql .= " AND idcompteur ".sqlIn($idcompteur) ; }
			if(!empty($noidcompteur)){ $sql .= " AND idcompteur ".sqlNotIn($noidcompteur) ; }
			if(!empty($dateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlIn($dateCreationCompteur) ; }
			if(!empty($nodateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlNotIn($nodateCreationCompteur) ; }
			if(!empty($heureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlIn($heureCreationCompteur) ; }
			if(!empty($noheureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlNotIn($noheureCreationCompteur) ; }
			if(!empty($valeurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlIn($valeurCompteurNB) ; }
			if(!empty($novaleurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlNotIn($novaleurCompteurNB) ; }
			if(!empty($valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlIn($valeurCompteurCouleur) ; }
			if(!empty($novaleurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlNotIn($novaleurCompteurCouleur) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlIn($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotIn($nosaisiePar) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from compteur where 1 " ;
			if(!empty($idcompteur)){ $sql .= " AND idcompteur ".sqlSearch($idcompteur) ; }
			if(!empty($noidcompteur)){ $sql .= " AND idcompteur ".sqlNotSearch($noidcompteur) ; }
			if(!empty($dateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlSearch($dateCreationCompteur) ; }
			if(!empty($nodateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlNotSearch($nodateCreationCompteur) ; }
			if(!empty($heureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlSearch($heureCreationCompteur) ; }
			if(!empty($noheureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlNotSearch($noheureCreationCompteur) ; }
			if(!empty($valeurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlSearch($valeurCompteurNB) ; }
			if(!empty($novaleurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlNotSearch($novaleurCompteurNB) ; }
			if(!empty($valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlSearch($valeurCompteurCouleur) ; }
			if(!empty($novaleurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlNotSearch($novaleurCompteurCouleur) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlSearch($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotSearch($nosaisiePar) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllCompteur(){ 
		$sql="SELECT * FROM  compteur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCompteur($name="idcompteur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCompteur , idcompteur FROM compteur WHERE  1 ";  
			if(!empty($idcompteur)){ $sql .= " AND idcompteur ".sqlIn($idcompteur) ; }
			if(!empty($noidcompteur)){ $sql .= " AND idcompteur ".sqlNotIn($noidcompteur) ; }
			if(!empty($dateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlIn($dateCreationCompteur) ; }
			if(!empty($nodateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlNotIn($nodateCreationCompteur) ; }
			if(!empty($heureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlIn($heureCreationCompteur) ; }
			if(!empty($noheureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlNotIn($noheureCreationCompteur) ; }
			if(!empty($valeurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlIn($valeurCompteurNB) ; }
			if(!empty($novaleurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlNotIn($novaleurCompteurNB) ; }
			if(!empty($valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlIn($valeurCompteurCouleur) ; }
			if(!empty($novaleurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlNotIn($novaleurCompteurCouleur) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlIn($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotIn($nosaisiePar) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCompteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCompteur($name="idcompteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCompteur , idcompteur FROM compteur ORDER BY nomCompteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCompteur = new Compteur(); ?>