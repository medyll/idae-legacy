<?   
class IdaeMateriel
{
	var $conn = null;
	function IdaeMateriel(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createIdaeMateriel($params){ 
		$this->conn->AutoExecute("vue_materiel", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_materiel", $params, "UPDATE", "idvue_materiel = ".$idvue_materiel); 
	}
	
	function deleteIdaeMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_materiel WHERE idvue_materiel = $idvue_materiel"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeMateriel($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_materiel where 1 " ;
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlIn($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotIn($noestActifMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlIn($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotIn($nodateCreationMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlIn($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotIn($nopahtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlIn($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotIn($nocommentaireMateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeMateriel($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_materiel where 1 " ;
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlSearch($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotSearch($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlSearch($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotSearch($noestActifMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlSearch($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotSearch($nodateCreationMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlSearch($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotSearch($nopahtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlSearch($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotSearch($nocommentaireMateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeMateriel(){ 
		$sql="SELECT * FROM  vue_materiel"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeMateriel($name="idvue_materiel",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_materiel , idvue_materiel FROM vue_materiel WHERE  1 ";  
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($fournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlIn($fournisseur_idfournisseur) ; }
			if(!empty($nofournisseur_idfournisseur)){ $sql .= " AND fournisseur_idfournisseur ".sqlNotIn($nofournisseur_idfournisseur) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlIn($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotIn($noestActifMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlIn($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotIn($nodateCreationMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlIn($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotIn($nopahtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlIn($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotIn($nocommentaireMateriel) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeMateriel($name="idvue_materiel",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_materiel , idvue_materiel FROM vue_materiel ORDER BY nomVue_materiel ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeMateriel = new IdaeMateriel(); ?>