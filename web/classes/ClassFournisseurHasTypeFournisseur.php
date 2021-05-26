<?   
class FournisseurHasTypeFournisseur
{
	var $conn = null;
	function FournisseurHasTypeFournisseur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createFournisseurHasTypeFournisseur($params){ 
		$this->conn->AutoExecute("fournisseur_has_type_fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateFournisseurHasTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("fournisseur_has_type_fournisseur", $params, "UPDATE", "idfournisseur_has_type_fournisseur = ".$idfournisseur_has_type_fournisseur); 
	}
	
	function deleteFournisseurHasTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  fournisseur_has_type_fournisseur WHERE idfournisseur_has_type_fournisseur = $idfournisseur_has_type_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneFournisseurHasTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from fournisseur_has_type_fournisseur where 1 " ;
			if(!empty($idfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlIn($idfournisseur_has_type_fournisseur) ; }
			if(!empty($noidfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlNotIn($noidfournisseur_has_type_fournisseur) ; }
			if(!empty($type_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlIn($type_fournisseur_idtype_fournisseur) ; }
			if(!empty($notype_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlNotIn($notype_fournisseur_idtype_fournisseur) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllFournisseurHasTypeFournisseur(){ 
		$sql="SELECT * FROM  fournisseur_has_type_fournisseur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneFournisseurHasTypeFournisseur($name="idfournisseur_has_type_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomFournisseur_has_type_fournisseur , idfournisseur_has_type_fournisseur FROM WHERE 1 ";  
			if(!empty($idfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlIn($idfournisseur_has_type_fournisseur) ; }
			if(!empty($noidfournisseur_has_type_fournisseur)){ $sql .= " AND idfournisseur_has_type_fournisseur ".sqlNotIn($noidfournisseur_has_type_fournisseur) ; }
			if(!empty($type_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlIn($type_fournisseur_idtype_fournisseur) ; }
			if(!empty($notype_fournisseur_idtype_fournisseur)){ $sql .= " AND type_fournisseur_idtype_fournisseur ".sqlNotIn($notype_fournisseur_idtype_fournisseur) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomFournisseur_has_type_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectFournisseurHasTypeFournisseur($name="idfournisseur_has_type_fournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomFournisseur_has_type_fournisseur , idfournisseur_has_type_fournisseur FROM fournisseur_has_type_fournisseur ORDER BY nomFournisseur_has_type_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassFournisseurHasTypeFournisseur = new FournisseurHasTypeFournisseur(); ?>