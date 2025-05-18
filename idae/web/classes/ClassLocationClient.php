<?   
class LocationClient
{
	var $conn = null;
	function LocationClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLocationClient($params){ 
		$this->conn->AutoExecute("location_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("location_client", $params, "UPDATE", "idlocation_client = ".$idlocation_client); 
	}
	
	function deleteLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  location_client WHERE idlocation_client = $idlocation_client"; 
		return $this->conn->Execute($sql); 	
	}
	function install1(){ 
		$sql="INSERT INTO `type_location_client` VALUES ('1', 'Location', null, null, 'LOC');"; 
		$this->conn->Execute($sql); 	
		$sql="INSERT INTO `type_location_client` VALUES ('2', 'Vente', null, null, 'VTE');"; 
		return $this->conn->Execute($sql); 	
	}
	function install2(){ 
		//$sql="UPDATE location_client SET type_location_client_idtype_location_client = 1;"; 
		//return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="TRUNCATE TABLE location_client"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_location_client where 1 " ;
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
	function searchOneLocationClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from location_client where 1 " ;
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLocationClient(){ 
		$sql="SELECT * FROM  location_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLocationClient($name="idlocation_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT numeroLocation_client , idlocation_client FROM location_client WHERE  1 ";  
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
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY numeroLocation_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLocationClient($name="idlocation_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLocation_client , idlocation_client FROM location_client ORDER BY nomLocation_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLocationClient = new LocationClient(); ?>