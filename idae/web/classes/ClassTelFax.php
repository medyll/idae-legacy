<?   
class TelFax
{
	var $conn = null;
	function TelFax(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTelFax($params){ 
		$this->conn->AutoExecute("telfax", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTelFax($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("telfax", $params, "UPDATE", "idtelfax = ".$idtelfax); 
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE telfax "; 
		return $this->conn->Execute($sql); 	
	}
	function deleteTelFax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  telfax WHERE idtelfax = $idtelfax"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTelfax($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_telfax where 1 " ;
			if(!empty($nomType_telfax)){ $sql .= " AND nomType_telfax ".sqlIn($nomType_telfax) ; }
			if(!empty($nonomType_telfax)){ $sql .= " AND nomType_telfax ".sqlNotIn($nonomType_telfax) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($codeType_telfax)){ $sql .= " AND codeType_telfax ".sqlIn($codeType_telfax) ; }
			if(!empty($nocodeType_telfax)){ $sql .= " AND codeType_telfax ".sqlNotIn($nocodeType_telfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTelfax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_telfax where 1 " ;
			if(!empty($nomType_telfax)){ $sql .= " AND nomType_telfax ".sqlSearch($nomType_telfax) ; }
			if(!empty($nonomType_telfax)){ $sql .= " AND nomType_telfax ".sqlNotSearch($nonomType_telfax) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlSearch($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotSearch($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlSearch($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotSearch($notype_telfax_idtype_telfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlSearch($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotSearch($nonumeroTelfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlSearch($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotSearch($nocommentaireTelfax) ; }
			if(!empty($codeType_telfax)){ $sql .= " AND codeType_telfax ".sqlSearch($codeType_telfax) ; }
			if(!empty($nocodeType_telfax)){ $sql .= " AND codeType_telfax ".sqlNotSearch($nocodeType_telfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllTelFax(){ 
		$sql="SELECT * FROM  telfax"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTelFax($name="idtelfax",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomTelfax , idtelfax FROM WHERE 1 ";  
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomTelfax ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTelFax($name="idtelfax",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomTelfax , idtelfax FROM telfax ORDER BY nomTelfax ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTelFax = new TelFax(); ?>