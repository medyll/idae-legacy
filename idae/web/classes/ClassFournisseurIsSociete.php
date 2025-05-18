<?   
class FournisseurIsSociete
{
	var $conn = null;
	function FournisseurIsSociete(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createFournisseurIsSociete($params){ 
		$this->conn->AutoExecute("fournisseur_is_societe", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateFournisseurIsSociete($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("fournisseur_is_societe", $params, "UPDATE", "idfournisseur_is_societe = ".$idfournisseur_is_societe); 
	}
	
	function deleteFournisseurIsSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  fournisseur_is_societe WHERE idfournisseur_is_societe = $idfournisseur_is_societe"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneFournisseurIsSociete($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from fournisseur_is_societe where 1 " ;
			if(!empty($idfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlIn($idfournisseur_is_societe) ; }
			if(!empty($noidfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlNotIn($noidfournisseur_is_societe) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllFournisseurIsSociete(){ 
		$sql="SELECT * FROM  fournisseur_is_societe"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneFournisseurIsSociete($name="CHOISIR ID",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT ... , ... FROM WHERE 1 ";  
			if(!empty($idfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlIn($idfournisseur_is_societe) ; }
			if(!empty($noidfournisseur_is_societe)){ $sql .= " AND idfournisseur_is_societe ".sqlNotIn($noidfournisseur_is_societe) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; } 
		$sql .=" ORDER BY ...";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectFournisseurIsSociete($name="CHOISIR ID",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT ... , ... FROM fournisseur_is_societe ORDER BY ...";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassFournisseurIsSociete = new FournisseurIsSociete(); ?>