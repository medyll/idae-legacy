<?   
class Secteur
{
	var $conn = null;
	function Secteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSecteur($params){ 
		$this->conn->AutoExecute("secteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("secteur", $params, "UPDATE", "idsecteur = ".$idsecteur); 
	}
	
	function deleteSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  secteur WHERE idsecteur = $idsecteur"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="truncate table secteur "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_secteur where 1 " ;
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlIn($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotIn($noidsecteur) ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlIn($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotIn($nonomSecteur) ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlIn($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotIn($nocommentaireSecteur) ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlIn($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotIn($nodateCreationSecteur) ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlIn($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotIn($nodateDebutSecteur) ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlIn($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotIn($nodateFinSecteur) ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlIn($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotIn($nodateClotureSecteur) ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlIn($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotIn($noestActifSecteur) ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlIn($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotIn($noordreSecteur) ; }
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlIn($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotIn($noidsecteur_has_cp) ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlIn($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotIn($nocpSecteur_has_cp) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_secteur where 1 " ;
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlSearch($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotSearch($noidsecteur) ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlSearch($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotSearch($nonomSecteur) ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlSearch($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotSearch($nocommentaireSecteur) ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlSearch($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotSearch($nodateCreationSecteur) ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlSearch($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotSearch($nodateDebutSecteur) ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlSearch($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotSearch($nodateFinSecteur) ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlSearch($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotSearch($nodateClotureSecteur) ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlSearch($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotSearch($noestActifSecteur) ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlSearch($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotSearch($noordreSecteur) ; }
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlSearch($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotSearch($noidsecteur_has_cp) ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlSearch($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotSearch($nocpSecteur_has_cp) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSecteur(){ 
		$sql="SELECT * FROM  secteur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSecteur($name="idsecteur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSecteur , idsecteur FROM vue_secteur WHERE  1 ";  
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlIn($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotIn($noidsecteur) ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlIn($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotIn($nonomSecteur) ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlIn($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotIn($nocommentaireSecteur) ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlIn($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotIn($nodateCreationSecteur) ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlIn($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotIn($nodateDebutSecteur) ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlIn($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotIn($nodateFinSecteur) ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlIn($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotIn($nodateClotureSecteur) ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlIn($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotIn($noestActifSecteur) ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlIn($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotIn($noordreSecteur) ; }
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlIn($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotIn($noidsecteur_has_cp) ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlIn($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotIn($nocpSecteur_has_cp) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSecteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSecteur($name="idsecteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSecteur , idsecteur FROM vue_secteur ORDER BY nomSecteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSecteur = new Secteur(); ?>