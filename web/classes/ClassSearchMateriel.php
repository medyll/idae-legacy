<?  
/*
Generated: 12:22:2010  |  23:24
ClassSearchMateriel 
ClassSearchMateriel.php
*/
	
class SearchMateriel extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idproduit','gamme_produit_idgamme_produit','marque_idmarque','referenceInterneProduit','nomProduit','pahtProduit','pvhtProduit','commentaireProduit','estActifProduit','qteAlerteStockProduit','idmateriel','stock_materiel_idstock_materiel','produit_idproduit','numeroSerieInterneMateriel','numeroSerieExterneMateriel','dateCreationMateriel','dateAcquisitionMateriel','estActifMateriel','commentaireMateriel','idcategorie_produit','nomCategorie_produit','idligne_location_client','nomSociete','localisation_idlocalisation','dateFinMateriel','pvhtMateriel','pahtMateriel','villeAdresse','codePostalAdresse','adresse1','entite_identite');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "gamme_produit_idgamme_produit";
		return $id;				
	}
	
	function createSearchMateriel($params){ 
		$this->conn->AutoExecute("vue_search_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSearchMateriel($params){ 
		$this->conn->AutoExecute("vue_search_materiel", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteSearchMateriel($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  vue_search_materiel WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSearchMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_search_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneSearchMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_search_materiel where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneSearchMateriel($name="idvue_search_materiel",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select Vue_search_materiel , ".$this->getIdPri()." FROM vue_search_materiel WHERE 1 ";
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