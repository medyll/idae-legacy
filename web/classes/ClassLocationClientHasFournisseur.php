<?   
class LocationClientHasFournisseur
{
	var $conn = null;
	function LocationClientHasFournisseur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLocationClientHasFournisseur($params){ 
		$this->conn->AutoExecute("location_client_has_fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLocationClientHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("location_client_has_fournisseur", $params, "UPDATE", "idlocation_client_has_fournisseur = ".$idlocation_client_has_fournisseur); 
	}
	
	function deleteLocationClientHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  location_client_has_fournisseur WHERE idlocation_client_has_fournisseur = $idlocation_client_has_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneLocationClientHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_location_client_has_fournisseur where 1 " ;
			if(!empty($idlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlIn($idlocation_client_has_fournisseur) ; }
			if(!empty($noidlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlNotIn($noidlocation_client_has_fournisseur) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
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
			if(!empty($idfournisseur)){ $sql .= " AND idfournisseur ".sqlIn($idfournisseur) ; }
			if(!empty($noidfournisseur)){ $sql .= " AND idfournisseur ".sqlNotIn($noidfournisseur) ; }
			if(!empty($commentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlIn($commentaireFournisseur) ; }
			if(!empty($nocommentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlNotIn($nocommentaireFournisseur) ; }
			if(!empty($dateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlIn($dateCreationFournisseur) ; }
			if(!empty($nodateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlNotIn($nodateCreationFournisseur) ; }
			if(!empty($idfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlIn($idfournisseur_has_type_fournisseur) ; }
			if(!empty($noidfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlNotIn($noidfournisseur_has_type_fournisseur) ; }
			if(!empty($type_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlIn($type_fournisseur_idtype_fournisseur) ; }
			if(!empty($notype_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlNotIn($notype_fournisseur_idtype_fournisseur) ; }
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlIn($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotIn($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlIn($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotIn($nonomType_fournisseur) ; }
			if(!empty($commentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlIn($commentaireType_fournisseur) ; }
			if(!empty($nocommentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlNotIn($nocommentaireType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlIn($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotIn($noordreType_fournisseur) ; }
			if(!empty($codeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlIn($codeType_fournisseur) ; }
			if(!empty($nocodeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlNotIn($nocodeType_fournisseur) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLocationClientHasFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_location_client_has_fournisseur where 1 " ;
			if(!empty($idlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlSearch($idlocation_client_has_fournisseur) ; }
			if(!empty($noidlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlNotSearch($noidlocation_client_has_fournisseur) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($idlocation_client)){ $sql .= " AND idlocation_client ".sqlSearch($idlocation_client) ; }
			if(!empty($noidlocation_client)){ $sql .= " AND idlocation_client ".sqlNotSearch($noidlocation_client) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlSearch($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotSearch($notype_location_client_idtype_location_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($numeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlSearch($numeroLocation_client) ; }
			if(!empty($nonumeroLocation_client)){ $sql .= " AND numeroLocation_client ".sqlNotSearch($nonumeroLocation_client) ; }
			if(!empty($dateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlSearch($dateCreationLocation_client) ; }
			if(!empty($nodateCreationLocation_client)){ $sql .= " AND dateCreationLocation_client ".sqlNotSearch($nodateCreationLocation_client) ; }
			if(!empty($dateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlSearch($dateDebutLocation_client) ; }
			if(!empty($nodateDebutLocation_client)){ $sql .= " AND dateDebutLocation_client ".sqlNotSearch($nodateDebutLocation_client) ; }
			if(!empty($dateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlSearch($dateFinLocation_client) ; }
			if(!empty($nodateFinLocation_client)){ $sql .= " AND dateFinLocation_client ".sqlNotSearch($nodateFinLocation_client) ; }
			if(!empty($dateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlSearch($dateClotureLocation_client) ; }
			if(!empty($nodateClotureLocation_client)){ $sql .= " AND dateClotureLocation_client ".sqlNotSearch($nodateClotureLocation_client) ; }
			if(!empty($idfournisseur)){ $sql .= " AND idfournisseur ".sqlSearch($idfournisseur) ; }
			if(!empty($noidfournisseur)){ $sql .= " AND idfournisseur ".sqlNotSearch($noidfournisseur) ; }
			if(!empty($commentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlSearch($commentaireFournisseur) ; }
			if(!empty($nocommentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlNotSearch($nocommentaireFournisseur) ; }
			if(!empty($dateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlSearch($dateCreationFournisseur) ; }
			if(!empty($nodateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlNotSearch($nodateCreationFournisseur) ; }
			if(!empty($idfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlSearch($idfournisseur_has_type_fournisseur) ; }
			if(!empty($noidfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlNotSearch($noidfournisseur_has_type_fournisseur) ; }
			if(!empty($type_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlSearch($type_fournisseur_idtype_fournisseur) ; }
			if(!empty($notype_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlNotSearch($notype_fournisseur_idtype_fournisseur) ; }
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlSearch($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotSearch($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlSearch($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotSearch($nonomType_fournisseur) ; }
			if(!empty($commentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlSearch($commentaireType_fournisseur) ; }
			if(!empty($nocommentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlNotSearch($nocommentaireType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlSearch($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotSearch($noordreType_fournisseur) ; }
			if(!empty($codeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlSearch($codeType_fournisseur) ; }
			if(!empty($nocodeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlNotSearch($nocodeType_fournisseur) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLocationClientHasFournisseur(){ 
		$sql="SELECT * FROM  location_client_has_fournisseur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLocationClientHasFournisseur($name="idlocation_client_has_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLocation_client_has_fournisseur , idlocation_client_has_fournisseur FROM location_client_has_fournisseur WHERE  1 ";  
			if(!empty($idlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlIn($idlocation_client_has_fournisseur) ; }
			if(!empty($noidlocation_client_has_fournisseur)){ $sql .= " AND idlocation_client_has_fournisseur ".sqlNotIn($noidlocation_client_has_fournisseur) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlIn($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotIn($nolocation_client_idlocation_client) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLocation_client_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLocationClientHasFournisseur($name="idlocation_client_has_fournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLocation_client_has_fournisseur , idlocation_client_has_fournisseur FROM location_client_has_fournisseur ORDER BY nomLocation_client_has_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLocationClientHasFournisseur = new LocationClientHasFournisseur(); ?>