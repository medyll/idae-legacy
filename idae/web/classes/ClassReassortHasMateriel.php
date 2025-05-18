<?   
class ReassortHasMateriel
{
	var $conn = null;
	function ReassortHasMateriel(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createReassortHasMateriel($params){ 
		$this->conn->AutoExecute("reassort_has_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateReassortHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("reassort_has_materiel", $params, "UPDATE", "idreassort_has_materiel = ".$idreassort_has_materiel); 
	}
	
	function deleteReassortHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  reassort_has_materiel WHERE idreassort_has_materiel = $idreassort_has_materiel"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneReassortHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_reassort_has_materiel where 1 " ;
			if(!empty($idreassort)){ $sql .= " AND idreassort ".sqlIn($idreassort) ; }
			if(!empty($noidreassort)){ $sql .= " AND idreassort ".sqlNotIn($noidreassort) ; }
			if(!empty($nomReassort)){ $sql .= " AND nomReassort ".sqlIn($nomReassort) ; }
			if(!empty($nonomReassort)){ $sql .= " AND nomReassort ".sqlNotIn($nonomReassort) ; }
			if(!empty($commentaireReassort)){ $sql .= " AND commentaireReassort ".sqlIn($commentaireReassort) ; }
			if(!empty($nocommentaireReassort)){ $sql .= " AND commentaireReassort ".sqlNotIn($nocommentaireReassort) ; }
			if(!empty($dateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlIn($dateCreationReassort) ; }
			if(!empty($nodateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlNotIn($nodateCreationReassort) ; }
			if(!empty($estActifReassort)){ $sql .= " AND estActifReassort ".sqlIn($estActifReassort) ; }
			if(!empty($noestActifReassort)){ $sql .= " AND estActifReassort ".sqlNotIn($noestActifReassort) ; }
			if(!empty($idreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlIn($idreassort_has_materiel) ; }
			if(!empty($noidreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlNotIn($noidreassort_has_materiel) ; }
			if(!empty($dateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlIn($dateReassort_has_materiel) ; }
			if(!empty($nodateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlNotIn($nodateReassort_has_materiel) ; }
			if(!empty($commentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlIn($commentaireReassort_has_materiel) ; }
			if(!empty($nocommentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlNotIn($nocommentaireReassort_has_materiel) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlIn($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotIn($nonumeroSerieExterneMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlIn($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotIn($nodateCreationMateriel) ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlIn($dateAcquisitionMateriel) ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotIn($nodateAcquisitionMateriel) ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlIn($dateFinMateriel) ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotIn($nodateFinMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlIn($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotIn($noestActifMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlIn($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotIn($nopahtMateriel) ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlIn($pvhtMateriel) ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotIn($nopvhtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlIn($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotIn($nocommentaireMateriel) ; }
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneReassortHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_reassort_has_materiel where 1 " ;
			if(!empty($idreassort)){ $sql .= " AND idreassort ".sqlSearch($idreassort) ; }
			if(!empty($noidreassort)){ $sql .= " AND idreassort ".sqlNotSearch($noidreassort) ; }
			if(!empty($nomReassort)){ $sql .= " AND nomReassort ".sqlSearch($nomReassort) ; }
			if(!empty($nonomReassort)){ $sql .= " AND nomReassort ".sqlNotSearch($nonomReassort) ; }
			if(!empty($commentaireReassort)){ $sql .= " AND commentaireReassort ".sqlSearch($commentaireReassort) ; }
			if(!empty($nocommentaireReassort)){ $sql .= " AND commentaireReassort ".sqlNotSearch($nocommentaireReassort) ; }
			if(!empty($dateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlSearch($dateCreationReassort) ; }
			if(!empty($nodateCreationReassort)){ $sql .= " AND dateCreationReassort ".sqlNotSearch($nodateCreationReassort) ; }
			if(!empty($estActifReassort)){ $sql .= " AND estActifReassort ".sqlSearch($estActifReassort) ; }
			if(!empty($noestActifReassort)){ $sql .= " AND estActifReassort ".sqlNotSearch($noestActifReassort) ; }
			if(!empty($idreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlSearch($idreassort_has_materiel) ; }
			if(!empty($noidreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlNotSearch($noidreassort_has_materiel) ; }
			if(!empty($dateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlSearch($dateReassort_has_materiel) ; }
			if(!empty($nodateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlNotSearch($nodateReassort_has_materiel) ; }
			if(!empty($commentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlSearch($commentaireReassort_has_materiel) ; }
			if(!empty($nocommentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlNotSearch($nocommentaireReassort_has_materiel) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlSearch($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotSearch($nonumeroSerieExterneMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlSearch($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotSearch($nodateCreationMateriel) ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlSearch($dateAcquisitionMateriel) ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotSearch($nodateAcquisitionMateriel) ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlSearch($dateFinMateriel) ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotSearch($nodateFinMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlSearch($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotSearch($noestActifMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlSearch($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotSearch($nopahtMateriel) ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlSearch($pvhtMateriel) ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotSearch($nopvhtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlSearch($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotSearch($nocommentaireMateriel) ; }
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllReassortHasMateriel(){ 
		$sql="SELECT * FROM  reassort_has_materiel"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneReassortHasMateriel($name="idreassort_has_materiel",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomReassort_has_materiel , idreassort_has_materiel FROM reassort_has_materiel WHERE  1 ";  
			if(!empty($idreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlIn($idreassort_has_materiel) ; }
			if(!empty($noidreassort_has_materiel)){ $sql .= " AND idreassort_has_materiel ".sqlNotIn($noidreassort_has_materiel) ; }
			if(!empty($dateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlIn($dateReassort_has_materiel) ; }
			if(!empty($nodateReassort_has_materiel)){ $sql .= " AND dateReassort_has_materiel ".sqlNotIn($nodateReassort_has_materiel) ; }
			if(!empty($commentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlIn($commentaireReassort_has_materiel) ; }
			if(!empty($nocommentaireReassort_has_materiel)){ $sql .= " AND commentaireReassort_has_materiel ".sqlNotIn($nocommentaireReassort_has_materiel) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($reassort_idreassort)){ $sql .= " AND reassort_idreassort ".sqlIn($reassort_idreassort) ; }
			if(!empty($noreassort_idreassort)){ $sql .= " AND reassort_idreassort ".sqlNotIn($noreassort_idreassort) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomReassort_has_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectReassortHasMateriel($name="idreassort_has_materiel",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomReassort_has_materiel , idreassort_has_materiel FROM reassort_has_materiel ORDER BY nomReassort_has_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassReassortHasMateriel = new ReassortHasMateriel(); ?>