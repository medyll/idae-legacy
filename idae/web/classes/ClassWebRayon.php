<?   
class WebRayon
{
	var $conn = null;
	function WebRayon(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createWebRayon($params){ 
		$this->conn->AutoExecute("web_rayon", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateWebRayon($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("web_rayon", $params, "UPDATE", "idweb_rayon = ".$idweb_rayon); 
	}
	
	function deleteWebRayon($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  web_rayon WHERE idweb_rayon = $idweb_rayon"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneWebRayon($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from web_rayon where 1 " ;
			if(!empty($idweb_rayon)){ $sql .= " AND idweb_rayon ".sqlIn($idweb_rayon) ; }
			if(!empty($noidweb_rayon)){ $sql .= " AND idweb_rayon ".sqlNotIn($noidweb_rayon) ; }
			if(!empty($nomWeb_rayon)){ $sql .= " AND nomWeb_rayon ".sqlIn($nomWeb_rayon) ; }
			if(!empty($nonomWeb_rayon)){ $sql .= " AND nomWeb_rayon ".sqlNotIn($nonomWeb_rayon) ; }
			if(!empty($commentaireWeb_rayon)){ $sql .= " AND commentaireWeb_rayon ".sqlIn($commentaireWeb_rayon) ; }
			if(!empty($nocommentaireWeb_rayon)){ $sql .= " AND commentaireWeb_rayon ".sqlNotIn($nocommentaireWeb_rayon) ; }
			if(!empty($dateDebutWeb_rayon)){ $sql .= " AND dateDebutWeb_rayon ".sqlIn($dateDebutWeb_rayon) ; }
			if(!empty($nodateDebutWeb_rayon)){ $sql .= " AND dateDebutWeb_rayon ".sqlNotIn($nodateDebutWeb_rayon) ; }
			if(!empty($ordreWeb_rayon)){ $sql .= " AND ordreWeb_rayon ".sqlIn($ordreWeb_rayon) ; }
			if(!empty($noordreWeb_rayon)){ $sql .= " AND ordreWeb_rayon ".sqlNotIn($noordreWeb_rayon) ; }
			if(!empty($dateCreationWeb_rayon)){ $sql .= " AND dateCreationWeb_rayon ".sqlIn($dateCreationWeb_rayon) ; }
			if(!empty($nodateCreationWeb_rayon)){ $sql .= " AND dateCreationWeb_rayon ".sqlNotIn($nodateCreationWeb_rayon) ; }
			if(!empty($dateFinWeb_rayon)){ $sql .= " AND dateFinWeb_rayon ".sqlIn($dateFinWeb_rayon) ; }
			if(!empty($nodateFinWeb_rayon)){ $sql .= " AND dateFinWeb_rayon ".sqlNotIn($nodateFinWeb_rayon) ; }
			if(!empty($dateClotureWeb_rayon)){ $sql .= " AND dateClotureWeb_rayon ".sqlIn($dateClotureWeb_rayon) ; }
			if(!empty($nodateClotureWeb_rayon)){ $sql .= " AND dateClotureWeb_rayon ".sqlNotIn($nodateClotureWeb_rayon) ; }
			if(!empty($estActifWeb_rayon)){ $sql .= " AND estActifWeb_rayon ".sqlIn($estActifWeb_rayon) ; }
			if(!empty($noestActifWeb_rayon)){ $sql .= " AND estActifWeb_rayon ".sqlNotIn($noestActifWeb_rayon) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllWebRayon(){ 
		$sql="SELECT * FROM  web_rayon"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneWebRayon($name="idweb_rayon",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomWeb_rayon , idweb_rayon FROM WHERE 1 ";  
			if(!empty($idweb_rayon)){ $sql .= " AND idweb_rayon ".sqlIn($idweb_rayon) ; }
			if(!empty($noidweb_rayon)){ $sql .= " AND idweb_rayon ".sqlNotIn($noidweb_rayon) ; } 
			if(!empty($nomWeb_rayon)){ $sql .= " AND nomWeb_rayon ".sqlIn($nomWeb_rayon) ; }
			if(!empty($nonomWeb_rayon)){ $sql .= " AND nomWeb_rayon ".sqlNotIn($nonomWeb_rayon) ; } 
			if(!empty($commentaireWeb_rayon)){ $sql .= " AND commentaireWeb_rayon ".sqlIn($commentaireWeb_rayon) ; }
			if(!empty($nocommentaireWeb_rayon)){ $sql .= " AND commentaireWeb_rayon ".sqlNotIn($nocommentaireWeb_rayon) ; }
			if(!empty($dateDebutWeb_rayon)){ $sql .= " AND dateDebutWeb_rayon ".sqlIn($dateDebutWeb_rayon) ; }
			if(!empty($nodateDebutWeb_rayon)){ $sql .= " AND dateDebutWeb_rayon ".sqlNotIn($nodateDebutWeb_rayon) ; }
			if(!empty($ordreWeb_rayon)){ $sql .= " AND ordreWeb_rayon ".sqlIn($ordreWeb_rayon) ; }
			if(!empty($noordreWeb_rayon)){ $sql .= " AND ordreWeb_rayon ".sqlNotIn($noordreWeb_rayon) ; }
			if(!empty($dateCreationWeb_rayon)){ $sql .= " AND dateCreationWeb_rayon ".sqlIn($dateCreationWeb_rayon) ; }
			if(!empty($nodateCreationWeb_rayon)){ $sql .= " AND dateCreationWeb_rayon ".sqlNotIn($nodateCreationWeb_rayon) ; }
			if(!empty($dateFinWeb_rayon)){ $sql .= " AND dateFinWeb_rayon ".sqlIn($dateFinWeb_rayon) ; }
			if(!empty($nodateFinWeb_rayon)){ $sql .= " AND dateFinWeb_rayon ".sqlNotIn($nodateFinWeb_rayon) ; }
			if(!empty($dateClotureWeb_rayon)){ $sql .= " AND dateClotureWeb_rayon ".sqlIn($dateClotureWeb_rayon) ; }
			if(!empty($nodateClotureWeb_rayon)){ $sql .= " AND dateClotureWeb_rayon ".sqlNotIn($nodateClotureWeb_rayon) ; }
			if(!empty($estActifWeb_rayon)){ $sql .= " AND estActifWeb_rayon ".sqlIn($estActifWeb_rayon) ; }
			if(!empty($noestActifWeb_rayon)){ $sql .= " AND estActifWeb_rayon ".sqlNotIn($noestActifWeb_rayon) ; }
			$sql .=" ORDER BY nomWeb_rayon ";
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectWebRayon($name="idweb_rayon",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomWeb_rayon , idweb_rayon FROM web_rayon ORDER BY nomWeb_rayon ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassWebRayon = new WebRayon(); ?>