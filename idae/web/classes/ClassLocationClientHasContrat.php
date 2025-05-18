<?   
class LocationClientHasContrat
{
	var $conn = null;
	function LocationClientHasContrat(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLocationClientHasContrat($params){ 
		$this->conn->AutoExecute("location_client_has_contrat", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLocationClientHasContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("location_client_has_contrat", $params, "UPDATE", "idlocation_client_has_contrat = ".$idlocation_client_has_contrat); 
	}
	
	function deleteLocationClientHasContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  location_client_has_contrat WHERE idlocation_client_has_contrat = $idlocation_client_has_contrat"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE location_client_has_contrat "; 
		$this->conn->Execute($sql); 	
	}
	function getOneLocationClientHasContrat($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_location_client_has_contrat where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlIn($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotIn($noidcontrat) ; }
			if(!empty($lt_idcontrat)){ $sql .= " AND idcontrat < '".$lt_idcontrat."'" ; }
			if(!empty($gt_idcontrat)){ $sql .= " AND idcontrat > '".$gt_idcontrat."'" ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($lt_numContrat)){ $sql .= " AND numContrat < '".$lt_numContrat."'" ; }
			if(!empty($gt_numContrat)){ $sql .= " AND numContrat > '".$gt_numContrat."'" ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlIn($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotIn($nodureeContrat) ; }
			if(!empty($lt_dureeContrat)){ $sql .= " AND dureeContrat < '".$lt_dureeContrat."'" ; }
			if(!empty($gt_dureeContrat)){ $sql .= " AND dureeContrat > '".$gt_dureeContrat."'" ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($lt_dateDebutContrat)){ $sql .= " AND dateDebutContrat < '".$lt_dateDebutContrat."'" ; }
			if(!empty($gt_dateDebutContrat)){ $sql .= " AND dateDebutContrat > '".$gt_dateDebutContrat."'" ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlIn($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotIn($nodateFinContrat) ; }
			if(!empty($lt_dateFinContrat)){ $sql .= " AND dateFinContrat < '".$lt_dateFinContrat."'" ; }
			if(!empty($gt_dateFinContrat)){ $sql .= " AND dateFinContrat > '".$gt_dateFinContrat."'" ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlIn($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotIn($nodateClotureContrat) ; }
			if(!empty($lt_dateClotureContrat)){ $sql .= " AND dateClotureContrat < '".$lt_dateClotureContrat."'" ; }
			if(!empty($gt_dateClotureContrat)){ $sql .= " AND dateClotureContrat > '".$gt_dateClotureContrat."'" ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($lt_idlocation_client)){ $sql .= " AND idlocation_client < '".$lt_idlocation_client."'" ; }
			if(!empty($gt_idlocation_client)){ $sql .= " AND idlocation_client > '".$gt_idlocation_client."'" ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($lt_type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client < '".$lt_type_location_client_idtype_location_client."'" ; }
			if(!empty($gt_type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client > '".$gt_type_location_client_idtype_location_client."'" ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($lt_numeroLocation_client)){ $sql .= " AND numeroLocation_client < '".$lt_numeroLocation_client."'" ; }
			if(!empty($gt_numeroLocation_client)){ $sql .= " AND numeroLocation_client > '".$gt_numeroLocation_client."'" ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlIn($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotIn($nodateCreationLocation_client) ; }
			if(!empty($lt_dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client < '".$lt_dateCreationLocation_client."'" ; }
			if(!empty($gt_dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client > '".$gt_dateCreationLocation_client."'" ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlIn($dateDebutLocation_client) ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotIn($nodateDebutLocation_client) ; }
			if(!empty($lt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client < '".$lt_dateDebutLocation_client."'" ; }
			if(!empty($gt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client > '".$gt_dateDebutLocation_client."'" ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlIn($dateFinLocation_client) ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotIn($nodateFinLocation_client) ; }
			if(!empty($lt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client < '".$lt_dateFinLocation_client."'" ; }
			if(!empty($gt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client > '".$gt_dateFinLocation_client."'" ; }
			if(!empty($dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlIn($dateClotureLocation_client) ; }
			if(!empty($nodateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlNotIn($nodateClotureLocation_client) ; }
			if(!empty($lt_dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client < '".$lt_dateClotureLocation_client."'" ; }
			if(!empty($gt_dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client > '".$gt_dateClotureLocation_client."'" ; }
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlIn($idtype_location_client) ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotIn($noidtype_location_client) ; }
			if(!empty($lt_idtype_location_client)){ $sql .= " AND idtype_location_client < '".$lt_idtype_location_client."'" ; }
			if(!empty($gt_idtype_location_client)){ $sql .= " AND idtype_location_client > '".$gt_idtype_location_client."'" ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlIn($nomType_location_client) ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotIn($nonomType_location_client) ; }
			if(!empty($lt_nomType_location_client)){ $sql .= " AND nomType_location_client < '".$lt_nomType_location_client."'" ; }
			if(!empty($gt_nomType_location_client)){ $sql .= " AND nomType_location_client > '".$gt_nomType_location_client."'" ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlIn($commentaireType_location_client) ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotIn($nocommentaireType_location_client) ; }
			if(!empty($lt_commentaireType_location_client)){ $sql .= " AND commentaireType_location_client < '".$lt_commentaireType_location_client."'" ; }
			if(!empty($gt_commentaireType_location_client)){ $sql .= " AND commentaireType_location_client > '".$gt_commentaireType_location_client."'" ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlIn($ordreType_location_client) ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotIn($noordreType_location_client) ; }
			if(!empty($lt_ordreType_location_client)){ $sql .= " AND ordreType_location_client < '".$lt_ordreType_location_client."'" ; }
			if(!empty($gt_ordreType_location_client)){ $sql .= " AND ordreType_location_client > '".$gt_ordreType_location_client."'" ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlIn($codeType_location_client) ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotIn($nocodeType_location_client) ; }
			if(!empty($lt_codeType_location_client)){ $sql .= " AND codeType_location_client < '".$lt_codeType_location_client."'" ; }
			if(!empty($gt_codeType_location_client)){ $sql .= " AND codeType_location_client > '".$gt_codeType_location_client."'" ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($lt_location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client < '".$lt_location_client_idlocation_client."'" ; }
			if(!empty($gt_location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client > '".$gt_location_client_idlocation_client."'" ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlIn($contrat_idcontrat) ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotIn($nocontrat_idcontrat) ; }
			if(!empty($lt_contrat_idcontrat)){ $sql .= " AND contrat_idcontrat < '".$lt_contrat_idcontrat."'" ; }
			if(!empty($gt_contrat_idcontrat)){ $sql .= " AND contrat_idcontrat > '".$gt_contrat_idcontrat."'" ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($notype_contrat_idtype_contrat) ; }
			if(!empty($lt_type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat < '".$lt_type_contrat_idtype_contrat."'" ; }
			if(!empty($gt_type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat > '".$gt_type_contrat_idtype_contrat."'" ; }
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlIn($idtype_contrat) ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotIn($noidtype_contrat) ; }
			if(!empty($lt_idtype_contrat)){ $sql .= " AND idtype_contrat < '".$lt_idtype_contrat."'" ; }
			if(!empty($gt_idtype_contrat)){ $sql .= " AND idtype_contrat > '".$gt_idtype_contrat."'" ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlIn($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotIn($nonomType_contrat) ; }
			if(!empty($lt_nomType_contrat)){ $sql .= " AND nomType_contrat < '".$lt_nomType_contrat."'" ; }
			if(!empty($gt_nomType_contrat)){ $sql .= " AND nomType_contrat > '".$gt_nomType_contrat."'" ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlIn($ordreType_contrat) ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotIn($noordreType_contrat) ; }
			if(!empty($lt_ordreType_contrat)){ $sql .= " AND ordreType_contrat < '".$lt_ordreType_contrat."'" ; }
			if(!empty($gt_ordreType_contrat)){ $sql .= " AND ordreType_contrat > '".$gt_ordreType_contrat."'" ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotIn($nocodeType_contrat) ; }
			if(!empty($lt_codeType_contrat)){ $sql .= " AND codeType_contrat < '".$lt_codeType_contrat."'" ; }
			if(!empty($gt_codeType_contrat)){ $sql .= " AND codeType_contrat > '".$gt_codeType_contrat."'" ; }
			if(!empty($idcontrat_old)){ $sql .= " AND idcontrat_old ".sqlIn($idcontrat_old) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLocationClientHasContrat($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_location_client_has_contrat where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlSearch($idcontrat,"idcontrat") ; }
			if(!empty($lt_idcontrat)){ $sql .= " AND idcontrat < '".$lt_idcontrat."'" ; }
			if(!empty($gt_idcontrat)){ $sql .= " AND idcontrat > '".$gt_idcontrat."'" ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotSearch($noidcontrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlSearch($numContrat,"numContrat") ; }
			if(!empty($lt_numContrat)){ $sql .= " AND numContrat < '".$lt_numContrat."'" ; }
			if(!empty($gt_numContrat)){ $sql .= " AND numContrat > '".$gt_numContrat."'" ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotSearch($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlSearch($dureeContrat,"dureeContrat") ; }
			if(!empty($lt_dureeContrat)){ $sql .= " AND dureeContrat < '".$lt_dureeContrat."'" ; }
			if(!empty($gt_dureeContrat)){ $sql .= " AND dureeContrat > '".$gt_dureeContrat."'" ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotSearch($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlSearch($dateDebutContrat,"dateDebutContrat") ; }
			if(!empty($lt_dateDebutContrat)){ $sql .= " AND dateDebutContrat < '".$lt_dateDebutContrat."'" ; }
			if(!empty($gt_dateDebutContrat)){ $sql .= " AND dateDebutContrat > '".$gt_dateDebutContrat."'" ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotSearch($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlSearch($dateFinContrat,"dateFinContrat") ; }
			if(!empty($lt_dateFinContrat)){ $sql .= " AND dateFinContrat < '".$lt_dateFinContrat."'" ; }
			if(!empty($gt_dateFinContrat)){ $sql .= " AND dateFinContrat > '".$gt_dateFinContrat."'" ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotSearch($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlSearch($dateClotureContrat,"dateClotureContrat") ; }
			if(!empty($lt_dateClotureContrat)){ $sql .= " AND dateClotureContrat < '".$lt_dateClotureContrat."'" ; }
			if(!empty($gt_dateClotureContrat)){ $sql .= " AND dateClotureContrat > '".$gt_dateClotureContrat."'" ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotSearch($nodateClotureContrat) ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlSearch($idlocation_client,"idlocation_client") ; }
			if(!empty($lt_idlocation_client)){ $sql .= " AND idlocation_client < '".$lt_idlocation_client."'" ; }
			if(!empty($gt_idlocation_client)){ $sql .= " AND idlocation_client > '".$gt_idlocation_client."'" ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotSearch($noidlocation_client) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlSearch($type_location_client_idtype_location_client,"type_location_client_idtype_location_client") ; }
			if(!empty($lt_type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client < '".$lt_type_location_client_idtype_location_client."'" ; }
			if(!empty($gt_type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client > '".$gt_type_location_client_idtype_location_client."'" ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotSearch($notype_location_client_idtype_location_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient,"client_idclient") ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlSearch($numeroLocation_client,"numeroLocation_client") ; }
			if(!empty($lt_numeroLocation_client)){ $sql .= " AND numeroLocation_client < '".$lt_numeroLocation_client."'" ; }
			if(!empty($gt_numeroLocation_client)){ $sql .= " AND numeroLocation_client > '".$gt_numeroLocation_client."'" ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotSearch($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlSearch($dateCreationLocation_client,"dateCreationLocation_client") ; }
			if(!empty($lt_dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client < '".$lt_dateCreationLocation_client."'" ; }
			if(!empty($gt_dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client > '".$gt_dateCreationLocation_client."'" ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotSearch($nodateCreationLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlSearch($dateDebutLocation_client,"dateDebutLocation_client") ; }
			if(!empty($lt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client < '".$lt_dateDebutLocation_client."'" ; }
			if(!empty($gt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client > '".$gt_dateDebutLocation_client."'" ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotSearch($nodateDebutLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlSearch($dateFinLocation_client,"dateFinLocation_client") ; }
			if(!empty($lt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client < '".$lt_dateFinLocation_client."'" ; }
			if(!empty($gt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client > '".$gt_dateFinLocation_client."'" ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotSearch($nodateFinLocation_client) ; }
			if(!empty($dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlSearch($dateClotureLocation_client,"dateClotureLocation_client") ; }
			if(!empty($lt_dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client < '".$lt_dateClotureLocation_client."'" ; }
			if(!empty($gt_dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client > '".$gt_dateClotureLocation_client."'" ; }
			if(!empty($nodateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlNotSearch($nodateClotureLocation_client) ; }
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlSearch($idtype_location_client,"idtype_location_client") ; }
			if(!empty($lt_idtype_location_client)){ $sql .= " AND idtype_location_client < '".$lt_idtype_location_client."'" ; }
			if(!empty($gt_idtype_location_client)){ $sql .= " AND idtype_location_client > '".$gt_idtype_location_client."'" ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotSearch($noidtype_location_client) ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlSearch($nomType_location_client,"nomType_location_client") ; }
			if(!empty($lt_nomType_location_client)){ $sql .= " AND nomType_location_client < '".$lt_nomType_location_client."'" ; }
			if(!empty($gt_nomType_location_client)){ $sql .= " AND nomType_location_client > '".$gt_nomType_location_client."'" ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotSearch($nonomType_location_client) ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlSearch($commentaireType_location_client,"commentaireType_location_client") ; }
			if(!empty($lt_commentaireType_location_client)){ $sql .= " AND commentaireType_location_client < '".$lt_commentaireType_location_client."'" ; }
			if(!empty($gt_commentaireType_location_client)){ $sql .= " AND commentaireType_location_client > '".$gt_commentaireType_location_client."'" ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotSearch($nocommentaireType_location_client) ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlSearch($ordreType_location_client,"ordreType_location_client") ; }
			if(!empty($lt_ordreType_location_client)){ $sql .= " AND ordreType_location_client < '".$lt_ordreType_location_client."'" ; }
			if(!empty($gt_ordreType_location_client)){ $sql .= " AND ordreType_location_client > '".$gt_ordreType_location_client."'" ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotSearch($noordreType_location_client) ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlSearch($codeType_location_client,"codeType_location_client") ; }
			if(!empty($lt_codeType_location_client)){ $sql .= " AND codeType_location_client < '".$lt_codeType_location_client."'" ; }
			if(!empty($gt_codeType_location_client)){ $sql .= " AND codeType_location_client > '".$gt_codeType_location_client."'" ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotSearch($nocodeType_location_client) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client,"location_client_idlocation_client") ; }
			if(!empty($lt_location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client < '".$lt_location_client_idlocation_client."'" ; }
			if(!empty($gt_location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client > '".$gt_location_client_idlocation_client."'" ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlSearch($contrat_idcontrat,"contrat_idcontrat") ; }
			if(!empty($lt_contrat_idcontrat)){ $sql .= " AND contrat_idcontrat < '".$lt_contrat_idcontrat."'" ; }
			if(!empty($gt_contrat_idcontrat)){ $sql .= " AND contrat_idcontrat > '".$gt_contrat_idcontrat."'" ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotSearch($nocontrat_idcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlSearch($type_contrat_idtype_contrat,"type_contrat_idtype_contrat") ; }
			if(!empty($lt_type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat < '".$lt_type_contrat_idtype_contrat."'" ; }
			if(!empty($gt_type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat > '".$gt_type_contrat_idtype_contrat."'" ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotSearch($notype_contrat_idtype_contrat) ; }
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlSearch($idtype_contrat,"idtype_contrat") ; }
			if(!empty($lt_idtype_contrat)){ $sql .= " AND idtype_contrat < '".$lt_idtype_contrat."'" ; }
			if(!empty($gt_idtype_contrat)){ $sql .= " AND idtype_contrat > '".$gt_idtype_contrat."'" ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotSearch($noidtype_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlSearch($nomType_contrat,"nomType_contrat") ; }
			if(!empty($lt_nomType_contrat)){ $sql .= " AND nomType_contrat < '".$lt_nomType_contrat."'" ; }
			if(!empty($gt_nomType_contrat)){ $sql .= " AND nomType_contrat > '".$gt_nomType_contrat."'" ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotSearch($nonomType_contrat) ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlSearch($ordreType_contrat,"ordreType_contrat") ; }
			if(!empty($lt_ordreType_contrat)){ $sql .= " AND ordreType_contrat < '".$lt_ordreType_contrat."'" ; }
			if(!empty($gt_ordreType_contrat)){ $sql .= " AND ordreType_contrat > '".$gt_ordreType_contrat."'" ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotSearch($noordreType_contrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlSearch($codeType_contrat,"codeType_contrat") ; }
			if(!empty($lt_codeType_contrat)){ $sql .= " AND codeType_contrat < '".$lt_codeType_contrat."'" ; }
			if(!empty($gt_codeType_contrat)){ $sql .= " AND codeType_contrat > '".$gt_codeType_contrat."'" ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotSearch($nocodeType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLocationClientHasContrat(){ 
		$sql="SELECT * FROM  vue_location_client_has_contrat"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLocationClientHasContrat($name="idvue_location_client_has_contrat",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_location_client_has_contrat , idvue_location_client_has_contrat FROM vue_location_client_has_contrat WHERE  1 ";  
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlIn($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotIn($noidcontrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlIn($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotIn($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlIn($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotIn($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlIn($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotIn($nodateClotureContrat) ; }
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
			if(!empty($idtype_location_client)){ $sql .= " AND idtype_location_client ".sqlIn($idtype_location_client) ; }
			if(!empty($noidtype_location_client)){ $sql .= " AND idtype_location_client ".sqlNotIn($noidtype_location_client) ; }
			if(!empty($nomType_location_client)){ $sql .= " AND nomType_location_client ".sqlIn($nomType_location_client) ; }
			if(!empty($nonomType_location_client)){ $sql .= " AND nomType_location_client ".sqlNotIn($nonomType_location_client) ; }
			if(!empty($commentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlIn($commentaireType_location_client) ; }
			if(!empty($nocommentaireType_location_client)){ $sql .= " AND commentaireType_location_client ".sqlNotIn($nocommentaireType_location_client) ; }
			if(!empty($ordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlIn($ordreType_location_client) ; }
			if(!empty($noordreType_location_client)){ $sql .= " AND ordreType_location_client ".sqlNotIn($noordreType_location_client) ; }
			if(!empty($codeType_location_client)){ $sql .= " AND codeType_location_client ".sqlIn($codeType_location_client) ; }
			if(!empty($nocodeType_location_client)){ $sql .= " AND codeType_location_client ".sqlNotIn($nocodeType_location_client) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlIn($contrat_idcontrat) ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotIn($nocontrat_idcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($notype_contrat_idtype_contrat) ; }
			if(!empty($idtype_contrat)){ $sql .= " AND idtype_contrat ".sqlIn($idtype_contrat) ; }
			if(!empty($noidtype_contrat)){ $sql .= " AND idtype_contrat ".sqlNotIn($noidtype_contrat) ; }
			if(!empty($nomType_contrat)){ $sql .= " AND nomType_contrat ".sqlIn($nomType_contrat) ; }
			if(!empty($nonomType_contrat)){ $sql .= " AND nomType_contrat ".sqlNotIn($nonomType_contrat) ; }
			if(!empty($ordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlIn($ordreType_contrat) ; }
			if(!empty($noordreType_contrat)){ $sql .= " AND ordreType_contrat ".sqlNotIn($noordreType_contrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nocodeType_contrat)){ $sql .= " AND codeType_contrat ".sqlNotIn($nocodeType_contrat) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_location_client_has_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLocationClientHasContrat($name="idlocation_client_has_contrat",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLocation_client_has_contrat , idlocation_client_has_contrat FROM location_client_has_contrat ORDER BY nomLocation_client_has_contrat ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLocationClientHasContrat = new LocationClientHasContrat(); ?>