<?   
class IdaeImportSecteur
{
	var $conn = null;
	function IdaeImportSecteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeImportSecteur($params){ 
		$this->conn->AutoExecute("importsecteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeImportSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("importsecteur", $params, "UPDATE", "idimportsecteur = ".$idimportsecteur); 
	}
	
	function deleteIdaeImportSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  importsecteur WHERE idimportsecteur = $idimportsecteur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeImportSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from importsecteur where 1 " ;
			if(!empty($codepostal)){ $sql .= " AND codepostal ".sqlIn($codepostal) ; }
			if(!empty($nocodepostal)){ $sql .= " AND codepostal ".sqlNotIn($nocodepostal) ; }
			if(!empty($lt_codepostal)){ $sql .= " AND codepostal < '".$lt_codepostal."'" ; }
			if(!empty($gt_codepostal)){ $sql .= " AND codepostal > '".$gt_codepostal."'" ; }
			if(!empty($ville)){ $sql .= " AND ville ".sqlIn($ville) ; }
			if(!empty($noville)){ $sql .= " AND ville ".sqlNotIn($noville) ; }
			if(!empty($lt_ville)){ $sql .= " AND ville < '".$lt_ville."'" ; }
			if(!empty($gt_ville)){ $sql .= " AND ville > '".$gt_ville."'" ; }
			if(!empty($secteur)){ $sql .= " AND secteur ".sqlIn($secteur) ; }
			if(!empty($nosecteur)){ $sql .= " AND secteur ".sqlNotIn($nosecteur) ; }
			if(!empty($lt_secteur)){ $sql .= " AND secteur < '".$lt_secteur."'" ; }
			if(!empty($gt_secteur)){ $sql .= " AND secteur > '".$gt_secteur."'" ; }
			if(!empty($nomAgent)){ $sql .= " AND nomAgent ".sqlIn($nomAgent) ; }
			if(!empty($nonomAgent)){ $sql .= " AND nomAgent ".sqlNotIn($nonomAgent) ; }
			if(!empty($lt_nomAgent)){ $sql .= " AND nomAgent < '".$lt_nomAgent."'" ; }
			if(!empty($gt_nomAgent)){ $sql .= " AND nomAgent > '".$gt_nomAgent."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeImportSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from importsecteur where 1 " ;
			if(!empty($codepostal)){ $sql .= " AND codepostal ".sqlSearch($codepostal,"codepostal") ; }
		if(!empty($lt_codepostal)){ $sql .= " AND codepostal < '".$lt_codepostal."'" ; }
		if(!empty($gt_codepostal)){ $sql .= " AND codepostal > '".$gt_codepostal."'" ; }
			if(!empty($nocodepostal)){ $sql .= " AND codepostal ".sqlNotSearch($nocodepostal) ; }
			if(!empty($ville)){ $sql .= " AND ville ".sqlSearch($ville,"ville") ; }
		if(!empty($lt_ville)){ $sql .= " AND ville < '".$lt_ville."'" ; }
		if(!empty($gt_ville)){ $sql .= " AND ville > '".$gt_ville."'" ; }
			if(!empty($noville)){ $sql .= " AND ville ".sqlNotSearch($noville) ; }
			if(!empty($secteur)){ $sql .= " AND secteur ".sqlSearch($secteur,"secteur") ; }
		if(!empty($lt_secteur)){ $sql .= " AND secteur < '".$lt_secteur."'" ; }
		if(!empty($gt_secteur)){ $sql .= " AND secteur > '".$gt_secteur."'" ; }
			if(!empty($nosecteur)){ $sql .= " AND secteur ".sqlNotSearch($nosecteur) ; }
			if(!empty($nomAgent)){ $sql .= " AND nomAgent ".sqlSearch($nomAgent,"nomAgent") ; }
		if(!empty($lt_nomAgent)){ $sql .= " AND nomAgent < '".$lt_nomAgent."'" ; }
		if(!empty($gt_nomAgent)){ $sql .= " AND nomAgent > '".$gt_nomAgent."'" ; }
			if(!empty($nonomAgent)){ $sql .= " AND nomAgent ".sqlNotSearch($nonomAgent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeImportSecteur(){ 
		$sql="SELECT * FROM  importsecteur"; 
		return $this->conn->Execute($sql) ;	
	} 
	function getSelectIdaeImportSecteur($name="idimportsecteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomImportsecteur , idimportsecteur FROM importsecteur ORDER BY nomImportsecteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeImportSecteur = new IdaeImportSecteur(); ?>