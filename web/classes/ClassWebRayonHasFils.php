<?   
class WebRayonHasFils
{
	var $conn = null;
	function WebRayonHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createWebRayonHasFils($params){ 
		$this->conn->AutoExecute("web_rayon_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateWebRayonHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("web_rayon_has_fils", $params, "UPDATE", "idweb_rayon_has_fils = ".$idweb_rayon_has_fils); 
	}
	
	function deleteWebRayonHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  web_rayon_has_fils WHERE idweb_rayon_has_fils ".sqlIn($idweb_rayon_has_fils); 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneWebRayonHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from web_rayon_has_fils where 1 " ;
			if(!empty($idweb_rayon_has_fils)){ $sql .= " AND idweb_rayon_has_fils ".sqlIn($idweb_rayon_has_fils) ; }
			if(!empty($noidweb_rayon_has_fils)){ $sql .= " AND idweb_rayon_has_fils ".sqlNotIn($noidweb_rayon_has_fils) ; }
			if(!empty($web_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlIn($web_rayon_idweb_rayon) ; }
			if(!empty($noweb_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlNotIn($noweb_rayon_idweb_rayon) ; }
			if(!empty($idfilsweb_rayon)){ $sql .= " AND idfilsweb_rayon ".sqlIn($idfilsweb_rayon) ; }
			if(!empty($noidfilsweb_rayon)){ $sql .= " AND idfilsweb_rayon ".sqlNotIn($noidfilsweb_rayon) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllWebRayonHasFils(){ 
		$sql="SELECT * FROM  web_rayon_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneWebRayonHasFils($name="idweb_rayon_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomWeb_rayon_has_fils , idweb_rayon_has_fils FROM WHERE 1 ";  
			if(!empty($idweb_rayon_has_fils)){ $sql .= " AND idweb_rayon_has_fils ".sqlIn($idweb_rayon_has_fils) ; }
			if(!empty($noidweb_rayon_has_fils)){ $sql .= " AND idweb_rayon_has_fils ".sqlNotIn($noidweb_rayon_has_fils) ; }
			if(!empty($web_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlIn($web_rayon_idweb_rayon) ; }
			if(!empty($noweb_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlNotIn($noweb_rayon_idweb_rayon) ; }
			if(!empty($idfilsweb_rayon)){ $sql .= " AND idfilsweb_rayon ".sqlIn($idfilsweb_rayon) ; }
			if(!empty($noidfilsweb_rayon)){ $sql .= " AND idfilsweb_rayon ".sqlNotIn($noidfilsweb_rayon) ; }
		$sql .=" ORDER BY nomWeb_rayon_has_fils ";
		if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectWebRayonHasFils($name="idweb_rayon_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomWeb_rayon_has_fils , idweb_rayon_has_fils FROM web_rayon_has_fils ORDER BY nomWeb_rayon_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassWebRayonHasFils = new WebRayonHasFils(); ?>