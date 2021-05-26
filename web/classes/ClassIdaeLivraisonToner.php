<?   
class IdaeLivraisonToner
{
	var $conn = null;
	function IdaeLivraisonToner(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLivraisonToner($params){ 
		$this->conn->AutoExecute("livraisontoner", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLivraisonToner($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("livraisontoner", $params, "UPDATE", "idlivraisontoner = ".$idlivraisontoner); 
	}
	
	function deleteIdaeLivraisonToner($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  livraisontoner WHERE idlivraisontoner = $idlivraisontoner"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLivraisonToner($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from livraisontoner where 1 " ;
			if(!empty($idlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlIn($idlivraisonToner) ; }
			if(!empty($noidlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlNotIn($noidlivraisonToner) ; }
			if(!empty($tacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlIn($tacheTechnique_idtache) ; }
			if(!empty($notacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlNotIn($notacheTechnique_idtache) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlIn($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotIn($nodateLivraison) ; }
			if(!empty($idLigne)){ $sql .= " AND idLigne ".sqlIn($idLigne) ; }
			if(!empty($noidLigne)){ $sql .= " AND idLigne ".sqlNotIn($noidLigne) ; }
			if(!empty($arrayIdToner)){ $sql .= " AND arrayIdToner ".sqlIn($arrayIdToner) ; }
			if(!empty($noarrayIdToner)){ $sql .= " AND arrayIdToner ".sqlNotIn($noarrayIdToner) ; }
			if(!empty($idLivreur)){ $sql .= " AND idLivreur ".sqlIn($idLivreur) ; }
			if(!empty($noidLivreur)){ $sql .= " AND idLivreur ".sqlNotIn($noidLivreur) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlIn($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotIn($nocreerle) ; }
			if(!empty($numeroInterne)){ $sql .= " AND numeroInterne ".sqlIn($numeroInterne) ; }
			if(!empty($nonumeroInterne)){ $sql .= " AND numeroInterne ".sqlNotIn($nonumeroInterne) ; }
			if(!empty($numeroExterne)){ $sql .= " AND numeroExterne ".sqlIn($numeroExterne) ; }
			if(!empty($nonumeroExterne)){ $sql .= " AND numeroExterne ".sqlNotIn($nonumeroExterne) ; }
			if(!empty($arrayIdStock)){ $sql .= " AND arrayIdStock ".sqlIn($arrayIdStock) ; }
			if(!empty($noarrayIdStock)){ $sql .= " AND arrayIdStock ".sqlNotIn($noarrayIdStock) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLivraisonToner($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from livraisontoner where 1 " ;
			if(!empty($idlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlSearch($idlivraisonToner) ; }
			if(!empty($noidlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlNotSearch($noidlivraisonToner) ; }
			if(!empty($tacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlSearch($tacheTechnique_idtache) ; }
			if(!empty($notacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlNotSearch($notacheTechnique_idtache) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlSearch($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotSearch($nodateLivraison) ; }
			if(!empty($idLigne)){ $sql .= " AND idLigne ".sqlSearch($idLigne) ; }
			if(!empty($noidLigne)){ $sql .= " AND idLigne ".sqlNotSearch($noidLigne) ; }
			if(!empty($arrayIdToner)){ $sql .= " AND arrayIdToner ".sqlSearch($arrayIdToner) ; }
			if(!empty($noarrayIdToner)){ $sql .= " AND arrayIdToner ".sqlNotSearch($noarrayIdToner) ; }
			if(!empty($idLivreur)){ $sql .= " AND idLivreur ".sqlSearch($idLivreur) ; }
			if(!empty($noidLivreur)){ $sql .= " AND idLivreur ".sqlNotSearch($noidLivreur) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlSearch($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotSearch($nocreerle) ; }
			if(!empty($numeroInterne)){ $sql .= " AND numeroInterne ".sqlSearch($numeroInterne) ; }
			if(!empty($nonumeroInterne)){ $sql .= " AND numeroInterne ".sqlNotSearch($nonumeroInterne) ; }
			if(!empty($numeroExterne)){ $sql .= " AND numeroExterne ".sqlSearch($numeroExterne) ; }
			if(!empty($nonumeroExterne)){ $sql .= " AND numeroExterne ".sqlNotSearch($nonumeroExterne) ; }
			if(!empty($arrayIdStock)){ $sql .= " AND arrayIdStock ".sqlSearch($arrayIdStock) ; }
			if(!empty($noarrayIdStock)){ $sql .= " AND arrayIdStock ".sqlNotSearch($noarrayIdStock) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLivraisonToner(){ 
		$sql="SELECT * FROM  livraisontoner"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLivraisonToner($name="idlivraisontoner",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLivraisontoner , idlivraisontoner FROM livraisontoner WHERE  1 ";  
			if(!empty($idlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlIn($idlivraisonToner) ; }
			if(!empty($noidlivraisonToner)){ $sql .= " AND idlivraisonToner ".sqlNotIn($noidlivraisonToner) ; }
			if(!empty($tacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlIn($tacheTechnique_idtache) ; }
			if(!empty($notacheTechnique_idtache)){ $sql .= " AND tacheTechnique_idtache ".sqlNotIn($notacheTechnique_idtache) ; }
			if(!empty($dateLivraison)){ $sql .= " AND dateLivraison ".sqlIn($dateLivraison) ; }
			if(!empty($nodateLivraison)){ $sql .= " AND dateLivraison ".sqlNotIn($nodateLivraison) ; }
			if(!empty($idLigne)){ $sql .= " AND idLigne ".sqlIn($idLigne) ; }
			if(!empty($noidLigne)){ $sql .= " AND idLigne ".sqlNotIn($noidLigne) ; }
			if(!empty($arrayIdToner)){ $sql .= " AND arrayIdToner ".sqlIn($arrayIdToner) ; }
			if(!empty($noarrayIdToner)){ $sql .= " AND arrayIdToner ".sqlNotIn($noarrayIdToner) ; }
			if(!empty($idLivreur)){ $sql .= " AND idLivreur ".sqlIn($idLivreur) ; }
			if(!empty($noidLivreur)){ $sql .= " AND idLivreur ".sqlNotIn($noidLivreur) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlIn($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotIn($nocreerle) ; }
			if(!empty($numeroInterne)){ $sql .= " AND numeroInterne ".sqlIn($numeroInterne) ; }
			if(!empty($nonumeroInterne)){ $sql .= " AND numeroInterne ".sqlNotIn($nonumeroInterne) ; }
			if(!empty($numeroExterne)){ $sql .= " AND numeroExterne ".sqlIn($numeroExterne) ; }
			if(!empty($nonumeroExterne)){ $sql .= " AND numeroExterne ".sqlNotIn($nonumeroExterne) ; }
			if(!empty($arrayIdStock)){ $sql .= " AND arrayIdStock ".sqlIn($arrayIdStock) ; }
			if(!empty($noarrayIdStock)){ $sql .= " AND arrayIdStock ".sqlNotIn($noarrayIdStock) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLivraisontoner ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLivraisonToner($name="idlivraisontoner",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLivraisontoner , idlivraisontoner FROM livraisontoner ORDER BY nomLivraisontoner ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLivraisonToner = new IdaeLivraisonToner(); ?>