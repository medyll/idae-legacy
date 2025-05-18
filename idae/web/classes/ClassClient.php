<?   
class Client
{
	var $conn = null;
	function Client(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createClient($params){ 
		$this->conn->AutoExecute("client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("client", $params, "UPDATE", "idclient = ".$idclient); 
	}
	
	function deleteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  client WHERE idclient = $idclient"; 
		return $this->conn->Execute($sql); 	
	} 
	function getOneClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;

		$sql="select ".$all." from vue_client where 1 " ;
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($lt_idclient)){ $sql .= " AND idclient < '".$lt_idclient."'" ; }
			if(!empty($gt_idclient)){ $sql .= " AND idclient > '".$gt_idclient."'" ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; }
			if(!empty($lt_numeroClient)){ $sql .= " AND numeroClient < '".$lt_numeroClient."'" ; }
			if(!empty($gt_numeroClient)){ $sql .= " AND numeroClient > '".$gt_numeroClient."'" ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; }
			if(!empty($lt_dateCreationClient)){ $sql .= " AND dateCreationClient < '".$lt_dateCreationClient."'" ; }
			if(!empty($gt_dateCreationClient)){ $sql .= " AND dateCreationClient > '".$gt_dateCreationClient."'" ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; }
			if(!empty($lt_estProspectClient)){ $sql .= " AND estProspectClient < '".$lt_estProspectClient."'" ; }
			if(!empty($gt_estProspectClient)){ $sql .= " AND estProspectClient > '".$gt_estProspectClient."'" ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; }
			if(!empty($lt_estClientClient)){ $sql .= " AND estClientClient < '".$lt_estClientClient."'" ; }
			if(!empty($gt_estClientClient)){ $sql .= " AND estClientClient > '".$gt_estClientClient."'" ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; }
			if(!empty($lt_estSuspectClient)){ $sql .= " AND estSuspectClient < '".$lt_estSuspectClient."'" ; }
			if(!empty($gt_estSuspectClient)){ $sql .= " AND estSuspectClient > '".$gt_estSuspectClient."'" ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlIn($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotIn($nocommentaireClient) ; }
			if(!empty($lt_commentaireClient)){ $sql .= " AND commentaireClient < '".$lt_commentaireClient."'" ; }
			if(!empty($gt_commentaireClient)){ $sql .= " AND commentaireClient > '".$gt_commentaireClient."'" ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($lt_agent_idagent)){ $sql .= " AND agent_idagent < '".$lt_agent_idagent."'" ; }
			if(!empty($gt_agent_idagent)){ $sql .= " AND agent_idagent > '".$gt_agent_idagent."'" ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($lt_societe_idsociete)){ $sql .= " AND societe_idsociete < '".$lt_societe_idsociete."'" ; }
			if(!empty($gt_societe_idsociete)){ $sql .= " AND societe_idsociete > '".$gt_societe_idsociete."'" ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($lt_idsociete)){ $sql .= " AND idsociete < '".$lt_idsociete."'" ; }
			if(!empty($gt_idsociete)){ $sql .= " AND idsociete > '".$gt_idsociete."'" ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlIn($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotIn($nosiretSociete) ; }
			if(!empty($lt_siretSociete)){ $sql .= " AND siretSociete < '".$lt_siretSociete."'" ; }
			if(!empty($gt_siretSociete)){ $sql .= " AND siretSociete > '".$gt_siretSociete."'" ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlIn($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotIn($nosirenSociete) ; }
			if(!empty($lt_sirenSociete)){ $sql .= " AND sirenSociete < '".$lt_sirenSociete."'" ; }
			if(!empty($gt_sirenSociete)){ $sql .= " AND sirenSociete > '".$gt_sirenSociete."'" ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlIn($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotIn($noapeSociete) ; }
			if(!empty($lt_apeSociete)){ $sql .= " AND apeSociete < '".$lt_apeSociete."'" ; }
			if(!empty($gt_apeSociete)){ $sql .= " AND apeSociete > '".$gt_apeSociete."'" ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlIn($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotIn($notvaIntraSociete) ; }
			if(!empty($lt_tvaIntraSociete)){ $sql .= " AND tvaIntraSociete < '".$lt_tvaIntraSociete."'" ; }
			if(!empty($gt_tvaIntraSociete)){ $sql .= " AND tvaIntraSociete > '".$gt_tvaIntraSociete."'" ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlIn($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotIn($noformeJuridiqueSociete) ; }
			if(!empty($lt_formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete < '".$lt_formeJuridiqueSociete."'" ; }
			if(!empty($gt_formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete > '".$gt_formeJuridiqueSociete."'" ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlIn($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotIn($nocapitalSociete) ; }
			if(!empty($lt_capitalSociete)){ $sql .= " AND capitalSociete < '".$lt_capitalSociete."'" ; }
			if(!empty($gt_capitalSociete)){ $sql .= " AND capitalSociete > '".$gt_capitalSociete."'" ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlIn($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotIn($nodeviseCapitalSociete) ; }
			if(!empty($lt_deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete < '".$lt_deviseCapitalSociete."'" ; }
			if(!empty($gt_deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete > '".$gt_deviseCapitalSociete."'" ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlIn($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotIn($nodeviseFacturationSociete) ; }
			if(!empty($lt_deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete < '".$lt_deviseFacturationSociete."'" ; }
			if(!empty($gt_deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete > '".$gt_deviseFacturationSociete."'" ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlIn($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotIn($noactiviteSociete) ; }
			if(!empty($lt_activiteSociete)){ $sql .= " AND activiteSociete < '".$lt_activiteSociete."'" ; }
			if(!empty($gt_activiteSociete)){ $sql .= " AND activiteSociete > '".$gt_activiteSociete."'" ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlIn($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotIn($nochiffreAffaireSociete) ; }
			if(!empty($lt_chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete < '".$lt_chiffreAffaireSociete."'" ; }
			if(!empty($gt_chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete > '".$gt_chiffreAffaireSociete."'" ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlIn($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotIn($nonbSalarieSociete) ; }
			if(!empty($lt_nbSalarieSociete)){ $sql .= " AND nbSalarieSociete < '".$lt_nbSalarieSociete."'" ; }
			if(!empty($gt_nbSalarieSociete)){ $sql .= " AND nbSalarieSociete > '".$gt_nbSalarieSociete."'" ; }
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlIn($beneficeSocitete) ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotIn($nobeneficeSocitete) ; }
			if(!empty($lt_beneficeSocitete)){ $sql .= " AND beneficeSocitete < '".$lt_beneficeSocitete."'" ; }
			if(!empty($gt_beneficeSocitete)){ $sql .= " AND beneficeSocitete > '".$gt_beneficeSocitete."'" ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlIn($estFacturableSociete) ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotIn($noestFacturableSociete) ; }
			if(!empty($lt_estFacturableSociete)){ $sql .= " AND estFacturableSociete < '".$lt_estFacturableSociete."'" ; }
			if(!empty($gt_estFacturableSociete)){ $sql .= " AND estFacturableSociete > '".$gt_estFacturableSociete."'" ; }
			if(!empty($idclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect ".sqlIn($idclient_has_etat_prospect) ; }
			if(!empty($noidclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect ".sqlNotIn($noidclient_has_etat_prospect) ; }
			if(!empty($lt_idclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect < '".$lt_idclient_has_etat_prospect."'" ; }
			if(!empty($gt_idclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect > '".$gt_idclient_has_etat_prospect."'" ; }
			if(!empty($idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlIn($idclient_has_etat_client) ; }
			if(!empty($noidclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlNotIn($noidclient_has_etat_client) ; }
			if(!empty($lt_idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client < '".$lt_idclient_has_etat_client."'" ; }
			if(!empty($gt_idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client > '".$gt_idclient_has_etat_client."'" ; }
			if(!empty($nomEtat_client)){ $sql .= " AND nomEtat_client ".sqlIn($nomEtat_client) ; }
			if(!empty($nonomEtat_client)){ $sql .= " AND nomEtat_client ".sqlNotIn($nonomEtat_client) ; }
			if(!empty($lt_nomEtat_client)){ $sql .= " AND nomEtat_client < '".$lt_nomEtat_client."'" ; }
			if(!empty($gt_nomEtat_client)){ $sql .= " AND nomEtat_client > '".$gt_nomEtat_client."'" ; }
			if(!empty($ordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlIn($ordreEtat_client) ; }
			if(!empty($noordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlNotIn($noordreEtat_client) ; }
			if(!empty($lt_ordreEtat_client)){ $sql .= " AND ordreEtat_client < '".$lt_ordreEtat_client."'" ; }
			if(!empty($gt_ordreEtat_client)){ $sql .= " AND ordreEtat_client > '".$gt_ordreEtat_client."'" ; }
			if(!empty($ordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect ".sqlIn($ordreClient_has_etat_prospect) ; }
			if(!empty($noordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect ".sqlNotIn($noordreClient_has_etat_prospect) ; }
			if(!empty($lt_ordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect < '".$lt_ordreClient_has_etat_prospect."'" ; }
			if(!empty($gt_ordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect > '".$gt_ordreClient_has_etat_prospect."'" ; }
			if(!empty($dateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect ".sqlIn($dateClient_has_etat_prospect) ; }
			if(!empty($nodateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect ".sqlNotIn($nodateClient_has_etat_prospect) ; }
			if(!empty($lt_dateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect < '".$lt_dateClient_has_etat_prospect."'" ; }
			if(!empty($gt_dateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect > '".$gt_dateClient_has_etat_prospect."'" ; }
			if(!empty($dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlIn($dateClient_has_etat_client) ; }
			if(!empty($nodateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlNotIn($nodateClient_has_etat_client) ; }
			if(!empty($lt_dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client < '".$lt_dateClient_has_etat_client."'" ; }
			if(!empty($gt_dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client > '".$gt_dateClient_has_etat_client."'" ; }
			if(!empty($nomEtat_prospect)){ $sql .= " AND nomEtat_prospect ".sqlIn($nomEtat_prospect) ; }
			if(!empty($nonomEtat_prospect)){ $sql .= " AND nomEtat_prospect ".sqlNotIn($nonomEtat_prospect) ; }
			if(!empty($lt_nomEtat_prospect)){ $sql .= " AND nomEtat_prospect < '".$lt_nomEtat_prospect."'" ; }
			if(!empty($gt_nomEtat_prospect)){ $sql .= " AND nomEtat_prospect > '".$gt_nomEtat_prospect."'" ; }
			if(!empty($ordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect ".sqlIn($ordreEtat_prospect) ; }
			if(!empty($noordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect ".sqlNotIn($noordreEtat_prospect) ; }
			if(!empty($lt_ordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect < '".$lt_ordreEtat_prospect."'" ; }
			if(!empty($gt_ordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect > '".$gt_ordreEtat_prospect."'" ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($lt_client_idclient)){ $sql .= " AND client_idclient < '".$lt_client_idclient."'" ; }
			if(!empty($gt_client_idclient)){ $sql .= " AND client_idclient > '".$gt_client_idclient."'" ; }
			if(!empty($etat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlIn($etat_prospect_idetat_prospect) ; }
			if(!empty($noetat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlNotIn($noetat_prospect_idetat_prospect) ; }
			if(!empty($lt_etat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect < '".$lt_etat_prospect_idetat_prospect."'" ; }
			if(!empty($gt_etat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect > '".$gt_etat_prospect_idetat_prospect."'" ; }
			if(!empty($passwordClient)){ $sql .= " AND passwordClient ".sqlIn($passwordClient) ; }
			if(!empty($nopasswordClient)){ $sql .= " AND passwordClient ".sqlNotIn($nopasswordClient) ; }
			if(!empty($lt_passwordClient)){ $sql .= " AND passwordClient < '".$lt_passwordClient."'" ; }
			if(!empty($gt_passwordClient)){ $sql .= " AND passwordClient > '".$gt_passwordClient."'" ; }
			if(!empty($loginClient)){ $sql .= " AND loginClient ".sqlIn($loginClient) ; }
			if(!empty($nologinClient)){ $sql .= " AND loginClient ".sqlNotIn($nologinClient) ; }
			if(!empty($lt_loginClient)){ $sql .= " AND loginClient < '".$lt_loginClient."'" ; }
			if(!empty($gt_loginClient)){ $sql .= " AND loginClient > '".$gt_loginClient."'" ; }
			if(!empty($nombreClientVuAgent)){ $sql .= " AND nombreClientVuAgent ".sqlIn($nombreClientVuAgent) ; }
			if(!empty($nonombreClientVuAgent)){ $sql .= " AND nombreClientVuAgent ".sqlNotIn($nonombreClientVuAgent) ; }
			if(!empty($lt_nombreClientVuAgent)){ $sql .= " AND nombreClientVuAgent < '".$lt_nombreClientVuAgent."'" ; }
			if(!empty($gt_nombreClientVuAgent)){ $sql .= " AND nombreClientVuAgent > '".$gt_nombreClientVuAgent."'" ; }
			if(!empty($dateClientVuAgent)){ $sql .= " AND dateClientVuAgent ".sqlIn($dateClientVuAgent) ; }
			if(!empty($nodateClientVuAgent)){ $sql .= " AND dateClientVuAgent ".sqlNotIn($nodateClientVuAgent) ; }
			if(!empty($lt_dateClientVuAgent)){ $sql .= " AND dateClientVuAgent < '".$lt_dateClientVuAgent."'" ; }
			if(!empty($gt_dateClientVuAgent)){ $sql .= " AND dateClientVuAgent > '".$gt_dateClientVuAgent."'" ; }
			if(!empty($arretClient)){ $sql .= " AND arretClient ".sqlIn($arretClient) ; }
			if(!empty($entite_identite)){ $sql .= " AND entite_identite ".sqlIn($entite_identite) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneClient($params){ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_client where 1 " ;
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlSearch($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotSearch($noidclient) ; }
			if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlSearch($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotSearch($nonumeroClient) ; }
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlSearch($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotSearch($nodateCreationClient) ; }
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlSearch($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotSearch($noestProspectClient) ; }
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlSearch($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotSearch($noestClientClient) ; }
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlSearch($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotSearch($noestSuspectClient) ; }
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlSearch($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotSearch($nocommentaireClient) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlSearch($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotSearch($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlSearch($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotSearch($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlSearch($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotSearch($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlSearch($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotSearch($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlSearch($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotSearch($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlSearch($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotSearch($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlSearch($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotSearch($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlSearch($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotSearch($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlSearch($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotSearch($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlSearch($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotSearch($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlSearch($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotSearch($nonbSalarieSociete) ; }
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlSearch($beneficeSocitete) ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotSearch($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlSearch($estFacturableSociete) ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotSearch($noestFacturableSociete) ; }
			if(!empty($idclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect ".sqlSearch($idclient_has_etat_prospect) ; }
			if(!empty($noidclient_has_etat_prospect)){ $sql .= " AND idclient_has_etat_prospect ".sqlNotSearch($noidclient_has_etat_prospect) ; }
			if(!empty($idclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlSearch($idclient_has_etat_client) ; }
			if(!empty($noidclient_has_etat_client)){ $sql .= " AND idclient_has_etat_client ".sqlNotSearch($noidclient_has_etat_client) ; }
			if(!empty($nomEtat_client)){ $sql .= " AND nomEtat_client ".sqlSearch($nomEtat_client) ; }
			if(!empty($nonomEtat_client)){ $sql .= " AND nomEtat_client ".sqlNotSearch($nonomEtat_client) ; }
			if(!empty($ordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlSearch($ordreEtat_client) ; }
			if(!empty($noordreEtat_client)){ $sql .= " AND ordreEtat_client ".sqlNotSearch($noordreEtat_client) ; }
			if(!empty($ordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect ".sqlSearch($ordreClient_has_etat_prospect) ; }
			if(!empty($noordreClient_has_etat_prospect)){ $sql .= " AND ordreClient_has_etat_prospect ".sqlNotSearch($noordreClient_has_etat_prospect) ; }
			if(!empty($dateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect ".sqlSearch($dateClient_has_etat_prospect) ; }
			if(!empty($nodateClient_has_etat_prospect)){ $sql .= " AND dateClient_has_etat_prospect ".sqlNotSearch($nodateClient_has_etat_prospect) ; }
			if(!empty($dateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlSearch($dateClient_has_etat_client) ; }
			if(!empty($nodateClient_has_etat_client)){ $sql .= " AND dateClient_has_etat_client ".sqlNotSearch($nodateClient_has_etat_client) ; }
			if(!empty($nomEtat_prospect)){ $sql .= " AND nomEtat_prospect ".sqlSearch($nomEtat_prospect) ; }
			if(!empty($nonomEtat_prospect)){ $sql .= " AND nomEtat_prospect ".sqlNotSearch($nonomEtat_prospect) ; }
			if(!empty($ordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect ".sqlSearch($ordreEtat_prospect) ; }
			if(!empty($noordreEtat_prospect)){ $sql .= " AND ordreEtat_prospect ".sqlNotSearch($noordreEtat_prospect) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($etat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlSearch($etat_prospect_idetat_prospect) ; }
			if(!empty($noetat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlNotSearch($noetat_prospect_idetat_prospect) ; }
			if(!empty($nombreVuClient)){ $sql .= " AND nombreVuClient ".sqlSearch($nombreVuClient) ; }
			if(!empty($nonombreVuClient)){ $sql .= " AND nombreVuClient ".sqlNotSearch($nonombreVuClient) ; }
			if(!empty($passwordClient)){ $sql .= " AND passwordClient ".sqlSearch($passwordClient) ; }
			if(!empty($nopasswordClient)){ $sql .= " AND passwordClient ".sqlNotSearch($nopasswordClient) ; }
			if(!empty($loginClient)){ $sql .= " AND loginClient ".sqlSearch($loginClient) ; }
			if(!empty($nologinClient)){ $sql .= " AND loginClient ".sqlNotSearch($nologinClient) ; }
			if(!empty($arretClient)){ $sql .= " AND arretClient ".sqlIn($arretClient) ; }
			if(!empty($entite_identite)){ $sql .= " AND entite_identite ".sqlIn($entite_identite) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllClient(){ 
		$sql="SELECT * FROM  client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneClient($name="idclient",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomClient , idclient FROM WHERE 1 ";  
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			 if(!empty($etat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlIn($etat_prospect_idetat_prospect) ; }
			if(!empty($noetat_prospect_idetat_prospect)){ $sql .= " AND etat_prospect_idetat_prospect ".sqlNotIn($noetat_prospect_idetat_prospect) ; }
			 if(!empty($etat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlIn($etat_client_idetat_client) ; }
			if(!empty($noetat_client_idetat_client)){ $sql .= " AND etat_client_idetat_client ".sqlNotIn($noetat_client_idetat_client) ; }
			 if(!empty($numeroClient)){ $sql .= " AND numeroClient ".sqlIn($numeroClient) ; }
			if(!empty($nonumeroClient)){ $sql .= " AND numeroClient ".sqlNotIn($nonumeroClient) ; } 
			if(!empty($dateCreationClient)){ $sql .= " AND dateCreationClient ".sqlIn($dateCreationClient) ; }
			if(!empty($nodateCreationClient)){ $sql .= " AND dateCreationClient ".sqlNotIn($nodateCreationClient) ; } 
			if(!empty($estProspectClient)){ $sql .= " AND estProspectClient ".sqlIn($estProspectClient) ; }
			if(!empty($noestProspectClient)){ $sql .= " AND estProspectClient ".sqlNotIn($noestProspectClient) ; } 
			if(!empty($estClientClient)){ $sql .= " AND estClientClient ".sqlIn($estClientClient) ; }
			if(!empty($noestClientClient)){ $sql .= " AND estClientClient ".sqlNotIn($noestClientClient) ; } 
			if(!empty($estSuspectClient)){ $sql .= " AND estSuspectClient ".sqlIn($estSuspectClient) ; }
			if(!empty($noestSuspectClient)){ $sql .= " AND estSuspectClient ".sqlNotIn($noestSuspectClient) ; } 
			if(!empty($commentaireClient)){ $sql .= " AND commentaireClient ".sqlIn($commentaireClient) ; }
			if(!empty($nocommentaireClient)){ $sql .= " AND commentaireClient ".sqlNotIn($nocommentaireClient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomClient ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectClient($name="idclient",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomClient , idclient FROM client ORDER BY nomClient ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassClient = new Client(); ?>