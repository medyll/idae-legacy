<?   
class RechercheAgent
{
	var $conn = null;
	function RechercheAgent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createRechercheAgent($params){ 
		$this->conn->AutoExecute("recherche_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateRechercheAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("recherche_agent", $params, "UPDATE", "idrecherche_agent = ".$idrecherche_agent); 
	}
	
	function deleteRechercheAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  recherche_agent WHERE idrecherche_agent = $idrecherche_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneRechercheAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from recherche_agent where 1 " ;
			if(!empty($idrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlIn($idrecherche_agent) ; }
			if(!empty($noidrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlNotIn($noidrecherche_agent) ; }
			if(!empty($lt_idrecherche_agent)){ $sql .= " AND idrecherche_agent < '".$lt_idrecherche_agent."'" ; }
			if(!empty($gt_idrecherche_agent)){ $sql .= " AND idrecherche_agent > '".$gt_idrecherche_agent."'" ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($dateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlIn($dateRecherche_agent) ; }
			if(!empty($nodateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlNotIn($nodateRecherche_agent) ; }
			if(!empty($lt_dateRecherche_agent)){ $sql .= " AND dateRecherche_agent < '".$lt_dateRecherche_agent."'" ; }
			if(!empty($gt_dateRecherche_agent)){ $sql .= " AND dateRecherche_agent > '".$gt_dateRecherche_agent."'" ; }
			if(!empty($objetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlIn($objetRechercheAgent) ; }
			if(!empty($noobjetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlNotIn($noobjetRechercheAgent) ; }
			if(!empty($lt_objetRechercheAgent)){ $sql .= " AND objetRechercheAgent < '".$lt_objetRechercheAgent."'" ; }
			if(!empty($gt_objetRechercheAgent)){ $sql .= " AND objetRechercheAgent > '".$gt_objetRechercheAgent."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneRechercheAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from recherche_agent where 1 " ;
			if(!empty($idrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlSearch($idrecherche_agent,"idrecherche_agent") ; }
			if(!empty($lt_idrecherche_agent)){ $sql .= " AND idrecherche_agent < '".$lt_idrecherche_agent."'" ; }
			if(!empty($gt_idrecherche_agent)){ $sql .= " AND idrecherche_agent > '".$gt_idrecherche_agent."'" ; }
			if(!empty($noidrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlNotSearch($noidrecherche_agent) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent,"agent_idagent") ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($dateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlSearch($dateRecherche_agent,"dateRecherche_agent") ; }
			if(!empty($lt_dateRecherche_agent)){ $sql .= " AND dateRecherche_agent < '".$lt_dateRecherche_agent."'" ; }
			if(!empty($gt_dateRecherche_agent)){ $sql .= " AND dateRecherche_agent > '".$gt_dateRecherche_agent."'" ; }
			if(!empty($nodateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlNotSearch($nodateRecherche_agent) ; }
			if(!empty($objetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlSearch($objetRechercheAgent,"objetRechercheAgent") ; }
			if(!empty($lt_objetRechercheAgent)){ $sql .= " AND objetRechercheAgent < '".$lt_objetRechercheAgent."'" ; }
			if(!empty($gt_objetRechercheAgent)){ $sql .= " AND objetRechercheAgent > '".$gt_objetRechercheAgent."'" ; }
			if(!empty($noobjetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlNotSearch($noobjetRechercheAgent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllRechercheAgent(){ 
		$sql="SELECT * FROM  recherche_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneRechercheAgent($name="idrecherche_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomRecherche_agent , idrecherche_agent FROM recherche_agent WHERE  1 ";  
			if(!empty($idrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlIn($idrecherche_agent) ; }
			if(!empty($gt_idrecherche_agent)){ $sql .= " AND idrecherche_agent > ".$gt_idrecherche_agent ; }
			if(!empty($noidrecherche_agent)){ $sql .= " AND idrecherche_agent ".sqlNotIn($noidrecherche_agent) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > ".$gt_agent_idagent ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlIn($dateRecherche_agent) ; }
			if(!empty($gt_dateRecherche_agent)){ $sql .= " AND dateRecherche_agent > ".$gt_dateRecherche_agent ; }
			if(!empty($nodateRecherche_agent)){ $sql .= " AND dateRecherche_agent ".sqlNotIn($nodateRecherche_agent) ; }
			if(!empty($objetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlIn($objetRechercheAgent) ; }
			if(!empty($gt_objetRechercheAgent)){ $sql .= " AND objetRechercheAgent > ".$gt_objetRechercheAgent ; }
			if(!empty($noobjetRechercheAgent)){ $sql .= " AND objetRechercheAgent ".sqlNotIn($noobjetRechercheAgent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomRecherche_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectRechercheAgent($name="idrecherche_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomRecherche_agent , idrecherche_agent FROM recherche_agent ORDER BY nomRecherche_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassRechercheAgent = new RechercheAgent(); ?>