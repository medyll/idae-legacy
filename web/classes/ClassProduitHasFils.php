<?   
class ProduitHasFils
{
	var $conn = null;
	function ProduitHasFils(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduitHasFils($params){ 
		$this->conn->AutoExecute("produit_has_fils", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_fils", $params, "UPDATE", "idproduit_has_fils = ".$idproduit_has_fils); 
	}
	
	function deleteProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_fils WHERE idproduit_has_fils = $idproduit_has_fils"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE produit_has_fils "; 
		$this->conn->Execute($sql); 	
	}
	function getOneProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from produit_has_fils where 1 " ;
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlIn($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotIn($noidproduit_has_fils) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($idFilsProduit)){ $sql .= " AND idFilsProduit ".sqlIn($idFilsProduit) ; }
			if(!empty($noidFilsProduit)){ $sql .= " AND idFilsProduit ".sqlNotIn($noidFilsProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneProduitHasFils($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_fils where 1 " ;
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlSearch($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotSearch($noidproduit_has_fils) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($idFilsProduit)){ $sql .= " AND idFilsProduit ".sqlSearch($idFilsProduit) ; }
			if(!empty($noidFilsProduit)){ $sql .= " AND idFilsProduit ".sqlNotSearch($noidFilsProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllProduitHasFils(){ 
		$sql="SELECT * FROM  produit_has_fils"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduitHasFils($name="idproduit_has_fils",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_fils , idproduit_has_fils FROM produit_has_fils WHERE  1 ";  
			if(!empty($idproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlIn($idproduit_has_fils) ; }
			if(!empty($noidproduit_has_fils)){ $sql .= " AND idproduit_has_fils ".sqlNotIn($noidproduit_has_fils) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($idFilsProduit)){ $sql .= " AND idFilsProduit ".sqlIn($idFilsProduit) ; }
			if(!empty($noidFilsProduit)){ $sql .= " AND idFilsProduit ".sqlNotIn($noidFilsProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomProduit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduitHasFils($name="idproduit_has_fils",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_fils , idproduit_has_fils FROM produit_has_fils ORDER BY nomProduit_has_fils ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduitHasFils = new ProduitHasFils(); ?>