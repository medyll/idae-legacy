<?   
class IdaeProduitHasFils
{
	var $conn = null;
	function IdaeProduitHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeProduitHasFils($params){ 
		$this->conn->AutoExecute("produit_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_fils", $params, "UPDATE", "idproduit_has_fils = ".$idproduit_has_fils); 
	}
	
	function deleteIdaeProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_fils WHERE idproduit_has_fils = $idproduit_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from produit_has_fils where 1 " ;
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlIn($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotIn($noidproduit_has_fils) ; }
			if(!empty($produits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlIn($produits_segment_produit_idsegment_produit) ; }
			if(!empty($noproduits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlNotIn($noproduits_segment_produit_idsegment_produit) ; }
			if(!empty($produits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlIn($produits_marques_idmarques) ; }
			if(!empty($noproduits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlNotIn($noproduits_marques_idmarques) ; }
			if(!empty($produits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlIn($produits_fournisseurs_idfournisseurs) ; }
			if(!empty($noproduits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlNotIn($noproduits_fournisseurs_idfournisseurs) ; }
			if(!empty($produits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlIn($produits_categorieProduits_id) ; }
			if(!empty($noproduits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlNotIn($noproduits_categorieProduits_id) ; }
			if(!empty($produits_id)){ $sql .= " AND produits_id ".sqlIn($produits_id) ; }
			if(!empty($noproduits_id)){ $sql .= " AND produits_id ".sqlNotIn($noproduits_id) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlIn($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotIn($nofils) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_fils where 1 " ;
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlSearch($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotSearch($noidproduit_has_fils) ; }
			if(!empty($produits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlSearch($produits_segment_produit_idsegment_produit) ; }
			if(!empty($noproduits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlNotSearch($noproduits_segment_produit_idsegment_produit) ; }
			if(!empty($produits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlSearch($produits_marques_idmarques) ; }
			if(!empty($noproduits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlNotSearch($noproduits_marques_idmarques) ; }
			if(!empty($produits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlSearch($produits_fournisseurs_idfournisseurs) ; }
			if(!empty($noproduits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlNotSearch($noproduits_fournisseurs_idfournisseurs) ; }
			if(!empty($produits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlSearch($produits_categorieProduits_id) ; }
			if(!empty($noproduits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlNotSearch($noproduits_categorieProduits_id) ; }
			if(!empty($produits_id)){ $sql .= " AND produits_id ".sqlSearch($produits_id) ; }
			if(!empty($noproduits_id)){ $sql .= " AND produits_id ".sqlNotSearch($noproduits_id) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlSearch($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotSearch($nofils) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeProduitHasFils(){ 
		$sql="SELECT * FROM  produit_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeProduitHasFils($name="idproduit_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_fils , idproduit_has_fils FROM produit_has_fils WHERE  1 ";  
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlIn($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotIn($noidproduit_has_fils) ; }
			if(!empty($produits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlIn($produits_segment_produit_idsegment_produit) ; }
			if(!empty($noproduits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlNotIn($noproduits_segment_produit_idsegment_produit) ; }
			if(!empty($produits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlIn($produits_marques_idmarques) ; }
			if(!empty($noproduits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlNotIn($noproduits_marques_idmarques) ; }
			if(!empty($produits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlIn($produits_fournisseurs_idfournisseurs) ; }
			if(!empty($noproduits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlNotIn($noproduits_fournisseurs_idfournisseurs) ; }
			if(!empty($produits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlIn($produits_categorieProduits_id) ; }
			if(!empty($noproduits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlNotIn($noproduits_categorieProduits_id) ; }
			if(!empty($produits_id)){ $sql .= " AND produits_id ".sqlIn($produits_id) ; }
			if(!empty($noproduits_id)){ $sql .= " AND produits_id ".sqlNotIn($noproduits_id) ; }
			if(!empty($fils)){ $sql .= " AND fils ".sqlIn($fils) ; }
			if(!empty($nofils)){ $sql .= " AND fils ".sqlNotIn($nofils) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomProduit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeProduitHasFils($name="idproduit_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_fils , idproduit_has_fils FROM produit_has_fils ORDER BY nomProduit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeProduitHasFils = new IdaeProduitHasFils(); ?>