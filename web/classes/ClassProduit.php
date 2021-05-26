<?   
class Produit
{
	var $conn = null;
	function Produit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduit($params){ 
		$this->conn->AutoExecute("produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit", $params, "UPDATE", "idproduit = ".$idproduit); 
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE produit "; 
		return $this->conn->Execute($sql); 	
	}
	function deleteProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit WHERE idproduit = $idproduit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_produit where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlIn($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotIn($nogamme_produit_idgamme_produit) ; }
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
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlIn($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotIn($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlIn($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotIn($noestActifProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlIn($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotIn($noqteAlerteStockProduit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlIn($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotIn($nonomGamme_produit) ; }
			if(!empty($ordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlIn($ordreGamme_produit) ; }
			if(!empty($noordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlNotIn($noordreGamme_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($estActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlIn($estActifCategorie_produit) ; }
			if(!empty($noestActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlNotIn($noestActifCategorie_produit) ; }
			if(!empty($ordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlIn($ordreCategorie_produit) ; }
			if(!empty($noordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlNotIn($noordreCategorie_produit) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlIn($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotIn($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($codeCategorie_produit)){ $sql .= " AND codeCategorie_produit ".sqlIn($codeCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_produit where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlSearch($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotSearch($noidproduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlSearch($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotSearch($nogamme_produit_idgamme_produit) ; }
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
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlSearch($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotSearch($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlSearch($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotSearch($noestActifProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlSearch($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotSearch($noqteAlerteStockProduit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlSearch($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotSearch($nonomMarque) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlSearch($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotSearch($nonomGamme_produit) ; }
			if(!empty($ordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlSearch($ordreGamme_produit) ; }
			if(!empty($noordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlNotSearch($noordreGamme_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlSearch($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotSearch($nonomCategorie_produit) ; }
			if(!empty($estActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlSearch($estActifCategorie_produit) ; }
			if(!empty($noestActifCategorie_produit)){ $sql .= " AND estActifCategorie_produit ".sqlNotSearch($noestActifCategorie_produit) ; }
			if(!empty($ordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlSearch($ordreCategorie_produit) ; }
			if(!empty($noordreCategorie_produit)){ $sql .= " AND ordreCategorie_produit ".sqlNotSearch($noordreCategorie_produit) ; }
			if(!empty($categorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlSearch($categorie_produit_idcategorie_produit) ; }
			if(!empty($nocategorie_produit_idcategorie_produit)){ $sql .= " AND categorie_produit_idcategorie_produit ".sqlNotSearch($nocategorie_produit_idcategorie_produit) ; }
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlSearch($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotSearch($noidcategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllProduit(){ 
		$sql="SELECT * FROM  produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduit($name="idproduit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit , idproduit FROM WHERE 1 ";  
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlIn($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotIn($nogamme_produit_idgamme_produit) ; }
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
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlIn($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotIn($nocommentaireProduit) ; } 
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlIn($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotIn($noestActifProduit) ; } 
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlIn($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotIn($noqteAlerteStockProduit) ; }
		$sql .=" ORDER BY nomProduit ";
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduit($name="idproduit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit , idproduit FROM produit ORDER BY nomProduit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduit = new Produit(); ?>