<?   
class Fournisseur
{
	var $conn = null;
	function Fournisseur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createFournisseur($params){  
		$this->conn->AutoExecute("fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("fournisseur", $params, "UPDATE", "idfournisseur = ".$idfournisseur); 
	}
	
	function deleteFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  fournisseur WHERE idfournisseur = $idfournisseur"; 
		return $this->conn->Execute($sql);
	}
	
	function getOneFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_fournisseur where 1 " ;
			if(!empty($idfournisseur)){ $sql .= " AND idfournisseur ".sqlIn($idfournisseur) ; }
			if(!empty($noidfournisseur)){ $sql .= " AND idfournisseur ".sqlNotIn($noidfournisseur) ; }
			if(!empty($idfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlIn($idfournisseur_is_societe) ; }
			if(!empty($noidfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlNotIn($noidfournisseur_is_societe) ; }
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
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlIn($beneficeSocitete) ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotIn($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlIn($estFacturableSociete) ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotIn($noestFacturableSociete) ; }
			if(!empty($commentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlIn($commentaireFournisseur) ; }
			if(!empty($nocommentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlNotIn($nocommentaireFournisseur) ; }
			if(!empty($dateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlIn($dateCreationFournisseur) ; }
			if(!empty($nodateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlNotIn($nodateCreationFournisseur) ; }
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlIn($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotIn($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlIn($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotIn($nonomType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlIn($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotIn($noordreType_fournisseur) ; }
			if(!empty($codeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlIn($codeType_fournisseur) ; }
			if(!empty($nocodeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlNotIn($nocodeType_fournisseur) ; }
			if(!empty($groupBy)){  $sql .= " group by ".$groupBy;   }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllFournisseur(){ 
		$sql="SELECT * FROM  vue_fournisseur group by idfournisseur "; 
		return $this->conn->Execute($sql) ;	
	}

	function getSelectOneFournisseur($name="idvue_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomSociete , idfournisseur FROM vue_fournisseur WHERE 1 ";  
			if(!empty($idfournisseur)){ $sql .= " AND idfournisseur ".sqlIn($idfournisseur) ; }
			if(!empty($noidfournisseur)){ $sql .= " AND idfournisseur ".sqlNotIn($noidfournisseur) ; }
			if(!empty($idfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlIn($idfournisseur_is_societe) ; }
			if(!empty($noidfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlNotIn($noidfournisseur_is_societe) ; }
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
			if(!empty($beneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlIn($beneficeSocitete) ; }
			if(!empty($nobeneficeSocitete)){ $sql .= " AND beneficeSocitete ".sqlNotIn($nobeneficeSocitete) ; }
			if(!empty($estFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlIn($estFacturableSociete) ; }
			if(!empty($noestFacturableSociete)){ $sql .= " AND estFacturableSociete ".sqlNotIn($noestFacturableSociete) ; }
			if(!empty($commentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlIn($commentaireFournisseur) ; }
			if(!empty($nocommentaireFournisseur)){ $sql .= " AND commentaireFournisseur ".sqlNotIn($nocommentaireFournisseur) ; }
			if(!empty($dateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlIn($dateCreationFournisseur) ; }
			if(!empty($nodateCreationFournisseur)){ $sql .= " AND dateCreationFournisseur ".sqlNotIn($nodateCreationFournisseur) ; }
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlIn($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotIn($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlIn($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotIn($nonomType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlIn($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotIn($noordreType_fournisseur) ; }
			if(!empty($codeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlIn($codeType_fournisseur) ; }
			if(!empty($nocodeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlNotIn($nocodeType_fournisseur) ; } 
		$sql .=" ORDER BY nomSociete ";
			if(!empty($groupBy)){  $sql .= " group by ".$groupBy;   }
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}

	function getSelectFournisseur($name="idfournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomSociete , idfournisseur FROM vue_fournisseur ORDER BY nomSociete ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassFournisseur = new Fournisseur(); ?>