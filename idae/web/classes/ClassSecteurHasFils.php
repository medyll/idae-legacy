<?   
class SecteurHasFils
{
	var $conn = null;
	function SecteurHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSecteurHasFils($params){ 
		$this->conn->AutoExecute("secteur_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSecteurHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("secteur_has_fils", $params, "UPDATE", "idsecteur_has_fils = ".$idsecteur_has_fils); 
	}
	
	function deleteSecteurHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  secteur_has_fils WHERE idsecteur_has_fils = $idsecteur_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSecteurHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from secteur_has_fils where 1 " ;
			if(!empty($idsecteur_has_fils)){ $sql .= " AND idsecteur_has_fils ".sqlIn($idsecteur_has_fils) ; }
			if(!empty($noidsecteur_has_fils)){ $sql .= " AND idsecteur_has_fils ".sqlNotIn($noidsecteur_has_fils) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($idfilssecteur)){ $sql .= " AND idfilssecteur ".sqlIn($idfilssecteur) ; }
			if(!empty($noidfilssecteur)){ $sql .= " AND idfilssecteur ".sqlNotIn($noidfilssecteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllSecteurHasFils(){ 
		$sql="SELECT * FROM  secteur_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSecteurHasFils($name="idsecteur_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSecteur_has_fils , idsecteur_has_fils FROM WHERE 1 ";  
			if(!empty($idsecteur_has_fils)){ $sql .= " AND idsecteur_has_fils ".sqlIn($idsecteur_has_fils) ; }
			if(!empty($noidsecteur_has_fils)){ $sql .= " AND idsecteur_has_fils ".sqlNotIn($noidsecteur_has_fils) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($idfilssecteur)){ $sql .= " AND idfilssecteur ".sqlIn($idfilssecteur) ; }
			if(!empty($noidfilssecteur)){ $sql .= " AND idfilssecteur ".sqlNotIn($noidfilssecteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomSecteur_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSecteurHasFils($name="idsecteur_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSecteur_has_fils , idsecteur_has_fils FROM secteur_has_fils ORDER BY nomSecteur_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSecteurHasFils = new SecteurHasFils(); ?>