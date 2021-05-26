<?   
class IdaeCategorieProduitHasFils
{ 
	var $conn = null;
	function IdaeCategorieProduitHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeCategorieProduitHasFils($params){ 
		$this->conn->AutoExecute("vue_categorie_produit_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_categorie_produit_has_fils", $params, "UPDATE", "idvue_categorie_produit_has_fils = ".$idvue_categorie_produit_has_fils); 
	}
	
	function deleteIdaeCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_categorie_produit_has_fils WHERE idvue_categorie_produit_has_fils = $idvue_categorie_produit_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_categorie_produit_has_fils where 1 " ;
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlIn($idfils_categorie_produit) ; }
			if(!empty($noidfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlNotIn($noidfils_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_categorie_produit_has_fils where 1 " ;
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlSearch($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotSearch($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlSearch($idfils_categorie_produit) ; }
			if(!empty($noidfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlNotSearch($noidfils_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeCategorieProduitHasFils(){ 
		$sql="SELECT * FROM  vue_categorie_produit_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeCategorieProduitHasFils($name="idvue_categorie_produit_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_categorie_produit_has_fils , idvue_categorie_produit_has_fils FROM vue_categorie_produit_has_fils WHERE  1 ";  
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlIn($idfils_categorie_produit) ; }
			if(!empty($noidfils_categorie_produit)){ $sql .= " AND idfils_categorie_produit ".sqlNotIn($noidfils_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 

		$sql .=" ORDER BY nomVue_categorie_produit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeCategorieProduitHasFils($name="idvue_categorie_produit_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_categorie_produit_has_fils , idvue_categorie_produit_has_fils FROM vue_categorie_produit_has_fils ORDER BY nomVue_categorie_produit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeCategorieProduitHasFils = new IdaeCategorieProduitHasFils(); ?>