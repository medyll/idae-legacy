<?   
class ProduitHasDate
{
	var $conn = null;
	function ProduitHasDate(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createProduitHasDate($params){ 
		$this->conn->AutoExecute("produit_has_date", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateProduitHasDate($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("produit_has_date", $params, "UPDATE", "idproduit_has_date = ".$idproduit_has_date); 
	}
	
	function deleteProduitHasDate($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  produit_has_date WHERE idproduit_has_date = $idproduit_has_date"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneProduitHasDate($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from produit_has_date where 1 " ;
			if(!empty($idproduit_has_date)){ $sql .= " AND idproduit_has_date ".sqlIn($idproduit_has_date) ; }
			if(!empty($noidproduit_has_date)){ $sql .= " AND idproduit_has_date ".sqlNotIn($noidproduit_has_date) ; }
			if(!empty($date_iddate)){ $sql .= " AND date_iddate ".sqlIn($date_iddate) ; }
			if(!empty($nodate_iddate)){ $sql .= " AND date_iddate ".sqlNotIn($nodate_iddate) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($ordreProduit_has_date)){ $sql .= " AND ordreProduit_has_date ".sqlIn($ordreProduit_has_date) ; }
			if(!empty($noordreProduit_has_date)){ $sql .= " AND ordreProduit_has_date ".sqlNotIn($noordreProduit_has_date) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($debug)){ echo $sql;   }
	return $this->conn->Execute($sql) ;	
	}
	
	function getAllProduitHasDate(){ 
		$sql="SELECT * FROM  produit_has_date"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneProduitHasDate($name="idproduit_has_date",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomProduit_has_date , idproduit_has_date FROM WHERE 1 ";  
			if(!empty($idproduit_has_date)){ $sql .= " AND idproduit_has_date ".sqlIn($idproduit_has_date) ; }
			if(!empty($noidproduit_has_date)){ $sql .= " AND idproduit_has_date ".sqlNotIn($noidproduit_has_date) ; } 
			if(!empty($date_iddate)){ $sql .= " AND date_iddate ".sqlIn($date_iddate) ; }
			if(!empty($nodate_iddate)){ $sql .= " AND date_iddate ".sqlNotIn($nodate_iddate) ; } 
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; } 
			if(!empty($ordreProduit_has_date)){ $sql .= " AND ordreProduit_has_date ".sqlIn($ordreProduit_has_date) ; }
			if(!empty($noordreProduit_has_date)){ $sql .= " AND ordreProduit_has_date ".sqlNotIn($noordreProduit_has_date) ; }
		$sql .=" ORDER BY nomProduit_has_date ";
			if(!empty($groupBy)){ $sql .= " group by $groupBy" ; } 
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectProduitHasDate($name="idproduit_has_date",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomProduit_has_date , idproduit_has_date FROM produit_has_date ORDER BY nomProduit_has_date ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassProduitHasDate = new ProduitHasDate(); ?>