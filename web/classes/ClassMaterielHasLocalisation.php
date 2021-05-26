<?   
class MaterielHasLocalisation
{
	var $conn = null;
	function MaterielHasLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createMaterielHasLocalisation($params){ 
		$this->conn->AutoExecute("vue_materiel_has_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateMaterielHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_materiel_has_localisation", $params, "UPDATE", "idvue_materiel_has_localisation = ".$idvue_materiel_has_localisation); 
	}
	
	function deleteMaterielHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_materiel_has_localisation WHERE idvue_materiel_has_localisation = $idvue_materiel_has_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneMaterielHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_materiel_has_localisation where 1 " ;
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
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneMaterielHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_materiel_has_localisation where 1 " ;
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
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlSearch($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotSearch($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlSearch($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotSearch($notype_localisation_idtype_localisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlSearch($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotSearch($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlSearch($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotSearch($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlSearch($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotSearch($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlSearch($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotSearch($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlSearch($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotSearch($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlSearch($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotSearch($nocommentaireAdresse) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlSearch($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotSearch($noidadresse) ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlSearch($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotSearch($noidlocalisation) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllMaterielHasLocalisation(){ 
		$sql="SELECT * FROM  vue_materiel_has_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneMaterielHasLocalisation($name="idvue_materiel_has_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_materiel_has_localisation , idvue_materiel_has_localisation FROM vue_materiel_has_localisation WHERE  1 ";  
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
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_materiel_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectMaterielHasLocalisation($name="idvue_materiel_has_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_materiel_has_localisation , idvue_materiel_has_localisation FROM vue_materiel_has_localisation ORDER BY nomVue_materiel_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassMaterielHasLocalisation = new MaterielHasLocalisation(); ?>