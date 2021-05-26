<?   
class IdaeProduit
{
	var $conn = null;
	function IdaeProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeProduit($params){ 
		$this->conn->AutoExecute("vue_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_produit", $params, "UPDATE", "idvue_produit = ".$idvue_produit); 
	}
	
	function deleteIdaeProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_produit WHERE idvue_produit = $idvue_produit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_produit where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlIn($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotIn($nomarque_idmarque) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlIn($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotIn($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($pahtProduit)){ $sql .= " AND pahtProduit ".sqlIn($pahtProduit) ; }
			if(!empty($nopahtProduit)){ $sql .= " AND pahtProduit ".sqlNotIn($nopahtProduit) ; }
			if(!empty($pvhtProduit)){ $sql .= " AND pvhtProduit ".sqlIn($pvhtProduit) ; }
			if(!empty($nopvhtProduit)){ $sql .= " AND pvhtProduit ".sqlNotIn($nopvhtProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlIn($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotIn($noqteAlerteStockProduit) ; }
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlIn($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotIn($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlIn($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotIn($noestActifProduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlIn($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotIn($nogamme_produit_idgamme_produit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_produit where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlSearch($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotSearch($noidproduit) ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlSearch($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotSearch($nomarque_idmarque) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlSearch($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotSearch($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlSearch($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotSearch($nonomProduit) ; }
			if(!empty($pahtProduit)){ $sql .= " AND pahtProduit ".sqlSearch($pahtProduit) ; }
			if(!empty($nopahtProduit)){ $sql .= " AND pahtProduit ".sqlNotSearch($nopahtProduit) ; }
			if(!empty($pvhtProduit)){ $sql .= " AND pvhtProduit ".sqlSearch($pvhtProduit) ; }
			if(!empty($nopvhtProduit)){ $sql .= " AND pvhtProduit ".sqlNotSearch($nopvhtProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlSearch($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotSearch($noqteAlerteStockProduit) ; }
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlSearch($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotSearch($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlSearch($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotSearch($noestActifProduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlSearch($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotSearch($nogamme_produit_idgamme_produit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlSearch($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotSearch($nonomMarque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeProduit(){ 
		$sql="SELECT * FROM  vue_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeProduit($name="idvue_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_produit , idvue_produit FROM vue_produit WHERE  1 ";  
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlIn($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotIn($nomarque_idmarque) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlIn($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotIn($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($pahtProduit)){ $sql .= " AND pahtProduit ".sqlIn($pahtProduit) ; }
			if(!empty($nopahtProduit)){ $sql .= " AND pahtProduit ".sqlNotIn($nopahtProduit) ; }
			if(!empty($pvhtProduit)){ $sql .= " AND pvhtProduit ".sqlIn($pvhtProduit) ; }
			if(!empty($nopvhtProduit)){ $sql .= " AND pvhtProduit ".sqlNotIn($nopvhtProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlIn($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotIn($noqteAlerteStockProduit) ; }
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlIn($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotIn($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlIn($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotIn($noestActifProduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlIn($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotIn($nogamme_produit_idgamme_produit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeProduit($name="idvue_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_produit , idvue_produit FROM vue_produit ORDER BY nomVue_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeProduit = new IdaeProduit(); ?>