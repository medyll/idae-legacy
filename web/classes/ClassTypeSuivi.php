<?   
class TypeSuivi
{
	var $conn = null;
	function TypeSuivi(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeSuivi($params){ 
		$this->conn->AutoExecute("type_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_suivi", $params, "UPDATE", "idtype_suivi = ".$idtype_suivi); 
	}
	
	function deleteTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_suivi WHERE idtype_suivi = $idtype_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from type_suivi where 1 " ;
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
	function searchOneTypeSuivi($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_suivi where 1 " ;
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
	function getAllTypeSuivi(){ 
		$sql="SELECT * FROM  type_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeSuivi($name="idtype_suivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_suivi , idtype_suivi FROM type_suivi WHERE 1 ";  
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeSuivi($name="idtype_suivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_suivi , idtype_suivi FROM type_suivi ORDER BY nomType_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeSuivi = new TypeSuivi(); ?>