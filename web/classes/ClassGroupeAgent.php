<?   
class GroupeAgent
{
	var $conn = null;
	function GroupeAgent(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createGroupeAgent($params){ 
		$this->conn->AutoExecute("groupe_agent", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateGroupeAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("groupe_agent", $params, "UPDATE", "idgroupe_agent = ".$idgroupe_agent); 
	}
	
	function truncate(){ 
		$sql="truncate table groupe_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function deleteGroupeAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  groupe_agent WHERE idgroupe_agent = $idgroupe_agent"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneGroupeAgent($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from groupe_agent where 1 " ;
			if(!empty($idgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlIn($idgroupe_agent) ; }
			if(!empty($noidgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlNotIn($noidgroupe_agent) ; }
			if(!empty($lt_idgroupe_agent)){ $sql .= " AND idgroupe_agent < '".$lt_idgroupe_agent."'" ; }
			if(!empty($gt_idgroupe_agent)){ $sql .= " AND idgroupe_agent > '".$gt_idgroupe_agent."'" ; }
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlIn($nomGroupe_agent) ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlNotIn($nonomGroupe_agent) ; }
			if(!empty($lt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent < '".$lt_nomGroupe_agent."'" ; }
			if(!empty($gt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent > '".$gt_nomGroupe_agent."'" ; }
			if(!empty($commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlIn($commentaireGroupe_agent) ; }
			if(!empty($nocommentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlNotIn($nocommentaireGroupe_agent) ; }
			if(!empty($lt_commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent < '".$lt_commentaireGroupe_agent."'" ; }
			if(!empty($gt_commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent > '".$gt_commentaireGroupe_agent."'" ; }
			if(!empty($dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlIn($dateCreationGroupe_agent) ; }
			if(!empty($nodateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlNotIn($nodateCreationGroupe_agent) ; }
			if(!empty($lt_dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent < '".$lt_dateCreationGroupe_agent."'" ; }
			if(!empty($gt_dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent > '".$gt_dateCreationGroupe_agent."'" ; }
			if(!empty($dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlIn($dateFinGroupe_agent) ; }
			if(!empty($nodateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlNotIn($nodateFinGroupe_agent) ; }
			if(!empty($lt_dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent < '".$lt_dateFinGroupe_agent."'" ; }
			if(!empty($gt_dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent > '".$gt_dateFinGroupe_agent."'" ; }
			if(!empty($dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlIn($dateDebutGroupe_agent) ; }
			if(!empty($nodateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlNotIn($nodateDebutGroupe_agent) ; }
			if(!empty($lt_dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent < '".$lt_dateDebutGroupe_agent."'" ; }
			if(!empty($gt_dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent > '".$gt_dateDebutGroupe_agent."'" ; }
			if(!empty($estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlIn($estActifGroupe_agent) ; }
			if(!empty($noestActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlNotIn($noestActifGroupe_agent) ; }
			if(!empty($lt_estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent < '".$lt_estActifGroupe_agent."'" ; }
			if(!empty($gt_estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent > '".$gt_estActifGroupe_agent."'" ; }
			if(!empty($ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlIn($ordreGroupe_agent) ; }
			if(!empty($noordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlNotIn($noordreGroupe_agent) ; }
			if(!empty($lt_ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent < '".$lt_ordreGroupe_agent."'" ; }
			if(!empty($gt_ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent > '".$gt_ordreGroupe_agent."'" ; }
			if(!empty($dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlIn($dateClotureGroupe_agent) ; }
			if(!empty($nodateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlNotIn($nodateClotureGroupe_agent) ; }
			if(!empty($lt_dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent < '".$lt_dateClotureGroupe_agent."'" ; }
			if(!empty($gt_dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent > '".$gt_dateClotureGroupe_agent."'" ; }
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			if(!empty($lt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent < '".$lt_codeGroupe_agent."'" ; }
			if(!empty($gt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent > '".$gt_codeGroupe_agent."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneGroupeAgent($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from groupe_agent where 1 " ;
			if(!empty($idgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlSearch($idgroupe_agent,"idgroupe_agent") ; }
			if(!empty($lt_idgroupe_agent)){ $sql .= " AND idgroupe_agent < '".$lt_idgroupe_agent."'" ; }
			if(!empty($gt_idgroupe_agent)){ $sql .= " AND idgroupe_agent > '".$gt_idgroupe_agent."'" ; }
			if(!empty($noidgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlNotSearch($noidgroupe_agent) ; }
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlSearch($nomGroupe_agent,"nomGroupe_agent") ; }
			if(!empty($lt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent < '".$lt_nomGroupe_agent."'" ; }
			if(!empty($gt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent > '".$gt_nomGroupe_agent."'" ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlNotSearch($nonomGroupe_agent) ; }
			if(!empty($commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlSearch($commentaireGroupe_agent,"commentaireGroupe_agent") ; }
			if(!empty($lt_commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent < '".$lt_commentaireGroupe_agent."'" ; }
			if(!empty($gt_commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent > '".$gt_commentaireGroupe_agent."'" ; }
			if(!empty($nocommentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlNotSearch($nocommentaireGroupe_agent) ; }
			if(!empty($dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlSearch($dateCreationGroupe_agent,"dateCreationGroupe_agent") ; }
			if(!empty($lt_dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent < '".$lt_dateCreationGroupe_agent."'" ; }
			if(!empty($gt_dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent > '".$gt_dateCreationGroupe_agent."'" ; }
			if(!empty($nodateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlNotSearch($nodateCreationGroupe_agent) ; }
			if(!empty($dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlSearch($dateFinGroupe_agent,"dateFinGroupe_agent") ; }
			if(!empty($lt_dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent < '".$lt_dateFinGroupe_agent."'" ; }
			if(!empty($gt_dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent > '".$gt_dateFinGroupe_agent."'" ; }
			if(!empty($nodateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlNotSearch($nodateFinGroupe_agent) ; }
			if(!empty($dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlSearch($dateDebutGroupe_agent,"dateDebutGroupe_agent") ; }
			if(!empty($lt_dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent < '".$lt_dateDebutGroupe_agent."'" ; }
			if(!empty($gt_dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent > '".$gt_dateDebutGroupe_agent."'" ; }
			if(!empty($nodateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlNotSearch($nodateDebutGroupe_agent) ; }
			if(!empty($estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlSearch($estActifGroupe_agent,"estActifGroupe_agent") ; }
			if(!empty($lt_estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent < '".$lt_estActifGroupe_agent."'" ; }
			if(!empty($gt_estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent > '".$gt_estActifGroupe_agent."'" ; }
			if(!empty($noestActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlNotSearch($noestActifGroupe_agent) ; }
			if(!empty($ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlSearch($ordreGroupe_agent,"ordreGroupe_agent") ; }
			if(!empty($lt_ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent < '".$lt_ordreGroupe_agent."'" ; }
			if(!empty($gt_ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent > '".$gt_ordreGroupe_agent."'" ; }
			if(!empty($noordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlNotSearch($noordreGroupe_agent) ; }
			if(!empty($dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlSearch($dateClotureGroupe_agent,"dateClotureGroupe_agent") ; }
			if(!empty($lt_dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent < '".$lt_dateClotureGroupe_agent."'" ; }
			if(!empty($gt_dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent > '".$gt_dateClotureGroupe_agent."'" ; }
			if(!empty($nodateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlNotSearch($nodateClotureGroupe_agent) ; }
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlSearch($codeGroupe_agent,"codeGroupe_agent") ; }
			if(!empty($lt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent < '".$lt_codeGroupe_agent."'" ; }
			if(!empty($gt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent > '".$gt_codeGroupe_agent."'" ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotSearch($nocodeGroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllGroupeAgent(){ 
		$sql="SELECT * FROM  groupe_agent"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneGroupeAgent($name="idgroupe_agent",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomGroupe_agent , idgroupe_agent FROM groupe_agent WHERE  1 ";  
			if(!empty($idgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlIn($idgroupe_agent) ; }
			if(!empty($gt_idgroupe_agent)){ $sql .= " AND idgroupe_agent > ".$gt_idgroupe_agent ; }
			if(!empty($noidgroupe_agent)){ $sql .= " AND idgroupe_agent ".sqlNotIn($noidgroupe_agent) ; }
			if(!empty($nomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlIn($nomGroupe_agent) ; }
			if(!empty($gt_nomGroupe_agent)){ $sql .= " AND nomGroupe_agent > ".$gt_nomGroupe_agent ; }
			if(!empty($nonomGroupe_agent)){ $sql .= " AND nomGroupe_agent ".sqlNotIn($nonomGroupe_agent) ; }
			if(!empty($commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlIn($commentaireGroupe_agent) ; }
			if(!empty($gt_commentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent > ".$gt_commentaireGroupe_agent ; }
			if(!empty($nocommentaireGroupe_agent)){ $sql .= " AND commentaireGroupe_agent ".sqlNotIn($nocommentaireGroupe_agent) ; }
			if(!empty($dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlIn($dateCreationGroupe_agent) ; }
			if(!empty($gt_dateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent > ".$gt_dateCreationGroupe_agent ; }
			if(!empty($nodateCreationGroupe_agent)){ $sql .= " AND dateCreationGroupe_agent ".sqlNotIn($nodateCreationGroupe_agent) ; }
			if(!empty($dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlIn($dateFinGroupe_agent) ; }
			if(!empty($gt_dateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent > ".$gt_dateFinGroupe_agent ; }
			if(!empty($nodateFinGroupe_agent)){ $sql .= " AND dateFinGroupe_agent ".sqlNotIn($nodateFinGroupe_agent) ; }
			if(!empty($dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlIn($dateDebutGroupe_agent) ; }
			if(!empty($gt_dateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent > ".$gt_dateDebutGroupe_agent ; }
			if(!empty($nodateDebutGroupe_agent)){ $sql .= " AND dateDebutGroupe_agent ".sqlNotIn($nodateDebutGroupe_agent) ; }
			if(!empty($estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlIn($estActifGroupe_agent) ; }
			if(!empty($gt_estActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent > ".$gt_estActifGroupe_agent ; }
			if(!empty($noestActifGroupe_agent)){ $sql .= " AND estActifGroupe_agent ".sqlNotIn($noestActifGroupe_agent) ; }
			if(!empty($ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlIn($ordreGroupe_agent) ; }
			if(!empty($gt_ordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent > ".$gt_ordreGroupe_agent ; }
			if(!empty($noordreGroupe_agent)){ $sql .= " AND ordreGroupe_agent ".sqlNotIn($noordreGroupe_agent) ; }
			if(!empty($dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlIn($dateClotureGroupe_agent) ; }
			if(!empty($gt_dateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent > ".$gt_dateClotureGroupe_agent ; }
			if(!empty($nodateClotureGroupe_agent)){ $sql .= " AND dateClotureGroupe_agent ".sqlNotIn($nodateClotureGroupe_agent) ; }
			if(!empty($codeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlIn($codeGroupe_agent) ; }
			if(!empty($gt_codeGroupe_agent)){ $sql .= " AND codeGroupe_agent > ".$gt_codeGroupe_agent ; }
			if(!empty($nocodeGroupe_agent)){ $sql .= " AND codeGroupe_agent ".sqlNotIn($nocodeGroupe_agent) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomGroupe_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectGroupeAgent($name="idgroupe_agent",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomGroupe_agent , idgroupe_agent FROM groupe_agent ORDER BY nomGroupe_agent ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassGroupeAgent = new GroupeAgent(); ?>