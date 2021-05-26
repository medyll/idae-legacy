<?   
class StatutTacheHasTypeSuivi
{
	var $conn = null;
	function StatutTacheHasTypeSuivi(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createStatutTacheHasTypeSuivi($params){ 
		$this->conn->AutoExecute("statut_tache_has_type_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateStatutTacheHasTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("statut_tache_has_type_suivi", $params, "UPDATE", "idstatut_tache_has_type_suivi = ".$idstatut_tache_has_type_suivi); 
	}
	
	function deleteStatutTacheHasTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  statut_tache_has_type_suivi WHERE idstatut_tache_has_type_suivi = $idstatut_tache_has_type_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE statut_tache_has_type_suivi "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneStatutTacheHasTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from statut_tache_has_type_suivi where 1 " ;
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlIn($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotIn($noidstatut_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneStatutTacheHasTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from statut_tache_has_type_suivi where 1 " ;
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlSearch($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotSearch($noidstatut_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlSearch($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotSearch($nostatut_tache_idstatut_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllStatutTacheHasTypeSuivi(){ 
		$sql="SELECT * FROM  statut_tache_has_type_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneStatutTacheHasTypeSuivi($name="idstatut_tache_has_type_suivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomStatut_tache_has_type_suivi , idstatut_tache_has_type_suivi FROM statut_tache_has_type_suivi WHERE  1 ";  
			if(!empty($idstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlIn($idstatut_tache_has_type_suivi) ; }
			if(!empty($noidstatut_tache_has_type_suivi)){ $sql .= " AND idstatut_tache_has_type_suivi ".sqlNotIn($noidstatut_tache_has_type_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomStatut_tache_has_type_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectStatutTacheHasTypeSuivi($name="idstatut_tache_has_type_suivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomStatut_tache_has_type_suivi , idstatut_tache_has_type_suivi FROM statut_tache_has_type_suivi ORDER BY nomStatut_tache_has_type_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassStatutTacheHasTypeSuivi = new StatutTacheHasTypeSuivi(); ?>