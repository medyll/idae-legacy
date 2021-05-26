<?   
class IdaeStock
{
	var $conn = null;
	function IdaeStock(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeStock($params){ 
		$this->conn->AutoExecute("stockmachine", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeStock($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("stockmachine", $params, "UPDATE", "idstockmachine = ".$idstockmachine); 
	}
	
	function deleteIdaeStock($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  stockmachine WHERE idstockmachine = $idstockmachine"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeStock($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from stockmachine where 1 " ;
			if(!empty($idstockMachine)){ $sql .= " AND idstockMachine ".sqlIn($idstockMachine) ; }
			if(!empty($noidstockMachine)){ $sql .= " AND idstockMachine ".sqlNotIn($noidstockMachine) ; }
			if(!empty($lt_idstockMachine)){ $sql .= " AND idstockMachine < '".$lt_idstockMachine."'" ; }
			if(!empty($gt_idstockMachine)){ $sql .= " AND idstockMachine > '".$gt_idstockMachine."'" ; }
			if(!empty($lieu)){ $sql .= " AND lieu ".sqlIn($lieu) ; }
			if(!empty($nolieu)){ $sql .= " AND lieu ".sqlNotIn($nolieu) ; }
			if(!empty($lt_lieu)){ $sql .= " AND lieu < '".$lt_lieu."'" ; }
			if(!empty($gt_lieu)){ $sql .= " AND lieu > '".$gt_lieu."'" ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlIn($commentaires) ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotIn($nocommentaires) ; }
			if(!empty($lt_commentaires)){ $sql .= " AND commentaires < '".$lt_commentaires."'" ; }
			if(!empty($gt_commentaires)){ $sql .= " AND commentaires > '".$gt_commentaires."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeStock($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from stockmachine where 1 " ;
			if(!empty($idstockMachine)){ $sql .= " AND idstockMachine ".sqlSearch($idstockMachine,"idstockMachine") ; }
			if(!empty($lt_idstockMachine)){ $sql .= " AND idstockMachine < '".$lt_idstockMachine."'" ; }
			if(!empty($gt_idstockMachine)){ $sql .= " AND idstockMachine > '".$gt_idstockMachine."'" ; }
			if(!empty($noidstockMachine)){ $sql .= " AND idstockMachine ".sqlNotSearch($noidstockMachine) ; }
			if(!empty($lieu)){ $sql .= " AND lieu ".sqlSearch($lieu,"lieu") ; }
			if(!empty($lt_lieu)){ $sql .= " AND lieu < '".$lt_lieu."'" ; }
			if(!empty($gt_lieu)){ $sql .= " AND lieu > '".$gt_lieu."'" ; }
			if(!empty($nolieu)){ $sql .= " AND lieu ".sqlNotSearch($nolieu) ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlSearch($commentaires,"commentaires") ; }
			if(!empty($lt_commentaires)){ $sql .= " AND commentaires < '".$lt_commentaires."'" ; }
			if(!empty($gt_commentaires)){ $sql .= " AND commentaires > '".$gt_commentaires."'" ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotSearch($nocommentaires) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeStock(){ 
		$sql="SELECT * FROM  stockmachine"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeStock($name="idstockmachine",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomStockmachine , idstockmachine FROM stockmachine WHERE  1 ";  
			if(!empty($idstockMachine)){ $sql .= " AND idstockMachine ".sqlIn($idstockMachine) ; }
			if(!empty($gt_idstockMachine)){ $sql .= " AND idstockMachine > ".$gt_idstockMachine ; }
			if(!empty($noidstockMachine)){ $sql .= " AND idstockMachine ".sqlNotIn($noidstockMachine) ; }
			if(!empty($lieu)){ $sql .= " AND lieu ".sqlIn($lieu) ; }
			if(!empty($gt_lieu)){ $sql .= " AND lieu > ".$gt_lieu ; }
			if(!empty($nolieu)){ $sql .= " AND lieu ".sqlNotIn($nolieu) ; }
			if(!empty($commentaires)){ $sql .= " AND commentaires ".sqlIn($commentaires) ; }
			if(!empty($gt_commentaires)){ $sql .= " AND commentaires > ".$gt_commentaires ; }
			if(!empty($nocommentaires)){ $sql .= " AND commentaires ".sqlNotIn($nocommentaires) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomStockmachine ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeStock($name="idstockmachine",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomStockmachine , idstockmachine FROM stockmachine ORDER BY nomStockmachine ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeStock = new IdaeStock(); ?>