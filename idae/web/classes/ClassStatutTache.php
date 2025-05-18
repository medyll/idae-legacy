<?   
class StatutTache
{
	var $conn = null;
	function StatutTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createStatutTache($params){ 
		$this->conn->AutoExecute("statut_tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("statut_tache", $params, "UPDATE", "idstatut_tache = ".$idstatut_tache); 
	}
	
	function deleteStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_statut_tache WHERE idstatut_tache = $idstatut_tache"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE statut_tache "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_statut_tache where 1 " ;
			if(!empty($idstatut_tache)){ $sql .= " AND idstatut_tache ".sqlIn($idstatut_tache) ; }
			if(!empty($noidstatut_tache)){ $sql .= " AND idstatut_tache ".sqlNotIn($noidstatut_tache) ; }
			if(!empty($nomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlIn($nomStatut_tache) ; }
			if(!empty($nonomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlNotIn($nonomStatut_tache) ; }
			if(!empty($valeurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlIn($valeurStatut_tache) ; }
			if(!empty($novaleurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlNotIn($novaleurStatut_tache) ; }
			if(!empty($commentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlIn($commentaireStatut_tache) ; }
			if(!empty($nocommentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlNotIn($nocommentaireStatut_tache) ; }
			if(!empty($ordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlIn($ordreStatut_tache) ; }
			if(!empty($noordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlNotIn($noordreStatut_tache) ; }
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlIn($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotIn($noidstatut_tache_has_type_suivi) ; }
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
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlIn($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotIn($nocodeStatut_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneStatutTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_statut_tache where 1 " ;
			if(!empty($idstatut_tache)){ $sql .= " AND idstatut_tache ".sqlSearch($idstatut_tache) ; }
			if(!empty($noidstatut_tache)){ $sql .= " AND idstatut_tache ".sqlNotSearch($noidstatut_tache) ; }
			if(!empty($nomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlIn($nomStatut_tache) ; }
			if(!empty($nonomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlNotIn($nonomStatut_tache) ; }
			if(!empty($valeurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlSearch($valeurStatut_tache) ; }
			if(!empty($novaleurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlNotSearch($novaleurStatut_tache) ; }
			if(!empty($commentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlSearch($commentaireStatut_tache) ; }
			if(!empty($nocommentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlNotSearch($nocommentaireStatut_tache) ; }
			if(!empty($ordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlSearch($ordreStatut_tache) ; }
			if(!empty($noordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlNotSearch($noordreStatut_tache) ; }
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlSearch($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotSearch($noidstatut_tache_has_type_suivi) ; }
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
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlSearch($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotSearch($nocodeStatut_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllStatutTache(){ 
		$sql="SELECT * FROM  vue_statut_tache"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneStatutTache($name="idstatut_tache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomStatut_tache , idstatut_tache FROM vue_statut_tache WHERE  1 ";  
			if(!empty($idstatut_tache)){ $sql .= " AND idstatut_tache ".sqlIn($idstatut_tache) ; }
			if(!empty($noidstatut_tache)){ $sql .= " AND idstatut_tache ".sqlNotIn($noidstatut_tache) ; }
			if(!empty($nomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlIn($nomStatut_tache) ; }
			if(!empty($nonomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlNotIn($nonomStatut_tache) ; }
			if(!empty($valeurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlIn($valeurStatut_tache) ; }
			if(!empty($novaleurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlNotIn($novaleurStatut_tache) ; }
			if(!empty($commentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlIn($commentaireStatut_tache) ; }
			if(!empty($nocommentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlNotIn($nocommentaireStatut_tache) ; }
			if(!empty($ordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlIn($ordreStatut_tache) ; }
			if(!empty($noordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlNotIn($noordreStatut_tache) ; }
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlIn($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotIn($noidstatut_tache_has_type_suivi) ; }
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
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlIn($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotIn($nocodeStatut_tache) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY ordreStatut_tache ";
		if(!empty($debug)){ echo $sql;   }
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectStatutTache($name="idstatut_tache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomStatut_tache , idstatut_tache FROM vue_statut_tache ORDER BY ordreStatut_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassStatutTache = new StatutTache(); ?>