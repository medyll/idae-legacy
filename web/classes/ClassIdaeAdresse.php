<?   
class IdaeAdresse
{
	var $conn = null;
	function IdaeAdresse(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeAdresse($params){ 
		$this->conn->AutoExecute("vue_adresse", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeAdresse($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_adresse", $params, "UPDATE", "idvue_adresse = ".$idvue_adresse); 
	}
	
	function deleteIdaeAdresse($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_adresse WHERE idvue_adresse = $idvue_adresse"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeAdresse($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_adresse where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($types)){ $sql .= " AND types ".sqlIn($types) ; }
			if(!empty($notypes)){ $sql .= " AND types ".sqlNotIn($notypes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeAdresse($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_adresse where 1 " ;
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlSearch($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotSearch($noidadresse) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlSearch($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotSearch($noadresse1) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlSearch($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotSearch($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlSearch($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotSearch($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlSearch($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotSearch($nopays) ; }
			if(!empty($types)){ $sql .= " AND types ".sqlSearch($types) ; }
			if(!empty($notypes)){ $sql .= " AND types ".sqlNotSearch($notypes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeAdresse(){ 
		$sql="SELECT * FROM  vue_adresse"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeAdresse($name="idvue_adresse",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_adresse , idvue_adresse FROM vue_adresse WHERE  1 ";  
			if(!empty($idadresse)){ $sql .= " AND idadresse ".sqlIn($idadresse) ; }
			if(!empty($noidadresse)){ $sql .= " AND idadresse ".sqlNotIn($noidadresse) ; }
			if(!empty($adresse1)){ $sql .= " AND adresse1 ".sqlIn($adresse1) ; }
			if(!empty($noadresse1)){ $sql .= " AND adresse1 ".sqlNotIn($noadresse1) ; }
			if(!empty($codePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlIn($codePostalAdresse) ; }
			if(!empty($nocodePostalAdresse)){ $sql .= " AND codePostalAdresse ".sqlNotIn($nocodePostalAdresse) ; }
			if(!empty($villeAdresse)){ $sql .= " AND villeAdresse ".sqlIn($villeAdresse) ; }
			if(!empty($novilleAdresse)){ $sql .= " AND villeAdresse ".sqlNotIn($novilleAdresse) ; }
			if(!empty($pays)){ $sql .= " AND pays ".sqlIn($pays) ; }
			if(!empty($nopays)){ $sql .= " AND pays ".sqlNotIn($nopays) ; }
			if(!empty($types)){ $sql .= " AND types ".sqlIn($types) ; }
			if(!empty($notypes)){ $sql .= " AND types ".sqlNotIn($notypes) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_adresse ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeAdresse($name="idvue_adresse",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_adresse , idvue_adresse FROM vue_adresse ORDER BY nomVue_adresse ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeAdresse = new IdaeAdresse(); ?>