<?   
class Droit
{
	var $conn = null;
	function Droit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createDroit($params){ 
		$this->conn->AutoExecute("droit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateDroit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("droit", $params, "UPDATE", "iddroit = ".$iddroit); 
	}
	
	function deleteDroit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  droit WHERE iddroit = $iddroit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneDroit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from droit where 1 " ;
			if(!empty($iddroit)){ $sql .= " AND iddroit ".sqlIn($iddroit) ; }
			if(!empty($noiddroit)){ $sql .= " AND iddroit ".sqlNotIn($noiddroit) ; }
			if(!empty($nomDroit)){ $sql .= " AND nomDroit ".sqlIn($nomDroit) ; }
			if(!empty($nonomDroit)){ $sql .= " AND nomDroit ".sqlNotIn($nonomDroit) ; }
			if(!empty($codeDroit)){ $sql .= " AND codeDroit ".sqlIn($codeDroit) ; }
			if(!empty($nocodeDroit)){ $sql .= " AND codeDroit ".sqlNotIn($nocodeDroit) ; }
			if(!empty($ordreDroit)){ $sql .= " AND ordreDroit ".sqlIn($ordreDroit) ; }
			if(!empty($noordreDroit)){ $sql .= " AND ordreDroit ".sqlNotIn($noordreDroit) ; }
			if(!empty($codeGroupeDroit)){ $sql .= " AND codeGroupeDroit ".sqlIn($codeGroupeDroit) ; }
			if(!empty($nocodeGroupeDroit)){ $sql .= " AND codeGroupeDroit ".sqlNotIn($nocodeGroupeDroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneDroit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from droit where 1 " ;
			if(!empty($iddroit)){ $sql .= " AND iddroit ".sqlSearch($iddroit) ; }
			if(!empty($noiddroit)){ $sql .= " AND iddroit ".sqlNotSearch($noiddroit) ; }
			if(!empty($nomDroit)){ $sql .= " AND nomDroit ".sqlSearch($nomDroit) ; }
			if(!empty($nonomDroit)){ $sql .= " AND nomDroit ".sqlNotSearch($nonomDroit) ; }
			if(!empty($codeDroit)){ $sql .= " AND codeDroit ".sqlSearch($codeDroit) ; }
			if(!empty($nocodeDroit)){ $sql .= " AND codeDroit ".sqlNotSearch($nocodeDroit) ; }
			if(!empty($ordreDroit)){ $sql .= " AND ordreDroit ".sqlSearch($ordreDroit) ; }
			if(!empty($noordreDroit)){ $sql .= " AND ordreDroit ".sqlNotSearch($noordreDroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllDroit(){ 
		$sql="SELECT * FROM  droit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneDroit($name="iddroit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomDroit , iddroit FROM droit WHERE  1 ";  
			if(!empty($iddroit)){ $sql .= " AND iddroit ".sqlIn($iddroit) ; }
			if(!empty($noiddroit)){ $sql .= " AND iddroit ".sqlNotIn($noiddroit) ; }
			if(!empty($nomDroit)){ $sql .= " AND nomDroit ".sqlIn($nomDroit) ; }
			if(!empty($nonomDroit)){ $sql .= " AND nomDroit ".sqlNotIn($nonomDroit) ; }
			if(!empty($codeDroit)){ $sql .= " AND codeDroit ".sqlIn($codeDroit) ; }
			if(!empty($nocodeDroit)){ $sql .= " AND codeDroit ".sqlNotIn($nocodeDroit) ; }
			if(!empty($ordreDroit)){ $sql .= " AND ordreDroit ".sqlIn($ordreDroit) ; }
			if(!empty($noordreDroit)){ $sql .= " AND ordreDroit ".sqlNotIn($noordreDroit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomDroit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectDroit($name="iddroit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomDroit , iddroit FROM droit ORDER BY nomDroit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassDroit = new Droit(); ?>