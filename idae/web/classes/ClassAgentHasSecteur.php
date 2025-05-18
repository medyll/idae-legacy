<?   
class AgentHasSecteur
{
	var $conn = null;
	function AgentHasSecteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAgentHasSecteur($params){ 
		$this->conn->AutoExecute("agent_has_secteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAgentHasSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_has_secteur", $params, "UPDATE", "idagent_has_secteur = ".$idagent_has_secteur); 
	}
	
	function deleteAgentHasSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent_has_secteur WHERE idagent_has_secteur = $idagent_has_secteur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneAgentHasSecteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_agent_has_secteur where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlIn($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotIn($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlIn($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotIn($nopasswordAgent) ; }
			if(!empty($dateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlIn($dateCreationAgent) ; }
			if(!empty($nodateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlNotIn($nodateCreationAgent) ; }
			if(!empty($dateFinAgent)){ $sql .= " AND dateFinAgent ".sqlIn($dateFinAgent) ; }
			if(!empty($nodateFinAgent)){ $sql .= " AND dateFinAgent ".sqlNotIn($nodateFinAgent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlIn($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotIn($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlIn($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotIn($nodateDebutAgent) ; }
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlIn($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotIn($noidagent_has_secteur) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($dateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlIn($dateCreationAgent_has_secteur) ; }
			if(!empty($nodateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlNotIn($nodateCreationAgent_has_secteur) ; }
			if(!empty($dateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlIn($dateFinAgent_has_secteur) ; }
			if(!empty($nodateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlNotIn($nodateFinAgent_has_secteur) ; }
			if(!empty($principaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlIn($principaleAgent_has_secteur) ; }
			if(!empty($noprincipaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlNotIn($noprincipaleAgent_has_secteur) ; }
			if(!empty($ordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlIn($ordreAgent_has_secteur) ; }
			if(!empty($noordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlNotIn($noordreAgent_has_secteur) ; }
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlIn($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotIn($noidsecteur) ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlIn($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotIn($nonomSecteur) ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlIn($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotIn($nocommentaireSecteur) ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlIn($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotIn($nodateCreationSecteur) ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlIn($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotIn($nodateDebutSecteur) ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlIn($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotIn($nodateFinSecteur) ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlIn($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotIn($nodateClotureSecteur) ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlIn($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotIn($noestActifSecteur) ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlIn($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotIn($noordreSecteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAgentHasSecteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_agent_has_secteur where 1 " ;
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($loginAgent)){ $sql .= " AND loginAgent ".sqlSearch($loginAgent) ; }
			if(!empty($nologinAgent)){ $sql .= " AND loginAgent ".sqlNotSearch($nologinAgent) ; }
			if(!empty($passwordAgent)){ $sql .= " AND passwordAgent ".sqlSearch($passwordAgent) ; }
			if(!empty($nopasswordAgent)){ $sql .= " AND passwordAgent ".sqlNotSearch($nopasswordAgent) ; }
			if(!empty($dateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlSearch($dateCreationAgent) ; }
			if(!empty($nodateCreationAgent)){ $sql .= " AND dateCreationAgent ".sqlNotSearch($nodateCreationAgent) ; }
			if(!empty($dateFinAgent)){ $sql .= " AND dateFinAgent ".sqlSearch($dateFinAgent) ; }
			if(!empty($nodateFinAgent)){ $sql .= " AND dateFinAgent ".sqlNotSearch($nodateFinAgent) ; }
			if(!empty($estActifAgent)){ $sql .= " AND estActifAgent ".sqlSearch($estActifAgent) ; }
			if(!empty($noestActifAgent)){ $sql .= " AND estActifAgent ".sqlNotSearch($noestActifAgent) ; }
			if(!empty($dateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlSearch($dateDebutAgent) ; }
			if(!empty($nodateDebutAgent)){ $sql .= " AND dateDebutAgent ".sqlNotSearch($nodateDebutAgent) ; }
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlSearch($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotSearch($noidagent_has_secteur) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlSearch($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotSearch($nosecteur_idsecteur) ; }
			if(!empty($dateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlSearch($dateCreationAgent_has_secteur) ; }
			if(!empty($nodateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlNotSearch($nodateCreationAgent_has_secteur) ; }
			if(!empty($dateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlSearch($dateFinAgent_has_secteur) ; }
			if(!empty($nodateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlNotSearch($nodateFinAgent_has_secteur) ; }
			if(!empty($principaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlSearch($principaleAgent_has_secteur) ; }
			if(!empty($noprincipaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlNotSearch($noprincipaleAgent_has_secteur) ; }
			if(!empty($ordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlSearch($ordreAgent_has_secteur) ; }
			if(!empty($noordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlNotSearch($noordreAgent_has_secteur) ; }
			if(!empty($idsecteur)){ $sql .= " AND idsecteur ".sqlSearch($idsecteur) ; }
			if(!empty($noidsecteur)){ $sql .= " AND idsecteur ".sqlNotSearch($noidsecteur) ; }
			if(!empty($nomSecteur)){ $sql .= " AND nomSecteur ".sqlSearch($nomSecteur) ; }
			if(!empty($nonomSecteur)){ $sql .= " AND nomSecteur ".sqlNotSearch($nonomSecteur) ; }
			if(!empty($commentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlSearch($commentaireSecteur) ; }
			if(!empty($nocommentaireSecteur)){ $sql .= " AND commentaireSecteur ".sqlNotSearch($nocommentaireSecteur) ; }
			if(!empty($dateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlSearch($dateCreationSecteur) ; }
			if(!empty($nodateCreationSecteur)){ $sql .= " AND dateCreationSecteur ".sqlNotSearch($nodateCreationSecteur) ; }
			if(!empty($dateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlSearch($dateDebutSecteur) ; }
			if(!empty($nodateDebutSecteur)){ $sql .= " AND dateDebutSecteur ".sqlNotSearch($nodateDebutSecteur) ; }
			if(!empty($dateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlSearch($dateFinSecteur) ; }
			if(!empty($nodateFinSecteur)){ $sql .= " AND dateFinSecteur ".sqlNotSearch($nodateFinSecteur) ; }
			if(!empty($dateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlSearch($dateClotureSecteur) ; }
			if(!empty($nodateClotureSecteur)){ $sql .= " AND dateClotureSecteur ".sqlNotSearch($nodateClotureSecteur) ; }
			if(!empty($estActifSecteur)){ $sql .= " AND estActifSecteur ".sqlSearch($estActifSecteur) ; }
			if(!empty($noestActifSecteur)){ $sql .= " AND estActifSecteur ".sqlNotSearch($noestActifSecteur) ; }
			if(!empty($ordreSecteur)){ $sql .= " AND ordreSecteur ".sqlSearch($ordreSecteur) ; }
			if(!empty($noordreSecteur)){ $sql .= " AND ordreSecteur ".sqlNotSearch($noordreSecteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAgentHasSecteur(){ 
		$sql="SELECT * FROM  vue_agent_has_secteur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAgentHasSecteur($name="idagent_has_secteur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAgent_has_secteur , idagent_has_secteur FROM agent_has_secteur WHERE  1 ";  
			if(!empty($idagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlIn($idagent_has_secteur) ; }
			if(!empty($noidagent_has_secteur)){ $sql .= " AND idagent_has_secteur ".sqlNotIn($noidagent_has_secteur) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($secteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlIn($secteur_idsecteur) ; }
			if(!empty($nosecteur_idsecteur)){ $sql .= " AND secteur_idsecteur ".sqlNotIn($nosecteur_idsecteur) ; }
			if(!empty($dateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlIn($dateCreationAgent_has_secteur) ; }
			if(!empty($nodateCreationAgent_has_secteur)){ $sql .= " AND dateCreationAgent_has_secteur ".sqlNotIn($nodateCreationAgent_has_secteur) ; }
			if(!empty($dateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlIn($dateFinAgent_has_secteur) ; }
			if(!empty($nodateFinAgent_has_secteur)){ $sql .= " AND dateFinAgent_has_secteur ".sqlNotIn($nodateFinAgent_has_secteur) ; }
			if(!empty($principaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlIn($principaleAgent_has_secteur) ; }
			if(!empty($noprincipaleAgent_has_secteur)){ $sql .= " AND principaleAgent_has_secteur ".sqlNotIn($noprincipaleAgent_has_secteur) ; }
			if(!empty($ordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlIn($ordreAgent_has_secteur) ; }
			if(!empty($noordreAgent_has_secteur)){ $sql .= " AND ordreAgent_has_secteur ".sqlNotIn($noordreAgent_has_secteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAgent_has_secteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAgentHasSecteur($name="idagent_has_secteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAgent_has_secteur , idagent_has_secteur FROM agent_has_secteur ORDER BY nomAgent_has_secteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAgentHasSecteur = new AgentHasSecteur(); ?>