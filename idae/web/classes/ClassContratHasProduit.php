<?   
class ContratHasProduit
{
	var $conn = null;
	function ContratHasProduit(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createContratHasProduit($params){ 
		$this->conn->AutoExecute("contrat_has_produit", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateContratHasProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("contrat_has_produit", $params, "UPDATE", "idcontrat_has_produit = ".$idcontrat_has_produit); 
	}
	
	function deleteContratHasProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  contrat_has_produit WHERE idcontrat_has_produit = $idcontrat_has_produit"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 	
		$sql="TRUNCATE TABLE contrat_has_produit "; 
		$this->conn->Execute($sql); 	
	}
	function getOneContratHasProduit($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_contrat_has_produit where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlIn($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotIn($noidcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlIn($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotIn($notype_contrat_idtype_contrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlIn($numContrat) ; }
			if(!empty($codeType_contrat)){ $sql .= " AND codeType_contrat ".sqlIn($codeType_contrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotIn($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlIn($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotIn($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlIn($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotIn($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlIn($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotIn($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlIn($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotIn($nodateClotureContrat) ; }
			if(!empty($idcontrat_old)){ $sql .= " AND idcontrat_old ".sqlIn($idcontrat_old) ; }
			if(!empty($noidcontrat_old)){ $sql .= " AND idcontrat_old ".sqlNotIn($noidcontrat_old) ; }
			if(!empty($idcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlIn($idcontrat_has_produit) ; }
			if(!empty($noidcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlNotIn($noidcontrat_has_produit) ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlIn($contrat_idcontrat) ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotIn($nocontrat_idcontrat) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlIn($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotIn($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   } 
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneContratHasProduit($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_contrat_has_produit where 1 " ;
			if(!empty($idcontrat)){ $sql .= " AND idcontrat ".sqlSearch($idcontrat) ; }
			if(!empty($noidcontrat)){ $sql .= " AND idcontrat ".sqlNotSearch($noidcontrat) ; }
			if(!empty($type_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlSearch($type_contrat_idtype_contrat) ; }
			if(!empty($notype_contrat_idtype_contrat)){ $sql .= " AND type_contrat_idtype_contrat ".sqlNotSearch($notype_contrat_idtype_contrat) ; }
			if(!empty($numContrat)){ $sql .= " AND numContrat ".sqlSearch($numContrat) ; }
			if(!empty($nonumContrat)){ $sql .= " AND numContrat ".sqlNotSearch($nonumContrat) ; }
			if(!empty($dureeContrat)){ $sql .= " AND dureeContrat ".sqlSearch($dureeContrat) ; }
			if(!empty($nodureeContrat)){ $sql .= " AND dureeContrat ".sqlNotSearch($nodureeContrat) ; }
			if(!empty($dateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlSearch($dateDebutContrat) ; }
			if(!empty($nodateDebutContrat)){ $sql .= " AND dateDebutContrat ".sqlNotSearch($nodateDebutContrat) ; }
			if(!empty($dateFinContrat)){ $sql .= " AND dateFinContrat ".sqlSearch($dateFinContrat) ; }
			if(!empty($nodateFinContrat)){ $sql .= " AND dateFinContrat ".sqlNotSearch($nodateFinContrat) ; }
			if(!empty($dateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlSearch($dateClotureContrat) ; }
			if(!empty($nodateClotureContrat)){ $sql .= " AND dateClotureContrat ".sqlNotSearch($nodateClotureContrat) ; }
			if(!empty($idcontrat_old)){ $sql .= " AND idcontrat_old ".sqlSearch($idcontrat_old) ; }
			if(!empty($noidcontrat_old)){ $sql .= " AND idcontrat_old ".sqlNotSearch($noidcontrat_old) ; }
			if(!empty($idcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlSearch($idcontrat_has_produit) ; }
			if(!empty($noidcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlNotSearch($noidcontrat_has_produit) ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlSearch($contrat_idcontrat) ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotSearch($nocontrat_idcontrat) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlSearch($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotSearch($noreferenceInterneProduit) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlSearch($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotSearch($nonomProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllContratHasProduit(){ 
		$sql="SELECT * FROM  contrat_has_produit"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneContratHasProduit($name="idcontrat_has_produit",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomContrat_has_produit , idcontrat_has_produit FROM contrat_has_produit WHERE  1 ";  
			if(!empty($idcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlIn($idcontrat_has_produit) ; }
			if(!empty($noidcontrat_has_produit)){ $sql .= " AND idcontrat_has_produit ".sqlNotIn($noidcontrat_has_produit) ; }
			if(!empty($contrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlIn($contrat_idcontrat) ; }
			if(!empty($nocontrat_idcontrat)){ $sql .= " AND contrat_idcontrat ".sqlNotIn($nocontrat_idcontrat) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomContrat_has_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectContratHasProduit($name="idcontrat_has_produit",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomContrat_has_produit , idcontrat_has_produit FROM contrat_has_produit ORDER BY nomContrat_has_produit ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassContratHasProduit = new ContratHasProduit(); ?>