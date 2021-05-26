<?   
class Image
{
	var $conn = null;
	function Image(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createImage($params){ 
		$this->conn->AutoExecute("image", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateImage($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("image", $params, "UPDATE", "idimage = ".$idimage); 
	}
	
	function deleteImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  image WHERE idimage = $idimage"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from image where 1 " ;
			if(!empty($idimage)){ $sql .= " AND idimage ".sqlIn($idimage) ; }
			if(!empty($noidimage)){ $sql .= " AND idimage ".sqlNotIn($noidimage) ; }
			if(!empty($type_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlIn($type_image_idtype_image) ; }
			if(!empty($notype_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlNotIn($notype_image_idtype_image) ; }
			if(!empty($urlImage)){ $sql .= " AND urlImage ".sqlIn($urlImage) ; }
			if(!empty($nourlImage)){ $sql .= " AND urlImage ".sqlNotIn($nourlImage) ; }
			if(!empty($titreImage)){ $sql .= " AND titreImage ".sqlIn($titreImage) ; }
			if(!empty($notitreImage)){ $sql .= " AND titreImage ".sqlNotIn($notitreImage) ; }
			if(!empty($commentaireImage)){ $sql .= " AND commentaireImage ".sqlIn($commentaireImage) ; }
			if(!empty($nocommentaireImage)){ $sql .= " AND commentaireImage ".sqlNotIn($nocommentaireImage) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneImage($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from image where 1 " ;
			if(!empty($idimage)){ $sql .= " AND idimage ".sqlSearch($idimage) ; }
			if(!empty($noidimage)){ $sql .= " AND idimage ".sqlNotSearch($noidimage) ; }
			if(!empty($type_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlSearch($type_image_idtype_image) ; }
			if(!empty($notype_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlNotSearch($notype_image_idtype_image) ; }
			if(!empty($urlImage)){ $sql .= " AND urlImage ".sqlSearch($urlImage) ; }
			if(!empty($nourlImage)){ $sql .= " AND urlImage ".sqlNotSearch($nourlImage) ; }
			if(!empty($titreImage)){ $sql .= " AND titreImage ".sqlSearch($titreImage) ; }
			if(!empty($notitreImage)){ $sql .= " AND titreImage ".sqlNotSearch($notitreImage) ; }
			if(!empty($commentaireImage)){ $sql .= " AND commentaireImage ".sqlSearch($commentaireImage) ; }
			if(!empty($nocommentaireImage)){ $sql .= " AND commentaireImage ".sqlNotSearch($nocommentaireImage) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllImage(){ 
		$sql="SELECT * FROM  image"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneImage($name="idimage",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomImage , idimage FROM WHERE 1 ";  
			if(!empty($idimage)){ $sql .= " AND idimage ".sqlIn($idimage) ; }
			if(!empty($noidimage)){ $sql .= " AND idimage ".sqlNotIn($noidimage) ; }
			if(!empty($type_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlIn($type_image_idtype_image) ; }
			if(!empty($notype_image_idtype_image)){ $sql .= " AND type_image_idtype_image ".sqlNotIn($notype_image_idtype_image) ; }
			if(!empty($urlImage)){ $sql .= " AND urlImage ".sqlIn($urlImage) ; }
			if(!empty($nourlImage)){ $sql .= " AND urlImage ".sqlNotIn($nourlImage) ; }
			if(!empty($titreImage)){ $sql .= " AND titreImage ".sqlIn($titreImage) ; }
			if(!empty($notitreImage)){ $sql .= " AND titreImage ".sqlNotIn($notitreImage) ; }
			if(!empty($commentaireImage)){ $sql .= " AND commentaireImage ".sqlIn($commentaireImage) ; }
			if(!empty($nocommentaireImage)){ $sql .= " AND commentaireImage ".sqlNotIn($nocommentaireImage) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomImage ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectImage($name="idimage",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomImage , idimage FROM image ORDER BY nomImage ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassImage = new Image(); ?>