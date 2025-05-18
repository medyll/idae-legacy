<?   
class Adresse
{
	var $conn = null;
	function Adresse(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createAdresse($params){ 
		$this->conn->AutoExecute("adresse", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateAdresse($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("adresse", $params, "UPDATE", "idadresse = ".$idadresse); 
	}
	
	function deleteAdresse($params){
		extract($params,EXTR_OVERWRITE);
		$sql="DELETE FROM  adresse WHERE idadresse = $idadresse"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE adresse"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneAdresse($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_adresse where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($lt_idadresse)){ $sql .= " AND idadresse < '".$lt_idadresse."'" ; }
			if(!empty($gt_idadresse)){ $sql .= " AND idadresse > '".$gt_idadresse."'" ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($lt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation < '".$lt_localisation_idlocalisation."'" ; }
			if(!empty($gt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation > '".$gt_localisation_idlocalisation."'" ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($lt_adresse1)){ $sql .= " AND adresse1 < '".$lt_adresse1."'" ; }
			if(!empty($gt_adresse1)){ $sql .= " AND adresse1 > '".$gt_adresse1."'" ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($lt_adresse2)){ $sql .= " AND adresse2 < '".$lt_adresse2."'" ; }
			if(!empty($gt_adresse2)){ $sql .= " AND adresse2 > '".$gt_adresse2."'" ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($lt_codePostalAdresse)){ $sql .= " AND codePostalAdresse < '".$lt_codePostalAdresse."'" ; }
			if(!empty($gt_codePostalAdresse)){ $sql .= " AND codePostalAdresse > '".$gt_codePostalAdresse."'" ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($lt_villeAdresse)){ $sql .= " AND villeAdresse < '".$lt_villeAdresse."'" ; }
			if(!empty($gt_villeAdresse)){ $sql .= " AND villeAdresse > '".$gt_villeAdresse."'" ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($lt_pays)){ $sql .= " AND pays < '".$lt_pays."'" ; }
			if(!empty($gt_pays)){ $sql .= " AND pays > '".$gt_pays."'" ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($lt_commentaireAdresse)){ $sql .= " AND commentaireAdresse < '".$lt_commentaireAdresse."'" ; }
			if(!empty($gt_commentaireAdresse)){ $sql .= " AND commentaireAdresse > '".$gt_commentaireAdresse."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  } 
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneAdresse($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from adresse where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlSearch($idadresse,"idadresse") ; }
			if(!empty($lt_idadresse)){ $sql .= " AND idadresse < '".$lt_idadresse."'" ; }
			if(!empty($gt_idadresse)){ $sql .= " AND idadresse > '".$gt_idadresse."'" ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotSearch($noidadresse) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation,"localisation_idlocalisation") ; }
			if(!empty($lt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation < '".$lt_localisation_idlocalisation."'" ; }
			if(!empty($gt_localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation > '".$gt_localisation_idlocalisation."'" ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlSearch($adresse1,"adresse1") ; }
			if(!empty($lt_adresse1)){ $sql .= " AND adresse1 < '".$lt_adresse1."'" ; }
			if(!empty($gt_adresse1)){ $sql .= " AND adresse1 > '".$gt_adresse1."'" ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotSearch($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlSearch($adresse2,"adresse2") ; }
			if(!empty($lt_adresse2)){ $sql .= " AND adresse2 < '".$lt_adresse2."'" ; }
			if(!empty($gt_adresse2)){ $sql .= " AND adresse2 > '".$gt_adresse2."'" ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotSearch($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlSearch($codePostalAdresse,"codePostalAdresse") ; }
			if(!empty($lt_codePostalAdresse)){ $sql .= " AND codePostalAdresse < '".$lt_codePostalAdresse."'" ; }
			if(!empty($gt_codePostalAdresse)){ $sql .= " AND codePostalAdresse > '".$gt_codePostalAdresse."'" ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotSearch($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlSearch($villeAdresse,"villeAdresse") ; }
			if(!empty($lt_villeAdresse)){ $sql .= " AND villeAdresse < '".$lt_villeAdresse."'" ; }
			if(!empty($gt_villeAdresse)){ $sql .= " AND villeAdresse > '".$gt_villeAdresse."'" ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotSearch($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlSearch($pays,"pays") ; }
			if(!empty($lt_pays)){ $sql .= " AND pays < '".$lt_pays."'" ; }
			if(!empty($gt_pays)){ $sql .= " AND pays > '".$gt_pays."'" ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotSearch($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlSearch($commentaireAdresse,"commentaireAdresse") ; }
			if(!empty($lt_commentaireAdresse)){ $sql .= " AND commentaireAdresse < '".$lt_commentaireAdresse."'" ; }
			if(!empty($gt_commentaireAdresse)){ $sql .= " AND commentaireAdresse > '".$gt_commentaireAdresse."'" ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotSearch($nocommentaireAdresse) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllAdresse(){ 
		$sql="SELECT * FROM  adresse"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneAdresse($name="idadresse",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAdresse , idadresse FROM WHERE 1 ";  
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($adresse2)){ $sql .= " AND adresse2 ".sqlIn($adresse2) ; }
			if(!empty($noadresse2)){ $sql .= " AND adresse2 ".sqlNotIn($noadresse2) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($commentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlIn($commentaireAdresse) ; }
			if(!empty($nocommentaireAdresse)){ $sql .= " AND commentaireAdresse ".sqlNotIn($nocommentaireAdresse) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAdresse ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectAdresse($name="idadresse",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAdresse , idadresse FROM adresse ORDER BY nomAdresse ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassAdresse = new Adresse(); ?>