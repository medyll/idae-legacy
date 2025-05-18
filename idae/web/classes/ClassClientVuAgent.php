<?  
/*
Generated: 11:28:2010  |  16:09
ClassClientVuAgent 
ClassClientVuAgent.php
*/
	
class ClientVuAgent extends daoSkel
{
	var $conn = null; 
	
	function __construct(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getFields(){ 
		$arrFields = array( 'idclient_vu_agent','agent_idagent','client_idclient','nombreClientVuAgent','dateClientVuAgent','idclient','numeroClient','dateCreationClient','estProspectClient','estClientClient','estSuspectClient','commentaireClient','loginClient','passwordClient','nombreVuClient','arretClient');
		return $arrFields;				
	}
	
	function getIdPri(){ 
		$id = "idclient_vu_agent";
		return $id;				
	}
	
	function createClientVuAgent($params){ 
		$this->conn->AutoExecute("client_vu_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateClientVuAgent($params){ 
		$this->conn->AutoExecute("client_vu_agent", $params, "UPDATE", $this->getIdPri() ."=". $params[$this->getIdPri()]); 
	}
	
	function deleteClientVuAgent($params){
		$id = $this->getIdPri();
		$sql='DELETE FROM  client_vu_agent WHERE '.$id.' ='.$params[$id]   ; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneClientVuAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql="select ".$all." from vue_client_vu_agent where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSelect($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	}
	function searchOneClientVuAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		$sql=" select ".$all." from vue_client_vu_agent where 1 " ;
		foreach($this->getFields() as $key=>$value){
		$sql .= $this->getParseSearch($value,$params);
		}
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
		if(!empty($debug)){ echo $sql;   }
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
		return $this->conn->Execute($sql) ;	
	} 
	
	function getSelectOneClientVuAgent($name="idclient_vu_agent",$id="",$allowEmpty= false,$params=array())
	{ 
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? " * " : $all  ; 	
		$sql="select Client_vu_agent , ".$this->getIdPri()." FROM client_vu_agent WHERE 1 ";
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