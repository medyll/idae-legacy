<?   
class Suivi
{
	var $conn = null;
	function Suivi(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSuivi($params){ 
		$this->conn->AutoExecute("suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("suivi", $params, "UPDATE", "idsuivi = ".$idsuivi); 
	}
	
	function deleteSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  suivi WHERE idsuivi = $idsuivi"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){	
		$sql="TRUNCATE TABLE suivi"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_suivi where 1 " ;
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlIn($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotIn($nodateCreationSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlIn($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotIn($nodateDebutSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlIn($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotIn($nodateFinSuivi) ; }
			if(!empty($dateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlIn($dateClotureSuivi) ; }
			if(!empty($nodateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlNotIn($nodateClotureSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlIn($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotIn($noobjetSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlIn($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotIn($nocommentaireSuivi) ; }
			if(!empty($idtype_suivi)){ $sql .= " AND idtype_suivi ".sqlIn($idtype_suivi) ; }
			if(!empty($noidtype_suivi)){ $sql .= " AND idtype_suivi ".sqlNotIn($noidtype_suivi) ; }
			if(!empty($nomType_suivi)){ $sql .= " AND nomType_suivi ".sqlIn($nomType_suivi) ; }
			if(!empty($nonomType_suivi)){ $sql .= " AND nomType_suivi ".sqlNotIn($nonomType_suivi) ; }
			if(!empty($commentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlIn($commentaireType_suivi) ; }
			if(!empty($nocommentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlNotIn($nocommentaireType_suivi) ; }
			if(!empty($dateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlIn($dateDebutType_suivi) ; }
			if(!empty($nodateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlNotIn($nodateDebutType_suivi) ; }
			if(!empty($dateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlIn($dateFinType_suivi) ; }
			if(!empty($nodateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlNotIn($nodateFinType_suivi) ; }
			if(!empty($ordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlIn($ordreTypeSuivi) ; }
			if(!empty($noordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlNotIn($noordreTypeSuivi) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_suivi where 1 " ;
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlSearch($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotSearch($noidsuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlSearch($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotSearch($nodateCreationSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlSearch($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotSearch($nodateDebutSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlSearch($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotSearch($nodateFinSuivi) ; }
			if(!empty($dateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlSearch($dateClotureSuivi) ; }
			if(!empty($nodateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlNotSearch($nodateClotureSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlSearch($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotSearch($noobjetSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlSearch($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotSearch($nocommentaireSuivi) ; }
			if(!empty($idtype_suivi)){ $sql .= " AND idtype_suivi ".sqlSearch($idtype_suivi) ; }
			if(!empty($noidtype_suivi)){ $sql .= " AND idtype_suivi ".sqlNotSearch($noidtype_suivi) ; }
			if(!empty($nomType_suivi)){ $sql .= " AND nomType_suivi ".sqlSearch($nomType_suivi) ; }
			if(!empty($nonomType_suivi)){ $sql .= " AND nomType_suivi ".sqlNotSearch($nonomType_suivi) ; }
			if(!empty($commentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlSearch($commentaireType_suivi) ; }
			if(!empty($nocommentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlNotSearch($nocommentaireType_suivi) ; }
			if(!empty($dateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlSearch($dateDebutType_suivi) ; }
			if(!empty($nodateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlNotSearch($nodateDebutType_suivi) ; }
			if(!empty($dateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlSearch($dateFinType_suivi) ; }
			if(!empty($nodateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlNotSearch($nodateFinType_suivi) ; }
			if(!empty($ordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlSearch($ordreTypeSuivi) ; }
			if(!empty($noordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlNotSearch($noordreTypeSuivi) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSuivi(){ 
		$sql="SELECT * FROM  suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSuivi($name="idsuivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSuivi , idsuivi FROM WHERE 1 ";  
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlIn($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotIn($nodateCreationSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlIn($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotIn($nodateDebutSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlIn($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotIn($nodateFinSuivi) ; }
			if(!empty($dateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlIn($dateClotureSuivi) ; }
			if(!empty($nodateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlNotIn($nodateClotureSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlIn($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotIn($noobjetSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlIn($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotIn($nocommentaireSuivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSuivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSuivi($name="idsuivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSuivi , idsuivi FROM suivi ORDER BY nomSuivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSuivi = new Suivi(); ?>