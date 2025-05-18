<?   
class HistoStockMateriel
{
	var $conn = null;
	function HistoStockMateriel(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createHistoStockMateriel($params){ 
		$this->conn->AutoExecute("histo_materiel_stock", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateHistoStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("histo_materiel_stock", $params, "UPDATE", "idhisto_materiel_stock = ".$idhisto_materiel_stock); 
	}
	
	function deleteHistoStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  histo_materiel_stock WHERE idhisto_materiel_stock = $idhisto_materiel_stock"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneHistoStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from histo_materiel_stock where 1 " ;
			if(!empty($idhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlIn($idhisto_stock_materiel) ; }
			if(!empty($noidhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlNotIn($noidhisto_stock_materiel) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($dateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlIn($dateDebutHisto_stock_materiel) ; }
			if(!empty($nodateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlNotIn($nodateDebutHisto_stock_materiel) ; }
			if(!empty($dateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlIn($dateClotureHisto_stock_materiel) ; }
			if(!empty($nodateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlNotIn($nodateClotureHisto_stock_materiel) ; }
			if(!empty($commentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlIn($commentaireDebutHisto_stock_materiel) ; }
			if(!empty($nocommentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlNotIn($nocommentaireDebutHisto_stock_materiel) ; }
			if(!empty($commentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlIn($commentaireClotureHisto_stock_materiel) ; }
			if(!empty($nocommentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlNotIn($nocommentaireClotureHisto_stock_materiel) ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlIn($compteurNBRetrait) ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotIn($nocompteurNBRetrait) ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlIn($compteurCoulRetrait) ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotIn($nocompteurCoulRetrait) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlIn($pvHt) ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotIn($nopvHt) ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlIn($paHt) ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotIn($nopaHt) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneHistoStockMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from histo_materiel_stock where 1 " ;
			if(!empty($idhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlSearch($idhisto_stock_materiel) ; }
			if(!empty($noidhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlNotSearch($noidhisto_stock_materiel) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($dateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlSearch($dateDebutHisto_stock_materiel) ; }
			if(!empty($nodateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlNotSearch($nodateDebutHisto_stock_materiel) ; }
			if(!empty($dateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlSearch($dateClotureHisto_stock_materiel) ; }
			if(!empty($nodateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlNotSearch($nodateClotureHisto_stock_materiel) ; }
			if(!empty($commentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlSearch($commentaireDebutHisto_stock_materiel) ; }
			if(!empty($nocommentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlNotSearch($nocommentaireDebutHisto_stock_materiel) ; }
			if(!empty($commentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlSearch($commentaireClotureHisto_stock_materiel) ; }
			if(!empty($nocommentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlNotSearch($nocommentaireClotureHisto_stock_materiel) ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlSearch($compteurNBRetrait) ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotSearch($nocompteurNBRetrait) ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlSearch($compteurCoulRetrait) ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotSearch($nocompteurCoulRetrait) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlSearch($pvHt) ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotSearch($nopvHt) ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlSearch($paHt) ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotSearch($nopaHt) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllHistoStockMateriel(){ 
		$sql="SELECT * FROM  histo_materiel_stock"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneHistoStockMateriel($name="idhisto_materiel_stock",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomHisto_materiel_stock , idhisto_materiel_stock FROM histo_materiel_stock WHERE  1 ";  
			if(!empty($idhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlIn($idhisto_stock_materiel) ; }
			if(!empty($noidhisto_stock_materiel)){ $sql .= " AND idhisto_stock_materiel ".sqlNotIn($noidhisto_stock_materiel) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($dateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlIn($dateDebutHisto_stock_materiel) ; }
			if(!empty($nodateDebutHisto_stock_materiel)){ $sql .= " AND dateDebutHisto_stock_materiel ".sqlNotIn($nodateDebutHisto_stock_materiel) ; }
			if(!empty($dateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlIn($dateClotureHisto_stock_materiel) ; }
			if(!empty($nodateClotureHisto_stock_materiel)){ $sql .= " AND dateClotureHisto_stock_materiel ".sqlNotIn($nodateClotureHisto_stock_materiel) ; }
			if(!empty($commentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlIn($commentaireDebutHisto_stock_materiel) ; }
			if(!empty($nocommentaireDebutHisto_stock_materiel)){ $sql .= " AND commentaireDebutHisto_stock_materiel ".sqlNotIn($nocommentaireDebutHisto_stock_materiel) ; }
			if(!empty($commentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlIn($commentaireClotureHisto_stock_materiel) ; }
			if(!empty($nocommentaireClotureHisto_stock_materiel)){ $sql .= " AND commentaireClotureHisto_stock_materiel ".sqlNotIn($nocommentaireClotureHisto_stock_materiel) ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlIn($compteurNBRetrait) ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotIn($nocompteurNBRetrait) ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlIn($compteurCoulRetrait) ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotIn($nocompteurCoulRetrait) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlIn($pvHt) ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotIn($nopvHt) ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlIn($paHt) ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotIn($nopaHt) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomHisto_materiel_stock ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectHistoStockMateriel($name="idhisto_materiel_stock",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomHisto_materiel_stock , idhisto_materiel_stock FROM histo_materiel_stock ORDER BY nomHisto_materiel_stock ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassHistoStockMateriel = new HistoStockMateriel(); ?>