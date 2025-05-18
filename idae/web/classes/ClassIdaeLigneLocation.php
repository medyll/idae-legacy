<?   
class IdaeLigneLocation
{
	var $conn = null;
	function IdaeLigneLocation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLigneLocation($params){ 
		$this->conn->AutoExecute("vue_ligne_location_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_ligne_location_client", $params, "UPDATE", "idvue_ligne_location_client = ".$idvue_ligne_location_client); 
	}
	
	function deleteIdaeLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_ligne_location_client WHERE idvue_ligne_location_client = $idvue_ligne_location_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_ligne_location_client where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlIn($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotIn($nodureeLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlIn($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotIn($nodateDebutLigne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlIn($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotIn($nopvhtLigne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getOneTableLigneParcs($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from ligne_parcs where 1 " ;
			if(!empty($idligne_parcs)){ $sql .= " AND idligne_parcs ".sqlIn($idligne_parcs) ; }
			if(!empty($noidligne_parcs)){ $sql .= " AND idligne_parcs ".sqlNotIn($noidligne_parcs) ; }
			if(!empty($parcs_idparcs)){ $sql .= " AND parcs_idparcs ".sqlIn($parcs_idparcs) ; }
			if(!empty($noparcs_idparcs)){ $sql .= " AND parcs_idparcs ".sqlNotIn($noparcs_idparcs) ; }
			if(!empty($parcs_client_idclient)){ $sql .= " AND parcs_client_idclient ".sqlIn($parcs_client_idclient) ; }
			if(!empty($noparcs_client_idclient)){ $sql .= " AND parcs_client_idclient ".sqlNotIn($noparcs_client_idclient) ; }
			if(!empty($produits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlIn($produits_marques_idmarques) ; }
			if(!empty($noproduits_marques_idmarques)){ $sql .= " AND produits_marques_idmarques ".sqlNotIn($noproduits_marques_idmarques) ; }
			if(!empty($produits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlIn($produits_fournisseurs_idfournisseurs) ; }
			if(!empty($noproduits_fournisseurs_idfournisseurs)){ $sql .= " AND produits_fournisseurs_idfournisseurs ".sqlNotIn($noproduits_fournisseurs_idfournisseurs) ; }
			if(!empty($produits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlIn($produits_categorieProduits_id) ; }
			if(!empty($noproduits_categorieProduits_id)){ $sql .= " AND produits_categorieProduits_id ".sqlNotIn($noproduits_categorieProduits_id) ; }
			if(!empty($produits_id)){ $sql .= " AND produits_id ".sqlIn($produits_id) ; }
			if(!empty($noproduits_id)){ $sql .= " AND produits_id ".sqlNotIn($noproduits_id) ; }
			if(!empty($typeligne)){ $sql .= " AND typeligne ".sqlIn($typeligne) ; }
			if(!empty($notypeligne)){ $sql .= " AND typeligne ".sqlNotIn($notypeligne) ; }
			if(!empty($idConfigs)){ $sql .= " AND idConfigs ".sqlIn($idConfigs) ; }
			if(!empty($noidConfigs)){ $sql .= " AND idConfigs ".sqlNotIn($noidConfigs) ; }
			if(!empty($toner_type)){ $sql .= " AND toner_type ".sqlIn($toner_type) ; }
			if(!empty($notoner_type)){ $sql .= " AND toner_type ".sqlNotIn($notoner_type) ; }
			if(!empty($abont_trim)){ $sql .= " AND abont_trim ".sqlIn($abont_trim) ; }
			if(!empty($noabont_trim)){ $sql .= " AND abont_trim ".sqlNotIn($noabont_trim) ; }
			if(!empty($cout_copie_couleur)){ $sql .= " AND cout_copie_couleur ".sqlIn($cout_copie_couleur) ; }
			if(!empty($nocout_copie_couleur)){ $sql .= " AND cout_copie_couleur ".sqlNotIn($nocout_copie_couleur) ; }
			if(!empty($cout_copie_noir)){ $sql .= " AND cout_copie_noir ".sqlIn($cout_copie_noir) ; }
			if(!empty($nocout_copie_noir)){ $sql .= " AND cout_copie_noir ".sqlNotIn($nocout_copie_noir) ; }
			if(!empty($cv_trim)){ $sql .= " AND cv_trim ".sqlIn($cv_trim) ; }
			if(!empty($nocv_trim)){ $sql .= " AND cv_trim ".sqlNotIn($nocv_trim) ; }
			if(!empty($duree_trim)){ $sql .= " AND duree_trim ".sqlIn($duree_trim) ; }
			if(!empty($noduree_trim)){ $sql .= " AND duree_trim ".sqlNotIn($noduree_trim) ; }
			if(!empty($num_serie)){ $sql .= " AND num_serie ".sqlIn($num_serie) ; }
			if(!empty($nonum_serie)){ $sql .= " AND num_serie ".sqlNotIn($nonum_serie) ; }
			if(!empty($installation)){ $sql .= " AND installation ".sqlIn($installation) ; }
			if(!empty($noinstallation)){ $sql .= " AND installation ".sqlNotIn($noinstallation) ; }
			if(!empty($cv_trim_couleur)){ $sql .= " AND cv_trim_couleur ".sqlIn($cv_trim_couleur) ; }
			if(!empty($nocv_trim_couleur)){ $sql .= " AND cv_trim_couleur ".sqlNotIn($nocv_trim_couleur) ; }
			if(!empty($loyer_trim)){ $sql .= " AND loyer_trim ".sqlIn($loyer_trim) ; }
			if(!empty($noloyer_trim)){ $sql .= " AND loyer_trim ".sqlNotIn($noloyer_trim) ; }
			if(!empty($prestataire)){ $sql .= " AND prestataire ".sqlIn($prestataire) ; }
			if(!empty($noprestataire)){ $sql .= " AND prestataire ".sqlNotIn($noprestataire) ; }
			if(!empty($produits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlIn($produits_segment_produit_idsegment_produit) ; }
			if(!empty($noproduits_segment_produit_idsegment_produit)){ $sql .= " AND produits_segment_produit_idsegment_produit ".sqlNotIn($noproduits_segment_produit_idsegment_produit) ; }
			if(!empty($idmachine)){ $sql .= " AND idmachine ".sqlIn($idmachine) ; }
			if(!empty($noidmachine)){ $sql .= " AND idmachine ".sqlNotIn($noidmachine) ; }
			if(!empty($rang)){ $sql .= " AND rang ".sqlIn($rang) ; }
			if(!empty($norang)){ $sql .= " AND rang ".sqlNotIn($norang) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlIn($pvHt) ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotIn($nopvHt) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_location_client where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlSearch($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotSearch($noidligne_location_client) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlSearch($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotSearch($nodureeLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlSearch($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotSearch($nodateDebutLigne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlSearch($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotSearch($nopvhtLigne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLigneLocation(){ 
		$sql="SELECT * FROM  vue_ligne_location_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLigneLocation($name="idvue_ligne_location_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_ligne_location_client , idvue_ligne_location_client FROM vue_ligne_location_client WHERE  1 ";  
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlIn($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotIn($nodureeLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlIn($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotIn($nodateDebutLigne_location_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlIn($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotIn($nopvhtLigne_location_client) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_ligne_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLigneLocation($name="idvue_ligne_location_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_ligne_location_client , idvue_ligne_location_client FROM vue_ligne_location_client ORDER BY nomVue_ligne_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLigneLocation = new IdaeLigneLocation(); ?>