<?   
class TypeLocationClient
{
	var $conn = null;
	function TypeLocationClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeLocationClient($params){ 
		$this->conn->AutoExecute("type_location_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_location_client", $params, "UPDATE", "idtype_location_client = ".$idtype_location_client); 
	}
	
	function deleteTypeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_location_client WHERE idtype_location_client = $idtype_location_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from type_location_client where 1 " ;
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlIn($idtype_location_client) ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotIn($noidtype_location_client) ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlIn($nomType_location_client) ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotIn($nonomType_location_client) ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlIn($commentaireType_location_client) ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotIn($nocommentaireType_location_client) ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlIn($ordreType_location_client) ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotIn($noordreType_location_client) ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlIn($codeType_location_client) ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotIn($nocodeType_location_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTypeLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_location_client where 1 " ;
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlSearch($idtype_location_client) ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotSearch($noidtype_location_client) ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlSearch($nomType_location_client) ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotSearch($nonomType_location_client) ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlSearch($commentaireType_location_client) ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotSearch($nocommentaireType_location_client) ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlSearch($ordreType_location_client) ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotSearch($noordreType_location_client) ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlSearch($codeType_location_client) ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotSearch($nocodeType_location_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTypeLocationClient(){ 
		$sql="SELECT * FROM  type_location_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeLocationClient($name="idtype_location_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_location_client , idtype_location_client FROM type_location_client WHERE  1 ";  
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlIn($idtype_location_client) ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotIn($noidtype_location_client) ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlIn($nomType_location_client) ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotIn($nonomType_location_client) ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlIn($commentaireType_location_client) ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotIn($nocommentaireType_location_client) ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlIn($ordreType_location_client) ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotIn($noordreType_location_client) ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlIn($codeType_location_client) ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotIn($nocodeType_location_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomType_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeLocationClient($name="idtype_location_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_location_client , idtype_location_client FROM type_location_client ORDER BY nomType_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeLocationClient = new TypeLocationClient(); ?>