<?   
class CategorieProduit
{
	var $conn = null;
	function CategorieProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createCategorieProduit($params){ 
		$this->conn->AutoExecute("categorie_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("categorie_produit", $params, "UPDATE", "idcategorie_produit = ".$idcategorie_produit); 
	}
	
	function deleteCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  categorie_produit WHERE idcategorie_produit = $idcategorie_produit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function truncate(){ 	
		$sql="TRUNCATE TABLE categorie_produit "; 
		$this->conn->Execute($sql); 	
	}
	
	function getOneCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from categorie_produit where 1 " ;
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($commentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlIn($commentaireCategorie_produit) ; }
			if(!empty($nocommentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlNotIn($nocommentaireCategorie_produit) ; }
			if(!empty($dateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlIn($dateDebutCategorie_produit) ; }
			if(!empty($nodateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlNotIn($nodateDebutCategorie_produit) ; }
			if(!empty($dateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlIn($dateFinCategorie_produit) ; }
			if(!empty($nodateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlNotIn($nodateFinCategorie_produit) ; }
			if(!empty($dateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlIn($dateCreationCategorie_produit) ; }
			if(!empty($nodateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlNotIn($nodateCreationCategorie_produit) ; }
			if(!empty($dateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlIn($dateClotureCategorie_produit) ; }
			if(!empty($nodateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlNotIn($nodateClotureCategorie_produit) ; }
			if(!empty($estActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlIn($estActifCategorie_produit) ; }
			if(!empty($noestActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlNotIn($noestActifCategorie_produit) ; }
			if(!empty($ordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlIn($ordreCategorie_produit) ; }
			if(!empty($noordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlNotIn($noordreCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from categorie_produit where 1 " ;
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlSearch($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotSearch($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlSearch($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotSearch($nonomCategorie_produit) ; }
			if(!empty($commentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlSearch($commentaireCategorie_produit) ; }
			if(!empty($nocommentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlNotSearch($nocommentaireCategorie_produit) ; }
			if(!empty($dateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlSearch($dateDebutCategorie_produit) ; }
			if(!empty($nodateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlNotSearch($nodateDebutCategorie_produit) ; }
			if(!empty($dateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlSearch($dateFinCategorie_produit) ; }
			if(!empty($nodateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlNotSearch($nodateFinCategorie_produit) ; }
			if(!empty($dateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlSearch($dateCreationCategorie_produit) ; }
			if(!empty($nodateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlNotSearch($nodateCreationCategorie_produit) ; }
			if(!empty($dateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlSearch($dateClotureCategorie_produit) ; }
			if(!empty($nodateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlNotSearch($nodateClotureCategorie_produit) ; }
			if(!empty($estActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlSearch($estActifCategorie_produit) ; }
			if(!empty($noestActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlNotSearch($noestActifCategorie_produit) ; }
			if(!empty($ordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlSearch($ordreCategorie_produit) ; }
			if(!empty($noordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlNotSearch($noordreCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllCategorieProduit(){ 
		$sql="SELECT * FROM  categorie_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneCategorieProduit($name="idcategorie_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomCategorie_produit , idcategorie_produit FROM categorie_produit WHERE  1 ";  
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($commentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlIn($commentaireCategorie_produit) ; }
			if(!empty($nocommentaireCategorie_produit)){ $sql .= " AND commentaireCategorie_produit ".sqlNotIn($nocommentaireCategorie_produit) ; }
			if(!empty($dateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlIn($dateDebutCategorie_produit) ; }
			if(!empty($nodateDebutCategorie_produit)){ $sql .= " AND dateDebutCategorie_produit ".sqlNotIn($nodateDebutCategorie_produit) ; }
			if(!empty($dateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlIn($dateFinCategorie_produit) ; }
			if(!empty($nodateFinCategorie_produit)){ $sql .= " AND dateFinCategorie_produit ".sqlNotIn($nodateFinCategorie_produit) ; }
			if(!empty($dateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlIn($dateCreationCategorie_produit) ; }
			if(!empty($nodateCreationCategorie_produit)){ $sql .= " AND dateCreationCategorie_produit ".sqlNotIn($nodateCreationCategorie_produit) ; }
			if(!empty($dateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlIn($dateClotureCategorie_produit) ; }
			if(!empty($nodateClotureCategorie_produit)){ $sql .= " AND dateClotureCategorie_produit ".sqlNotIn($nodateClotureCategorie_produit) ; }
			if(!empty($estActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlIn($estActifCategorie_produit) ; }
			if(!empty($noestActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlNotIn($noestActifCategorie_produit) ; }
			if(!empty($ordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlIn($ordreCategorie_produit) ; }
			if(!empty($noordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlNotIn($noordreCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomCategorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectCategorieProduit($name="idcategorie_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomCategorie_produit , idcategorie_produit FROM categorie_produit ORDER BY nomCategorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassCategorieProduit = new CategorieProduit(); ?>