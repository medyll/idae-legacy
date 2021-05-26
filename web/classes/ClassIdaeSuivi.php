<?   
class IdaeSuivi
{
	var $conn = null;
	function IdaeSuivi(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeSuivi($params){ 
		$this->conn->AutoExecute("vue_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_suivi", $params, "UPDATE", "idvue_suivi = ".$idvue_suivi); 
	}
	
	function deleteIdaeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_suivi WHERE idvue_suivi = $idvue_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_suivi where 1 " ;
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlIn($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotIn($nodateCreationSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlIn($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotIn($nocommentaireSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlIn($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotIn($noobjetSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlIn($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotIn($nodateFinSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlIn($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotIn($nodateDebutSuivi) ; }
			if(!empty($suivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlIn($suivi_idSuivi) ; }
			if(!empty($nosuivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlNotIn($nosuivi_idSuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_suivi where 1 " ;
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlSearch($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotSearch($noidsuivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlSearch($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotSearch($nodateCreationSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlSearch($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotSearch($nocommentaireSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlSearch($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotSearch($noobjetSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlSearch($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotSearch($nodateFinSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlSearch($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotSearch($nodateDebutSuivi) ; }
			if(!empty($suivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlSearch($suivi_idSuivi) ; }
			if(!empty($nosuivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlNotSearch($nosuivi_idSuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeSuivi(){ 
		$sql="SELECT * FROM  vue_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeSuivi($name="idvue_suivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_suivi , idvue_suivi FROM vue_suivi WHERE  1 ";  
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlIn($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotIn($nodateCreationSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlIn($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotIn($nocommentaireSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlIn($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotIn($noobjetSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlIn($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotIn($nodateFinSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlIn($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotIn($nodateDebutSuivi) ; }
			if(!empty($suivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlIn($suivi_idSuivi) ; }
			if(!empty($nosuivi_idSuivi)){ $sql .= " AND suivi_idSuivi ".sqlNotIn($nosuivi_idSuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeSuivi($name="idvue_suivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_suivi , idvue_suivi FROM vue_suivi ORDER BY nomVue_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeSuivi = new IdaeSuivi(); ?>