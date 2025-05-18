<?   
class IdaeLigneLocationClient
{
	var $conn = null;
	function IdaeLigneLocationClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLigneLocationClient($params){ 
		$this->conn->AutoExecute("vue_ligne_location_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_ligne_location_client", $params, "UPDATE", "idvue_ligne_location_client = ".$idvue_ligne_location_client); 
	}
	
	function deleteIdaeLigneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_ligne_location_client WHERE idvue_ligne_location_client = $idvue_ligne_location_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLigneLocationClient($params){
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
	function searchOneIdaeLigneLocationClient($params){
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
	function getAllIdaeLigneLocationClient(){ 
		$sql="SELECT * FROM  vue_ligne_location_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLigneLocationClient($name="idvue_ligne_location_client",$id="",$allowEmpty= false,$params)
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
	function getSelectIdaeLigneLocationClient($name="idvue_ligne_location_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_ligne_location_client , idvue_ligne_location_client FROM vue_ligne_location_client ORDER BY nomVue_ligne_location_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLigneLocationClient = new IdaeLigneLocationClient(); ?>