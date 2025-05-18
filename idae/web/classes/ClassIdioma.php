<?   
class Idioma
{
	var $conn = null;
	function Idioma(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createIdioma($params){ 
		$this->conn->AutoExecute("idioma", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdioma($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("idioma", $params, "UPDATE", "ididioma = ".$ididioma); 
	}
	
	function deleteIdioma($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  idioma WHERE ididioma = $ididioma"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdioma($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from idioma where 1 " ;
			if(!empty($ididioma)){ $sql .= " AND ididioma ".sqlIn($ididioma) ; }
			if(!empty($noididioma)){ $sql .= " AND ididioma ".sqlNotIn($noididioma) ; }
			if(!empty($keyIdioma)){ $sql .= " AND keyIdioma ".sqlIn($keyIdioma) ; }
			if(!empty($nokeyIdioma)){ $sql .= " AND keyIdioma ".sqlNotIn($nokeyIdioma) ; }
			if(!empty($fr)){ $sql .= " AND fr ".sqlIn($fr) ; }
			if(!empty($nofr)){ $sql .= " AND fr ".sqlNotIn($nofr) ; }
			if(!empty($uk)){ $sql .= " AND uk ".sqlIn($uk) ; }
			if(!empty($nouk)){ $sql .= " AND uk ".sqlNotIn($nouk) ; }
			if(!empty($it)){ $sql .= " AND it ".sqlIn($it) ; }
			if(!empty($noit)){ $sql .= " AND it ".sqlNotIn($noit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdioma($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from idioma where 1 " ;
			if(!empty($ididioma)){ $sql .= " AND ididioma ".sqlSearch($ididioma) ; }
			if(!empty($noididioma)){ $sql .= " AND ididioma ".sqlNotSearch($noididioma) ; }
			if(!empty($keyIdioma)){ $sql .= " AND keyIdioma ".sqlSearch($keyIdioma) ; }
			if(!empty($nokeyIdioma)){ $sql .= " AND keyIdioma ".sqlNotSearch($nokeyIdioma) ; }
			if(!empty($fr)){ $sql .= " AND fr ".sqlSearch($fr) ; }
			if(!empty($nofr)){ $sql .= " AND fr ".sqlNotSearch($nofr) ; }
			if(!empty($uk)){ $sql .= " AND uk ".sqlSearch($uk) ; }
			if(!empty($nouk)){ $sql .= " AND uk ".sqlNotSearch($nouk) ; }
			if(!empty($it)){ $sql .= " AND it ".sqlSearch($it) ; }
			if(!empty($noit)){ $sql .= " AND it ".sqlNotSearch($noit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdioma(){ 
		$sql="SELECT * FROM  idioma"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdioma($name="ididioma",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomIdioma , ididioma FROM WHERE 1 ";  
			if(!empty($ididioma)){ $sql .= " AND ididioma ".sqlIn($ididioma) ; }
			if(!empty($noididioma)){ $sql .= " AND ididioma ".sqlNotIn($noididioma) ; }
			if(!empty($keyIdioma)){ $sql .= " AND keyIdioma ".sqlIn($keyIdioma) ; }
			if(!empty($nokeyIdioma)){ $sql .= " AND keyIdioma ".sqlNotIn($nokeyIdioma) ; }
			if(!empty($fr)){ $sql .= " AND fr ".sqlIn($fr) ; }
			if(!empty($nofr)){ $sql .= " AND fr ".sqlNotIn($nofr) ; }
			if(!empty($uk)){ $sql .= " AND uk ".sqlIn($uk) ; }
			if(!empty($nouk)){ $sql .= " AND uk ".sqlNotIn($nouk) ; }
			if(!empty($it)){ $sql .= " AND it ".sqlIn($it) ; }
			if(!empty($noit)){ $sql .= " AND it ".sqlNotIn($noit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomIdioma ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdioma($name="ididioma",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomIdioma , ididioma FROM idioma ORDER BY nomIdioma ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdioma = new Idioma(); ?>