<?   
class SearchTache
{
	var $conn = null;
	function SearchTache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createSearchTache($params){ 
		$this->conn->AutoExecute("vue_search_tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateSearchTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("vue_search_tache", $params, "UPDATE", "idvue_search_tache = ".$idvue_search_tache); 
	}
	
	function deleteSearchTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  vue_search_tache WHERE idvue_search_tache = $idvue_search_tache"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneSearchTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_search_tache where 1 " ;
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlIn($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotIn($nonumeroSerieExterneMateriel) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($orCommentaireTache)){ $sql .= " OR commentaireTache ".sqlIn($orCommentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; } 
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($idpersonne)){ $sql .= " AND idpersonne ".sqlIn($idpersonne) ; }
			if(!empty($noidpersonne)){ $sql .= " AND idpersonne ".sqlNotIn($idpersonne) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneSearchTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_search_tache where 1 " ;
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlSearch($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotSearch($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlSearch($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotSearch($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlSearch($numeroSerieInterneMateriel,'numeroSerieInterneMateriel') ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotSearch($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlSearch($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotSearch($nonumeroSerieExterneMateriel) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlSearch($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotSearch($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlSearch($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotSearch($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlSearch($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotSearch($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlSearch($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotSearch($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlSearch($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotSearch($noobjetTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlSearch($commentaireTache) ; }
			if(!empty($orCommentaireTache)){ $sql .= " OR commentaireTache ".sqlSearch($orCommentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotSearch($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlSearch($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotSearch($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlSearch($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotSearch($noresultatTache) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlSearch($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotSearch($noidagent_writer) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlSearch($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotSearch($noidsociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlSearch($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotSearch($nosociete_idsociete) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlSearch($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotSearch($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlSearch($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotSearch($noprenomPersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllSearchTache(){ 
		$sql="SELECT * FROM  vue_search_tache"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneSearchTache($name="idvue_search_tache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomVue_search_tache , idvue_search_tache FROM vue_search_tache WHERE  1 ";  
			if(!empty($idmateriel)){ $sql .= " AND idmateriel ".sqlIn($idmateriel) ; }
			if(!empty($noidmateriel)){ $sql .= " AND idmateriel ".sqlNotIn($noidmateriel) ; }
			if(!empty($stock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlIn($stock_materiel_idstock_materiel) ; }
			if(!empty($nostock_materiel_idstock_materiel)){ $sql .= " AND stock_materiel_idstock_materiel ".sqlNotIn($nostock_materiel_idstock_materiel) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($numeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlIn($numeroSerieInterneMateriel) ; }
			if(!empty($nonumeroSerieInterneMateriel)){ $sql .= " AND numeroSerieInterneMateriel ".sqlNotIn($nonumeroSerieInterneMateriel) ; }
			if(!empty($numeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlIn($numeroSerieExterneMateriel) ; }
			if(!empty($nonumeroSerieExterneMateriel)){ $sql .= " AND numeroSerieExterneMateriel ".sqlNotIn($nonumeroSerieExterneMateriel) ; }
			if(!empty($idtache)){ $sql .= " AND idtache ".sqlIn($idtache) ; }
			if(!empty($noidtache)){ $sql .= " AND idtache ".sqlNotIn($noidtache) ; }
			if(!empty($suivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlIn($suivi_idsuivi) ; }
			if(!empty($nosuivi_idsuivi)){ $sql .= " AND suivi_idsuivi ".sqlNotIn($nosuivi_idsuivi) ; }
			if(!empty($statut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlIn($statut_tache_idstatut_tache) ; }
			if(!empty($nostatut_tache_idstatut_tache)){ $sql .= " AND statut_tache_idstatut_tache ".sqlNotIn($nostatut_tache_idstatut_tache) ; }
			if(!empty($type_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlIn($type_tache_idtype_tache) ; }
			if(!empty($notype_tache_idtype_tache)){ $sql .= " AND type_tache_idtype_tache ".sqlNotIn($notype_tache_idtype_tache) ; }
			if(!empty($objetTache)){ $sql .= " AND objetTache ".sqlIn($objetTache) ; }
			if(!empty($noobjetTache)){ $sql .= " AND objetTache ".sqlNotIn($noobjetTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($idsociete)){ $sql .= " AND idsociete ".sqlIn($idsociete) ; }
			if(!empty($noidsociete)){ $sql .= " AND idsociete ".sqlNotIn($noidsociete) ; }
			if(!empty($societe_idsociete)){ $sql .= " AND societe_idsociete ".sqlIn($societe_idsociete) ; }
			if(!empty($nosociete_idsociete)){ $sql .= " AND societe_idsociete ".sqlNotIn($nosociete_idsociete) ; }
			if(!empty($nomPersonne)){ $sql .= " AND nomPersonne ".sqlIn($nomPersonne) ; }
			if(!empty($nonomPersonne)){ $sql .= " AND nomPersonne ".sqlNotIn($nonomPersonne) ; }
			if(!empty($prenomPersonne)){ $sql .= " AND prenomPersonne ".sqlIn($prenomPersonne) ; }
			if(!empty($noprenomPersonne)){ $sql .= " AND prenomPersonne ".sqlNotIn($noprenomPersonne) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomVue_search_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectSearchTache($name="idvue_search_tache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomVue_search_tache , idvue_search_tache FROM vue_search_tache ORDER BY nomVue_search_tache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassSearchTache = new SearchTache(); ?>