<?   
class ProduitHasImage
{
	var $conn = null;
	function ProduitHasImage(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduitHasImage($params){ 
		$this->conn->AutoExecute("produit_has_image", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduitHasImage($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_image", $params, "UPDATE", "idproduit_has_image = ".$idproduit_has_image); 
	}
	
	function deleteProduitHasImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_image WHERE idproduit_has_image = $idproduit_has_image"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneProduitHasImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_image where 1 " ;
			if(!empty($idproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlIn($idproduit_has_image) ; }
			if(!empty($noidproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlNotIn($noidproduit_has_image) ; }
			if(!empty($image_idimage)){ $sql .= " AND image_idimage ".sqlIn($image_idimage) ; }
			if(!empty($noimage_idimage)){ $sql .= " AND image_idimage ".sqlNotIn($noimage_idimage) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneProduitHasImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_image where 1 " ;
			if(!empty($idproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlSearch($idproduit_has_image) ; }
			if(!empty($noidproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlNotSearch($noidproduit_has_image) ; }
			if(!empty($image_idimage)){ $sql .= " AND image_idimage ".sqlSearch($image_idimage) ; }
			if(!empty($noimage_idimage)){ $sql .= " AND image_idimage ".sqlNotSearch($noimage_idimage) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllProduitHasImage(){ 
		$sql="SELECT * FROM  produit_has_image"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduitHasImage($name="idproduit_has_image",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_image , idproduit_has_image FROM WHERE 1 ";  
			if(!empty($idproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlIn($idproduit_has_image) ; }
			if(!empty($noidproduit_has_image)){ $sql .= " AND idproduit_has_image ".sqlNotIn($noidproduit_has_image) ; }
			if(!empty($image_idimage)){ $sql .= " AND image_idimage ".sqlIn($image_idimage) ; }
			if(!empty($noimage_idimage)){ $sql .= " AND image_idimage ".sqlNotIn($noimage_idimage) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomProduit_has_image ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduitHasImage($name="idproduit_has_image",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_image , idproduit_has_image FROM produit_has_image ORDER BY nomProduit_has_image ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduitHasImage = new ProduitHasImage(); ?>