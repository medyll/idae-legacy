<?   
class Materiel
{
	var $conn = null;
	function Materiel(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createMateriel($params){ 
		$this->conn->AutoExecute("materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("materiel", $params, "UPDATE", "idmateriel = ".$idmateriel); 
	}
	
	function deleteMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  materiel WHERE idmateriel = $idmateriel"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE  materiel"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_materiel where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($lt_idproduit)){ $sql .= " AND idproduit < '".$lt_idproduit."'" ; }
			if(!empty($gt_idproduit)){ $sql .= " AND idproduit > '".$gt_idproduit."'" ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlIn($gamme_produit_idgamme_produit) ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotIn($nogamme_produit_idgamme_produit) ; }
			if(!empty($lt_gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit < '".$lt_gamme_produit_idgamme_produit."'" ; }
			if(!empty($gt_gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit > '".$gt_gamme_produit_idgamme_produit."'" ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlIn($marque_idmarque) ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotIn($nomarque_idmarque) ; }
			if(!empty($lt_marque_idmarque)){ $sql .= " AND marque_idmarque < '".$lt_marque_idmarque."'" ; }
			if(!empty($gt_marque_idmarque)){ $sql .= " AND marque_idmarque > '".$gt_marque_idmarque."'" ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlIn($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotIn($noreferenceInterneProduit) ; }
			if(!empty($lt_referenceInterneProduit)){ $sql .= " AND referenceInterneProduit < '".$lt_referenceInterneProduit."'" ; }
			if(!empty($gt_referenceInterneProduit)){ $sql .= " AND referenceInterneProduit > '".$gt_referenceInterneProduit."'" ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($lt_nomProduit)){ $sql .= " AND nomProduit < '".$lt_nomProduit."'" ; }
			if(!empty($gt_nomProduit)){ $sql .= " AND nomProduit > '".$gt_nomProduit."'" ; }
			if(!empty($pahtProduit)){ $sql .= " AND pahtProduit ".sqlIn($pahtProduit) ; }
			if(!empty($nopahtProduit)){ $sql .= " AND pahtProduit ".sqlNotIn($nopahtProduit) ; }
			if(!empty($lt_pahtProduit)){ $sql .= " AND pahtProduit < '".$lt_pahtProduit."'" ; }
			if(!empty($gt_pahtProduit)){ $sql .= " AND pahtProduit > '".$gt_pahtProduit."'" ; }
			if(!empty($pvhtProduit)){ $sql .= " AND pvhtProduit ".sqlIn($pvhtProduit) ; }
			if(!empty($nopvhtProduit)){ $sql .= " AND pvhtProduit ".sqlNotIn($nopvhtProduit) ; }
			if(!empty($lt_pvhtProduit)){ $sql .= " AND pvhtProduit < '".$lt_pvhtProduit."'" ; }
			if(!empty($gt_pvhtProduit)){ $sql .= " AND pvhtProduit > '".$gt_pvhtProduit."'" ; }
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlIn($commentaireProduit) ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotIn($nocommentaireProduit) ; }
			if(!empty($lt_commentaireProduit)){ $sql .= " AND commentaireProduit < '".$lt_commentaireProduit."'" ; }
			if(!empty($gt_commentaireProduit)){ $sql .= " AND commentaireProduit > '".$gt_commentaireProduit."'" ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlIn($estActifProduit) ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotIn($noestActifProduit) ; }
			if(!empty($lt_estActifProduit)){ $sql .= " AND estActifProduit < '".$lt_estActifProduit."'" ; }
			if(!empty($gt_estActifProduit)){ $sql .= " AND estActifProduit > '".$gt_estActifProduit."'" ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlIn($qteAlerteStockProduit) ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotIn($noqteAlerteStockProduit) ; }
			if(!empty($lt_qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit < '".$lt_qteAlerteStockProduit."'" ; }
			if(!empty($gt_qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit > '".$gt_qteAlerteStockProduit."'" ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($lt_idmateriel)){ $sql .= " AND idmateriel < '".$lt_idmateriel."'" ; }
			if(!empty($gt_idmateriel)){ $sql .= " AND idmateriel > '".$gt_idmateriel."'" ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($lt_stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel < '".$lt_stock_materiel_idstock_materiel."'" ; }
			if(!empty($gt_stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel > '".$gt_stock_materiel_idstock_materiel."'" ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($lt_produit_idproduit)){ $sql .= " AND produit_idproduit < '".$lt_produit_idproduit."'" ; }
			if(!empty($gt_produit_idproduit)){ $sql .= " AND produit_idproduit > '".$gt_produit_idproduit."'" ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($lt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel < '".$lt_numeroSerieInterneMateriel."'" ; }
			if(!empty($gt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel > '".$gt_numeroSerieInterneMateriel."'" ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlIn($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotIn($nonumeroSerieExterneMateriel) ; }
			if(!empty($lt_numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel < '".$lt_numeroSerieExterneMateriel."'" ; }
			if(!empty($gt_numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel > '".$gt_numeroSerieExterneMateriel."'" ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlIn($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotIn($nodateCreationMateriel) ; }
			if(!empty($lt_dateCreationMateriel)){ $sql .= " AND dateCreationMateriel < '".$lt_dateCreationMateriel."'" ; }
			if(!empty($gt_dateCreationMateriel)){ $sql .= " AND dateCreationMateriel > '".$gt_dateCreationMateriel."'" ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlIn($dateAcquisitionMateriel) ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotIn($nodateAcquisitionMateriel) ; }
			if(!empty($lt_dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel < '".$lt_dateAcquisitionMateriel."'" ; }
			if(!empty($gt_dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel > '".$gt_dateAcquisitionMateriel."'" ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlIn($dateFinMateriel) ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotIn($nodateFinMateriel) ; }
			if(!empty($lt_dateFinMateriel)){ $sql .= " AND dateFinMateriel < '".$lt_dateFinMateriel."'" ; }
			if(!empty($gt_dateFinMateriel)){ $sql .= " AND dateFinMateriel > '".$gt_dateFinMateriel."'" ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlIn($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotIn($noestActifMateriel) ; }
			if(!empty($lt_estActifMateriel)){ $sql .= " AND estActifMateriel < '".$lt_estActifMateriel."'" ; }
			if(!empty($gt_estActifMateriel)){ $sql .= " AND estActifMateriel > '".$gt_estActifMateriel."'" ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlIn($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotIn($nopahtMateriel) ; }
			if(!empty($lt_pahtMateriel)){ $sql .= " AND pahtMateriel < '".$lt_pahtMateriel."'" ; }
			if(!empty($gt_pahtMateriel)){ $sql .= " AND pahtMateriel > '".$gt_pahtMateriel."'" ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlIn($pvhtMateriel) ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotIn($nopvhtMateriel) ; }
			if(!empty($lt_pvhtMateriel)){ $sql .= " AND pvhtMateriel < '".$lt_pvhtMateriel."'" ; }
			if(!empty($gt_pvhtMateriel)){ $sql .= " AND pvhtMateriel > '".$gt_pvhtMateriel."'" ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlIn($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotIn($nocommentaireMateriel) ; }
			if(!empty($lt_commentaireMateriel)){ $sql .= " AND commentaireMateriel < '".$lt_commentaireMateriel."'" ; }
			if(!empty($gt_commentaireMateriel)){ $sql .= " AND commentaireMateriel > '".$gt_commentaireMateriel."'" ; }
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlIn($idcategorie_produit) ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotIn($noidcategorie_produit) ; }
			if(!empty($lt_idcategorie_produit)){ $sql .= " AND idcategorie_produit < '".$lt_idcategorie_produit."'" ; }
			if(!empty($gt_idcategorie_produit)){ $sql .= " AND idcategorie_produit > '".$gt_idcategorie_produit."'" ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlIn($nomCategorie_produit) ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotIn($nonomCategorie_produit) ; }
			if(!empty($lt_nomCategorie_produit)){ $sql .= " AND nomCategorie_produit < '".$lt_nomCategorie_produit."'" ; }
			if(!empty($gt_nomCategorie_produit)){ $sql .= " AND nomCategorie_produit > '".$gt_nomCategorie_produit."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_materiel where 1 " ;
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlSearch($idproduit,"idproduit") ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotSearch($noidproduit) ; }
			if(!empty($gamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlSearch($gamme_produit_idgamme_produit,"gamme_produit_idgamme_produit") ; }
			if(!empty($nogamme_produit_idgamme_produit)){ $sql .= " AND gamme_produit_idgamme_produit ".sqlNotSearch($nogamme_produit_idgamme_produit) ; }
			if(!empty($marque_idmarque)){ $sql .= " AND marque_idmarque ".sqlSearch($marque_idmarque,"marque_idmarque") ; }
			if(!empty($nomarque_idmarque)){ $sql .= " AND marque_idmarque ".sqlNotSearch($nomarque_idmarque) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlSearch($referenceInterneProduit,"referenceInterneProduit") ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotSearch($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlSearch($nomProduit,"nomProduit") ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotSearch($nonomProduit) ; }
			if(!empty($pahtProduit)){ $sql .= " AND pahtProduit ".sqlSearch($pahtProduit,"pahtProduit") ; }
			if(!empty($nopahtProduit)){ $sql .= " AND pahtProduit ".sqlNotSearch($nopahtProduit) ; }
			if(!empty($pvhtProduit)){ $sql .= " AND pvhtProduit ".sqlSearch($pvhtProduit,"pvhtProduit") ; }
			if(!empty($nopvhtProduit)){ $sql .= " AND pvhtProduit ".sqlNotSearch($nopvhtProduit) ; }
			if(!empty($commentaireProduit)){ $sql .= " AND commentaireProduit ".sqlSearch($commentaireProduit,"commentaireProduit") ; }
			if(!empty($nocommentaireProduit)){ $sql .= " AND commentaireProduit ".sqlNotSearch($nocommentaireProduit) ; }
			if(!empty($estActifProduit)){ $sql .= " AND estActifProduit ".sqlSearch($estActifProduit,"estActifProduit") ; }
			if(!empty($noestActifProduit)){ $sql .= " AND estActifProduit ".sqlNotSearch($noestActifProduit) ; }
			if(!empty($qteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlSearch($qteAlerteStockProduit,"qteAlerteStockProduit") ; }
			if(!empty($noqteAlerteStockProduit)){ $sql .= " AND qteAlerteStockProduit ".sqlNotSearch($noqteAlerteStockProduit) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel,"idmateriel") ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel,"stock_materiel_idstock_materiel") ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit,"produit_idproduit") ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel,"numeroSerieInterneMateriel") ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlSearch($numeroSerieExterneMateriel,"numeroSerieExterneMateriel") ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotSearch($nonumeroSerieExterneMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlSearch($dateCreationMateriel,"dateCreationMateriel") ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotSearch($nodateCreationMateriel) ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlSearch($dateAcquisitionMateriel,"dateAcquisitionMateriel") ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotSearch($nodateAcquisitionMateriel) ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlSearch($dateFinMateriel,"dateFinMateriel") ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotSearch($nodateFinMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlSearch($estActifMateriel,"estActifMateriel") ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotSearch($noestActifMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlSearch($pahtMateriel,"pahtMateriel") ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotSearch($nopahtMateriel) ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlSearch($pvhtMateriel,"pvhtMateriel") ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotSearch($nopvhtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlSearch($commentaireMateriel,"commentaireMateriel") ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotSearch($nocommentaireMateriel) ; }
			if(!empty($idcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlSearch($idcategorie_produit,"idcategorie_produit") ; }
			if(!empty($noidcategorie_produit)){ $sql .= " AND idcategorie_produit ".sqlNotSearch($noidcategorie_produit) ; }
			if(!empty($nomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlSearch($nomCategorie_produit,"nomCategorie_produit") ; }
			if(!empty($nonomCategorie_produit)){ $sql .= " AND nomCategorie_produit ".sqlNotSearch($nonomCategorie_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllMateriel(){ 
		$sql="SELECT * FROM  vue_materiel"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneMateriel($name="idvue_materiel",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_materiel , idvue_materiel FROM vue_materiel WHERE  1 ";  
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectMateriel($name="idmateriel",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomMateriel , idmateriel FROM materiel ORDER BY nomMateriel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassMateriel = new Materiel(); ?>