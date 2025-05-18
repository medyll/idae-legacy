<?   
class Note
{
	var $conn = null;
	function Note(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createNote($params){ 
		$this->conn->AutoExecute("agent_note", $params,"INSERT");
		return $this->conn->Insert_ID();				
	}
	function updateNote($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("agent_note", $params, "UPDATE", "idnote = ".$idnote);
	}
	function deleteNote($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  agent_note WHERE idnote = $idnote";
		return $this->conn->Execute($sql); 	
	}
	function getOneNote($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_note where 1 " ;
			if(!empty($idnote)){ $sql .= " AND idnote ".sqlIn($idnote) ; }
			if(!empty($noidnote)){ $sql .= " AND idnote ".sqlNotIn($noidnote) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlIn($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotIn($noidagent) ; }
			if(!empty($dateCreationNote)){ $sql .= " AND dateCreationNote ".sqlIn($dateCreationNote) ; }
			if(!empty($nodateCreationNote)){ $sql .= " AND dateCreationNote ".sqlNotIn($nodateCreationNote) ; }
			if(!empty($texteNote)){ $sql .= " AND texteNote ".sqlIn($texteNote) ; }
			if(!empty($notexteNote)){ $sql .= " AND texteNote ".sqlNotIn($notexteNote) ; }
			if(!empty($dateFinNote)){ $sql .= " AND dateFinNote ".sqlIn($dateFinNote) ; }
			if(!empty($nodateFinNote)){ $sql .= " AND dateFinNote ".sqlNotIn($nodateFinNote) ; }
			if(!empty($dateClotureNote)){ $sql .= " AND dateClotureNote ".sqlIn($dateClotureNote) ; }
			if(!empty($nodateClotureNote)){ $sql .= " AND dateClotureNote ".sqlNotIn($nodateClotureNote) ; }
			if(!empty($estActiveNote)){ $sql .= " AND estActiveNote ".sqlIn($estActiveNote) ; }
			if(!empty($noestActiveNote)){ $sql .= " AND estActiveNote ".sqlNotIn($noestActiveNote) ; }
			if(!empty($note_idnote)){ $sql .= " AND note_idnote ".sqlIn($note_idnote) ; }
			if(!empty($nonote_idnote)){ $sql .= " AND note_idnote ".sqlNotIn($nonote_idnote) ; }
			if(!empty($dateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlIn($dateLectureNote_has_agent) ; }
			if(!empty($nodateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlNotIn($nodateLectureNote_has_agent) ; }
			if(!empty($dateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlIn($dateClotureNote_has_agent) ; }
			if(!empty($nodateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlNotIn($nodateClotureNote_has_agent) ; }
			if(!empty($estActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlIn($estActifNote_has_agent) ; }
			if(!empty($noestActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlNotIn($noestActifNote_has_agent) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneNote($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_note where 1 " ;
			if(!empty($idnote)){ $sql .= " AND idnote ".sqlSearch($idnote) ; }
			if(!empty($noidnote)){ $sql .= " AND idnote ".sqlNotSearch($noidnote) ; }
			if(!empty($idagent)){ $sql .= " AND idagent ".sqlSearch($idagent) ; }
			if(!empty($noidagent)){ $sql .= " AND idagent ".sqlNotSearch($noidagent) ; }
			if(!empty($dateCreationNote)){ $sql .= " AND dateCreationNote ".sqlSearch($dateCreationNote) ; }
			if(!empty($nodateCreationNote)){ $sql .= " AND dateCreationNote ".sqlNotSearch($nodateCreationNote) ; }
			if(!empty($texteNote)){ $sql .= " AND texteNote ".sqlSearch($texteNote) ; }
			if(!empty($notexteNote)){ $sql .= " AND texteNote ".sqlNotSearch($notexteNote) ; }
			if(!empty($dateFinNote)){ $sql .= " AND dateFinNote ".sqlSearch($dateFinNote) ; }
			if(!empty($nodateFinNote)){ $sql .= " AND dateFinNote ".sqlNotSearch($nodateFinNote) ; }
			if(!empty($dateClotureNote)){ $sql .= " AND dateClotureNote ".sqlSearch($dateClotureNote) ; }
			if(!empty($nodateClotureNote)){ $sql .= " AND dateClotureNote ".sqlNotSearch($nodateClotureNote) ; }
			if(!empty($estActiveNote)){ $sql .= " AND estActiveNote ".sqlSearch($estActiveNote) ; }
			if(!empty($noestActiveNote)){ $sql .= " AND estActiveNote ".sqlNotSearch($noestActiveNote) ; }
			if(!empty($note_idnote)){ $sql .= " AND note_idnote ".sqlSearch($note_idnote) ; }
			if(!empty($nonote_idnote)){ $sql .= " AND note_idnote ".sqlNotSearch($nonote_idnote) ; }
			if(!empty($dateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlSearch($dateLectureNote_has_agent) ; }
			if(!empty($nodateLectureNote_has_agent)){ $sql .= " AND dateLectureNote_has_agent ".sqlNotSearch($nodateLectureNote_has_agent) ; }
			if(!empty($dateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlSearch($dateClotureNote_has_agent) ; }
			if(!empty($nodateClotureNote_has_agent)){ $sql .= " AND dateClotureNote_has_agent ".sqlNotSearch($nodateClotureNote_has_agent) ; }
			if(!empty($estActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlSearch($estActifNote_has_agent) ; }
			if(!empty($noestActifNote_has_agent)){ $sql .= " AND estActifNote_has_agent ".sqlNotSearch($noestActifNote_has_agent) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllNote(){ 
		$sql="SELECT * FROM  agent_note";
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneNote($name="idnote",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomNote , idnote FROM WHERE 1 ";  
			if(!empty($idnote)){ $sql .= " AND idnote ".sqlIn($idnote) ; }
			if(!empty($noidnote)){ $sql .= " AND idnote ".sqlNotIn($noidnote) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($dateCreationNote)){ $sql .= " AND dateCreationNote ".sqlIn($dateCreationNote) ; }
			if(!empty($nodateCreationNote)){ $sql .= " AND dateCreationNote ".sqlNotIn($nodateCreationNote) ; }
			if(!empty($texteNote)){ $sql .= " AND texteNote ".sqlIn($texteNote) ; }
			if(!empty($notexteNote)){ $sql .= " AND texteNote ".sqlNotIn($notexteNote) ; }
			if(!empty($dateFinNote)){ $sql .= " AND dateFinNote ".sqlIn($dateFinNote) ; }
			if(!empty($nodateFinNote)){ $sql .= " AND dateFinNote ".sqlNotIn($nodateFinNote) ; }
			if(!empty($dateClotureNote)){ $sql .= " AND dateClotureNote ".sqlIn($dateClotureNote) ; }
			if(!empty($nodateClotureNote)){ $sql .= " AND dateClotureNote ".sqlNotIn($nodateClotureNote) ; }
			if(!empty($estActiveNote)){ $sql .= " AND estActiveNote ".sqlIn($estActiveNote) ; }
			if(!empty($noestActiveNote)){ $sql .= " AND estActiveNote ".sqlNotIn($noestActiveNote) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomNote ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectNote($name="idnote",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomNote , idnote FROM agent_note ORDER BY nomNote ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassNote = new Note(); ?>