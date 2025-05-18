<?   
class IdaeAdresseLigne
{
	var $conn = null;
	function IdaeAdresseLigne(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeAdresseLigne($params){ 
		$this->conn->AutoExecute("adresseslignesparcs", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeAdresseLigne($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("adresseslignesparcs", $params, "UPDATE", "idadresseslignesparcs = ".$idadresseslignesparcs); 
	}
	
	function deleteIdaeAdresseLigne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  adresseslignesparcs WHERE idadresseslignesparcs = $idadresseslignesparcs"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeAdresseLigne($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from adresseslignesparcs where 1 " ;
			if(!empty($idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlIn($idadressesLignesParcs) ; }
			if(!empty($noidadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlNotIn($noidadressesLignesParcs) ; }
			if(!empty($lt_idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs < '".$lt_idadressesLignesParcs."'" ; }
			if(!empty($gt_idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs > '".$gt_idadressesLignesParcs."'" ; }
			if(!empty($idligne_parcs)){ $sql .= " AND idligne_parcs ".sqlIn($idligne_parcs) ; }
			if(!empty($noidligne_parcs)){ $sql .= " AND idligne_parcs ".sqlNotIn($noidligne_parcs) ; }
			if(!empty($lt_idligne_parcs)){ $sql .= " AND idligne_parcs < '".$lt_idligne_parcs."'" ; }
			if(!empty($gt_idligne_parcs)){ $sql .= " AND idligne_parcs > '".$gt_idligne_parcs."'" ; }
			if(!empty($adresse)){ $sql .= " AND adresse ".sqlIn($adresse) ; }
			if(!empty($noadresse)){ $sql .= " AND adresse ".sqlNotIn($noadresse) ; }
			if(!empty($lt_adresse)){ $sql .= " AND adresse < '".$lt_adresse."'" ; }
			if(!empty($gt_adresse)){ $sql .= " AND adresse > '".$gt_adresse."'" ; }
			if(!empty($codePostal)){ $sql .= " AND codePostal ".sqlIn($codePostal) ; }
			if(!empty($nocodePostal)){ $sql .= " AND codePostal ".sqlNotIn($nocodePostal) ; }
			if(!empty($lt_codePostal)){ $sql .= " AND codePostal < '".$lt_codePostal."'" ; }
			if(!empty($gt_codePostal)){ $sql .= " AND codePostal > '".$gt_codePostal."'" ; }
			if(!empty($ville)){ $sql .= " AND ville ".sqlIn($ville) ; }
			if(!empty($noville)){ $sql .= " AND ville ".sqlNotIn($noville) ; }
			if(!empty($lt_ville)){ $sql .= " AND ville < '".$lt_ville."'" ; }
			if(!empty($gt_ville)){ $sql .= " AND ville > '".$gt_ville."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeAdresseLigne($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from adresseslignesparcs where 1 " ;
			if(!empty($idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlSearch($idadressesLignesParcs,"idadressesLignesParcs") ; }
			if(!empty($lt_idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs < '".$lt_idadressesLignesParcs."'" ; }
			if(!empty($gt_idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs > '".$gt_idadressesLignesParcs."'" ; }
			if(!empty($noidadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlNotSearch($noidadressesLignesParcs) ; }
			if(!empty($idligne_parcs)){ $sql .= " AND idligne_parcs ".sqlSearch($idligne_parcs,"idligne_parcs") ; }
			if(!empty($lt_idligne_parcs)){ $sql .= " AND idligne_parcs < '".$lt_idligne_parcs."'" ; }
			if(!empty($gt_idligne_parcs)){ $sql .= " AND idligne_parcs > '".$gt_idligne_parcs."'" ; }
			if(!empty($noidligne_parcs)){ $sql .= " AND idligne_parcs ".sqlNotSearch($noidligne_parcs) ; }
			if(!empty($adresse)){ $sql .= " AND adresse ".sqlSearch($adresse,"adresse") ; }
			if(!empty($lt_adresse)){ $sql .= " AND adresse < '".$lt_adresse."'" ; }
			if(!empty($gt_adresse)){ $sql .= " AND adresse > '".$gt_adresse."'" ; }
			if(!empty($noadresse)){ $sql .= " AND adresse ".sqlNotSearch($noadresse) ; }
			if(!empty($codePostal)){ $sql .= " AND codePostal ".sqlSearch($codePostal,"codePostal") ; }
			if(!empty($lt_codePostal)){ $sql .= " AND codePostal < '".$lt_codePostal."'" ; }
			if(!empty($gt_codePostal)){ $sql .= " AND codePostal > '".$gt_codePostal."'" ; }
			if(!empty($nocodePostal)){ $sql .= " AND codePostal ".sqlNotSearch($nocodePostal) ; }
			if(!empty($ville)){ $sql .= " AND ville ".sqlSearch($ville,"ville") ; }
			if(!empty($lt_ville)){ $sql .= " AND ville < '".$lt_ville."'" ; }
			if(!empty($gt_ville)){ $sql .= " AND ville > '".$gt_ville."'" ; }
			if(!empty($noville)){ $sql .= " AND ville ".sqlNotSearch($noville) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeAdresseLigne(){ 
		$sql="SELECT * FROM  adresseslignesparcs"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeAdresseLigne($name="idadresseslignesparcs",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomAdresseslignesparcs , idadresseslignesparcs FROM adresseslignesparcs WHERE  1 ";  
			if(!empty($idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlIn($idadressesLignesParcs) ; }
			if(!empty($gt_idadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs > ".$gt_idadressesLignesParcs ; }
			if(!empty($noidadressesLignesParcs)){ $sql .= " AND idadressesLignesParcs ".sqlNotIn($noidadressesLignesParcs) ; }
			if(!empty($idligne_parcs)){ $sql .= " AND idligne_parcs ".sqlIn($idligne_parcs) ; }
			if(!empty($gt_idligne_parcs)){ $sql .= " AND idligne_parcs > ".$gt_idligne_parcs ; }
			if(!empty($noidligne_parcs)){ $sql .= " AND idligne_parcs ".sqlNotIn($noidligne_parcs) ; }
			if(!empty($adresse)){ $sql .= " AND adresse ".sqlIn($adresse) ; }
			if(!empty($gt_adresse)){ $sql .= " AND adresse > ".$gt_adresse ; }
			if(!empty($noadresse)){ $sql .= " AND adresse ".sqlNotIn($noadresse) ; }
			if(!empty($codePostal)){ $sql .= " AND codePostal ".sqlIn($codePostal) ; }
			if(!empty($gt_codePostal)){ $sql .= " AND codePostal > ".$gt_codePostal ; }
			if(!empty($nocodePostal)){ $sql .= " AND codePostal ".sqlNotIn($nocodePostal) ; }
			if(!empty($ville)){ $sql .= " AND ville ".sqlIn($ville) ; }
			if(!empty($gt_ville)){ $sql .= " AND ville > ".$gt_ville ; }
			if(!empty($noville)){ $sql .= " AND ville ".sqlNotIn($noville) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomAdresseslignesparcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeAdresseLigne($name="idadresseslignesparcs",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomAdresseslignesparcs , idadresseslignesparcs FROM adresseslignesparcs ORDER BY nomAdresseslignesparcs ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeAdresseLigne = new IdaeAdresseLigne(); ?>