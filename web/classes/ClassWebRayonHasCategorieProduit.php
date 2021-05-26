<?   
class WebRayonHasCategorieProduit
{
	var $conn = null;
	function WebRayonHasCategorieProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createWebRayonHasCategorieProduit($params){ 
		$this->conn->AutoExecute("web_rayon_has_categorie_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateWebRayonHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("web_rayon_has_categorie_produit", $params, "UPDATE", "idweb_rayon_has_categorie_produit = ".$idweb_rayon_has_categorie_produit); 
	}
	
	function deleteWebRayonHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  web_rayon_has_categorie_produit WHERE idweb_rayon_has_categorie_produit = $idweb_rayon_has_categorie_produit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneWebRayonHasCategorieProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from web_rayon_has_categorie_produit where 1 " ;
			if(!empty($idweb_rayon_has_categorie_produit)){ $sql .= " AND idweb_rayon_has_categorie_produit ".sqlIn($idweb_rayon_has_categorie_produit) ; }
			if(!empty($noidweb_rayon_has_categorie_produit)){ $sql .= " AND idweb_rayon_has_categorie_produit ".sqlNotIn($noidweb_rayon_has_categorie_produit) ; }
			if(!empty($web_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlIn($web_rayon_idweb_rayon) ; }
			if(!empty($noweb_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlNotIn($noweb_rayon_idweb_rayon) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllWebRayonHasCategorieProduit(){ 
		$sql="SELECT * FROM  web_rayon_has_categorie_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneWebRayonHasCategorieProduit($name="idweb_rayon_has_categorie_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomWeb_rayon_has_categorie_produit , idweb_rayon_has_categorie_produit FROM WHERE 1 ";  
			if(!empty($idweb_rayon_has_categorie_produit)){ $sql .= " AND idweb_rayon_has_categorie_produit ".sqlIn($idweb_rayon_has_categorie_produit) ; }
			if(!empty($noidweb_rayon_has_categorie_produit)){ $sql .= " AND idweb_rayon_has_categorie_produit ".sqlNotIn($noidweb_rayon_has_categorie_produit) ; }
			if(!empty($web_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlIn($web_rayon_idweb_rayon) ; }
			if(!empty($noweb_rayon_idweb_rayon)){ $sql .= " AND web_rayon_idweb_rayon ".sqlNotIn($noweb_rayon_idweb_rayon) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			$sql .=" ORDER BY nomWeb_rayon_has_categorie_produit ";
		if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectWebRayonHasCategorieProduit($name="idweb_rayon_has_categorie_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomWeb_rayon_has_categorie_produit , idweb_rayon_has_categorie_produit FROM web_rayon_has_categorie_produit ORDER BY nomWeb_rayon_has_categorie_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassWebRayonHasCategorieProduit = new WebRayonHasCategorieProduit(); ?>