<?   
class IdaeCompteur
{
	var $conn = null;
	function IdaeCompteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeCompteur($params){ 
		$this->conn->AutoExecute("compteurs", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("compteurs", $params, "UPDATE", "idcompteurs = ".$idcompteurs); 
	}
	
	function deleteIdaeCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  compteurs WHERE idcompteurs = $idcompteurs"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from compteurs where 1 " ;
			if(!empty($idcompteurs)){ $sql .= " AND idcompteurs ".sqlIn($idcompteurs) ; }
			if(!empty($noidcompteurs)){ $sql .= " AND idcompteurs ".sqlNotIn($noidcompteurs) ; }
			if(!empty($dateCompteur)){ $sql .= " AND dateCompteur ".sqlIn($dateCompteur) ; }
			if(!empty($nodateCompteur)){ $sql .= " AND dateCompteur ".sqlNotIn($nodateCompteur) ; }
			if(!empty($heureCompteurs)){ $sql .= " AND heureCompteurs ".sqlIn($heureCompteurs) ; }
			if(!empty($noheureCompteurs)){ $sql .= " AND heureCompteurs ".sqlNotIn($noheureCompteurs) ; }
			if(!empty($valeurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlIn($valeurCompteursNB) ; }
			if(!empty($novaleurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlNotIn($novaleurCompteursNB) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlIn($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotIn($nosaisiePar) ; }
			if(!empty($valeurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlIn($valeurCompteursCouleur) ; }
			if(!empty($novaleurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlNotIn($novaleurCompteursCouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from compteurs where 1 " ;
			if(!empty($idcompteurs)){ $sql .= " AND idcompteurs ".sqlSearch($idcompteurs,"idcompteurs") ; }
			if(!empty($noidcompteurs)){ $sql .= " AND idcompteurs ".sqlNotSearch($noidcompteurs) ; }
			if(!empty($dateCompteur)){ $sql .= " AND dateCompteur ".sqlSearch($dateCompteur,"dateCompteur") ; }
			if(!empty($nodateCompteur)){ $sql .= " AND dateCompteur ".sqlNotSearch($nodateCompteur) ; }
			if(!empty($heureCompteurs)){ $sql .= " AND heureCompteurs ".sqlSearch($heureCompteurs,"heureCompteurs") ; }
			if(!empty($noheureCompteurs)){ $sql .= " AND heureCompteurs ".sqlNotSearch($noheureCompteurs) ; }
			if(!empty($valeurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlSearch($valeurCompteursNB,"valeurCompteursNB") ; }
			if(!empty($novaleurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlNotSearch($novaleurCompteursNB) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlSearch($saisiePar,"saisiePar") ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotSearch($nosaisiePar) ; }
			if(!empty($valeurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlSearch($valeurCompteursCouleur,"valeurCompteursCouleur") ; }
			if(!empty($novaleurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlNotSearch($novaleurCompteursCouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeCompteur(){ 
		$sql="SELECT * FROM  compteurs"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeCompteur($name="idcompteurs",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCompteurs , idcompteurs FROM compteurs WHERE  1 ";  
			if(!empty($idcompteurs)){ $sql .= " AND idcompteurs ".sqlIn($idcompteurs) ; }
			if(!empty($noidcompteurs)){ $sql .= " AND idcompteurs ".sqlNotIn($noidcompteurs) ; }
			if(!empty($dateCompteur)){ $sql .= " AND dateCompteur ".sqlIn($dateCompteur) ; }
			if(!empty($nodateCompteur)){ $sql .= " AND dateCompteur ".sqlNotIn($nodateCompteur) ; }
			if(!empty($heureCompteurs)){ $sql .= " AND heureCompteurs ".sqlIn($heureCompteurs) ; }
			if(!empty($noheureCompteurs)){ $sql .= " AND heureCompteurs ".sqlNotIn($noheureCompteurs) ; }
			if(!empty($valeurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlIn($valeurCompteursNB) ; }
			if(!empty($novaleurCompteursNB)){ $sql .= " AND valeurCompteursNB ".sqlNotIn($novaleurCompteursNB) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlIn($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotIn($nosaisiePar) ; }
			if(!empty($valeurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlIn($valeurCompteursCouleur) ; }
			if(!empty($novaleurCompteursCouleur)){ $sql .= " AND valeurCompteursCouleur ".sqlNotIn($novaleurCompteursCouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCompteurs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeCompteur($name="idcompteurs",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCompteurs , idcompteurs FROM compteurs ORDER BY nomCompteurs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeCompteur = new IdaeCompteur(); ?>