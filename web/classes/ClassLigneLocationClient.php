<?   
class LigneLocationClient
{
	var $conn = null;
	function LigneLocationClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLigneLocationClient($params){ 
		$this->conn->AutoExecute("ligne_location_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_location_client", $params, "UPDATE", "idligne_location_client = ".$idligne_location_client); 
	}
	
	function deleteLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_location_client WHERE idligne_location_client = $idligne_location_client"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE ligne_location_client"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_ligne_location_client where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($dateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlIn($dateCreationLigne_location_client) ; }
			if(!empty($nodateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlNotIn($nodateCreationLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlIn($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotIn($nodateDebutLigne_location_client) ; }
			if(!empty($dateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlIn($dateClotureLigne_location_client) ; }
			if(!empty($nodateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlNotIn($nodateClotureLigne_location_client) ; }
			if(!empty($dateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlIn($dateFinLigne_location_client) ; }
			if(!empty($nodateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlNotIn($nodateFinLigne_location_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlIn($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotIn($nodureeLigne_location_client) ; }
			if(!empty($uniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlIn($uniteDureeLigne_location_client) ; }
			if(!empty($nouniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlNotIn($nouniteDureeLigne_location_client) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlIn($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotIn($nopvhtLigne_location_client) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
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
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlIn($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotIn($nodateCreationLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlIn($dateDebutLocation_client) ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotIn($nodateDebutLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlIn($dateFinLocation_client) ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotIn($nodateFinLocation_client) ; }
			if(!empty($dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlIn($dateClotureLocation_client) ; }
			if(!empty($nodateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlNotIn($nodateClotureLocation_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_location_client where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlSearch($idligne_location_client,"idligne_location_client") ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotSearch($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation,"localisation_idlocalisation") ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel,"materiel_idmateriel") ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client,"location_client_idlocation_client") ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($dateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlSearch($dateCreationLigne_location_client,"dateCreationLigne_location_client") ; }
			if(!empty($nodateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlNotSearch($nodateCreationLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlSearch($dateDebutLigne_location_client,"dateDebutLigne_location_client") ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotSearch($nodateDebutLigne_location_client) ; }
			if(!empty($dateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlSearch($dateClotureLigne_location_client,"dateClotureLigne_location_client") ; }
			if(!empty($nodateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlNotSearch($nodateClotureLigne_location_client) ; }
			if(!empty($dateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlSearch($dateFinLigne_location_client,"dateFinLigne_location_client") ; }
			if(!empty($nodateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlNotSearch($nodateFinLigne_location_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlSearch($dureeLigne_location_client,"dureeLigne_location_client") ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotSearch($nodureeLigne_location_client) ; }
			if(!empty($uniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlSearch($uniteDureeLigne_location_client,"uniteDureeLigne_location_client") ; }
			if(!empty($nouniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlNotSearch($nouniteDureeLigne_location_client) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlSearch($pvhtLigne_location_client,"pvhtLigne_location_client") ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotSearch($nopvhtLigne_location_client) ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlSearch($idlocation_client,"idlocation_client") ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotSearch($noidlocation_client) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlSearch($type_location_client_idtype_location_client,"type_location_client_idtype_location_client") ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotSearch($notype_location_client_idtype_location_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient,"client_idclient") ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlSearch($numeroLocation_client,"numeroLocation_client") ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotSearch($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlSearch($dateCreationLocation_client,"dateCreationLocation_client") ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotSearch($nodateCreationLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlSearch($dateDebutLocation_client,"dateDebutLocation_client") ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotSearch($nodateDebutLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlSearch($dateFinLocation_client,"dateFinLocation_client") ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotSearch($nodateFinLocation_client) ; }
			if(!empty($dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlSearch($dateClotureLocation_client,"dateClotureLocation_client") ; }
			if(!empty($nodateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlNotSearch($nodateClotureLocation_client) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel,"idmateriel") ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneLocationClient(){ 
		$sql="SELECT * FROM  ligne_location_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneLocationClient($name="idligne_location_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_location_client , idligne_location_client FROM ligne_location_client WHERE  1 ";  
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($dateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlIn($dateCreationLigne_location_client) ; }
			if(!empty($nodateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlNotIn($nodateCreationLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlIn($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotIn($nodateDebutLigne_location_client) ; }
			if(!empty($dateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlIn($dateClotureLigne_location_client) ; }
			if(!empty($nodateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlNotIn($nodateClotureLigne_location_client) ; }
			if(!empty($dateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlIn($dateFinLigne_location_client) ; }
			if(!empty($nodateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlNotIn($nodateFinLigne_location_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlIn($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotIn($nodureeLigne_location_client) ; }
			if(!empty($uniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlIn($uniteDureeLigne_location_client) ; }
			if(!empty($nouniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlNotIn($nouniteDureeLigne_location_client) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlIn($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotIn($nopvhtLigne_location_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneLocationClient($name="idligne_location_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_location_client , idligne_location_client FROM ligne_location_client ORDER BY nomLigne_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLigneLocationClient = new LigneLocationClient(); ?>