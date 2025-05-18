<?   
class TypeCritereLigneVente
{
	var $conn = null;
	function TypeCritereLigneVente(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeCritereLigneVente($params){ 
		$this->conn->AutoExecute("type_critere_ligne_vente", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeCritereLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_critere_ligne_vente", $params, "UPDATE", "idtype_critere_ligne_vente = ".$idtype_critere_ligne_vente); 
	}
	
	function deleteTypeCritereLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_critere_ligne_vente WHERE idtype_critere_ligne_vente = $idtype_critere_ligne_vente"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeCritereLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_critere_ligne_vente where 1 " ;
			if(!empty($idtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlIn($idtype_critere_ligne_vente) ; }
			if(!empty($noidtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlNotIn($noidtype_critere_ligne_vente) ; }
			if(!empty($type_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlIn($type_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($notype_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlNotIn($notype_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($nomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlIn($nomType_critere_ligne_vente) ; }
			if(!empty($nonomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlNotIn($nonomType_critere_ligne_vente) ; }
			if(!empty($uniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlIn($uniteType_critere_ligne_vente) ; }
			if(!empty($nouniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlNotIn($nouniteType_critere_ligne_vente) ; }
			if(!empty($codeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlIn($codeType_critere_ligne_vente) ; }
			if(!empty($nocodeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlNotIn($nocodeType_critere_ligne_vente) ; }
			if(!empty($ordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlIn($ordreType_critere_ligne_vente) ; }
			if(!empty($noordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlNotIn($noordreType_critere_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllTypeCritereLigneVente(){ 
		$sql="SELECT * FROM  type_critere_ligne_vente"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeCritereLigneVente($name="idtype_critere_ligne_vente",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_critere_ligne_vente , idtype_critere_ligne_vente FROM WHERE 1 ";  
			if(!empty($idtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlIn($idtype_critere_ligne_vente) ; }
			if(!empty($noidtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlNotIn($noidtype_critere_ligne_vente) ; }
			if(!empty($type_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlIn($type_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($notype_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlNotIn($notype_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($nomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlIn($nomType_critere_ligne_vente) ; }
			if(!empty($nonomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlNotIn($nonomType_critere_ligne_vente) ; }
			if(!empty($uniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlIn($uniteType_critere_ligne_vente) ; }
			if(!empty($nouniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlNotIn($nouniteType_critere_ligne_vente) ; }
			if(!empty($codeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlIn($codeType_critere_ligne_vente) ; }
			if(!empty($nocodeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlNotIn($nocodeType_critere_ligne_vente) ; }
			if(!empty($ordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlIn($ordreType_critere_ligne_vente) ; }
			if(!empty($noordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlNotIn($noordreType_critere_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomType_critere_ligne_vente ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeCritereLigneVente($name="idtype_critere_ligne_vente",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_critere_ligne_vente , idtype_critere_ligne_vente FROM type_critere_ligne_vente ORDER BY nomType_critere_ligne_vente ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeCritereLigneVente = new TypeCritereLigneVente(); ?>