<?   
class IdaeTacheTech
{
	var $conn = null;
	function IdaeTacheTech(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeTacheTech($params){ 
		$this->conn->AutoExecute("vue_tache_tech", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeTacheTech($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_tache_tech", $params, "UPDATE", "idvue_tache_tech = ".$idvue_tache_tech); 
	}
	
	function deleteIdaeTacheTech($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_tache_tech WHERE idvue_tache_tech = $idvue_tache_tech"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeTacheTech($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_tache_tech where 1 " ;
		if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlIn($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotIn($nocommentaire) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlIn($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotIn($nocreerle) ; }
			if(!empty($idAffectation)){ $sql .= " AND idAffectation ".sqlIn($idAffectation) ; }
			if(isset($noidAffectation)){ $sql .= " AND idAffectation ".sqlNotIn($noidAffectation) ; }
			if(!empty($dateIntervention)){ $sql .= " AND dateIntervention ".sqlIn($dateIntervention) ; }
			if(!empty($nodateIntervention)){ $sql .= " AND dateIntervention ".sqlNotIn($nodateIntervention) ; }
			if(!empty($dateCloture)){ $sql .= " AND dateCloture ".sqlIn($dateCloture) ; }
			if(!empty($nodateCloture)){ $sql .= " AND dateCloture ".sqlNotIn($nodateCloture) ; }
			if(!empty($objet)){ $sql .= " AND objet ".sqlIn($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotIn($noobjet) ; }
			if(!empty($heureIntervention)){ $sql .= " AND heureIntervention ".sqlIn($heureIntervention) ; }
			if(!empty($noheureIntervention)){ $sql .= " AND heureIntervention ".sqlNotIn($noheureIntervention) ; }
			if(!empty($heureCloture)){ $sql .= " AND heureCloture ".sqlIn($heureCloture) ; }
			if(!empty($noheureCloture)){ $sql .= " AND heureCloture ".sqlNotIn($noheureCloture) ; }
			if(!empty($statutTache)){ $sql .= " AND statutTache ".sqlIn($statutTache) ; }
			if(!empty($nostatutTache)){ $sql .= " AND statutTache ".sqlNotIn($nostatutTache) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlIn($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotIn($noidTachePere) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($idcontact)){ $sql .= " AND idcontact ".sqlIn($idcontact) ; }
			if(!empty($noidcontact)){ $sql .= " AND idcontact ".sqlNotIn($noidcontact) ; }
			if(!empty($autreContact)){ $sql .= " AND autreContact ".sqlIn($autreContact) ; }
			if(!empty($noautreContact)){ $sql .= " AND autreContact ".sqlNotIn($noautreContact) ; }
			if(!empty($numSerie)){ $sql .= " AND numSerie ".sqlIn($numSerie) ; }
			if(!empty($nonumSerie)){ $sql .= " AND numSerie ".sqlNotIn($nonumSerie) ; }
			if(!empty($typeTache)){ $sql .= " AND typeTache ".sqlIn($typeTache) ; }
			if(!empty($notypeTache)){ $sql .= " AND typeTache ".sqlNotIn($notypeTache) ; }
			if(!empty($codeErreur)){ $sql .= " AND codeErreur ".sqlIn($codeErreur) ; }
			if(!empty($nocodeErreur)){ $sql .= " AND codeErreur ".sqlNotIn($nocodeErreur) ; }
			if(!empty($idligneParc)){ $sql .= " AND idligneParc ".sqlIn($idligneParc) ; }
			if(!empty($noidligneParc)){ $sql .= " AND idligneParc ".sqlNotIn($noidligneParc) ; }
			if(!empty($idUser)){ $sql .= " AND idUser ".sqlIn($idUser) ; }
			if(!empty($noidUser)){ $sql .= " AND idUser ".sqlNotIn($noidUser) ; }
			if(!empty($commentaireFin)){ $sql .= " AND commentaireFin ".sqlIn($commentaireFin) ; }
			if(!empty($nocommentaireFin)){ $sql .= " AND commentaireFin ".sqlNotIn($nocommentaireFin) ; }
			if(!empty($heureCreele)){ $sql .= " AND heureCreele ".sqlIn($heureCreele) ; }
			if(!empty($noheureCreele)){ $sql .= " AND heureCreele ".sqlNotIn($noheureCreele) ; }
			if(!empty($reference)){ $sql .= " AND reference ".sqlIn($reference) ; }
			if(!empty($noreference)){ $sql .= " AND reference ".sqlNotIn($noreference) ; }
			if(!empty($commentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlIn($commentaireLivraison) ; }
			if(!empty($nocommentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlNotIn($nocommentaireLivraison) ; }
			if(!empty($telephone)){ $sql .= " AND telephone ".sqlIn($telephone) ; }
			if(!empty($notelephone)){ $sql .= " AND telephone ".sqlNotIn($notelephone) ; }
			if(!empty($idmachine)){ $sql .= " AND idmachine ".sqlIn($idmachine) ; }
			if(!empty($noidmachine)){ $sql .= " AND idmachine ".sqlNotIn($noidmachine) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeTacheTech($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_tache_tech where 1 " ;
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlSearch($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotSearch($nocommentaire) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlSearch($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotSearch($nocreerle) ; }
			if(!empty($idAffectation)){ $sql .= " AND idAffectation ".sqlSearch($idAffectation) ; }
			if(!empty($noidAffectation)){ $sql .= " AND idAffectation ".sqlNotSearch($noidAffectation) ; }
			if(!empty($dateIntervention)){ $sql .= " AND dateIntervention ".sqlSearch($dateIntervention) ; }
			if(!empty($nodateIntervention)){ $sql .= " AND dateIntervention ".sqlNotSearch($nodateIntervention) ; }
			if(!empty($dateCloture)){ $sql .= " AND dateCloture ".sqlSearch($dateCloture) ; }
			if(!empty($nodateCloture)){ $sql .= " AND dateCloture ".sqlNotSearch($nodateCloture) ; }
			if(!empty($objet)){ $sql .= " AND objet ".sqlSearch($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotSearch($noobjet) ; }
			if(!empty($heureIntervention)){ $sql .= " AND heureIntervention ".sqlSearch($heureIntervention) ; }
			if(!empty($noheureIntervention)){ $sql .= " AND heureIntervention ".sqlNotSearch($noheureIntervention) ; }
			if(!empty($heureCloture)){ $sql .= " AND heureCloture ".sqlSearch($heureCloture) ; }
			if(!empty($noheureCloture)){ $sql .= " AND heureCloture ".sqlNotSearch($noheureCloture) ; }
			if(!empty($statutTache)){ $sql .= " AND statutTache ".sqlSearch($statutTache) ; }
			if(!empty($nostatutTache)){ $sql .= " AND statutTache ".sqlNotSearch($nostatutTache) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlSearch($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotSearch($noidTachePere) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($idcontact)){ $sql .= " AND idcontact ".sqlSearch($idcontact) ; }
			if(!empty($noidcontact)){ $sql .= " AND idcontact ".sqlNotSearch($noidcontact) ; }
			if(!empty($autreContact)){ $sql .= " AND autreContact ".sqlSearch($autreContact) ; }
			if(!empty($noautreContact)){ $sql .= " AND autreContact ".sqlNotSearch($noautreContact) ; }
			if(!empty($numSerie)){ $sql .= " AND numSerie ".sqlSearch($numSerie) ; }
			if(!empty($nonumSerie)){ $sql .= " AND numSerie ".sqlNotSearch($nonumSerie) ; }
			if(!empty($typeTache)){ $sql .= " AND typeTache ".sqlSearch($typeTache) ; }
			if(!empty($notypeTache)){ $sql .= " AND typeTache ".sqlNotSearch($notypeTache) ; }
			if(!empty($codeErreur)){ $sql .= " AND codeErreur ".sqlSearch($codeErreur) ; }
			if(!empty($nocodeErreur)){ $sql .= " AND codeErreur ".sqlNotSearch($nocodeErreur) ; }
			if(!empty($idligneParc)){ $sql .= " AND idligneParc ".sqlSearch($idligneParc) ; }
			if(!empty($noidligneParc)){ $sql .= " AND idligneParc ".sqlNotSearch($noidligneParc) ; }
			if(!empty($idUser)){ $sql .= " AND idUser ".sqlSearch($idUser) ; }
			if(!empty($noidUser)){ $sql .= " AND idUser ".sqlNotSearch($noidUser) ; }
			if(!empty($commentaireFin)){ $sql .= " AND commentaireFin ".sqlSearch($commentaireFin) ; }
			if(!empty($nocommentaireFin)){ $sql .= " AND commentaireFin ".sqlNotSearch($nocommentaireFin) ; }
			if(!empty($heureCreele)){ $sql .= " AND heureCreele ".sqlSearch($heureCreele) ; }
			if(!empty($noheureCreele)){ $sql .= " AND heureCreele ".sqlNotSearch($noheureCreele) ; }
			if(!empty($reference)){ $sql .= " AND reference ".sqlSearch($reference) ; }
			if(!empty($noreference)){ $sql .= " AND reference ".sqlNotSearch($noreference) ; }
			if(!empty($commentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlSearch($commentaireLivraison) ; }
			if(!empty($nocommentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlNotSearch($nocommentaireLivraison) ; }
			if(!empty($telephone)){ $sql .= " AND telephone ".sqlSearch($telephone) ; }
			if(!empty($notelephone)){ $sql .= " AND telephone ".sqlNotSearch($notelephone) ; }
			if(!empty($idmachine)){ $sql .= " AND idmachine ".sqlSearch($idmachine) ; }
			if(!empty($noidmachine)){ $sql .= " AND idmachine ".sqlNotSearch($noidmachine) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeTacheTech(){ 
		$sql="SELECT * FROM  vue_tache_tech"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeTacheTech($name="idvue_tache_tech",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_tache_tech , idvue_tache_tech FROM vue_tache_tech WHERE  1 ";  
			if(!empty($commentaire)){ $sql .= " AND commentaire ".sqlIn($commentaire) ; }
			if(!empty($nocommentaire)){ $sql .= " AND commentaire ".sqlNotIn($nocommentaire) ; }
			if(!empty($creerle)){ $sql .= " AND creerle ".sqlIn($creerle) ; }
			if(!empty($nocreerle)){ $sql .= " AND creerle ".sqlNotIn($nocreerle) ; }
			if(!empty($idAffectation)){ $sql .= " AND idAffectation ".sqlIn($idAffectation) ; }
			if(!empty($noidAffectation)){ $sql .= " AND idAffectation ".sqlNotIn($noidAffectation) ; }
			if(!empty($dateIntervention)){ $sql .= " AND dateIntervention ".sqlIn($dateIntervention) ; }
			if(!empty($nodateIntervention)){ $sql .= " AND dateIntervention ".sqlNotIn($nodateIntervention) ; }
			if(!empty($dateCloture)){ $sql .= " AND dateCloture ".sqlIn($dateCloture) ; }
			if(!empty($nodateCloture)){ $sql .= " AND dateCloture ".sqlNotIn($nodateCloture) ; }
			if(!empty($objet)){ $sql .= " AND objet ".sqlIn($objet) ; }
			if(!empty($noobjet)){ $sql .= " AND objet ".sqlNotIn($noobjet) ; }
			if(!empty($heureIntervention)){ $sql .= " AND heureIntervention ".sqlIn($heureIntervention) ; }
			if(!empty($noheureIntervention)){ $sql .= " AND heureIntervention ".sqlNotIn($noheureIntervention) ; }
			if(!empty($heureCloture)){ $sql .= " AND heureCloture ".sqlIn($heureCloture) ; }
			if(!empty($noheureCloture)){ $sql .= " AND heureCloture ".sqlNotIn($noheureCloture) ; }
			if(!empty($statutTache)){ $sql .= " AND statutTache ".sqlIn($statutTache) ; }
			if(!empty($nostatutTache)){ $sql .= " AND statutTache ".sqlNotIn($nostatutTache) ; }
			if(!empty($idTachePere)){ $sql .= " AND idTachePere ".sqlIn($idTachePere) ; }
			if(!empty($noidTachePere)){ $sql .= " AND idTachePere ".sqlNotIn($noidTachePere) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($idcontact)){ $sql .= " AND idcontact ".sqlIn($idcontact) ; }
			if(!empty($noidcontact)){ $sql .= " AND idcontact ".sqlNotIn($noidcontact) ; }
			if(!empty($autreContact)){ $sql .= " AND autreContact ".sqlIn($autreContact) ; }
			if(!empty($noautreContact)){ $sql .= " AND autreContact ".sqlNotIn($noautreContact) ; }
			if(!empty($numSerie)){ $sql .= " AND numSerie ".sqlIn($numSerie) ; }
			if(!empty($nonumSerie)){ $sql .= " AND numSerie ".sqlNotIn($nonumSerie) ; }
			if(!empty($typeTache)){ $sql .= " AND typeTache ".sqlIn($typeTache) ; }
			if(!empty($notypeTache)){ $sql .= " AND typeTache ".sqlNotIn($notypeTache) ; }
			if(!empty($codeErreur)){ $sql .= " AND codeErreur ".sqlIn($codeErreur) ; }
			if(!empty($nocodeErreur)){ $sql .= " AND codeErreur ".sqlNotIn($nocodeErreur) ; }
			if(!empty($idligneParc)){ $sql .= " AND idligneParc ".sqlIn($idligneParc) ; }
			if(!empty($noidligneParc)){ $sql .= " AND idligneParc ".sqlNotIn($noidligneParc) ; }
			if(!empty($idUser)){ $sql .= " AND idUser ".sqlIn($idUser) ; }
			if(!empty($noidUser)){ $sql .= " AND idUser ".sqlNotIn($noidUser) ; }
			if(!empty($commentaireFin)){ $sql .= " AND commentaireFin ".sqlIn($commentaireFin) ; }
			if(!empty($nocommentaireFin)){ $sql .= " AND commentaireFin ".sqlNotIn($nocommentaireFin) ; }
			if(!empty($heureCreele)){ $sql .= " AND heureCreele ".sqlIn($heureCreele) ; }
			if(!empty($noheureCreele)){ $sql .= " AND heureCreele ".sqlNotIn($noheureCreele) ; }
			if(!empty($reference)){ $sql .= " AND reference ".sqlIn($reference) ; }
			if(!empty($noreference)){ $sql .= " AND reference ".sqlNotIn($noreference) ; }
			if(!empty($commentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlIn($commentaireLivraison) ; }
			if(!empty($nocommentaireLivraison)){ $sql .= " AND commentaireLivraison ".sqlNotIn($nocommentaireLivraison) ; }
			if(!empty($telephone)){ $sql .= " AND telephone ".sqlIn($telephone) ; }
			if(!empty($notelephone)){ $sql .= " AND telephone ".sqlNotIn($notelephone) ; }
			if(!empty($idmachine)){ $sql .= " AND idmachine ".sqlIn($idmachine) ; }
			if(!empty($noidmachine)){ $sql .= " AND idmachine ".sqlNotIn($noidmachine) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_tache_tech ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeTacheTech($name="idvue_tache_tech",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_tache_tech , idvue_tache_tech FROM vue_tache_tech ORDER BY nomVue_tache_tech ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeTacheTech = new IdaeTacheTech(); ?>