<?   
class TypeTache
{
	var $conn = null;
	function TypeTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeTache($params){ 
		$this->conn->AutoExecute("type_tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_tache", $params, "UPDATE", "idtype_tache = ".$idtype_tache); 
	}
	
	function deleteTypeTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_tache WHERE idtype_tache = $idtype_tache"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE  type_tache  "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneTypeTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_type_tache where 1 " ;
			if(!empty($idtype_tache)){ $sql .= " AND idtype_tache ".sqlIn($idtype_tache) ; }
			if(!empty($noidtype_tache)){ $sql .= " AND idtype_tache ".sqlNotIn($noidtype_tache) ; }
			if(!empty($nomType_tache)){ $sql .= " AND nomType_tache ".sqlIn($nomType_tache) ; }
			if(!empty($nonomType_tache)){ $sql .= " AND nomType_tache ".sqlNotIn($nonomType_tache) ; }
			if(!empty($ordreType_tache)){ $sql .= " AND ordreType_tache ".sqlIn($ordreType_tache) ; }
			if(!empty($noordreType_tache)){ $sql .= " AND ordreType_tache ".sqlNotIn($noordreType_tache) ; }
			if(!empty($commentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlIn($commentaireType_tache) ; }
			if(!empty($nocommentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlNotIn($nocommentaireType_tache) ; }
			if(!empty($idtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlIn($idtype_tache_has_type_suivi) ; }
			if(!empty($noidtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlNotIn($noidtype_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($ordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlIn($ordreType_tache_has_type_suivi) ; }
			if(!empty($noordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlNotIn($noordreType_tache_has_type_suivi) ; }
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
			if(!empty($codeType_tache)){ $sql .= " AND codeType_tache ".sqlIn($codeType_tache) ; }
			if(!empty($nocodeType_tache)){ $sql .= " AND codeType_tache ".sqlNotIn($nocodeType_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_type_tache where 1 " ;
			if(!empty($idtype_tache)){ $sql .= " AND idtype_tache ".sqlSearch($idtype_tache) ; }
			if(!empty($noidtype_tache)){ $sql .= " AND idtype_tache ".sqlNotSearch($noidtype_tache) ; }
			if(!empty($nomType_tache)){ $sql .= " AND nomType_tache ".sqlSearch($nomType_tache) ; }
			if(!empty($nonomType_tache)){ $sql .= " AND nomType_tache ".sqlNotSearch($nonomType_tache) ; }
			if(!empty($ordreType_tache)){ $sql .= " AND ordreType_tache ".sqlSearch($ordreType_tache) ; }
			if(!empty($noordreType_tache)){ $sql .= " AND ordreType_tache ".sqlNotSearch($noordreType_tache) ; }
			if(!empty($commentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlSearch($commentaireType_tache) ; }
			if(!empty($nocommentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlNotSearch($nocommentaireType_tache) ; }
			if(!empty($idtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlSearch($idtype_tache_has_type_suivi) ; }
			if(!empty($noidtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlNotSearch($noidtype_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlSearch($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotSearch($notype_tache_idtype_tache) ; }
			if(!empty($ordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlSearch($ordreType_tache_has_type_suivi) ; }
			if(!empty($noordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlNotSearch($noordreType_tache_has_type_suivi) ; }
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
			if(!empty($codeType_tache)){ $sql .= " AND codeType_tache ".sqlSearch($codeType_tache) ; }
			if(!empty($nocodeType_tache)){ $sql .= " AND codeType_tache ".sqlNotSearch($nocodeType_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeTache(){ 
		$sql="SELECT * FROM  vue_type_tache"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeTache($name="idvue_type_tache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_tache , type_tache_idtype_tache FROM vue_type_tache WHERE 1 ";  
			if(!empty($idtype_tache)){ $sql .= " AND idtype_tache ".sqlIn($idtype_tache) ; }
			if(!empty($noidtype_tache)){ $sql .= " AND idtype_tache ".sqlNotIn($noidtype_tache) ; }
			if(!empty($nomType_tache)){ $sql .= " AND nomType_tache ".sqlIn($nomType_tache) ; }
			if(!empty($nonomType_tache)){ $sql .= " AND nomType_tache ".sqlNotIn($nonomType_tache) ; }
			if(!empty($ordreType_tache)){ $sql .= " AND ordreType_tache ".sqlIn($ordreType_tache) ; }
			if(!empty($noordreType_tache)){ $sql .= " AND ordreType_tache ".sqlNotIn($noordreType_tache) ; }
			if(!empty($commentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlIn($commentaireType_tache) ; }
			if(!empty($nocommentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlNotIn($nocommentaireType_tache) ; }
			if(!empty($idtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlIn($idtype_tache_has_type_suivi) ; }
			if(!empty($noidtype_tache_has_type_suivi)){ $sql .= " AND idtype_tache_has_type_suivi ".sqlNotIn($noidtype_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($ordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlIn($ordreType_tache_has_type_suivi) ; }
			if(!empty($noordreType_tache_has_type_suivi)){ $sql .= " AND ordreType_tache_has_type_suivi ".sqlNotIn($noordreType_tache_has_type_suivi) ; }
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
			if(!empty($codeType_tache)){ $sql .= " AND codeType_tache ".sqlIn($codeType_tache) ; }
			if(!empty($nocodeType_tache)){ $sql .= " AND codeType_tache ".sqlNotIn($nocodeType_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
			if(!empty($orderBy)){ $sql .= " ORDER BY $orderBy " ; }  
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeTache($name="idvue_type_tache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_type_tache , idvue_type_tache FROM vue_type_tache ORDER BY nomVue_type_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeTache = new TypeTache(); ?>