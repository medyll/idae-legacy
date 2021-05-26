<?   
class ListeClient
{
	var $conn = null;
	function ListeClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createListeClient($params){ 
		$this->conn->AutoExecute("liste_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateListeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("liste_client", $params, "UPDATE", "idliste_client = ".$idliste_client); 
	}
	
	function deleteListeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  liste_client WHERE idliste_client = $idliste_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneListeClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from liste_client where 1 " ;
			if(!empty($idliste_client)){ $sql .= " AND idliste_client ".sqlIn($idliste_client) ; }
			if(!empty($noidliste_client)){ $sql .= " AND idliste_client ".sqlNotIn($noidliste_client) ; }
			if(!empty($nomListe_client)){ $sql .= " AND nomListe_client ".sqlIn($nomListe_client) ; }
			if(!empty($nonomListe_client)){ $sql .= " AND nomListe_client ".sqlNotIn($nonomListe_client) ; }
			if(!empty($ordreListe_client)){ $sql .= " AND ordreListe_client ".sqlIn($ordreListe_client) ; }
			if(!empty($noordreListe_client)){ $sql .= " AND ordreListe_client ".sqlNotIn($noordreListe_client) ; }
			if(!empty($dateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlIn($dateCreationListe_client) ; }
			if(!empty($nodateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlNotIn($nodateCreationListe_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneListeClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from liste_client where 1 " ;
			if(!empty($idliste_client)){ $sql .= " AND idliste_client ".sqlSearch($idliste_client) ; }
			if(!empty($noidliste_client)){ $sql .= " AND idliste_client ".sqlNotSearch($noidliste_client) ; }
			if(!empty($nomListe_client)){ $sql .= " AND nomListe_client ".sqlSearch($nomListe_client) ; }
			if(!empty($nonomListe_client)){ $sql .= " AND nomListe_client ".sqlNotSearch($nonomListe_client) ; }
			if(!empty($ordreListe_client)){ $sql .= " AND ordreListe_client ".sqlSearch($ordreListe_client) ; }
			if(!empty($noordreListe_client)){ $sql .= " AND ordreListe_client ".sqlNotSearch($noordreListe_client) ; }
			if(!empty($dateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlSearch($dateCreationListe_client) ; }
			if(!empty($nodateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlNotSearch($nodateCreationListe_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllListeClient(){ 
		$sql="SELECT * FROM  liste_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneListeClient($name="idliste_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomListe_client , idliste_client FROM liste_client WHERE  1 ";  
			if(!empty($idliste_client)){ $sql .= " AND idliste_client ".sqlIn($idliste_client) ; }
			if(!empty($noidliste_client)){ $sql .= " AND idliste_client ".sqlNotIn($noidliste_client) ; }
			if(!empty($nomListe_client)){ $sql .= " AND nomListe_client ".sqlIn($nomListe_client) ; }
			if(!empty($nonomListe_client)){ $sql .= " AND nomListe_client ".sqlNotIn($nonomListe_client) ; }
			if(!empty($ordreListe_client)){ $sql .= " AND ordreListe_client ".sqlIn($ordreListe_client) ; }
			if(!empty($noordreListe_client)){ $sql .= " AND ordreListe_client ".sqlNotIn($noordreListe_client) ; }
			if(!empty($dateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlIn($dateCreationListe_client) ; }
			if(!empty($nodateCreationListe_client)){ $sql .= " AND dateCreationListe_client ".sqlNotIn($nodateCreationListe_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomListe_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectListeClient($name="idliste_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomListe_client , idliste_client FROM liste_client ORDER BY nomListe_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassListeClient = new ListeClient(); ?>