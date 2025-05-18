<?   
class IdaeLigneLocationPersonne
{
	var $conn = null;
	function IdaeLigneLocationPersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLigneLocationPersonne($params){ 
		$this->conn->AutoExecute("vue_ligne_location_client_has_personne", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLigneLocationPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_ligne_location_client_has_personne", $params, "UPDATE", "idvue_ligne_location_client_has_personne = ".$idvue_ligne_location_client_has_personne); 
	}
	
	function deleteIdaeLigneLocationPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_ligne_location_client_has_personne WHERE idvue_ligne_location_client_has_personne = $idvue_ligne_location_client_has_personne"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLigneLocationPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_ligne_location_client_has_personne where 1 " ;
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlIn($idligne_location_client_has_personne) ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotIn($noidligne_location_client_has_personne) ; }
			if(!empty($lt_idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne < '".$lt_idligne_location_client_has_personne."'" ; }
			if(!empty($gt_idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne > '".$gt_idligne_location_client_has_personne."'" ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($lt_ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client < '".$lt_ligne_location_client_idligne_location_client."'" ; }
			if(!empty($gt_ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client > '".$gt_ligne_location_client_idligne_location_client."'" ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($lt_personne_idpersonne)){ $sql .= " AND personne_idpersonne < '".$lt_personne_idpersonne."'" ; }
			if(!empty($gt_personne_idpersonne)){ $sql .= " AND personne_idpersonne > '".$gt_personne_idpersonne."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLigneLocationPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_location_client_has_personne where 1 " ;
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlSearch($idligne_location_client_has_personne,"idligne_location_client_has_personne") ; }
			if(!empty($lt_idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne < '".$lt_idligne_location_client_has_personne."'" ; }
			if(!empty($gt_idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne > '".$gt_idligne_location_client_has_personne."'" ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotSearch($noidligne_location_client_has_personne) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlSearch($ligne_location_client_idligne_location_client,"ligne_location_client_idligne_location_client") ; }
			if(!empty($lt_ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client < '".$lt_ligne_location_client_idligne_location_client."'" ; }
			if(!empty($gt_ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client > '".$gt_ligne_location_client_idligne_location_client."'" ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotSearch($noligne_location_client_idligne_location_client) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne,"personne_idpersonne") ; }
			if(!empty($lt_personne_idpersonne)){ $sql .= " AND personne_idpersonne < '".$lt_personne_idpersonne."'" ; }
			if(!empty($gt_personne_idpersonne)){ $sql .= " AND personne_idpersonne > '".$gt_personne_idpersonne."'" ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLigneLocationPersonne(){ 
		$sql="SELECT * FROM  vue_ligne_location_client_has_personne"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLigneLocationPersonne($name="idvue_ligne_location_client_has_personne",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_ligne_location_client_has_personne , idvue_ligne_location_client_has_personne FROM vue_ligne_location_client_has_personne WHERE  1 ";  
			if(!empty($idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlIn($idligne_location_client_has_personne) ; }
			if(!empty($gt_idligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne > ".$gt_idligne_location_client_has_personne ; }
			if(!empty($noidligne_location_client_has_personne)){ $sql .= " AND idligne_location_client_has_personne ".sqlNotIn($noidligne_location_client_has_personne) ; }
			if(!empty($ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlIn($ligne_location_client_idligne_location_client) ; }
			if(!empty($gt_ligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client > ".$gt_ligne_location_client_idligne_location_client ; }
			if(!empty($noligne_location_client_idligne_location_client)){ $sql .= " AND ligne_location_client_idligne_location_client ".sqlNotIn($noligne_location_client_idligne_location_client) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($gt_personne_idpersonne)){ $sql .= " AND personne_idpersonne > ".$gt_personne_idpersonne ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_ligne_location_client_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLigneLocationPersonne($name="idvue_ligne_location_client_has_personne",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_ligne_location_client_has_personne , idvue_ligne_location_client_has_personne FROM vue_ligne_location_client_has_personne ORDER BY nomVue_ligne_location_client_has_personne ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLigneLocationPersonne = new IdaeLigneLocationPersonne(); ?>