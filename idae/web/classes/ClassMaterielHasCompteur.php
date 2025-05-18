<?   
class MaterielHasCompteur
{
	var $conn = null;
	function MaterielHasCompteur(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createMaterielHasCompteur($params){ 
		$this->conn->AutoExecute("materiel_has_compteur", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("materiel_has_compteur", $params, "UPDATE", "idmateriel_has_compteur = ".$idmateriel_has_compteur); 
	}
	
	function deleteMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  materiel_has_compteur WHERE idmateriel_has_compteur = $idmateriel_has_compteur"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){ 
		$sql="truncate table  materiel_has_compteur "; 
		return $this->conn->Execute($sql); 	
	}
	function getOneMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_materiel_has_compteur where 1 " ;
			if(!empty($idmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlIn($idmateriel_has_compteur) ; }
			if(!empty($noidmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlNotIn($noidmateriel_has_compteur) ; }
			if(!empty($lt_idmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur < '".$lt_idmateriel_has_compteur."'" ; }
			if(!empty($gt_idmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur > '".$gt_idmateriel_has_compteur."'" ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($lt_idmateriel)){ $sql .= " AND idmateriel < '".$lt_idmateriel."'" ; }
			if(!empty($gt_idmateriel)){ $sql .= " AND idmateriel > '".$gt_idmateriel."'" ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($lt_stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel < '".$lt_stock_materiel_idstock_materiel."'" ; }
			if(!empty($gt_stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel > '".$gt_stock_materiel_idstock_materiel."'" ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($lt_produit_idproduit)){ $sql .= " AND produit_idproduit < '".$lt_produit_idproduit."'" ; }
			if(!empty($gt_produit_idproduit)){ $sql .= " AND produit_idproduit > '".$gt_produit_idproduit."'" ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($lt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel < '".$lt_numeroSerieInterneMateriel."'" ; }
			if(!empty($gt_numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel > '".$gt_numeroSerieInterneMateriel."'" ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlIn($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotIn($nonumeroSerieExterneMateriel) ; }
			if(!empty($lt_numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel < '".$lt_numeroSerieExterneMateriel."'" ; }
			if(!empty($gt_numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel > '".$gt_numeroSerieExterneMateriel."'" ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlIn($dateCreationMateriel) ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotIn($nodateCreationMateriel) ; }
			if(!empty($lt_dateCreationMateriel)){ $sql .= " AND dateCreationMateriel < '".$lt_dateCreationMateriel."'" ; }
			if(!empty($gt_dateCreationMateriel)){ $sql .= " AND dateCreationMateriel > '".$gt_dateCreationMateriel."'" ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlIn($dateAcquisitionMateriel) ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotIn($nodateAcquisitionMateriel) ; }
			if(!empty($lt_dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel < '".$lt_dateAcquisitionMateriel."'" ; }
			if(!empty($gt_dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel > '".$gt_dateAcquisitionMateriel."'" ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlIn($dateFinMateriel) ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotIn($nodateFinMateriel) ; }
			if(!empty($lt_dateFinMateriel)){ $sql .= " AND dateFinMateriel < '".$lt_dateFinMateriel."'" ; }
			if(!empty($gt_dateFinMateriel)){ $sql .= " AND dateFinMateriel > '".$gt_dateFinMateriel."'" ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlIn($estActifMateriel) ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotIn($noestActifMateriel) ; }
			if(!empty($lt_estActifMateriel)){ $sql .= " AND estActifMateriel < '".$lt_estActifMateriel."'" ; }
			if(!empty($gt_estActifMateriel)){ $sql .= " AND estActifMateriel > '".$gt_estActifMateriel."'" ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlIn($pahtMateriel) ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotIn($nopahtMateriel) ; }
			if(!empty($lt_pahtMateriel)){ $sql .= " AND pahtMateriel < '".$lt_pahtMateriel."'" ; }
			if(!empty($gt_pahtMateriel)){ $sql .= " AND pahtMateriel > '".$gt_pahtMateriel."'" ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlIn($pvhtMateriel) ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotIn($nopvhtMateriel) ; }
			if(!empty($lt_pvhtMateriel)){ $sql .= " AND pvhtMateriel < '".$lt_pvhtMateriel."'" ; }
			if(!empty($gt_pvhtMateriel)){ $sql .= " AND pvhtMateriel > '".$gt_pvhtMateriel."'" ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlIn($commentaireMateriel) ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotIn($nocommentaireMateriel) ; }
			if(!empty($lt_commentaireMateriel)){ $sql .= " AND commentaireMateriel < '".$lt_commentaireMateriel."'" ; }
			if(!empty($gt_commentaireMateriel)){ $sql .= " AND commentaireMateriel > '".$gt_commentaireMateriel."'" ; }
			if(!empty($idcompteur)){ $sql .= " AND idcompteur ".sqlIn($idcompteur) ; }
			if(!empty($noidcompteur)){ $sql .= " AND idcompteur ".sqlNotIn($noidcompteur) ; }
			if(!empty($lt_idcompteur)){ $sql .= " AND idcompteur < '".$lt_idcompteur."'" ; }
			if(!empty($gt_idcompteur)){ $sql .= " AND idcompteur > '".$gt_idcompteur."'" ; }
			if(!empty($dateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlIn($dateCreationCompteur) ; }
			if(!empty($nodateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlNotIn($nodateCreationCompteur) ; }
			if(!empty($lt_dateCreationCompteur)){ $sql .= " AND dateCreationCompteur < '".$lt_dateCreationCompteur."'" ; }
			if(!empty($gt_dateCreationCompteur)){ $sql .= " AND dateCreationCompteur > '".$gt_dateCreationCompteur."'" ; }
			if(!empty($heureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlIn($heureCreationCompteur) ; }
			if(!empty($noheureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlNotIn($noheureCreationCompteur) ; }
			if(!empty($lt_heureCreationCompteur)){ $sql .= " AND heureCreationCompteur < '".$lt_heureCreationCompteur."'" ; }
			if(!empty($gt_heureCreationCompteur)){ $sql .= " AND heureCreationCompteur > '".$gt_heureCreationCompteur."'" ; }
			if(!empty($valeurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlIn($valeurCompteurNB) ; }
			if(!empty($novaleurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlNotIn($novaleurCompteurNB) ; }
			if(!empty($lt_valeurCompteurNB)){ $sql .= " AND valeurCompteurNB < '".$lt_valeurCompteurNB."'" ; }
			if(!empty($gt_valeurCompteurNB)){ $sql .= " AND valeurCompteurNB > '".$gt_valeurCompteurNB."'" ; }
			if(!empty($valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlIn($valeurCompteurCouleur) ; }
			if(!empty($novaleurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlNotIn($novaleurCompteurCouleur) ; }
			if(!empty($lt_valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur < '".$lt_valeurCompteurCouleur."'" ; }
			if(!empty($gt_valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur > '".$gt_valeurCompteurCouleur."'" ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlIn($saisiePar) ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotIn($nosaisiePar) ; }
			if(!empty($lt_saisiePar)){ $sql .= " AND saisiePar < '".$lt_saisiePar."'" ; }
			if(!empty($gt_saisiePar)){ $sql .= " AND saisiePar > '".$gt_saisiePar."'" ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($lt_materiel_idmateriel)){ $sql .= " AND materiel_idmateriel < '".$lt_materiel_idmateriel."'" ; }
			if(!empty($gt_materiel_idmateriel)){ $sql .= " AND materiel_idmateriel > '".$gt_materiel_idmateriel."'" ; }
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlIn($compteur_idcompteur) ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotIn($nocompteur_idcompteur) ; }
			if(!empty($lt_compteur_idcompteur)){ $sql .= " AND compteur_idcompteur < '".$lt_compteur_idcompteur."'" ; }
			if(!empty($gt_compteur_idcompteur)){ $sql .= " AND compteur_idcompteur > '".$gt_compteur_idcompteur."'" ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlIn($nomProduit) ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotIn($nonomProduit) ; }
			if(!empty($lt_nomProduit)){ $sql .= " AND nomProduit < '".$lt_nomProduit."'" ; }
			if(!empty($gt_nomProduit)){ $sql .= " AND nomProduit > '".$gt_nomProduit."'" ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlIn($referenceInterneProduit) ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotIn($noreferenceInterneProduit) ; }
			if(!empty($lt_referenceInterneProduit)){ $sql .= " AND referenceInterneProduit < '".$lt_referenceInterneProduit."'" ; }
			if(!empty($gt_referenceInterneProduit)){ $sql .= " AND referenceInterneProduit > '".$gt_referenceInterneProduit."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneMaterielHasCompteur($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_materiel_has_compteur where 1 " ;
			if(!empty($idmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlSearch($idmateriel_has_compteur,"idmateriel_has_compteur") ; }
			if(!empty($noidmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlNotSearch($noidmateriel_has_compteur) ; }
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel,"idmateriel") ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel,"stock_materiel_idstock_materiel") ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit,"produit_idproduit") ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel,"numeroSerieInterneMateriel") ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlSearch($numeroSerieExterneMateriel,"numeroSerieExterneMateriel") ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotSearch($nonumeroSerieExterneMateriel) ; }
			if(!empty($dateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlSearch($dateCreationMateriel,"dateCreationMateriel") ; }
			if(!empty($nodateCreationMateriel)){ $sql .= " AND dateCreationMateriel ".sqlNotSearch($nodateCreationMateriel) ; }
			if(!empty($dateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlSearch($dateAcquisitionMateriel,"dateAcquisitionMateriel") ; }
			if(!empty($nodateAcquisitionMateriel)){ $sql .= " AND dateAcquisitionMateriel ".sqlNotSearch($nodateAcquisitionMateriel) ; }
			if(!empty($dateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlSearch($dateFinMateriel,"dateFinMateriel") ; }
			if(!empty($nodateFinMateriel)){ $sql .= " AND dateFinMateriel ".sqlNotSearch($nodateFinMateriel) ; }
			if(!empty($estActifMateriel)){ $sql .= " AND estActifMateriel ".sqlSearch($estActifMateriel,"estActifMateriel") ; }
			if(!empty($noestActifMateriel)){ $sql .= " AND estActifMateriel ".sqlNotSearch($noestActifMateriel) ; }
			if(!empty($pahtMateriel)){ $sql .= " AND pahtMateriel ".sqlSearch($pahtMateriel,"pahtMateriel") ; }
			if(!empty($nopahtMateriel)){ $sql .= " AND pahtMateriel ".sqlNotSearch($nopahtMateriel) ; }
			if(!empty($pvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlSearch($pvhtMateriel,"pvhtMateriel") ; }
			if(!empty($nopvhtMateriel)){ $sql .= " AND pvhtMateriel ".sqlNotSearch($nopvhtMateriel) ; }
			if(!empty($commentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlSearch($commentaireMateriel,"commentaireMateriel") ; }
			if(!empty($nocommentaireMateriel)){ $sql .= " AND commentaireMateriel ".sqlNotSearch($nocommentaireMateriel) ; }
			if(!empty($idcompteur)){ $sql .= " AND idcompteur ".sqlSearch($idcompteur,"idcompteur") ; }
			if(!empty($noidcompteur)){ $sql .= " AND idcompteur ".sqlNotSearch($noidcompteur) ; }
			if(!empty($dateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlSearch($dateCreationCompteur,"dateCreationCompteur") ; }
			if(!empty($nodateCreationCompteur)){ $sql .= " AND dateCreationCompteur ".sqlNotSearch($nodateCreationCompteur) ; }
			if(!empty($heureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlSearch($heureCreationCompteur,"heureCreationCompteur") ; }
			if(!empty($noheureCreationCompteur)){ $sql .= " AND heureCreationCompteur ".sqlNotSearch($noheureCreationCompteur) ; }
			if(!empty($valeurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlSearch($valeurCompteurNB,"valeurCompteurNB") ; }
			if(!empty($novaleurCompteurNB)){ $sql .= " AND valeurCompteurNB ".sqlNotSearch($novaleurCompteurNB) ; }
			if(!empty($valeurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlSearch($valeurCompteurCouleur,"valeurCompteurCouleur") ; }
			if(!empty($novaleurCompteurCouleur)){ $sql .= " AND valeurCompteurCouleur ".sqlNotSearch($novaleurCompteurCouleur) ; }
			if(!empty($saisiePar)){ $sql .= " AND saisiePar ".sqlSearch($saisiePar,"saisiePar") ; }
			if(!empty($nosaisiePar)){ $sql .= " AND saisiePar ".sqlNotSearch($nosaisiePar) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel,"materiel_idmateriel") ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlSearch($compteur_idcompteur,"compteur_idcompteur") ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotSearch($nocompteur_idcompteur) ; }
			if(!empty($nomProduit)){ $sql .= " AND nomProduit ".sqlSearch($nomProduit,"nomProduit") ; }
			if(!empty($nonomProduit)){ $sql .= " AND nomProduit ".sqlNotSearch($nonomProduit) ; }
			if(!empty($referenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlSearch($referenceInterneProduit,"referenceInterneProduit") ; }
			if(!empty($noreferenceInterneProduit)){ $sql .= " AND referenceInterneProduit ".sqlNotSearch($noreferenceInterneProduit) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllMaterielHasCompteur(){ 
		$sql="SELECT * FROM  materiel_has_compteur"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneMaterielHasCompteur($name="idmateriel_has_compteur",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomMateriel_has_compteur , idmateriel_has_compteur FROM materiel_has_compteur WHERE  1 ";  
			if(!empty($idmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlIn($idmateriel_has_compteur) ; }
			if(!empty($noidmateriel_has_compteur)){ $sql .= " AND idmateriel_has_compteur ".sqlNotIn($noidmateriel_has_compteur) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($compteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlIn($compteur_idcompteur) ; }
			if(!empty($nocompteur_idcompteur)){ $sql .= " AND compteur_idcompteur ".sqlNotIn($nocompteur_idcompteur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomMateriel_has_compteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectMaterielHasCompteur($name="idmateriel_has_compteur",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomMateriel_has_compteur , idmateriel_has_compteur FROM materiel_has_compteur ORDER BY nomMateriel_has_compteur ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassMaterielHasCompteur = new MaterielHasCompteur(); ?>