<?   
class IdaeMaterielHasCompteur
{
	var $conn = null;
	function IdaeMaterielHasCompteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeMaterielHasCompteur($params){ 
		$this->conn->AutoExecute("vue_materiel_has_compteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_materiel_has_compteur", $params, "UPDATE", "idvue_materiel_has_compteur = ".$idvue_materiel_has_compteur); 
	}
	
	function deleteIdaeMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_materiel_has_compteur WHERE idvue_materiel_has_compteur = $idvue_materiel_has_compteur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_materiel_has_compteur where 1 " ;
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlIn($compteur_idcompteur) ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotIn($nocompteur_idcompteur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_materiel_has_compteur where 1 " ;
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlSearch($compteur_idcompteur,"compteur_idcompteur") ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotSearch($nocompteur_idcompteur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel,"materiel_idmateriel") ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeMaterielHasCompteur(){ 
		$sql="SELECT * FROM  vue_materiel_has_compteur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeMaterielHasCompteur($name="idvue_materiel_has_compteur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_materiel_has_compteur , idvue_materiel_has_compteur FROM vue_materiel_has_compteur WHERE  1 ";  
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlIn($compteur_idcompteur) ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotIn($nocompteur_idcompteur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_materiel_has_compteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeMaterielHasCompteur($name="idvue_materiel_has_compteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_materiel_has_compteur , idvue_materiel_has_compteur FROM vue_materiel_has_compteur ORDER BY nomVue_materiel_has_compteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeMaterielHasCompteur = new IdaeMaterielHasCompteur(); ?>