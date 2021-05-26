<?   
class CategorieProduitHasFils
{
	var $conn = null;
	function CategorieProduitHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCategorieProduitHasFils($params){ 
		$this->conn->AutoExecute("categorie_produit_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("categorie_produit_has_fils", $params, "UPDATE", "idcategorie_produit_has_fils = ".$idcategorie_produit_has_fils); 
	}
	
	function deleteCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  categorie_produit_has_fils WHERE idcategorie_produit_has_fils = $idcategorie_produit_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE categorie_produit_has_fils "; 
		$this->conn->Execute($sql); 	
	}
	function getOneCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_categorie_produit_has_fils where 1 " ;
			if(!empty($idcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlIn($idcategorie_produit_has_fils) ; }
			if(!empty($noidcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlNotIn($noidcategorie_produit_has_fils) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlIn($idfilscategorie_produit) ; }
			if(!empty($noidfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlNotIn($noidfilscategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($nomFilsCategorie_produit)){ $sql .= " AND nomFilsCategorie_produit ".sqlIn($nomFilsCategorie_produit) ; }
			if(!empty($nonomFilsCategorie_produit)){ $sql .= " AND nomFilsCategorie_produit ".sqlNotIn($nonomFilsCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneCategorieProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_categorie_produit_has_fils where 1 " ;
			if(!empty($idcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlSearch($idcategorie_produit_has_fils) ; }
			if(!empty($noidcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlNotSearch($noidcategorie_produit_has_fils) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlSearch($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotSearch($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlSearch($idfilscategorie_produit) ; }
			if(!empty($noidfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlNotSearch($noidfilscategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlSearch($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotSearch($nonomCategorie_produit) ; }
			if(!empty($nomFilsCategorie_produit)){ $sql .= " AND nomFilsCategorie_produit ".sqlSearch($nomFilsCategorie_produit) ; }
			if(!empty($nonomFilsCategorie_produit)){ $sql .= " AND nomFilsCategorie_produit ".sqlNotSearch($nonomFilsCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllCategorieProduitHasFils(){ 
		$sql="SELECT * FROM  categorie_produit_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCategorieProduitHasFils($name="idcategorie_produit_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCategorie_produit_has_fils , idcategorie_produit_has_fils FROM categorie_produit_has_fils WHERE  1 ";  
			if(!empty($idcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlIn($idcategorie_produit_has_fils) ; }
			if(!empty($noidcategorie_produit_has_fils)){ $sql .= " AND idcategorie_produit_has_fils ".sqlNotIn($noidcategorie_produit_has_fils) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlIn($idfilscategorie_produit) ; }
			if(!empty($noidfilscategorie_produit)){ $sql .= " AND idfilscategorie_produit ".sqlNotIn($noidfilscategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCategorie_produit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCategorieProduitHasFils($name="idcategorie_produit_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCategorie_produit_has_fils , idcategorie_produit_has_fils FROM categorie_produit_has_fils ORDER BY nomCategorie_produit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCategorieProduitHasFils = new CategorieProduitHasFils(); ?>