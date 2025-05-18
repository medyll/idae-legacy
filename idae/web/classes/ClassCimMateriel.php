<?  
/*
Generated: 06:18:2012  |  13:33
ClassCimMateriel 
ClassCimMateriel.php
*/
	
class CimMateriel extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idcim_materiel','produit_idproduit','materiel_idmateriel','cim_idcim','prixCimMateriel','idcim','cim_globale_idcim_globale','agent_idagent','dateCreationCim','dateSignatureCim','compteurNBCim','compteurCoulCim','localisation_idlocalisation','accordFinancementCim','codeCommandeCim','commentaireCim','statutCim','localisation_idlocalisationLiv','pvhtCim','idcim_globale','dateCreationCim_globale','client_idclient','dateSignatureCim_globale','compteurNBCim_globale','compteurCoulCim_globale','accordFinancementCim_globale','codeCommandeCim_globale','commentaireCim_globale','statutCim_globale','societe_idsociete','quantiemeCim_globale','aLivraisonCim_globale');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idcim_materiel";
		return $id;				
	}
	
	function createCimMateriel($params){ 
		$this->conn->AutoExecute("cim_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCimMateriel($params){ 
		$this->conn->AutoExecute("cim_materiel", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteCimMateriel($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  cim_materiel WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneCimMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_cim_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneCimMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_cim_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneCimMateriel($name="idcim_materiel",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select idcim_materiel , ".$this->getIdPri()." FROM vue_cim_materiel WHERE 1 ";
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