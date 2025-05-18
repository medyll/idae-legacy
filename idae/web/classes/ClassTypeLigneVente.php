<?   
class TypeLigneVente
{
	var $conn = null;
	function TypeLigneVente(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeLigneVente($params){ 
		$this->conn->AutoExecute("type_ligne_vente", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_ligne_vente", $params, "UPDATE", "idtype_ligne_vente = ".$idtype_ligne_vente); 
	}
	
	function deleteTypeLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_ligne_vente WHERE idtype_ligne_vente = $idtype_ligne_vente"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeLigneVente($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_ligne_vente where 1 " ;
			if(!empty($idtype_ligne_vente)){ $sql .= " AND idtype_ligne_vente ".sqlIn($idtype_ligne_vente) ; }
			if(!empty($noidtype_ligne_vente)){ $sql .= " AND idtype_ligne_vente ".sqlNotIn($noidtype_ligne_vente) ; }
			if(!empty($nomType_ligne_vente)){ $sql .= " AND nomType_ligne_vente ".sqlIn($nomType_ligne_vente) ; }
			if(!empty($nonomType_ligne_vente)){ $sql .= " AND nomType_ligne_vente ".sqlNotIn($nonomType_ligne_vente) ; }
			if(!empty($commentaireType_ligne_vente)){ $sql .= " AND commentaireType_ligne_vente ".sqlIn($commentaireType_ligne_vente) ; }
			if(!empty($nocommentaireType_ligne_vente)){ $sql .= " AND commentaireType_ligne_vente ".sqlNotIn($nocommentaireType_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllTypeLigneVente(){ 
		$sql="SELECT * FROM  type_ligne_vente"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeLigneVente($name="idtype_ligne_vente",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_ligne_vente , idtype_ligne_vente FROM WHERE 1 ";  
			if(!empty($idtype_ligne_vente)){ $sql .= " AND idtype_ligne_vente ".sqlIn($idtype_ligne_vente) ; }
			if(!empty($noidtype_ligne_vente)){ $sql .= " AND idtype_ligne_vente ".sqlNotIn($noidtype_ligne_vente) ; }
			if(!empty($nomType_ligne_vente)){ $sql .= " AND nomType_ligne_vente ".sqlIn($nomType_ligne_vente) ; }
			if(!empty($nonomType_ligne_vente)){ $sql .= " AND nomType_ligne_vente ".sqlNotIn($nonomType_ligne_vente) ; }
			if(!empty($commentaireType_ligne_vente)){ $sql .= " AND commentaireType_ligne_vente ".sqlIn($commentaireType_ligne_vente) ; }
			if(!empty($nocommentaireType_ligne_vente)){ $sql .= " AND commentaireType_ligne_vente ".sqlNotIn($nocommentaireType_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$sql .=" ORDER BY nomType_ligne_vente ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeLigneVente($name="idtype_ligne_vente",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_ligne_vente , idtype_ligne_vente FROM type_ligne_vente ORDER BY nomType_ligne_vente ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeLigneVente = new TypeLigneVente(); ?>