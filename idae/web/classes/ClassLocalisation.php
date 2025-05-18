<?   
class Localisation
{
	var $conn = null;
	function Localisation(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLocalisation($params){ 
		$this->conn->AutoExecute("localisation", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("localisation", $params, "UPDATE", "idlocalisation = ".$idlocalisation); 
	}
	
	function truncate(){ 	
		$sql="TRUNCATE TABLE  localisation "; 
		return $this->conn->Execute($sql); 	
	}
	
	function deleteLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  localisation WHERE idlocalisation = $idlocalisation"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_localisation where 1 " ;
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($idsociete_has_localisation)){ $sql .= " AND idsociete_has_localisation ".sqlIn($idsociete_has_localisation) ; }
			if(!empty($noidsociete_has_localisation)){ $sql .= " AND idsociete_has_localisation ".sqlNotIn($noidsociete_has_localisation) ; }

			if(!empty($idlieu_has_localisation)){ $sql .= " AND idlieu_has_localisation ".sqlIn($idlieu_has_localisation) ; }
			if(!empty($noidlieu_has_localisation)){ $sql .= " AND idlieu_has_localisation ".sqlNotIn($noidlieu_has_localisation) ; }
			if(!empty($idpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlIn($idpersonne_has_localisation) ; }
			if(!empty($noidpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlNotIn($noidpersonne_has_localisation) ; }
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlIn($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotIn($noidtype_localisation) ; }
			if(!empty($lieu_idlieu)){ $sql .= " AND lieu_idlieu ".sqlIn($lieu_idlieu) ; }
			if(!empty($nolieu_idlieu)){ $sql .= " AND lieu_idlieu ".sqlNotIn($nolieu_idlieu) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLocalisation($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_localisation where 1 " ;
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlSearch($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotSearch($noidlocalisation) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlSearch($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotSearch($notype_localisation_idtype_localisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlSearch($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotSearch($noprincipaleLocalisation) ; }
			
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($idlieu_has_localisation)){ $sql .= " AND idlieu_has_localisation ".sqlSearch($idlieu_has_localisation) ; }
			if(!empty($noidlieu_has_localisation)){ $sql .= " AND idlieu_has_localisation ".sqlNotSearch($noidlieu_has_localisation) ; }
			if(!empty($idpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlSearch($idpersonne_has_localisation) ; }
			if(!empty($noidpersonne_has_localisation)){ $sql .= " AND idpersonne_has_localisation ".sqlNotSearch($noidpersonne_has_localisation) ; }
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlSearch($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotSearch($noidtype_localisation) ; }
			if(!empty($lieu_idlieu)){ $sql .= " AND lieu_idlieu ".sqlSearch($lieu_idlieu) ; }
			if(!empty($nolieu_idlieu)){ $sql .= " AND lieu_idlieu ".sqlNotSearch($nolieu_idlieu) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlSearch($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotSearch($nonomType_localisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLocalisation(){ 
		$sql="SELECT * FROM  localisation"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLocalisation($name="idlocalisation",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT concat(ifnull(adresse1,''),' ',ifnull(codePostalAdresse,''),' ',ifnull(villeAdresse,'')) as adresse , idlocalisation FROM vue_localisation WHERE  1 ";  
			if(!empty($idlocalisation)){ $sql .= " AND idlocalisation ".sqlIn($idlocalisation) ; }
			if(!empty($noidlocalisation)){ $sql .= " AND idlocalisation ".sqlNotIn($noidlocalisation) ; }
			if(!empty($type_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlIn($type_localisation_idtype_localisation) ; }
			if(!empty($notype_localisation_idtype_localisation)){ $sql .= " AND type_localisation_idtype_localisation ".sqlNotIn($notype_localisation_idtype_localisation) ; }
			if(!empty($principaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlIn($principaleLocalisation) ; }
			if(!empty($noprincipaleLocalisation)){ $sql .= " AND principaleLocalisation ".sqlNotIn($noprincipaleLocalisation) ; }
			if(!empty($idtype_localisation)){ $sql .= " AND idtype_localisation ".sqlIn($idtype_localisation) ; }
			if(!empty($noidtype_localisation)){ $sql .= " AND idtype_localisation ".sqlNotIn($noidtype_localisation) ; }
			if(!empty($nomType_localisation)){ $sql .= " AND nomType_localisation ".sqlIn($nomType_localisation) ; }
			if(!empty($nonomType_localisation)){ $sql .= " AND nomType_localisation ".sqlNotIn($nonomType_localisation) ; }
			if(!empty($migrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlIn($migrateTypeLocalisation) ; }
			if(!empty($nomigrateTypeLocalisation)){ $sql .= " AND migrateTypeLocalisation ".sqlNotIn($nomigrateTypeLocalisation) ; }
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY adresse1 , villeAdresse ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLocalisation($name="idvue_localisation",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_localisation , idvue_localisation FROM vue_localisation ORDER BY nomVue_localisation ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLocalisation = new Localisation(); ?>