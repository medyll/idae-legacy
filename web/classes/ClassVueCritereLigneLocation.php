<?   
class VueCritereLigneLocation
{
	var $conn = null;
	function VueCritereLigneLocation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createVueCritereLigneLocation($params){ 
		$this->conn->AutoExecute("ligne_location_client_has_critere_ligne_location", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateVueCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_location_client_has_critere_ligne_location", $params, "UPDATE", "idligne_location_client_has_critere_ligne_location = ".$idligne_location_client_has_critere_ligne_location); 
	}
	
	function deleteVueCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_location_client_has_critere_ligne_location WHERE idligne_location_client_has_critere_ligne_location = $idligne_location_client_has_critere_ligne_location"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneVueCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_critere_ligne_location where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
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
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlIn($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotIn($noidcritere_ligne_location) ; }
			if(!empty($idligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlIn($idligne_location_client_has_critere_ligne_location) ; }
			if(!empty($noidligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlNotIn($noidligne_location_client_has_critere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneVueCritereLigneLocation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_critere_ligne_location where 1 " ;
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlSearch($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotSearch($noidligne_location_client) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($location_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlSearch($location_client_idlocation_client) ; }
			if(!empty($nolocation_client_idlocation_client)){ $sql .= " AND location_client_idlocation_client ".sqlNotSearch($nolocation_client_idlocation_client) ; }
			if(!empty($dateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlSearch($dateCreationLigne_location_client) ; }
			if(!empty($nodateCreationLigne_location_client)){ $sql .= " AND dateCreationLigne_location_client ".sqlNotSearch($nodateCreationLigne_location_client) ; }
			if(!empty($dateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlSearch($dateDebutLigne_location_client) ; }
			if(!empty($nodateDebutLigne_location_client)){ $sql .= " AND dateDebutLigne_location_client ".sqlNotSearch($nodateDebutLigne_location_client) ; }
			if(!empty($dateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlSearch($dateClotureLigne_location_client) ; }
			if(!empty($nodateClotureLigne_location_client)){ $sql .= " AND dateClotureLigne_location_client ".sqlNotSearch($nodateClotureLigne_location_client) ; }
			if(!empty($dateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlSearch($dateFinLigne_location_client) ; }
			if(!empty($nodateFinLigne_location_client)){ $sql .= " AND dateFinLigne_location_client ".sqlNotSearch($nodateFinLigne_location_client) ; }
			if(!empty($dureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlSearch($dureeLigne_location_client) ; }
			if(!empty($nodureeLigne_location_client)){ $sql .= " AND dureeLigne_location_client ".sqlNotSearch($nodureeLigne_location_client) ; }
			if(!empty($uniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlSearch($uniteDureeLigne_location_client) ; }
			if(!empty($nouniteDureeLigne_location_client)){ $sql .= " AND uniteDureeLigne_location_client ".sqlNotSearch($nouniteDureeLigne_location_client) ; }
			if(!empty($pvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlSearch($pvhtLigne_location_client) ; }
			if(!empty($nopvhtLigne_location_client)){ $sql .= " AND pvhtLigne_location_client ".sqlNotSearch($nopvhtLigne_location_client) ; }
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlSearch($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotSearch($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlSearch($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotSearch($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlSearch($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotSearch($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlSearch($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotSearch($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlSearch($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotSearch($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlSearch($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotSearch($noordreCritere_ligne_location) ; }
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlSearch($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotSearch($noidcritere_ligne_location) ; }
			if(!empty($idligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlSearch($idligne_location_client_has_critere_ligne_location) ; }
			if(!empty($noidligne_location_client_has_critere_ligne_location)){ $sql .= " AND idligne_location_client_has_critere_ligne_location ".sqlNotSearch($noidligne_location_client_has_critere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllVueCritereLigneLocation(){ 
		$sql="SELECT * FROM  vue_critere_ligne_location"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneVueCritereLigneLocation($name="idvue_critere_ligne_location",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_critere_ligne_location , idvue_critere_ligne_location FROM vue_critere_ligne_location WHERE  1 ";  
			if(!empty($idligne_location_client)){ $sql .= " AND idligne_location_client ".sqlIn($idligne_location_client) ; }
			if(!empty($noidligne_location_client)){ $sql .= " AND idligne_location_client ".sqlNotIn($noidligne_location_client) ; }
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
			if(!empty($valeurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlIn($valeurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($novaleurLigne_location_client_has_critere_ligne_location)){ $sql .= " AND valeurLigne_location_client_has_critere_ligne_location ".sqlNotIn($novaleurLigne_location_client_has_critere_ligne_location) ; }
			if(!empty($type_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlIn($type_location_client_idtype_location_client) ; }
			if(!empty($notype_location_client_idtype_location_client)){ $sql .= " AND type_location_client_idtype_location_client ".sqlNotIn($notype_location_client_idtype_location_client) ; }
			if(!empty($nomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlIn($nomCritere_ligne_location) ; }
			if(!empty($nonomCritere_ligne_location)){ $sql .= " AND nomCritere_ligne_location ".sqlNotIn($nonomCritere_ligne_location) ; }
			if(!empty($uniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlIn($uniteCritere_ligne_location) ; }
			if(!empty($nouniteCritere_ligne_location)){ $sql .= " AND uniteCritere_ligne_location ".sqlNotIn($nouniteCritere_ligne_location) ; }
			if(!empty($codeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlIn($codeCritere_ligne_location) ; }
			if(!empty($nocodeCritere_ligne_location)){ $sql .= " AND codeCritere_ligne_location ".sqlNotIn($nocodeCritere_ligne_location) ; }
			if(!empty($ordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlIn($ordreCritere_ligne_location) ; }
			if(!empty($noordreCritere_ligne_location)){ $sql .= " AND ordreCritere_ligne_location ".sqlNotIn($noordreCritere_ligne_location) ; }
			if(!empty($idcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlIn($idcritere_ligne_location) ; }
			if(!empty($noidcritere_ligne_location)){ $sql .= " AND idcritere_ligne_location ".sqlNotIn($noidcritere_ligne_location) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_critere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectVueCritereLigneLocation($name="idvue_critere_ligne_location",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_critere_ligne_location , idvue_critere_ligne_location FROM vue_critere_ligne_location ORDER BY nomVue_critere_ligne_location ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassVueCritereLigneLocation = new VueCritereLigneLocation(); ?>