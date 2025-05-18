<?   
class ProduitHasCategorieProduit
{
	var $conn = null;
	function ProduitHasCategorieProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduitHasCategorieProduit($params){ 
		$this->conn->AutoExecute("produit_has_categorie_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduitHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_categorie_produit", $params, "UPDATE", "idproduit_has_categorie_produit = ".$idproduit_has_categorie_produit); 
	}
	function truncate(){ 
		//$sql="TRUNCATE TABLE produit_has_categorie_produit "; 
		//return $this->conn->Execute($sql); 	
	}
	function deleteProduitHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_categorie_produit WHERE idproduit_has_categorie_produit = ".$idproduit_has_categorie_produit; 
		return $this->conn->Execute($sql); 	
	}
	function getOneProduitHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_categorie_produit where 1 " ;
			if(!empty($idproduit_has_categorie_produit)){ $sql .= " AND idproduit_has_categorie_produit ".sqlIn($idproduit_has_categorie_produit) ; }
			if(!empty($noidproduit_has_categorie_produit)){ $sql .= " AND idproduit_has_categorie_produit ".sqlNotIn($noidproduit_has_categorie_produit) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($ordreProduit_has_categorie_produit)){ $sql .= " AND ordreProduit_has_categorie_produit ".sqlIn($ordreProduit_has_categorie_produit) ; }
			if(!empty($noordreProduit_has_categorie_produit)){ $sql .= " AND ordreProduit_has_categorie_produit ".sqlNotIn($noordreProduit_has_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllProduitHasCategorieProduit(){ 
		$sql="SELECT * FROM  produit_has_categorie_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduitHasCategorieProduit($name="idproduit_has_categorie_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_categorie_produit , idproduit_has_categorie_produit FROM WHERE 1 ";  
			if(!empty($idproduit_has_categorie_produit)){ $sql .= " AND idproduit_has_categorie_produit ".sqlIn($idproduit_has_categorie_produit) ; }
			if(!empty($noidproduit_has_categorie_produit)){ $sql .= " AND idproduit_has_categorie_produit ".sqlNotIn($noidproduit_has_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; }
			if(!empty($ordreProduit_has_categorie_produit)){ $sql .= " AND ordreProduit_has_categorie_produit ".sqlIn($ordreProduit_has_categorie_produit) ; }
			if(!empty($noordreProduit_has_categorie_produit)){ $sql .= " AND ordreProduit_has_categorie_produit ".sqlNotIn($noordreProduit_has_categorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomProduit_has_categorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduitHasCategorieProduit($name="idproduit_has_categorie_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_categorie_produit , idproduit_has_categorie_produit FROM produit_has_categorie_produit ORDER BY nomProduit_has_categorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduitHasCategorieProduit = new ProduitHasCategorieProduit(); ?>