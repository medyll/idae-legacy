<?   
class IdaeTelfax
{
	var $conn = null;
	function IdaeTelfax(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeTelfax($params){ 
		$this->conn->AutoExecute("vue_telfax", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeTelfax($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_telfax", $params, "UPDATE", "idvue_telfax = ".$idvue_telfax); 
	}
	
	function deleteIdaeTelfax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_telfax WHERE idvue_telfax = $idvue_telfax"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeTelfax($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_telfax where 1 " ;
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeTelfax($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_telfax where 1 " ;
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlSearch($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotSearch($noidtelfax) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlSearch($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotSearch($nonumeroTelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlSearch($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotSearch($notype_telfax_idtype_telfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlSearch($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotSearch($nocommentaireTelfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeTelfax(){ 
		$sql="SELECT * FROM  vue_telfax"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeTelfax($name="idvue_telfax",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_telfax , idvue_telfax FROM vue_telfax WHERE  1 ";  
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_telfax ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeTelfax($name="idvue_telfax",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_telfax , idvue_telfax FROM vue_telfax ORDER BY nomVue_telfax ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeTelfax = new IdaeTelfax(); ?>