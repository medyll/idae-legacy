<?   
class SuiviHasSociete
{
	var $conn = null;
	function SuiviHasSociete(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSuiviHasSociete($params){ 
		$this->conn->AutoExecute("suivi_has_societe", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("suivi_has_societe", $params, "UPDATE", "idsuivi_has_societe = ".$idsuivi_has_societe); 
	}
	
	function deleteSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  suivi_has_societe WHERE idsuivi_has_societe = $idsuivi_has_societe"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){	
		$sql="TRUNCATE TABLE suivi_has_societe"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_suivi_societe where 1 " ;
			if(!empty($idtype_suivi)){ $sql .= " AND idtype_suivi ".sqlIn($idtype_suivi) ; }
			if(!empty($noidtype_suivi)){ $sql .= " AND idtype_suivi ".sqlNotIn($noidtype_suivi) ; }
			if(!empty($nomType_suivi)){ $sql .= " AND nomType_suivi ".sqlIn($nomType_suivi) ; }
			if(!empty($nonomType_suivi)){ $sql .= " AND nomType_suivi ".sqlNotIn($nonomType_suivi) ; }
			if(!empty($commentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlIn($commentaireType_suivi) ; }
			if(!empty($nocommentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlNotIn($nocommentaireType_suivi) ; }
			if(!empty($dateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlIn($dateDebutType_suivi) ; }
			if(!empty($nodateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlNotIn($nodateDebutType_suivi) ; }
			if(!empty($dateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlIn($dateFinType_suivi) ; }
			if(!empty($nodateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlNotIn($nodateFinType_suivi) ; }
			if(!empty($ordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlIn($ordreTypeSuivi) ; }
			if(!empty($noordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlNotIn($noordreTypeSuivi) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlIn($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotIn($nodateCreationSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlIn($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotIn($nodateDebutSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlIn($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotIn($nodateFinSuivi) ; }
			if(!empty($dateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlIn($dateClotureSuivi) ; }
			if(!empty($nodateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlNotIn($nodateClotureSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlIn($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotIn($noobjetSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlIn($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotIn($nocommentaireSuivi) ; }
			if(!empty($idsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlIn($idsuivi_has_societe) ; }
			if(!empty($noidsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlNotIn($noidsuivi_has_societe) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlIn($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotIn($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlIn($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotIn($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlIn($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotIn($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlIn($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotIn($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlIn($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotIn($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlIn($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotIn($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlIn($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotIn($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlIn($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotIn($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlIn($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotIn($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlIn($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotIn($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlIn($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotIn($nonbSalarieSociete) ; }
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlIn($beneficeSocitete) ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotIn($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlIn($estFacturableSociete) ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotIn($noestFacturableSociete) ; }
			if(!empty($idclient_is_societe)){ $sql .= " AND idclient_is_societe ".sqlIn($idclient_is_societe) ; }
			if(!empty($noidclient_is_societe)){ $sql .= " AND idclient_is_societe ".sqlNotIn($noidclient_is_societe) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotSearch($noidsuivi) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSuiviHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_suivi_societe where 1 " ;
			if(!empty($idtype_suivi)){ $sql .= " AND idtype_suivi ".sqlSearch($idtype_suivi) ; }
			if(!empty($noidtype_suivi)){ $sql .= " AND idtype_suivi ".sqlNotSearch($noidtype_suivi) ; }
			if(!empty($nomType_suivi)){ $sql .= " AND nomType_suivi ".sqlSearch($nomType_suivi) ; }
			if(!empty($nonomType_suivi)){ $sql .= " AND nomType_suivi ".sqlNotSearch($nonomType_suivi) ; }
			if(!empty($commentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlSearch($commentaireType_suivi) ; }
			if(!empty($nocommentaireType_suivi)){ $sql .= " AND commentaireType_suivi ".sqlNotSearch($nocommentaireType_suivi) ; }
			if(!empty($dateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlSearch($dateDebutType_suivi) ; }
			if(!empty($nodateDebutType_suivi)){ $sql .= " AND dateDebutType_suivi ".sqlNotSearch($nodateDebutType_suivi) ; }
			if(!empty($dateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlSearch($dateFinType_suivi) ; }
			if(!empty($nodateFinType_suivi)){ $sql .= " AND dateFinType_suivi ".sqlNotSearch($nodateFinType_suivi) ; }
			if(!empty($ordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlSearch($ordreTypeSuivi) ; }
			if(!empty($noordreTypeSuivi)){ $sql .= " AND ordreTypeSuivi ".sqlNotSearch($noordreTypeSuivi) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlSearch($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotSearch($noidsuivi) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($dateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlSearch($dateCreationSuivi) ; }
			if(!empty($nodateCreationSuivi)){ $sql .= " AND dateCreationSuivi ".sqlNotSearch($nodateCreationSuivi) ; }
			if(!empty($dateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlSearch($dateDebutSuivi) ; }
			if(!empty($nodateDebutSuivi)){ $sql .= " AND dateDebutSuivi ".sqlNotSearch($nodateDebutSuivi) ; }
			if(!empty($dateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlSearch($dateFinSuivi) ; }
			if(!empty($nodateFinSuivi)){ $sql .= " AND dateFinSuivi ".sqlNotSearch($nodateFinSuivi) ; }
			if(!empty($dateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlSearch($dateClotureSuivi) ; }
			if(!empty($nodateClotureSuivi)){ $sql .= " AND dateClotureSuivi ".sqlNotSearch($nodateClotureSuivi) ; }
			if(!empty($objetSuivi)){ $sql .= " AND objetSuivi ".sqlSearch($objetSuivi) ; }
			if(!empty($noobjetSuivi)){ $sql .= " AND objetSuivi ".sqlNotSearch($noobjetSuivi) ; }
			if(!empty($commentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlSearch($commentaireSuivi) ; }
			if(!empty($nocommentaireSuivi)){ $sql .= " AND commentaireSuivi ".sqlNotSearch($nocommentaireSuivi) ; }
			if(!empty($idsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlSearch($idsuivi_has_societe) ; }
			if(!empty($noidsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlNotSearch($noidsuivi_has_societe) ; }
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
			if(!empty($idclient_is_societe)){ $sql .= " AND idclient_is_societe ".sqlSearch($idclient_is_societe) ; }
			if(!empty($noidclient_is_societe)){ $sql .= " AND idclient_is_societe ".sqlNotSearch($noidclient_is_societe) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllSuiviHasSociete(){ 
		$sql="SELECT * FROM  suivi_has_societe"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSuiviHasSociete($name="idsuivi_has_societe",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSuivi_has_societe , idsuivi_has_societe FROM suivi_has_societe WHERE  1 ";  
			if(!empty($idsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlIn($idsuivi_has_societe) ; }
			if(!empty($noidsuivi_has_societe)){ $sql .= " AND idsuivi_has_societe ".sqlNotIn($noidsuivi_has_societe) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSuivi_has_societe ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSuiviHasSociete($name="idsuivi_has_societe",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSuivi_has_societe , idsuivi_has_societe FROM suivi_has_societe ORDER BY nomSuivi_has_societe ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSuiviHasSociete = new SuiviHasSociete(); ?>