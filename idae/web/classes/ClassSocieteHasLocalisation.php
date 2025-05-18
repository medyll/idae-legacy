<?   
class SocieteHasLocalisation
{
	var $conn = null;
	function SocieteHasLocalisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSocieteHasLocalisation($params){ 
		$this->conn->AutoExecute("societe_has_localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_societe_has_localisation", $params, "UPDATE", "idsociete_has_localisation = ".$idsociete_has_localisation); 
	}
	
	function deleteSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  societe_has_localisation WHERE idsociete_has_localisation = $idsociete_has_localisation"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="truncate table  societe_has_localisation "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_societe_has_localisation where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($lt_idadresse)){ $sql .= " AND idadresse < '".$lt_idadresse."'" ; }
			if(!empty($gt_idadresse)){ $sql .= " AND idadresse > '".$gt_idadresse."'" ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($lt_adresse1)){ $sql .= " AND adresse1 < '".$lt_adresse1."'" ; }
			if(!empty($gt_adresse1)){ $sql .= " AND adresse1 > '".$gt_adresse1."'" ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($lt_adresse2)){ $sql .= " AND adresse2 < '".$lt_adresse2."'" ; }
			if(!empty($gt_adresse2)){ $sql .= " AND adresse2 > '".$gt_adresse2."'" ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($lt_codePostalAdresse)){ $sql .= " AND codePostalAdresse < '".$lt_codePostalAdresse."'" ; }
			if(!empty($gt_codePostalAdresse)){ $sql .= " AND codePostalAdresse > '".$gt_codePostalAdresse."'" ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($lt_villeAdresse)){ $sql .= " AND villeAdresse < '".$lt_villeAdresse."'" ; }
			if(!empty($gt_villeAdresse)){ $sql .= " AND villeAdresse > '".$gt_villeAdresse."'" ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($lt_pays)){ $sql .= " AND pays < '".$lt_pays."'" ; }
			if(!empty($gt_pays)){ $sql .= " AND pays > '".$gt_pays."'" ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($lt_commentaireAdresse)){ $sql .= " AND commentaireAdresse < '".$lt_commentaireAdresse."'" ; }
			if(!empty($gt_commentaireAdresse)){ $sql .= " AND commentaireAdresse > '".$gt_commentaireAdresse."'" ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($lt_idlocalisation)){ $sql .= " AND idlocalisation < '".$lt_idlocalisation."'" ; }
			if(!empty($gt_idlocalisation)){ $sql .= " AND idlocalisation > '".$gt_idlocalisation."'" ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($lt_type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation < '".$lt_type_localisation_idtype_localisation."'" ; }
			if(!empty($gt_type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation > '".$gt_type_localisation_idtype_localisation."'" ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($lt_principaleLocalisation)){ $sql .= " AND principaleLocalisation < '".$lt_principaleLocalisation."'" ; }
			if(!empty($gt_principaleLocalisation)){ $sql .= " AND principaleLocalisation > '".$gt_principaleLocalisation."'" ; }
			if(!empty($migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlIn($migrateTypeLocalisation) ; }
			if(!empty($nomigrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlNotIn($nomigrateTypeLocalisation) ; }
			if(!empty($lt_migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation < '".$lt_migrateTypeLocalisation."'" ; }
			if(!empty($gt_migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation > '".$gt_migrateTypeLocalisation."'" ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND idsociete ".sqlNotIn($nosociete_idsociete) ; }
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
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($lt_idtelfax)){ $sql .= " AND idtelfax < '".$lt_idtelfax."'" ; }
			if(!empty($gt_idtelfax)){ $sql .= " AND idtelfax > '".$gt_idtelfax."'" ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($lt_type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax < '".$lt_type_telfax_idtype_telfax."'" ; }
			if(!empty($gt_type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax > '".$gt_type_telfax_idtype_telfax."'" ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($lt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation < '".$lt_localisation_idlocalisation."'" ; }
			if(!empty($gt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation > '".$gt_localisation_idlocalisation."'" ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($lt_numeroTelfax)){ $sql .= " AND numeroTelfax < '".$lt_numeroTelfax."'" ; }
			if(!empty($gt_numeroTelfax)){ $sql .= " AND numeroTelfax > '".$gt_numeroTelfax."'" ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($lt_commentaireTelfax)){ $sql .= " AND commentaireTelfax < '".$lt_commentaireTelfax."'" ; }
			if(!empty($gt_commentaireTelfax)){ $sql .= " AND commentaireTelfax > '".$gt_commentaireTelfax."'" ; }
			
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSocieteHasLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_societe_has_localisation where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlSearch($idadresse,"idadresse") ; }
			if(!empty($lt_idadresse)){ $sql .= " AND idadresse < '".$lt_idadresse."'" ; }
			if(!empty($gt_idadresse)){ $sql .= " AND idadresse > '".$gt_idadresse."'" ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotSearch($noidadresse) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlSearch($adresse1,"adresse1") ; }
			if(!empty($lt_adresse1)){ $sql .= " AND adresse1 < '".$lt_adresse1."'" ; }
			if(!empty($gt_adresse1)){ $sql .= " AND adresse1 > '".$gt_adresse1."'" ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotSearch($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlSearch($adresse2,"adresse2") ; }
			if(!empty($lt_adresse2)){ $sql .= " AND adresse2 < '".$lt_adresse2."'" ; }
			if(!empty($gt_adresse2)){ $sql .= " AND adresse2 > '".$gt_adresse2."'" ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotSearch($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlSearch($codePostalAdresse,"codePostalAdresse") ; }
			if(!empty($or_codePostalAdresse)){ $sql .= " OR codePostalAdresse ".sqlSearch($or_codePostalAdresse,"codePostalAdresse") ; }
			if(!empty($lt_codePostalAdresse)){ $sql .= " AND codePostalAdresse < '".$lt_codePostalAdresse."'" ; }
			if(!empty($gt_codePostalAdresse)){ $sql .= " AND codePostalAdresse > '".$gt_codePostalAdresse."'" ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotSearch($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlSearch($villeAdresse,"villeAdresse") ; }
			if(!empty($or_villeAdresse)){ $sql .= " OR villeAdresse ".sqlSearch($or_villeAdresse,"villeAdresse") ; }
			if(!empty($lt_villeAdresse)){ $sql .= " AND villeAdresse < '".$lt_villeAdresse."'" ; }
			if(!empty($gt_villeAdresse)){ $sql .= " AND villeAdresse > '".$gt_villeAdresse."'" ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotSearch($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlSearch($pays,"pays") ; }
			if(!empty($lt_pays)){ $sql .= " AND pays < '".$lt_pays."'" ; }
			if(!empty($gt_pays)){ $sql .= " AND pays > '".$gt_pays."'" ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotSearch($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlSearch($commentaireAdresse,"commentaireAdresse") ; }
			if(!empty($lt_commentaireAdresse)){ $sql .= " AND commentaireAdresse < '".$lt_commentaireAdresse."'" ; }
			if(!empty($gt_commentaireAdresse)){ $sql .= " AND commentaireAdresse > '".$gt_commentaireAdresse."'" ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotSearch($nocommentaireAdresse) ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlSearch($idlocalisation,"idlocalisation") ; }
			if(!empty($lt_idlocalisation)){ $sql .= " AND idlocalisation < '".$lt_idlocalisation."'" ; }
			if(!empty($gt_idlocalisation)){ $sql .= " AND idlocalisation > '".$gt_idlocalisation."'" ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotSearch($noidlocalisation) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlSearch($type_localisation_idtype_localisation,"type_localisation_idtype_localisation") ; }
			if(!empty($lt_type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation < '".$lt_type_localisation_idtype_localisation."'" ; }
			if(!empty($gt_type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation > '".$gt_type_localisation_idtype_localisation."'" ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotSearch($notype_localisation_idtype_localisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlSearch($principaleLocalisation,"principaleLocalisation") ; }
			if(!empty($lt_principaleLocalisation)){ $sql .= " AND principaleLocalisation < '".$lt_principaleLocalisation."'" ; }
			if(!empty($gt_principaleLocalisation)){ $sql .= " AND principaleLocalisation > '".$gt_principaleLocalisation."'" ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotSearch($noprincipaleLocalisation) ; }
			if(!empty($migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlSearch($migrateTypeLocalisation,"migrateTypeLocalisation") ; }
			if(!empty($lt_migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation < '".$lt_migrateTypeLocalisation."'" ; }
			if(!empty($gt_migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation > '".$gt_migrateTypeLocalisation."'" ; }
			if(!empty($nomigrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlNotSearch($nomigrateTypeLocalisation) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete,"idsociete") ; }
			if(!empty($lt_idsociete)){ $sql .= " AND idsociete < '".$lt_idsociete."'" ; }
			if(!empty($gt_idsociete)){ $sql .= " AND idsociete > '".$gt_idsociete."'" ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete,"nomSociete") ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlSearch($siretSociete,"siretSociete") ; }
			if(!empty($lt_siretSociete)){ $sql .= " AND siretSociete < '".$lt_siretSociete."'" ; }
			if(!empty($gt_siretSociete)){ $sql .= " AND siretSociete > '".$gt_siretSociete."'" ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotSearch($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlSearch($sirenSociete,"sirenSociete") ; }
			if(!empty($lt_sirenSociete)){ $sql .= " AND sirenSociete < '".$lt_sirenSociete."'" ; }
			if(!empty($gt_sirenSociete)){ $sql .= " AND sirenSociete > '".$gt_sirenSociete."'" ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotSearch($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlSearch($apeSociete,"apeSociete") ; }
			if(!empty($lt_apeSociete)){ $sql .= " AND apeSociete < '".$lt_apeSociete."'" ; }
			if(!empty($gt_apeSociete)){ $sql .= " AND apeSociete > '".$gt_apeSociete."'" ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotSearch($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlSearch($tvaIntraSociete,"tvaIntraSociete") ; }
			if(!empty($lt_tvaIntraSociete)){ $sql .= " AND tvaIntraSociete < '".$lt_tvaIntraSociete."'" ; }
			if(!empty($gt_tvaIntraSociete)){ $sql .= " AND tvaIntraSociete > '".$gt_tvaIntraSociete."'" ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotSearch($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlSearch($formeJuridiqueSociete,"formeJuridiqueSociete") ; }
			if(!empty($lt_formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete < '".$lt_formeJuridiqueSociete."'" ; }
			if(!empty($gt_formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete > '".$gt_formeJuridiqueSociete."'" ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotSearch($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlSearch($capitalSociete,"capitalSociete") ; }
			if(!empty($lt_capitalSociete)){ $sql .= " AND capitalSociete < '".$lt_capitalSociete."'" ; }
			if(!empty($gt_capitalSociete)){ $sql .= " AND capitalSociete > '".$gt_capitalSociete."'" ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotSearch($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlSearch($deviseCapitalSociete,"deviseCapitalSociete") ; }
			if(!empty($lt_deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete < '".$lt_deviseCapitalSociete."'" ; }
			if(!empty($gt_deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete > '".$gt_deviseCapitalSociete."'" ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotSearch($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlSearch($deviseFacturationSociete,"deviseFacturationSociete") ; }
			if(!empty($lt_deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete < '".$lt_deviseFacturationSociete."'" ; }
			if(!empty($gt_deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete > '".$gt_deviseFacturationSociete."'" ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotSearch($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlSearch($activiteSociete,"activiteSociete") ; }
			if(!empty($lt_activiteSociete)){ $sql .= " AND activiteSociete < '".$lt_activiteSociete."'" ; }
			if(!empty($gt_activiteSociete)){ $sql .= " AND activiteSociete > '".$gt_activiteSociete."'" ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotSearch($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlSearch($chiffreAffaireSociete,"chiffreAffaireSociete") ; }
			if(!empty($lt_chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete < '".$lt_chiffreAffaireSociete."'" ; }
			if(!empty($gt_chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete > '".$gt_chiffreAffaireSociete."'" ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotSearch($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlSearch($nbSalarieSociete,"nbSalarieSociete") ; }
			if(!empty($lt_nbSalarieSociete)){ $sql .= " AND nbSalarieSociete < '".$lt_nbSalarieSociete."'" ; }
			if(!empty($gt_nbSalarieSociete)){ $sql .= " AND nbSalarieSociete > '".$gt_nbSalarieSociete."'" ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotSearch($nonbSalarieSociete) ; }
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlSearch($beneficeSocitete,"beneficeSocitete") ; }
			if(!empty($lt_beneficeSocitete)){ $sql .= " AND beneficeSocitete < '".$lt_beneficeSocitete."'" ; }
			if(!empty($gt_beneficeSocitete)){ $sql .= " AND beneficeSocitete > '".$gt_beneficeSocitete."'" ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotSearch($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlSearch($estFacturableSociete,"estFacturableSociete") ; }
			if(!empty($lt_estFacturableSociete)){ $sql .= " AND estFacturableSociete < '".$lt_estFacturableSociete."'" ; }
			if(!empty($gt_estFacturableSociete)){ $sql .= " AND estFacturableSociete > '".$gt_estFacturableSociete."'" ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotSearch($noestFacturableSociete) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlSearch($idtelfax,"idtelfax") ; }
			if(!empty($lt_idtelfax)){ $sql .= " AND idtelfax < '".$lt_idtelfax."'" ; }
			if(!empty($gt_idtelfax)){ $sql .= " AND idtelfax > '".$gt_idtelfax."'" ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotSearch($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlSearch($type_telfax_idtype_telfax,"type_telfax_idtype_telfax") ; }
			if(!empty($lt_type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax < '".$lt_type_telfax_idtype_telfax."'" ; }
			if(!empty($gt_type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax > '".$gt_type_telfax_idtype_telfax."'" ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotSearch($notype_telfax_idtype_telfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation,"localisation_idlocalisation") ; }
			if(!empty($lt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation < '".$lt_localisation_idlocalisation."'" ; }
			if(!empty($gt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation > '".$gt_localisation_idlocalisation."'" ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlSearch($numeroTelfax,"numeroTelfax") ; }
			if(!empty($lt_numeroTelfax)){ $sql .= " AND numeroTelfax < '".$lt_numeroTelfax."'" ; }
			if(!empty($gt_numeroTelfax)){ $sql .= " AND numeroTelfax > '".$gt_numeroTelfax."'" ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotSearch($nonumeroTelfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlSearch($commentaireTelfax,"commentaireTelfax") ; }
			if(!empty($lt_commentaireTelfax)){ $sql .= " AND commentaireTelfax < '".$lt_commentaireTelfax."'" ; }
			if(!empty($gt_commentaireTelfax)){ $sql .= " AND commentaireTelfax > '".$gt_commentaireTelfax."'" ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotSearch($nocommentaireTelfax) ; }
			
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSocieteHasLocalisation(){ 
		$sql="SELECT * FROM  vue_societe_has_localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSocieteHasLocalisation($name="idsociete_has_localisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_societe_has_localisation , idsociete_has_localisation FROM vue_societe_has_localisation WHERE  1 ";  
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($gt_idadresse)){ $sql .= " AND idadresse > ".$gt_idadresse ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($gt_adresse1)){ $sql .= " AND adresse1 > ".$gt_adresse1 ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($gt_adresse2)){ $sql .= " AND adresse2 > ".$gt_adresse2 ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($gt_codePostalAdresse)){ $sql .= " AND codePostalAdresse > ".$gt_codePostalAdresse ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($gt_villeAdresse)){ $sql .= " AND villeAdresse > ".$gt_villeAdresse ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($gt_pays)){ $sql .= " AND pays > ".$gt_pays ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($gt_commentaireAdresse)){ $sql .= " AND commentaireAdresse > ".$gt_commentaireAdresse ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($gt_idlocalisation)){ $sql .= " AND idlocalisation > ".$gt_idlocalisation ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($gt_type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation > ".$gt_type_localisation_idtype_localisation ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($gt_principaleLocalisation)){ $sql .= " AND principaleLocalisation > ".$gt_principaleLocalisation ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlIn($migrateTypeLocalisation) ; }
			if(!empty($gt_migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation > ".$gt_migrateTypeLocalisation ; }
			if(!empty($nomigrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlNotIn($nomigrateTypeLocalisation) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($gt_idsociete)){ $sql .= " AND idsociete > ".$gt_idsociete ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > ".$gt_nomSociete ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlIn($siretSociete) ; }
			if(!empty($gt_siretSociete)){ $sql .= " AND siretSociete > ".$gt_siretSociete ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotIn($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlIn($sirenSociete) ; }
			if(!empty($gt_sirenSociete)){ $sql .= " AND sirenSociete > ".$gt_sirenSociete ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotIn($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlIn($apeSociete) ; }
			if(!empty($gt_apeSociete)){ $sql .= " AND apeSociete > ".$gt_apeSociete ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotIn($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlIn($tvaIntraSociete) ; }
			if(!empty($gt_tvaIntraSociete)){ $sql .= " AND tvaIntraSociete > ".$gt_tvaIntraSociete ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotIn($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlIn($formeJuridiqueSociete) ; }
			if(!empty($gt_formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete > ".$gt_formeJuridiqueSociete ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotIn($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlIn($capitalSociete) ; }
			if(!empty($gt_capitalSociete)){ $sql .= " AND capitalSociete > ".$gt_capitalSociete ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotIn($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlIn($deviseCapitalSociete) ; }
			if(!empty($gt_deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete > ".$gt_deviseCapitalSociete ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotIn($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlIn($deviseFacturationSociete) ; }
			if(!empty($gt_deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete > ".$gt_deviseFacturationSociete ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotIn($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlIn($activiteSociete) ; }
			if(!empty($gt_activiteSociete)){ $sql .= " AND activiteSociete > ".$gt_activiteSociete ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotIn($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlIn($chiffreAffaireSociete) ; }
			if(!empty($gt_chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete > ".$gt_chiffreAffaireSociete ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotIn($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlIn($nbSalarieSociete) ; }
			if(!empty($gt_nbSalarieSociete)){ $sql .= " AND nbSalarieSociete > ".$gt_nbSalarieSociete ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotIn($nonbSalarieSociete) ; }
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlIn($beneficeSocitete) ; }
			if(!empty($gt_beneficeSocitete)){ $sql .= " AND beneficeSocitete > ".$gt_beneficeSocitete ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotIn($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlIn($estFacturableSociete) ; }
			if(!empty($gt_estFacturableSociete)){ $sql .= " AND estFacturableSociete > ".$gt_estFacturableSociete ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotIn($noestFacturableSociete) ; }
			if(!empty($idtelfax)){ $sql .= " AND idtelfax ".sqlIn($idtelfax) ; }
			if(!empty($gt_idtelfax)){ $sql .= " AND idtelfax > ".$gt_idtelfax ; }
			if(!empty($noidtelfax)){ $sql .= " AND idtelfax ".sqlNotIn($noidtelfax) ; }
			if(!empty($type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlIn($type_telfax_idtype_telfax) ; }
			if(!empty($gt_type_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax > ".$gt_type_telfax_idtype_telfax ; }
			if(!empty($notype_telfax_idtype_telfax)){ $sql .= " AND type_telfax_idtype_telfax ".sqlNotIn($notype_telfax_idtype_telfax) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($gt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation > ".$gt_localisation_idlocalisation ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($numeroTelfax)){ $sql .= " AND numeroTelfax ".sqlIn($numeroTelfax) ; }
			if(!empty($gt_numeroTelfax)){ $sql .= " AND numeroTelfax > ".$gt_numeroTelfax ; }
			if(!empty($nonumeroTelfax)){ $sql .= " AND numeroTelfax ".sqlNotIn($nonumeroTelfax) ; }
			if(!empty($commentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlIn($commentaireTelfax) ; }
			if(!empty($gt_commentaireTelfax)){ $sql .= " AND commentaireTelfax > ".$gt_commentaireTelfax ; }
			if(!empty($nocommentaireTelfax)){ $sql .= " AND commentaireTelfax ".sqlNotIn($nocommentaireTelfax) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_societe_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSocieteHasLocalisation($name="idsociete_has_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_societe_has_localisation , idsociete_has_localisation FROM vue_societe_has_localisation ORDER BY nomVue_societe_has_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSocieteHasLocalisation = new SocieteHasLocalisation(); ?>