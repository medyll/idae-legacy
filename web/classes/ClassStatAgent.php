<?   
class StatAgent
{
	var $conn = null;
	function StatAgent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createStatAgent($params){ 
		$this->conn->AutoExecute("vue_stat_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateStatAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_stat_agent", $params, "UPDATE", "idvue_stat_agent = ".$idvue_stat_agent); 
	}
	
	function deleteStatAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_stat_agent WHERE idvue_stat_agent = $idvue_stat_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneStatAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_stat_agent where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($lt_idagent)){ $sql .= " AND idagent < '".$lt_idagent."'" ; }
			if(!empty($gt_idagent)){ $sql .= " AND idagent > '".$gt_idagent."'" ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($lt_idlocation_client)){ $sql .= " AND idlocation_client < '".$lt_idlocation_client."'" ; }
			if(!empty($gt_idlocation_client)){ $sql .= " AND idlocation_client > '".$gt_idlocation_client."'" ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($lt_numeroLocation_client)){ $sql .= " AND numeroLocation_client < '".$lt_numeroLocation_client."'" ; }
			if(!empty($gt_numeroLocation_client)){ $sql .= " AND numeroLocation_client > '".$gt_numeroLocation_client."'" ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlIn($dateFinLocation_client) ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotIn($nodateFinLocation_client) ; }
			if(!empty($lt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client < '".$lt_dateFinLocation_client."'" ; }
			if(!empty($gt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client > '".$gt_dateFinLocation_client."'" ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlIn($dateDebutLocation_client) ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotIn($nodateDebutLocation_client) ; }
			if(!empty($lt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client < '".$lt_dateDebutLocation_client."'" ; }
			if(!empty($gt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client > '".$gt_dateDebutLocation_client."'" ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($lt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel < '".$lt_numeroSerieInterneMateriel."'" ; }
			if(!empty($gt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel > '".$gt_numeroSerieInterneMateriel."'" ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($lt_nomProduit)){ $sql .= " AND nomProduit < '".$lt_nomProduit."'" ; }
			if(!empty($gt_nomProduit)){ $sql .= " AND nomProduit > '".$gt_nomProduit."'" ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($lt_valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location < '".$lt_valeurLigne_location_client_has_critere_ligne_location."'" ; }
			if(!empty($gt_valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location > '".$gt_valeurLigne_location_client_has_critere_ligne_location."'" ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlIn($critere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotIn($nocritere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($lt_critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location < '".$lt_critere_ligne_location_idcritere_ligne_location."'" ; }
			if(!empty($gt_critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location > '".$gt_critere_ligne_location_idcritere_ligne_location."'" ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($lt_nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location < '".$lt_nomCritere_ligne_location."'" ; }
			if(!empty($gt_nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location > '".$gt_nomCritere_ligne_location."'" ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($lt_uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location < '".$lt_uniteCritere_ligne_location."'" ; }
			if(!empty($gt_uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location > '".$gt_uniteCritere_ligne_location."'" ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($lt_codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location < '".$lt_codeCritere_ligne_location."'" ; }
			if(!empty($gt_codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location > '".$gt_codeCritere_ligne_location."'" ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($lt_ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location < '".$lt_ordreCritere_ligne_location."'" ; }
			if(!empty($gt_ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location > '".$gt_ordreCritere_ligne_location."'" ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlIn($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotIn($nonomGamme_produit) ; }
			if(!empty($lt_nomGamme_produit)){ $sql .= " AND nomGamme_produit < '".$lt_nomGamme_produit."'" ; }
			if(!empty($gt_nomGamme_produit)){ $sql .= " AND nomGamme_produit > '".$gt_nomGamme_produit."'" ; }
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlIn($idgamme_produit) ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotIn($noidgamme_produit) ; }
			if(!empty($lt_idgamme_produit)){ $sql .= " AND idgamme_produit < '".$lt_idgamme_produit."'" ; }
			if(!empty($gt_idgamme_produit)){ $sql .= " AND idgamme_produit > '".$gt_idgamme_produit."'" ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($lt_nomMarque)){ $sql .= " AND nomMarque < '".$lt_nomMarque."'" ; }
			if(!empty($gt_nomMarque)){ $sql .= " AND nomMarque > '".$gt_nomMarque."'" ; }
			if(!empty($idmarque)){ $sql .= " AND idmarque ".sqlIn($idmarque) ; }
			if(!empty($noidmarque)){ $sql .= " AND idmarque ".sqlNotIn($noidmarque) ; }
			if(!empty($lt_idmarque)){ $sql .= " AND idmarque < '".$lt_idmarque."'" ; }
			if(!empty($gt_idmarque)){ $sql .= " AND idmarque > '".$gt_idmarque."'" ; }
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($lt_idproduit)){ $sql .= " AND idproduit < '".$lt_idproduit."'" ; }
			if(!empty($gt_idproduit)){ $sql .= " AND idproduit > '".$gt_idproduit."'" ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($lt_estClientClient)){ $sql .= " AND estClientClient < '".$lt_estClientClient."'" ; }
			if(!empty($gt_estClientClient)){ $sql .= " AND estClientClient > '".$gt_estClientClient."'" ; } 
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($lt_idmateriel)){ $sql .= " AND idmateriel < '".$lt_idmateriel."'" ; }
			if(!empty($gt_idmateriel)){ $sql .= " AND idmateriel > '".$gt_idmateriel."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneStatAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_stat_agent where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent,"idagent") ; }
			if(!empty($lt_idagent)){ $sql .= " AND idagent < '".$lt_idagent."'" ; }
			if(!empty($gt_idagent)){ $sql .= " AND idagent > '".$gt_idagent."'" ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient,"client_idclient") ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlSearch($idlocation_client,"idlocation_client") ; }
			if(!empty($lt_idlocation_client)){ $sql .= " AND idlocation_client < '".$lt_idlocation_client."'" ; }
			if(!empty($gt_idlocation_client)){ $sql .= " AND idlocation_client > '".$gt_idlocation_client."'" ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotSearch($noidlocation_client) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlSearch($numeroLocation_client,"numeroLocation_client") ; }
			if(!empty($lt_numeroLocation_client)){ $sql .= " AND numeroLocation_client < '".$lt_numeroLocation_client."'" ; }
			if(!empty($gt_numeroLocation_client)){ $sql .= " AND numeroLocation_client > '".$gt_numeroLocation_client."'" ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotSearch($nonumeroLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlSearch($dateFinLocation_client,"dateFinLocation_client") ; }
			if(!empty($lt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client < '".$lt_dateFinLocation_client."'" ; }
			if(!empty($gt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client > '".$gt_dateFinLocation_client."'" ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotSearch($nodateFinLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlSearch($dateDebutLocation_client,"dateDebutLocation_client") ; }
			if(!empty($lt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client < '".$lt_dateDebutLocation_client."'" ; }
			if(!empty($gt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client > '".$gt_dateDebutLocation_client."'" ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotSearch($nodateDebutLocation_client) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete,"nomSociete") ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel,"numeroSerieInterneMateriel") ; }
			if(!empty($lt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel < '".$lt_numeroSerieInterneMateriel."'" ; }
			if(!empty($gt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel > '".$gt_numeroSerieInterneMateriel."'" ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlSearch($nomProduit,"nomProduit") ; }
			if(!empty($lt_nomProduit)){ $sql .= " AND nomProduit < '".$lt_nomProduit."'" ; }
			if(!empty($gt_nomProduit)){ $sql .= " AND nomProduit > '".$gt_nomProduit."'" ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotSearch($nonomProduit) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlSearch($valeurLigne_location_client_has_critere_ligne_location,"valeurLigne_location_client_has_critere_ligne_location") ; }
			if(!empty($lt_valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location < '".$lt_valeurLigne_location_client_has_critere_ligne_location."'" ; }
			if(!empty($gt_valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location > '".$gt_valeurLigne_location_client_has_critere_ligne_location."'" ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotSearch($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlSearch($critere_ligne_location_idcritere_ligne_location,"critere_ligne_location_idcritere_ligne_location") ; }
			if(!empty($lt_critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location < '".$lt_critere_ligne_location_idcritere_ligne_location."'" ; }
			if(!empty($gt_critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location > '".$gt_critere_ligne_location_idcritere_ligne_location."'" ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotSearch($nocritere_ligne_location_idcritere_ligne_location) ; }

			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlSearch($nomCritere_ligne_location,"nomCritere_ligne_location") ; }
			if(!empty($lt_nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location < '".$lt_nomCritere_ligne_location."'" ; }
			if(!empty($gt_nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location > '".$gt_nomCritere_ligne_location."'" ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotSearch($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlSearch($uniteCritere_ligne_location,"uniteCritere_ligne_location") ; }
			if(!empty($lt_uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location < '".$lt_uniteCritere_ligne_location."'" ; }
			if(!empty($gt_uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location > '".$gt_uniteCritere_ligne_location."'" ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotSearch($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlSearch($codeCritere_ligne_location,"codeCritere_ligne_location") ; }
			if(!empty($lt_codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location < '".$lt_codeCritere_ligne_location."'" ; }
			if(!empty($gt_codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location > '".$gt_codeCritere_ligne_location."'" ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotSearch($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlSearch($ordreCritere_ligne_location,"ordreCritere_ligne_location") ; }
			if(!empty($lt_ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location < '".$lt_ordreCritere_ligne_location."'" ; }
			if(!empty($gt_ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location > '".$gt_ordreCritere_ligne_location."'" ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotSearch($noordreCritere_ligne_location) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlSearch($nomGamme_produit,"nomGamme_produit") ; }
			if(!empty($lt_nomGamme_produit)){ $sql .= " AND nomGamme_produit < '".$lt_nomGamme_produit."'" ; }
			if(!empty($gt_nomGamme_produit)){ $sql .= " AND nomGamme_produit > '".$gt_nomGamme_produit."'" ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotSearch($nonomGamme_produit) ; }
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlSearch($idgamme_produit,"idgamme_produit") ; }
			if(!empty($lt_idgamme_produit)){ $sql .= " AND idgamme_produit < '".$lt_idgamme_produit."'" ; }
			if(!empty($gt_idgamme_produit)){ $sql .= " AND idgamme_produit > '".$gt_idgamme_produit."'" ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotSearch($noidgamme_produit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlSearch($nomMarque,"nomMarque") ; }
			if(!empty($lt_nomMarque)){ $sql .= " AND nomMarque < '".$lt_nomMarque."'" ; }
			if(!empty($gt_nomMarque)){ $sql .= " AND nomMarque > '".$gt_nomMarque."'" ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotSearch($nonomMarque) ; }
			if(!empty($idmarque)){ $sql .= " AND idmarque ".sqlSearch($idmarque,"idmarque") ; }
			if(!empty($lt_idmarque)){ $sql .= " AND idmarque < '".$lt_idmarque."'" ; }
			if(!empty($gt_idmarque)){ $sql .= " AND idmarque > '".$gt_idmarque."'" ; }
			if(!empty($noidmarque)){ $sql .= " AND idmarque ".sqlNotSearch($noidmarque) ; }
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlSearch($idproduit,"idproduit") ; }
			if(!empty($lt_idproduit)){ $sql .= " AND idproduit < '".$lt_idproduit."'" ; }
			if(!empty($gt_idproduit)){ $sql .= " AND idproduit > '".$gt_idproduit."'" ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotSearch($noidproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllStatAgent(){ 
		$sql="SELECT * FROM  vue_stat_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneStatAgent($name="idvue_stat_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_stat_agent , idvue_stat_agent FROM vue_stat_agent WHERE  1 ";  
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($gt_idagent)){ $sql .= " AND idagent > ".$gt_idagent ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > ".$gt_client_idclient ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlIn($idlocation_client) ; }
			if(!empty($gt_idlocation_client)){ $sql .= " AND idlocation_client > ".$gt_idlocation_client ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotIn($noidlocation_client) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlIn($numeroLocation_client) ; }
			if(!empty($gt_numeroLocation_client)){ $sql .= " AND numeroLocation_client > ".$gt_numeroLocation_client ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotIn($nonumeroLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlIn($dateFinLocation_client) ; }
			if(!empty($gt_dateFinLocation_client)){ $sql .= " AND dateFinLocation_client > ".$gt_dateFinLocation_client ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotIn($nodateFinLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlIn($dateDebutLocation_client) ; }
			if(!empty($gt_dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client > ".$gt_dateDebutLocation_client ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotIn($nodateDebutLocation_client) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > ".$gt_nomSociete ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($gt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel > ".$gt_numeroSerieInterneMateriel ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($gt_nomProduit)){ $sql .= " AND nomProduit > ".$gt_nomProduit ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($gt_valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location > ".$gt_valeurLigne_location_client_has_critere_ligne_location ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlIn($critere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($gt_critere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location > ".$gt_critere_ligne_location_idcritere_ligne_location ; }
			if(!empty($nocritere_ligne_location_idcritere_ligne_location)){ $sql .= " AND critere_ligne_location_idcritere_ligne_location ".sqlNotIn($nocritere_ligne_location_idcritere_ligne_location) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($gt_nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location > ".$gt_nomCritere_ligne_location ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($gt_uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location > ".$gt_uniteCritere_ligne_location ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($gt_codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location > ".$gt_codeCritere_ligne_location ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($gt_ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location > ".$gt_ordreCritere_ligne_location ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlIn($nomGamme_produit) ; }
			if(!empty($gt_nomGamme_produit)){ $sql .= " AND nomGamme_produit > ".$gt_nomGamme_produit ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotIn($nonomGamme_produit) ; }
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlIn($idgamme_produit) ; }
			if(!empty($gt_idgamme_produit)){ $sql .= " AND idgamme_produit > ".$gt_idgamme_produit ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotIn($noidgamme_produit) ; }
			if(!empty($nomMarque)){ $sql .= " AND nomMarque ".sqlIn($nomMarque) ; }
			if(!empty($gt_nomMarque)){ $sql .= " AND nomMarque > ".$gt_nomMarque ; }
			if(!empty($nonomMarque)){ $sql .= " AND nomMarque ".sqlNotIn($nonomMarque) ; }
			if(!empty($idmarque)){ $sql .= " AND idmarque ".sqlIn($idmarque) ; }
			if(!empty($gt_idmarque)){ $sql .= " AND idmarque > ".$gt_idmarque ; }
			if(!empty($noidmarque)){ $sql .= " AND idmarque ".sqlNotIn($noidmarque) ; }
			if(!empty($idproduit)){ $sql .= " AND idproduit ".sqlIn($idproduit) ; }
			if(!empty($gt_idproduit)){ $sql .= " AND idproduit > ".$gt_idproduit ; }
			if(!empty($noidproduit)){ $sql .= " AND idproduit ".sqlNotIn($noidproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_stat_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectStatAgent($name="idvue_stat_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_stat_agent , idvue_stat_agent FROM vue_stat_agent ORDER BY nomVue_stat_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassStatAgent = new StatAgent(); ?>