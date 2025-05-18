<?   
class TypeFournisseur
{
	var $conn = null;
	function TypeFournisseur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTypeFournisseur($params){ 
		$this->conn->AutoExecute("type_fournisseur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("type_fournisseur", $params, "UPDATE", "idtype_fournisseur = ".$idtype_fournisseur); 
	}
	
	function deleteTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  type_fournisseur WHERE idtype_fournisseur = $idtype_fournisseur"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneTypeFournisseur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from type_fournisseur where 1 " ;
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlIn($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotIn($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlIn($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotIn($nonomType_fournisseur) ; }
			if(!empty($commentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlIn($commentaireType_fournisseur) ; }
			if(!empty($nocommentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlNotIn($nocommentaireType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlIn($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotIn($noordreType_fournisseur) ; }
			if(!empty($codeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlIn($codeType_fournisseur) ; }
			if(!empty($nocodeType_fournisseur)){ $sql .= " AND codeType_fournisseur ".sqlNotIn($nocodeType_fournisseur) ; }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllTypeFournisseur(){ 
		$sql="SELECT * FROM  type_fournisseur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTypeFournisseur($name="idtype_fournisseur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomType_fournisseur , idtype_fournisseur FROM WHERE 1 ";  
			if(!empty($idtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlIn($idtype_fournisseur) ; }
			if(!empty($noidtype_fournisseur)){ $sql .= " AND idtype_fournisseur ".sqlNotIn($noidtype_fournisseur) ; }
			if(!empty($nomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlIn($nomType_fournisseur) ; }
			if(!empty($nonomType_fournisseur)){ $sql .= " AND nomType_fournisseur ".sqlNotIn($nonomType_fournisseur) ; }
			if(!empty($commentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlIn($commentaireType_fournisseur) ; }
			if(!empty($nocommentaireType_fournisseur)){ $sql .= " AND commentaireType_fournisseur ".sqlNotIn($nocommentaireType_fournisseur) ; }
			if(!empty($ordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlIn($ordreType_fournisseur) ; }
			if(!empty($noordreType_fournisseur)){ $sql .= " AND ordreType_fournisseur ".sqlNotIn($noordreType_fournisseur) ; } 
		$sql .=" ORDER BY nomType_fournisseur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTypeFournisseur($name="idtype_fournisseur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomType_fournisseur , idtype_fournisseur FROM type_fournisseur ORDER BY nomType_fournisseur";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTypeFournisseur = new TypeFournisseur(); ?>