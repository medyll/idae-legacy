<?   
class GammeProduit
{
	var $conn = null;
	function GammeProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createGammeProduit($params){ 
		$this->conn->AutoExecute("gamme_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateGammeProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("gamme_produit", $params, "UPDATE", "idgamme_produit = ".$idgamme_produit); 
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE    gamme_produit "; 
		return $this->conn->Execute($sql); 	
	}
	function deleteGammeProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  gamme_produit WHERE idgamme_produit = $idgamme_produit"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneGammeProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from gamme_produit where 1 " ;
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlIn($idgamme_produit) ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotIn($noidgamme_produit) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlIn($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotIn($nonomGamme_produit) ; }
			if(!empty($commentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlIn($commentaireGamme_produit) ; }
			if(!empty($nocommentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlNotIn($nocommentaireGamme_produit) ; }
			if(!empty($dateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlIn($dateDebutGamme_produit) ; }
			if(!empty($nodateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlNotIn($nodateDebutGamme_produit) ; }
			if(!empty($dateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlIn($dateFinGamme_produit) ; }
			if(!empty($nodateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlNotIn($nodateFinGamme_produit) ; }
			if(!empty($estActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlIn($estActifGamme_produit) ; }
			if(!empty($noestActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlNotIn($noestActifGamme_produit) ; }
			if(!empty($ordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlIn($ordreGamme_produit) ; }
			if(!empty($noordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlNotIn($noordreGamme_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneGammeProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from gamme_produit where 1 " ;
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlSearch($idgamme_produit) ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotSearch($noidgamme_produit) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlSearch($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotSearch($nonomGamme_produit) ; }
			if(!empty($commentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlSearch($commentaireGamme_produit) ; }
			if(!empty($nocommentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlNotSearch($nocommentaireGamme_produit) ; }
			if(!empty($dateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlSearch($dateDebutGamme_produit) ; }
			if(!empty($nodateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlNotSearch($nodateDebutGamme_produit) ; }
			if(!empty($dateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlSearch($dateFinGamme_produit) ; }
			if(!empty($nodateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlNotSearch($nodateFinGamme_produit) ; }
			if(!empty($estActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlSearch($estActifGamme_produit) ; }
			if(!empty($noestActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlNotSearch($noestActifGamme_produit) ; }
			if(!empty($ordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlSearch($ordreGamme_produit) ; }
			if(!empty($noordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlNotSearch($noordreGamme_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllGammeProduit(){ 
		$sql="SELECT * FROM  gamme_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneGammeProduit($name="idgamme_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomGamme_produit , idgamme_produit FROM gamme_produit WHERE  1 ";  
			if(!empty($idgamme_produit)){ $sql .= " AND idgamme_produit ".sqlIn($idgamme_produit) ; }
			if(!empty($noidgamme_produit)){ $sql .= " AND idgamme_produit ".sqlNotIn($noidgamme_produit) ; }
			if(!empty($nomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlIn($nomGamme_produit) ; }
			if(!empty($nonomGamme_produit)){ $sql .= " AND nomGamme_produit ".sqlNotIn($nonomGamme_produit) ; }
			if(!empty($commentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlIn($commentaireGamme_produit) ; }
			if(!empty($nocommentaireGamme_produit)){ $sql .= " AND commentaireGamme_produit ".sqlNotIn($nocommentaireGamme_produit) ; }
			if(!empty($dateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlIn($dateDebutGamme_produit) ; }
			if(!empty($nodateDebutGamme_produit)){ $sql .= " AND dateDebutGamme_produit ".sqlNotIn($nodateDebutGamme_produit) ; }
			if(!empty($dateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlIn($dateFinGamme_produit) ; }
			if(!empty($nodateFinGamme_produit)){ $sql .= " AND dateFinGamme_produit ".sqlNotIn($nodateFinGamme_produit) ; }
			if(!empty($estActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlIn($estActifGamme_produit) ; }
			if(!empty($noestActifGamme_produit)){ $sql .= " AND estActifGamme_produit ".sqlNotIn($noestActifGamme_produit) ; }
			if(!empty($ordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlIn($ordreGamme_produit) ; }
			if(!empty($noordreGamme_produit)){ $sql .= " AND ordreGamme_produit ".sqlNotIn($noordreGamme_produit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomGamme_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectGammeProduit($name="idgamme_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomGamme_produit , idgamme_produit FROM gamme_produit ORDER BY nomGamme_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectNameProduit($name="idgamme_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomGamme_produit , nomGamme_produit FROM gamme_produit ORDER BY nomGamme_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassGammeProduit = new GammeProduit(); ?>