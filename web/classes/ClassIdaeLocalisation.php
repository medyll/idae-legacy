<?   
class IdaeLocalisation
{
	var $conn = null;
	function IdaeLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLocalisation($params){ 
		$this->conn->AutoExecute("vue_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_localisation", $params, "UPDATE", "idvue_localisation = ".$idvue_localisation); 
	}
	
	function deleteIdaeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_localisation WHERE idvue_localisation = $idvue_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_localisation where 1 " ;
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_localisation where 1 " ;
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlSearch($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotSearch($noidlocalisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlSearch($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotSearch($noprincipaleLocalisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlSearch($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotSearch($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLocalisation(){ 
		$sql="SELECT * FROM  vue_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLocalisation($name="idvue_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_localisation , idvue_localisation FROM vue_localisation WHERE  1 ";  
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLocalisation($name="idvue_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_localisation , idvue_localisation FROM vue_localisation ORDER BY nomVue_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLocalisation = new IdaeLocalisation(); ?>