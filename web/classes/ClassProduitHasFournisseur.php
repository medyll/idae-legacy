<?   
class ProduitHasFournisseur
{
	var $conn = null;
	function ProduitHasFournisseur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduitHasFournisseur($params){ 
		$this->conn->AutoExecute("produit_has_fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduitHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_fournisseur", $params, "UPDATE", "idproduit_has_fournisseur = ".$idproduit_has_fournisseur); 
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE  produit_has_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	function deleteProduitHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_fournisseur WHERE idproduit_has_fournisseur = $idproduit_has_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneProduitHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from produit_has_fournisseur where 1 " ;
			if(!empty($idproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlIn($idproduit_has_fournisseur) ; }
			if(!empty($noidproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlNotIn($noidproduit_has_fournisseur) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($referenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlIn($referenceProduit_has_fournisseur) ; }
			if(!empty($noreferenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlNotIn($noreferenceProduit_has_fournisseur) ; }
			if(!empty($pahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlIn($pahtProduit_has_fournisseur) ; }
			if(!empty($nopahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlNotIn($nopahtProduit_has_fournisseur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneProduitHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_fournisseur where 1 " ;
			if(!empty($idproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlSearch($idproduit_has_fournisseur) ; }
			if(!empty($noidproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlNotSearch($noidproduit_has_fournisseur) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($referenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlSearch($referenceProduit_has_fournisseur) ; }
			if(!empty($noreferenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlNotSearch($noreferenceProduit_has_fournisseur) ; }
			if(!empty($pahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlSearch($pahtProduit_has_fournisseur) ; }
			if(!empty($nopahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlNotSearch($nopahtProduit_has_fournisseur) ; }

			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllProduitHasFournisseur(){ 
		$sql="SELECT * FROM  produit_has_fournisseur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduitHasFournisseur($name="idproduit_has_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_fournisseur , idproduit_has_fournisseur FROM produit_has_fournisseur WHERE  1 ";  
			if(!empty($idproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlIn($idproduit_has_fournisseur) ; }
			if(!empty($noidproduit_has_fournisseur)){ $sql .= " AND idproduit_has_fournisseur ".sqlNotIn($noidproduit_has_fournisseur) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($referenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlIn($referenceProduit_has_fournisseur) ; }
			if(!empty($noreferenceProduit_has_fournisseur)){ $sql .= " AND referenceProduit_has_fournisseur ".sqlNotIn($noreferenceProduit_has_fournisseur) ; }
			if(!empty($pahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlIn($pahtProduit_has_fournisseur) ; }
			if(!empty($nopahtProduit_has_fournisseur)){ $sql .= " AND pahtProduit_has_fournisseur ".sqlNotIn($nopahtProduit_has_fournisseur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomProduit_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduitHasFournisseur($name="idproduit_has_fournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_fournisseur , idproduit_has_fournisseur FROM produit_has_fournisseur ORDER BY nomProduit_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduitHasFournisseur = new ProduitHasFournisseur(); ?>