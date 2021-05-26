<?   
class IdaeSociete
{
	var $conn = null;
	function IdaeSociete(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeSociete($params){ 
		$this->conn->AutoExecute("vue_societe", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_societe", $params, "UPDATE", "idvue_societe = ".$idvue_societe); 
	}
	
	function deleteIdaeSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_societe WHERE idvue_societe = $idvue_societe"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_societe where 1 " ;
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlIn($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotIn($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlIn($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotIn($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlIn($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotIn($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlIn($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotIn($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlIn($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotIn($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlIn($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotIn($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlIn($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotIn($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlIn($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotIn($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlIn($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotIn($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlIn($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotIn($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlIn($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotIn($nonbSalarieSociete) ; }
			if(!empty($beneficeSociete)){ $sql .= " AND beneficeSociete ".sqlIn($beneficeSociete) ; }
			if(!empty($nobeneficeSociete)){ $sql .= " AND beneficeSociete ".sqlNotIn($nobeneficeSociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_societe where 1 " ;
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlSearch($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotSearch($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlSearch($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotSearch($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlSearch($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotSearch($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlSearch($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotSearch($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlSearch($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotSearch($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlSearch($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotSearch($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlSearch($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotSearch($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlSearch($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotSearch($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlSearch($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotSearch($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlSearch($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotSearch($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlSearch($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotSearch($nonbSalarieSociete) ; }
			if(!empty($beneficeSociete)){ $sql .= " AND beneficeSociete ".sqlSearch($beneficeSociete) ; }
			if(!empty($nobeneficeSociete)){ $sql .= " AND beneficeSociete ".sqlNotSearch($nobeneficeSociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeSociete(){ 
		$sql="SELECT * FROM  vue_societe"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeSociete($name="idvue_societe",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_societe , idvue_societe FROM vue_societe WHERE  1 ";  
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($siretSociete)){ $sql .= " AND siretSociete ".sqlIn($siretSociete) ; }
			if(!empty($nosiretSociete)){ $sql .= " AND siretSociete ".sqlNotIn($nosiretSociete) ; }
			if(!empty($sirenSociete)){ $sql .= " AND sirenSociete ".sqlIn($sirenSociete) ; }
			if(!empty($nosirenSociete)){ $sql .= " AND sirenSociete ".sqlNotIn($nosirenSociete) ; }
			if(!empty($apeSociete)){ $sql .= " AND apeSociete ".sqlIn($apeSociete) ; }
			if(!empty($noapeSociete)){ $sql .= " AND apeSociete ".sqlNotIn($noapeSociete) ; }
			if(!empty($tvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlIn($tvaIntraSociete) ; }
			if(!empty($notvaIntraSociete)){ $sql .= " AND tvaIntraSociete ".sqlNotIn($notvaIntraSociete) ; }
			if(!empty($formeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlIn($formeJuridiqueSociete) ; }
			if(!empty($noformeJuridiqueSociete)){ $sql .= " AND formeJuridiqueSociete ".sqlNotIn($noformeJuridiqueSociete) ; }
			if(!empty($capitalSociete)){ $sql .= " AND capitalSociete ".sqlIn($capitalSociete) ; }
			if(!empty($nocapitalSociete)){ $sql .= " AND capitalSociete ".sqlNotIn($nocapitalSociete) ; }
			if(!empty($deviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlIn($deviseCapitalSociete) ; }
			if(!empty($nodeviseCapitalSociete)){ $sql .= " AND deviseCapitalSociete ".sqlNotIn($nodeviseCapitalSociete) ; }
			if(!empty($deviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlIn($deviseFacturationSociete) ; }
			if(!empty($nodeviseFacturationSociete)){ $sql .= " AND deviseFacturationSociete ".sqlNotIn($nodeviseFacturationSociete) ; }
			if(!empty($activiteSociete)){ $sql .= " AND activiteSociete ".sqlIn($activiteSociete) ; }
			if(!empty($noactiviteSociete)){ $sql .= " AND activiteSociete ".sqlNotIn($noactiviteSociete) ; }
			if(!empty($chiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlIn($chiffreAffaireSociete) ; }
			if(!empty($nochiffreAffaireSociete)){ $sql .= " AND chiffreAffaireSociete ".sqlNotIn($nochiffreAffaireSociete) ; }
			if(!empty($nbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlIn($nbSalarieSociete) ; }
			if(!empty($nonbSalarieSociete)){ $sql .= " AND nbSalarieSociete ".sqlNotIn($nonbSalarieSociete) ; }
			if(!empty($beneficeSociete)){ $sql .= " AND beneficeSociete ".sqlIn($beneficeSociete) ; }
			if(!empty($nobeneficeSociete)){ $sql .= " AND beneficeSociete ".sqlNotIn($nobeneficeSociete) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_societe ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeSociete($name="idvue_societe",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_societe , idvue_societe FROM vue_societe ORDER BY nomVue_societe ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeSociete = new IdaeSociete(); ?>