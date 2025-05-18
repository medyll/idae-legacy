<?  
/*
Generated: 06:18:2012  |  13:34
ClassCimFrais 
ClassCimFrais.php
*/
	
class CimFrais extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idcim_frais','cim_idcim','prixCim_frais');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idcim_frais";
		return $id;				
	}
	
	function createCimFrais($params){ 
		$this->conn->AutoExecute("cim_frais", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateCimFrais($params){ 
		$this->conn->AutoExecute("cim_frais", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteCimFrais($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  cim_frais WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneCimFrais($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from cim_frais where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneCimFrais($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from cim_frais where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneCimFrais($name="idcim_frais",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select Cim_frais , ".$this->getIdPri()." FROM cim_frais WHERE 1 ";
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