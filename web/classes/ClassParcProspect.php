<?   
class ParcProspect
{
	var $conn = null;
	function ParcProspect(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createParcProspect($params){ 
		$this->conn->AutoExecute("parc_prospect", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("parc_prospect", $params, "UPDATE", "idparc_prospect = ".$idparc_prospect); 
	}
	
	function deleteParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  parc_prospect WHERE idparc_prospect = $idparc_prospect"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from parc_prospect where 1 " ;
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlIn($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotIn($noidparc_prospect) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from parc_prospect where 1 " ;
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlSearch($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotSearch($noidparc_prospect) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlSearch($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotSearch($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlSearch($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotSearch($nodureeEnTrim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllParcProspect(){ 
		$sql="SELECT * FROM  parc_prospect"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneParcProspect($name="idparc_prospect",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomParc_prospect , idparc_prospect FROM parc_prospect WHERE  1 ";  
			if(!empty($idparc_prospect)){ $sql .= " AND idparc_prospect ".sqlIn($idparc_prospect) ; }
			if(!empty($noidparc_prospect)){ $sql .= " AND idparc_prospect ".sqlNotIn($noidparc_prospect) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlIn($dureeEnTrim) ; }
			if(!empty($nodureeEnTrim)){ $sql .= " AND dureeEnTrim ".sqlNotIn($nodureeEnTrim) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomParc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectParcProspect($name="idparc_prospect",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomParc_prospect , idparc_prospect FROM parc_prospect ORDER BY nomParc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassParcProspect = new ParcProspect(); ?>