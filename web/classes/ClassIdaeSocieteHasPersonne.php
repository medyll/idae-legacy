<?   
class IdaeSocieteHasPersonne
{
	var $conn = null;
	function IdaeSocieteHasPersonne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeSocieteHasPersonne($params){ 
		$this->conn->AutoExecute("societe_has_personnes", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("societe_has_personnes", $params, "UPDATE", "idsociete_has_personnes = ".$idsociete_has_personnes); 
	}
	
	function deleteIdaeSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  societe_has_personnes WHERE idsociete_has_personnes = $idsociete_has_personnes"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_societe_has_personne where 1 " ;
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlIn($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotIn($nopersonnes_idpersonnes) ; }
			if(!empty($fonctions)){ $sql .= " AND fonctions ".sqlIn($fonctions) ; }
			if(!empty($nofonctions)){ $sql .= " AND fonctions ".sqlNotIn($nofonctions) ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlIn($commentaires) ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotIn($nocommentaires) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeSocieteHasPersonne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from societe_has_personnes where 1 " ;
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlSearch($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotSearch($nopersonnes_idpersonnes) ; }
			if(!empty($fonctions)){ $sql .= " AND fonctions ".sqlSearch($fonctions) ; }
			if(!empty($nofonctions)){ $sql .= " AND fonctions ".sqlNotSearch($nofonctions) ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlSearch($commentaires) ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotSearch($nocommentaires) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeSocieteHasPersonne(){ 
		$sql="SELECT * FROM  societe_has_personnes"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeSocieteHasPersonne($name="idsociete_has_personnes",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSociete_has_personnes , idsociete_has_personnes FROM societe_has_personnes WHERE  1 ";  
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($personnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlIn($personnes_idpersonnes) ; }
			if(!empty($nopersonnes_idpersonnes)){ $sql .= " AND personnes_idpersonnes ".sqlNotIn($nopersonnes_idpersonnes) ; }
			if(!empty($fonctions)){ $sql .= " AND fonctions ".sqlIn($fonctions) ; }
			if(!empty($nofonctions)){ $sql .= " AND fonctions ".sqlNotIn($nofonctions) ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlIn($commentaires) ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotIn($nocommentaires) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomSociete_has_personnes ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeSocieteHasPersonne($name="idsociete_has_personnes",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSociete_has_personnes , idsociete_has_personnes FROM societe_has_personnes ORDER BY nomSociete_has_personnes ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeSocieteHasPersonne = new IdaeSocieteHasPersonne(); ?>