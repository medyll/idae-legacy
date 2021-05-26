<?   
class IdaeCategorieProduit
{
	var $conn = null;
	function IdaeCategorieProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeCategorieProduit($params){ 
		$this->conn->AutoExecute("vue_categorie_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_categorie_produit", $params, "UPDATE", "idvue_categorie_produit = ".$idvue_categorie_produit); 
	}
	
	function deleteIdaeCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_categorie_produit WHERE idvue_categorie_produit = $idvue_categorie_produit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_categorie_produit where 1 " ;
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_categorie_produit where 1 " ;
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlSearch($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotSearch($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlSearch($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotSearch($nonomCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeCategorieProduit(){ 
		$sql="SELECT * FROM  vue_categorie_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeCategorieProduit($name="idvue_categorie_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_categorie_produit , idvue_categorie_produit FROM vue_categorie_produit WHERE  1 ";  
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_categorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeCategorieProduit($name="idvue_categorie_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_categorie_produit , idvue_categorie_produit FROM vue_categorie_produit ORDER BY nomVue_categorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeCategorieProduit = new IdaeCategorieProduit(); ?>