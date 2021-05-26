<?   
class LigneLivraison
{
	var $conn = null;
	function LigneLivraison(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLigneLivraison($params){ 
		$this->conn->AutoExecute("ligne_livraison", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_livraison", $params, "UPDATE", "idligne_livraison = ".$idligne_livraison); 
	}
	
	function deleteLigneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_livraison WHERE idligne_livraison = $idligne_livraison"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){
		$sql="TRUNCATE TABLE  ligne_livraison "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneLigneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from ligne_livraison where 1 " ;
			if(!empty($idligne_livraison)){ $sql .= " AND idligne_livraison ".sqlIn($idligne_livraison) ; }
			if(!empty($noidligne_livraison)){ $sql .= " AND idligne_livraison ".sqlNotIn($noidligne_livraison) ; }
			if(!empty($livraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlIn($livraison_idlivraison) ; }
			if(!empty($nolivraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlNotIn($nolivraison_idlivraison) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($quantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlIn($quantiteLigne_livraison) ; }
			if(!empty($noquantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlNotIn($noquantiteLigne_livraison) ; }
			if(!empty($prixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlIn($prixLigne_livraison) ; }
			if(!empty($noprixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlNotIn($noprixLigne_livraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneLivraison($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from ligne_livraison where 1 " ;
			if(!empty($idligne_livraison)){ $sql .= " AND idligne_livraison ".sqlSearch($idligne_livraison) ; }
			if(!empty($noidligne_livraison)){ $sql .= " AND idligne_livraison ".sqlNotSearch($noidligne_livraison) ; }
			if(!empty($livraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlSearch($livraison_idlivraison) ; }
			if(!empty($nolivraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlNotSearch($nolivraison_idlivraison) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($quantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlSearch($quantiteLigne_livraison) ; }
			if(!empty($noquantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlNotSearch($noquantiteLigne_livraison) ; }
			if(!empty($prixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlSearch($prixLigne_livraison) ; }
			if(!empty($noprixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlNotSearch($noprixLigne_livraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneLivraison(){ 
		$sql="SELECT * FROM  ligne_livraison"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneLivraison($name="idligne_livraison",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_livraison , idligne_livraison FROM ligne_livraison WHERE  1 ";  
			if(!empty($idligne_livraison)){ $sql .= " AND idligne_livraison ".sqlIn($idligne_livraison) ; }
			if(!empty($noidligne_livraison)){ $sql .= " AND idligne_livraison ".sqlNotIn($noidligne_livraison) ; }
			if(!empty($livraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlIn($livraison_idlivraison) ; }
			if(!empty($nolivraison_idlivraison)){ $sql .= " AND livraison_idlivraison ".sqlNotIn($nolivraison_idlivraison) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($quantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlIn($quantiteLigne_livraison) ; }
			if(!empty($noquantiteLigne_livraison)){ $sql .= " AND quantiteLigne_livraison ".sqlNotIn($noquantiteLigne_livraison) ; }
			if(!empty($prixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlIn($prixLigne_livraison) ; }
			if(!empty($noprixLigne_livraison)){ $sql .= " AND prixLigne_livraison ".sqlNotIn($noprixLigne_livraison) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_livraison ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneLivraison($name="idligne_livraison",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_livraison , idligne_livraison FROM ligne_livraison ORDER BY nomLigne_livraison ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLigneLivraison = new LigneLivraison(); ?>