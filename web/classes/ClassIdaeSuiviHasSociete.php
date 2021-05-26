<?   
class IdaeSuiviHasSociete
{
	var $conn = null;
	function IdaeSuiviHasSociete(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeSuiviHasSociete($params){ 
		$this->conn->AutoExecute("vue_client_suivi", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_client_suivi", $params, "UPDATE", "idvue_client_suivi = ".$idvue_client_suivi); 
	}
	
	function deleteIdaeSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_client_suivi WHERE idvue_client_suivi = $idvue_client_suivi"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_client_suivi where 1 " ;
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlIn($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotIn($nocreerLe) ; }
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlIn($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotIn($nocommentaire) ; }
			if(!empty($objet)){ $sql .= " AND objet ".sqlIn($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotIn($noobjet) ; }
			if(!empty($dateFin)){ $sql .= " AND dateFin ".sqlIn($dateFin) ; }
			if(!empty($nodateFin)){ $sql .= " AND dateFin ".sqlNotIn($nodateFin) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlIn($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotIn($nodateDebut) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($conseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlIn($conseillers_idConseiller) ; }
			if(!empty($noconseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlNotIn($noconseillers_idConseiller) ; }
			if(!empty($contact)){ $sql .= " AND contact ".sqlIn($contact) ; }
			if(!empty($nocontact)){ $sql .= " AND contact ".sqlNotIn($nocontact) ; }
			if(!empty($prospect)){ $sql .= " AND prospect ".sqlIn($prospect) ; }
			if(!empty($noprospect)){ $sql .= " AND prospect ".sqlNotIn($noprospect) ; }
			if(!empty($client)){ $sql .= " AND client ".sqlIn($client) ; }
			if(!empty($noclient)){ $sql .= " AND client ".sqlNotIn($noclient) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($typeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlIn($typeSuivi_idtypeSuivi) ; }
			if(!empty($notypeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlNotIn($notypeSuivi_idtypeSuivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_client_suivi where 1 " ;
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlSearch($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotSearch($nosuivi_idsuivi) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlSearch($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotSearch($nocreerLe) ; }
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlSearch($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotSearch($nocommentaire) ; }

			if(!empty($objet)){ $sql .= " AND objet ".sqlSearch($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotSearch($noobjet) ; }
			if(!empty($dateFin)){ $sql .= " AND dateFin ".sqlSearch($dateFin) ; }
			if(!empty($nodateFin)){ $sql .= " AND dateFin ".sqlNotSearch($nodateFin) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlSearch($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotSearch($nodateDebut) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlSearch($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotSearch($noidclient) ; }
			if(!empty($conseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlSearch($conseillers_idConseiller) ; }
			if(!empty($noconseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlNotSearch($noconseillers_idConseiller) ; }
			if(!empty($contact)){ $sql .= " AND contact ".sqlSearch($contact) ; }
			if(!empty($nocontact)){ $sql .= " AND contact ".sqlNotSearch($nocontact) ; }
			if(!empty($prospect)){ $sql .= " AND prospect ".sqlSearch($prospect) ; }
			if(!empty($noprospect)){ $sql .= " AND prospect ".sqlNotSearch($noprospect) ; }
			if(!empty($client)){ $sql .= " AND client ".sqlSearch($client) ; }
			if(!empty($noclient)){ $sql .= " AND client ".sqlNotSearch($noclient) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($typeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlSearch($typeSuivi_idtypeSuivi) ; }
			if(!empty($notypeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlNotSearch($notypeSuivi_idtypeSuivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeSuiviHasSociete(){ 
		$sql="SELECT * FROM  vue_client_suivi"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeSuiviHasSociete($name="idvue_client_suivi",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_client_suivi , idvue_client_suivi FROM vue_client_suivi WHERE  1 ";  
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($creerLe)){ $sql .= " AND creerLe ".sqlIn($creerLe) ; }
			if(!empty($nocreerLe)){ $sql .= " AND creerLe ".sqlNotIn($nocreerLe) ; }
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlIn($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotIn($nocommentaire) ; }
			if(!empty($objet)){ $sql .= " AND objet ".sqlIn($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotIn($noobjet) ; }
			if(!empty($dateFin)){ $sql .= " AND dateFin ".sqlIn($dateFin) ; }
			if(!empty($nodateFin)){ $sql .= " AND dateFin ".sqlNotIn($nodateFin) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlIn($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotIn($nodateDebut) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($conseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlIn($conseillers_idConseiller) ; }
			if(!empty($noconseillers_idConseiller)){ $sql .= " AND conseillers_idConseiller ".sqlNotIn($noconseillers_idConseiller) ; }
			if(!empty($contact)){ $sql .= " AND contact ".sqlIn($contact) ; }
			if(!empty($nocontact)){ $sql .= " AND contact ".sqlNotIn($nocontact) ; }
			if(!empty($prospect)){ $sql .= " AND prospect ".sqlIn($prospect) ; }
			if(!empty($noprospect)){ $sql .= " AND prospect ".sqlNotIn($noprospect) ; }
			if(!empty($client)){ $sql .= " AND client ".sqlIn($client) ; }
			if(!empty($noclient)){ $sql .= " AND client ".sqlNotIn($noclient) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($typeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlIn($typeSuivi_idtypeSuivi) ; }
			if(!empty($notypeSuivi_idtypeSuivi)){ $sql .= " AND typeSuivi_idtypeSuivi ".sqlNotIn($notypeSuivi_idtypeSuivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_client_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeSuiviHasSociete($name="idvue_client_suivi",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_client_suivi , idvue_client_suivi FROM vue_client_suivi ORDER BY nomVue_client_suivi ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeSuiviHasSociete = new IdaeSuiviHasSociete(); ?>