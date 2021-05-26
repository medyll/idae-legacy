<?   
class NoteHasAgent
{
	var $conn = null;
	function NoteHasAgent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createNoteHasAgent($params){ 
		$this->conn->AutoExecute("note_has_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateNoteHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("note_has_agent", $params, "UPDATE", "idnote_has_agent = ".$idnote_has_agent); 
	}
	
	function deleteNoteHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  note_has_agent WHERE idnote_has_agent = $idnote_has_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneNoteHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from note_has_agent where 1 " ;
			if(!empty($note_idnote)){ $sql .= " AND note_idnote ".sqlIn($note_idnote) ; }
			if(!empty($nonote_idnote)){ $sql .= " AND note_idnote ".sqlNotIn($nonote_idnote) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlIn($dateLectureNote_has_agent) ; }
			if(!empty($nodateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlNotIn($nodateLectureNote_has_agent) ; }
			if(!empty($dateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlIn($dateClotureNote_has_agent) ; }
			if(!empty($nodateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlNotIn($nodateClotureNote_has_agent) ; }
			if(!empty($estActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlIn($estActifNote_has_agent) ; }
			if(!empty($noestActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlNotIn($noestActifNote_has_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneNoteHasAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from note_has_agent where 1 " ;
			if(!empty($note_idnote)){ $sql .= " AND note_idnote ".sqlSearch($note_idnote) ; }
			if(!empty($nonote_idnote)){ $sql .= " AND note_idnote ".sqlNotSearch($nonote_idnote) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($dateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlSearch($dateLectureNote_has_agent) ; }
			if(!empty($nodateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlNotSearch($nodateLectureNote_has_agent) ; }
			if(!empty($dateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlSearch($dateClotureNote_has_agent) ; }
			if(!empty($nodateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlNotSearch($nodateClotureNote_has_agent) ; }
			if(!empty($estActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlSearch($estActifNote_has_agent) ; }
			if(!empty($noestActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlNotSearch($noestActifNote_has_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllNoteHasAgent(){ 
		$sql="SELECT * FROM  note_has_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneNoteHasAgent($name="idnote_has_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomNote_has_agent , idnote_has_agent FROM WHERE 1 ";  
			if(!empty($note_idnote)){ $sql .= " AND note_idnote ".sqlIn($note_idnote) ; }
			if(!empty($nonote_idnote)){ $sql .= " AND note_idnote ".sqlNotIn($nonote_idnote) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlIn($dateLectureNote_has_agent) ; }
			if(!empty($nodateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlNotIn($nodateLectureNote_has_agent) ; }
			if(!empty($dateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlIn($dateClotureNote_has_agent) ; }
			if(!empty($nodateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlNotIn($nodateClotureNote_has_agent) ; }
			if(!empty($estActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlIn($estActifNote_has_agent) ; }
			if(!empty($noestActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlNotIn($noestActifNote_has_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomNote_has_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectNoteHasAgent($name="idnote_has_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomNote_has_agent , idnote_has_agent FROM note_has_agent ORDER BY nomNote_has_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassNoteHasAgent = new NoteHasAgent(); ?>