<?   
class StockMateriel
{
	var $conn = null;
	function StockMateriel(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createStockMateriel($params){ 
		$this->conn->AutoExecute("stock_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("stock_materiel", $params, "UPDATE", "idstock_materiel = ".$idstock_materiel); 
	}
	
	function deleteStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  stock_materiel WHERE idstock_materiel = $idstock_materiel"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from stock_materiel where 1 " ;
			if(!empty($idstock_materiel)){ $sql .= " AND idstock_materiel ".sqlIn($idstock_materiel) ; }
			if(!empty($noidstock_materiel)){ $sql .= " AND idstock_materiel ".sqlNotIn($noidstock_materiel) ; }
			if(!empty($nomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlIn($nomStock_materiel) ; }
			if(!empty($nonomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlNotIn($nonomStock_materiel) ; }
			if(!empty($commentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlIn($commentaireStock_materiel) ; }
			if(!empty($nocommentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlNotIn($nocommentaireStock_materiel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from stock_materiel where 1 " ;
			if(!empty($idstock_materiel)){ $sql .= " AND idstock_materiel ".sqlSearch($idstock_materiel) ; }
			if(!empty($noidstock_materiel)){ $sql .= " AND idstock_materiel ".sqlNotSearch($noidstock_materiel) ; }
			if(!empty($nomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlSearch($nomStock_materiel) ; }
			if(!empty($nonomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlNotSearch($nonomStock_materiel) ; }
			if(!empty($commentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlSearch($commentaireStock_materiel) ; }
			if(!empty($nocommentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlNotSearch($nocommentaireStock_materiel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllStockMateriel(){ 
		$sql="SELECT * FROM  stock_materiel"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneStockMateriel($name="idstock_materiel",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomStock_materiel , idstock_materiel FROM stock_materiel WHERE  1 ";  
			if(!empty($idstock_materiel)){ $sql .= " AND idstock_materiel ".sqlIn($idstock_materiel) ; }
			if(!empty($noidstock_materiel)){ $sql .= " AND idstock_materiel ".sqlNotIn($noidstock_materiel) ; }
			if(!empty($nomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlIn($nomStock_materiel) ; }
			if(!empty($nonomStock_materiel)){ $sql .= " AND nomStock_materiel ".sqlNotIn($nonomStock_materiel) ; }
			if(!empty($commentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlIn($commentaireStock_materiel) ; }
			if(!empty($nocommentaireStock_materiel)){ $sql .= " AND commentaireStock_materiel ".sqlNotIn($nocommentaireStock_materiel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomStock_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectStockMateriel($name="idstock_materiel",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomStock_materiel , idstock_materiel FROM stock_materiel ORDER BY nomStock_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassStockMateriel = new StockMateriel(); ?>