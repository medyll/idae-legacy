<?  
/*
Generated: 11:27:2010  |  21:30
ClassContratHasMateriel 
ClassContratHasMateriel.php
*/
	
class ContratHasMateriel extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idcontrat','type_contrat_idtype_contrat','numContrat','dureeContrat','dateDebutContrat','dateFinContrat','dateClotureContrat','idcontrat_has_materiel','contrat_idcontrat','materiel_idmateriel','idmateriel','stock_materiel_idstock_materiel','produit_idproduit','numeroSerieInterneMateriel','numeroSerieExterneMateriel','dateCreationMateriel','dateAcquisitionMateriel','dateFinMateriel','estActifMateriel','pahtMateriel','pvhtMateriel','commentaireMateriel','idproduit','nomProduit','marque_idmarque','gamme_produit_idgamme_produit','estActifProduit','qteAlerteStockProduit','pvhtProduit','idtype_contrat','codeType_contrat','nomType_contrat','ordreType_contrat','referenceInterneProduit','idcontrat_old');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idcontrat_has_materiel";
		return $id;				
	}
	
	function createContratHasMateriel($params){ 
		$this->conn->AutoExecute("contrat_has_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateContratHasMateriel($params){ 
		$this->conn->AutoExecute("contrat_has_materiel", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteContratHasMateriel($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  contrat_has_materiel WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneContratHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_contrat_has_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneContratHasMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_contrat_has_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneContratHasMateriel($name="idvue_contrat_has_materiel",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select Vue_contrat_has_materiel , ".$this->getIdPri()." FROM vue_contrat_has_materiel WHERE 1 ";
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