<?  
/*
Generated: 06:18:2012  |  16:21
ClassCimGlobale 
ClassCimGlobale.php
*/
	
class CimGlobale extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idsociete','nomSociete','siretSociete','sirenSociete','apeSociete','tvaIntraSociete','formeJuridiqueSociete','capitalSociete','deviseCapitalSociete','deviseFacturationSociete','activiteSociete','chiffreAffaireSociete','nbSalarieSociete','beneficeSocitete','estFacturableSociete','idmateriel','idproduit','gamme_produit_idgamme_produit','marque_idmarque','referenceInterneProduit','nomProduit','pahtProduit','pvhtProduit','commentaireProduit','estActifProduit','qteAlerteStockProduit','stock_materiel_idstock_materiel','produit_idproduit','numeroSerieInterneMateriel','numeroSerieExterneMateriel','dateCreationMateriel','dateAcquisitionMateriel','dateFinMateriel','estActifMateriel','pahtMateriel','pvhtMateriel','commentaireMateriel','idgamme_produit','nomGamme_produit','commentaireGamme_produit','dateDebutGamme_produit','dateFinGamme_produit','estActifGamme_produit','ordreGamme_produit','idcim_globale','dateCreationCim_globale','client_idclient','materiel_idmateriel','agent_idagent','dateSignatureCim_globale','compteurNBCim_globale','compteurCoulCim_globale','localisation_idlocalisation','accordFinancementCim_globale','codeCommandeCim_globale','commentaireCim_globale','statutCim_globale','societe_idsociete','localisation_idlocalisationLiv');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idcim_globale";
		return $id;				
	}
	
	function createCimGlobale($params){ 
		$this->conn->AutoExecute("cim_globale", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCimGlobale($params){ 
		$this->conn->AutoExecute("cim_globale", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteCimGlobale($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  cim_globale WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneCimGlobale($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_cim_globale where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneCimGlobale($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_cim_globale where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneCimGlobale($name="idcim_globale",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select Cim_globale , ".$this->getIdPri()." FROM vue_cim_globale WHERE 1 ";
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		   
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
?>