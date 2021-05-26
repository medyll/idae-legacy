<?   
class IdaeContratEntretien
{
	var $conn = null;
	function IdaeContratEntretien(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeContratEntretien($params){ 
		$this->conn->AutoExecute("contratentretien", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeContratEntretien($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("contratentretien", $params, "UPDATE", "idcontratentretien = ".$idcontratentretien); 
	}
	
	function deleteIdaeContratEntretien($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  contratentretien WHERE idcontratentretien = $idcontratentretien"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeContratEntretien($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from contratentretien where 1 " ;
			if(!empty($idcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlIn($idcontratEntretien) ; }
			if(!empty($noidcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlNotIn($noidcontratEntretien) ; }
			if(!empty($coutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlIn($coutImpressionSupNoir) ; }
			if(!empty($nocoutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlNotIn($nocoutImpressionSupNoir) ; }
			if(!empty($abnServTrim)){ $sql .= " AND abnServTrim ".sqlIn($abnServTrim) ; }
			if(!empty($noabnServTrim)){ $sql .= " AND abnServTrim ".sqlNotIn($noabnServTrim) ; }
			if(!empty($participFraisInstall)){ $sql .= " AND participFraisInstall ".sqlIn($participFraisInstall) ; }
			if(!empty($noparticipFraisInstall)){ $sql .= " AND participFraisInstall ".sqlNotIn($noparticipFraisInstall) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeTrim)){ $sql .= " AND dureeTrim ".sqlIn($dureeTrim) ; }
			if(!empty($nodureeTrim)){ $sql .= " AND dureeTrim ".sqlNotIn($nodureeTrim) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlIn($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotIn($nodateDebut) ; }
			if(!empty($toners)){ $sql .= " AND toners ".sqlIn($toners) ; }
			if(!empty($notoners)){ $sql .= " AND toners ".sqlNotIn($notoners) ; }
			if(!empty($fraisLivraison)){ $sql .= " AND fraisLivraison ".sqlIn($fraisLivraison) ; }
			if(!empty($nofraisLivraison)){ $sql .= " AND fraisLivraison ".sqlNotIn($nofraisLivraison) ; }
			if(!empty($categorieTransport)){ $sql .= " AND categorieTransport ".sqlIn($categorieTransport) ; }
			if(!empty($nocategorieTransport)){ $sql .= " AND categorieTransport ".sqlNotIn($nocategorieTransport) ; }
			if(!empty($coutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlIn($coutImpressionSupCouleur) ; }
			if(!empty($nocoutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlNotIn($nocoutImpressionSupCouleur) ; }
			if(!empty($refCe)){ $sql .= " AND refCe ".sqlIn($refCe) ; }
			if(!empty($norefCe)){ $sql .= " AND refCe ".sqlNotIn($norefCe) ; }
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlIn($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotIn($noidparcs) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeContratEntretien($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from contratentretien where 1 " ;
			if(!empty($idcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlSearch($idcontratEntretien) ; }
			if(!empty($noidcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlNotSearch($noidcontratEntretien) ; }
			if(!empty($coutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlSearch($coutImpressionSupNoir) ; }
			if(!empty($nocoutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlNotSearch($nocoutImpressionSupNoir) ; }
			if(!empty($abnServTrim)){ $sql .= " AND abnServTrim ".sqlSearch($abnServTrim) ; }
			if(!empty($noabnServTrim)){ $sql .= " AND abnServTrim ".sqlNotSearch($noabnServTrim) ; }
			if(!empty($participFraisInstall)){ $sql .= " AND participFraisInstall ".sqlSearch($participFraisInstall) ; }
			if(!empty($noparticipFraisInstall)){ $sql .= " AND participFraisInstall ".sqlNotSearch($noparticipFraisInstall) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlSearch($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotSearch($nonumContrat) ; }
			if(!empty($dureeTrim)){ $sql .= " AND dureeTrim ".sqlSearch($dureeTrim) ; }
			if(!empty($nodureeTrim)){ $sql .= " AND dureeTrim ".sqlNotSearch($nodureeTrim) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlSearch($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotSearch($nodateDebut) ; }
			if(!empty($toners)){ $sql .= " AND toners ".sqlSearch($toners) ; }
			if(!empty($notoners)){ $sql .= " AND toners ".sqlNotSearch($notoners) ; }
			if(!empty($fraisLivraison)){ $sql .= " AND fraisLivraison ".sqlSearch($fraisLivraison) ; }
			if(!empty($nofraisLivraison)){ $sql .= " AND fraisLivraison ".sqlNotSearch($nofraisLivraison) ; }
			if(!empty($categorieTransport)){ $sql .= " AND categorieTransport ".sqlSearch($categorieTransport) ; }
			if(!empty($nocategorieTransport)){ $sql .= " AND categorieTransport ".sqlNotSearch($nocategorieTransport) ; }
			if(!empty($coutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlSearch($coutImpressionSupCouleur) ; }
			if(!empty($nocoutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlNotSearch($nocoutImpressionSupCouleur) ; }
			if(!empty($refCe)){ $sql .= " AND refCe ".sqlSearch($refCe) ; }
			if(!empty($norefCe)){ $sql .= " AND refCe ".sqlNotSearch($norefCe) ; }
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlSearch($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotSearch($noidparcs) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeContratEntretien(){ 
		$sql="SELECT * FROM  contratentretien"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeContratEntretien($name="idcontratentretien",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomContratentretien , idcontratentretien FROM contratentretien WHERE  1 ";  
			if(!empty($idcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlIn($idcontratEntretien) ; }
			if(!empty($noidcontratEntretien)){ $sql .= " AND idcontratEntretien ".sqlNotIn($noidcontratEntretien) ; }
			if(!empty($coutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlIn($coutImpressionSupNoir) ; }
			if(!empty($nocoutImpressionSupNoir)){ $sql .= " AND coutImpressionSupNoir ".sqlNotIn($nocoutImpressionSupNoir) ; }
			if(!empty($abnServTrim)){ $sql .= " AND abnServTrim ".sqlIn($abnServTrim) ; }
			if(!empty($noabnServTrim)){ $sql .= " AND abnServTrim ".sqlNotIn($noabnServTrim) ; }
			if(!empty($participFraisInstall)){ $sql .= " AND participFraisInstall ".sqlIn($participFraisInstall) ; }
			if(!empty($noparticipFraisInstall)){ $sql .= " AND participFraisInstall ".sqlNotIn($noparticipFraisInstall) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeTrim)){ $sql .= " AND dureeTrim ".sqlIn($dureeTrim) ; }
			if(!empty($nodureeTrim)){ $sql .= " AND dureeTrim ".sqlNotIn($nodureeTrim) ; }
			if(!empty($dateDebut)){ $sql .= " AND dateDebut ".sqlIn($dateDebut) ; }
			if(!empty($nodateDebut)){ $sql .= " AND dateDebut ".sqlNotIn($nodateDebut) ; }
			if(!empty($toners)){ $sql .= " AND toners ".sqlIn($toners) ; }
			if(!empty($notoners)){ $sql .= " AND toners ".sqlNotIn($notoners) ; }
			if(!empty($fraisLivraison)){ $sql .= " AND fraisLivraison ".sqlIn($fraisLivraison) ; }
			if(!empty($nofraisLivraison)){ $sql .= " AND fraisLivraison ".sqlNotIn($nofraisLivraison) ; }
			if(!empty($categorieTransport)){ $sql .= " AND categorieTransport ".sqlIn($categorieTransport) ; }
			if(!empty($nocategorieTransport)){ $sql .= " AND categorieTransport ".sqlNotIn($nocategorieTransport) ; }
			if(!empty($coutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlIn($coutImpressionSupCouleur) ; }
			if(!empty($nocoutImpressionSupCouleur)){ $sql .= " AND coutImpressionSupCouleur ".sqlNotIn($nocoutImpressionSupCouleur) ; }
			if(!empty($refCe)){ $sql .= " AND refCe ".sqlIn($refCe) ; }
			if(!empty($norefCe)){ $sql .= " AND refCe ".sqlNotIn($norefCe) ; }
			if(!empty($idparcs)){ $sql .= " AND idparcs ".sqlIn($idparcs) ; }
			if(!empty($noidparcs)){ $sql .= " AND idparcs ".sqlNotIn($noidparcs) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomContratentretien ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeContratEntretien($name="idcontratentretien",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomContratentretien , idcontratentretien FROM contratentretien ORDER BY nomContratentretien ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeContratEntretien = new IdaeContratEntretien(); ?>