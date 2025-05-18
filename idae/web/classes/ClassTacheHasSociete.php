<?  
/*
Generated: 02:08:2011  |  21:15
ClassTacheHasSociete 
ClassTacheHasSociete.php
*/
	
class TacheHasSociete extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idtache','suivi_idsuivi','statut_tache_idstatut_tache','type_tache_idtype_tache','objetTache','dateCreationTache','heureDebutTache','heureCreationTache','dateDebutTache','heureFinTache','dateFinTache','commentaireTache','valeurTache','resultatTache','migrateidtache','migrateidtacheTech','idagent_writer','idsociete','nomSociete','siretSociete','sirenSociete','apeSociete','tvaIntraSociete','formeJuridiqueSociete','capitalSociete','deviseCapitalSociete','deviseFacturationSociete','activiteSociete','chiffreAffaireSociete','nbSalarieSociete','beneficeSocitete','estFacturableSociete','societe_idsociete');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idtache_has_societe";
		return $id;				
	}
	
	function createTacheHasSociete($params){ 
		$this->conn->AutoExecute("tache_has_societe", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTacheHasSociete($params){ 
		$this->conn->AutoExecute("tache_has_societe", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteTacheHasSociete($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  tache_has_societe WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTacheHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_tache_has_societe where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneTacheHasSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_tache_has_societe where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneTacheHasSociete($name="idtache_has_societe",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select objetTache , ".$this->getIdPri()." FROM vue_tache_has_societe WHERE 1 ";
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