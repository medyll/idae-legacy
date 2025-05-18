<?   
class SecteurHasCp
{
	var $conn = null;
	function SecteurHasCp(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSecteurHasCp($params){ 
		$this->conn->AutoExecute("secteur_has_cp", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSecteurHasCp($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("secteur_has_cp", $params, "UPDATE", "idsecteur_has_cp = ".$idsecteur_has_cp); 
	}
	
	function deleteSecteurHasCp($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  secteur_has_cp WHERE idsecteur_has_cp = $idsecteur_has_cp"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="truncate table secteur_has_cp  "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneSecteurHasCp($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_secteur where 1 " ;
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlIn($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotIn($noidsecteur) ; }
			if(!empty($lt_idsecteur)){ $sql .= " AND idsecteur < '".$lt_idsecteur."'" ; }
			if(!empty($gt_idsecteur)){ $sql .= " AND idsecteur > '".$gt_idsecteur."'" ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlIn($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotIn($nonomSecteur) ; }
			if(!empty($lt_nomSecteur)){ $sql .= " AND nomSecteur < '".$lt_nomSecteur."'" ; }
			if(!empty($gt_nomSecteur)){ $sql .= " AND nomSecteur > '".$gt_nomSecteur."'" ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlIn($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotIn($nocommentaireSecteur) ; }
			if(!empty($lt_commentaireSecteur)){ $sql .= " AND commentaireSecteur < '".$lt_commentaireSecteur."'" ; }
			if(!empty($gt_commentaireSecteur)){ $sql .= " AND commentaireSecteur > '".$gt_commentaireSecteur."'" ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlIn($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotIn($nodateCreationSecteur) ; }
			if(!empty($lt_dateCreationSecteur)){ $sql .= " AND dateCreationSecteur < '".$lt_dateCreationSecteur."'" ; }
			if(!empty($gt_dateCreationSecteur)){ $sql .= " AND dateCreationSecteur > '".$gt_dateCreationSecteur."'" ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlIn($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotIn($nodateDebutSecteur) ; }
			if(!empty($lt_dateDebutSecteur)){ $sql .= " AND dateDebutSecteur < '".$lt_dateDebutSecteur."'" ; }
			if(!empty($gt_dateDebutSecteur)){ $sql .= " AND dateDebutSecteur > '".$gt_dateDebutSecteur."'" ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlIn($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotIn($nodateFinSecteur) ; }
			if(!empty($lt_dateFinSecteur)){ $sql .= " AND dateFinSecteur < '".$lt_dateFinSecteur."'" ; }
			if(!empty($gt_dateFinSecteur)){ $sql .= " AND dateFinSecteur > '".$gt_dateFinSecteur."'" ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlIn($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotIn($nodateClotureSecteur) ; }
			if(!empty($lt_dateClotureSecteur)){ $sql .= " AND dateClotureSecteur < '".$lt_dateClotureSecteur."'" ; }
			if(!empty($gt_dateClotureSecteur)){ $sql .= " AND dateClotureSecteur > '".$gt_dateClotureSecteur."'" ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlIn($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotIn($noestActifSecteur) ; }
			if(!empty($lt_estActifSecteur)){ $sql .= " AND estActifSecteur < '".$lt_estActifSecteur."'" ; }
			if(!empty($gt_estActifSecteur)){ $sql .= " AND estActifSecteur > '".$gt_estActifSecteur."'" ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlIn($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotIn($noordreSecteur) ; }
			if(!empty($lt_ordreSecteur)){ $sql .= " AND ordreSecteur < '".$lt_ordreSecteur."'" ; }
			if(!empty($gt_ordreSecteur)){ $sql .= " AND ordreSecteur > '".$gt_ordreSecteur."'" ; }
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlIn($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotIn($noidsecteur_has_cp) ; }
			if(!empty($lt_idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp < '".$lt_idsecteur_has_cp."'" ; }
			if(!empty($gt_idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp > '".$gt_idsecteur_has_cp."'" ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlIn($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotIn($nocpSecteur_has_cp) ; }
			if(!empty($lt_cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp < '".$lt_cpSecteur_has_cp."'" ; }
			if(!empty($gt_cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp > '".$gt_cpSecteur_has_cp."'" ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($lt_secteur_idsecteur)){ $sql .= " AND secteur_idsecteur < '".$lt_secteur_idsecteur."'" ; }
			if(!empty($gt_secteur_idsecteur)){ $sql .= " AND secteur_idsecteur > '".$gt_secteur_idsecteur."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSecteurHasCp($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from secteur_has_cp where 1 " ;
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlSearch($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotSearch($noidsecteur_has_cp) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlSearch($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotSearch($nosecteur_idsecteur) ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlSearch($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotSearch($nocpSecteur_has_cp) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSecteurHasCp(){ 
		$sql="SELECT * FROM  secteur_has_cp"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSecteurHasCp($name="idsecteur_has_cp",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSecteur_has_cp , idsecteur_has_cp FROM secteur_has_cp WHERE  1 ";  
			if(!empty($idsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlIn($idsecteur_has_cp) ; }
			if(!empty($noidsecteur_has_cp)){ $sql .= " AND idsecteur_has_cp ".sqlNotIn($noidsecteur_has_cp) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($cpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlIn($cpSecteur_has_cp) ; }
			if(!empty($nocpSecteur_has_cp)){ $sql .= " AND cpSecteur_has_cp ".sqlNotIn($nocpSecteur_has_cp) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSecteur_has_cp ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSecteurHasCp($name="idsecteur_has_cp",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSecteur_has_cp , idsecteur_has_cp FROM secteur_has_cp ORDER BY nomSecteur_has_cp ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSecteurHasCp = new SecteurHasCp(); ?>