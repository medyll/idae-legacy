<?   
class LigneVenteClient
{
	var $conn = null;
	function LigneVenteClient(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createLigneVenteClient($params){ 
		$this->conn->AutoExecute("ligne_vente_client", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_vente_client", $params, "UPDATE", "idligne_vente_client = ".$idligne_vente_client); 
	}
	
	function deleteLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_vente_client WHERE idligne_vente_client = $idligne_vente_client"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_vente_client where 1 " ;
			if(!empty($idligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlIn($idligne_vente_client) ; }
			if(!empty($noidligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlNotIn($noidligne_vente_client) ; }
			if(!empty($type_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlIn($type_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($notype_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlNotIn($notype_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($vente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlIn($vente_client_idvente_client) ; }
			if(!empty($novente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlNotIn($novente_client_idvente_client) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($dateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlIn($dateCreationLigne_vente_client) ; }
			if(!empty($nodateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlNotIn($nodateCreationLigne_vente_client) ; }
			if(!empty($dateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlIn($dateDebutLigne_vente_client) ; }
			if(!empty($nodateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlNotIn($nodateDebutLigne_vente_client) ; }
			if(!empty($dateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlIn($dateFinLigne_vente_client) ; }
			if(!empty($nodateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlNotIn($nodateFinLigne_vente_client) ; }
			if(!empty($dateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlIn($dateClotureLigne_vente_client) ; }
			if(!empty($nodateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlNotIn($nodateClotureLigne_vente_client) ; }
			if(!empty($pvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlIn($pvhtLigne_vente_client) ; }
			if(!empty($nopvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlNotIn($nopvhtLigne_vente_client) ; }
			if(!empty($commentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlIn($commentaireLigne_vente_client) ; }
			if(!empty($nocommentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlNotIn($nocommentaireLigne_vente_client) ; }
			if(!empty($quantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlIn($quantiteLigne_vente_client) ; }
			if(!empty($noquantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlNotIn($noquantiteLigne_vente_client) ; }
			if(!empty($ordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlIn($ordreLigne_vente_client) ; }
			if(!empty($noordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlNotIn($noordreLigne_vente_client) ; }
			if(!empty($pahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlIn($pahtLigne_vente_client) ; }
			if(!empty($nopahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlNotIn($nopahtLigne_vente_client) ; }
			if(!empty($idvente_client)){ $sql .= " AND idvente_client ".sqlIn($idvente_client) ; }
			if(!empty($noidvente_client)){ $sql .= " AND idvente_client ".sqlNotIn($noidvente_client) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlIn($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotIn($noagent_idagent) ; }
			if(!empty($type_vente_client_idtype_vente_client)){ $sql .= " AND type_vente_client_idtype_vente_client ".sqlIn($type_vente_client_idtype_vente_client) ; }
			if(!empty($notype_vente_client_idtype_vente_client)){ $sql .= " AND type_vente_client_idtype_vente_client ".sqlNotIn($notype_vente_client_idtype_vente_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlIn($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotIn($noclient_idclient) ; }
			if(!empty($numeroVente_client)){ $sql .= " AND numeroVente_client ".sqlIn($numeroVente_client) ; }
			if(!empty($nonumeroVente_client)){ $sql .= " AND numeroVente_client ".sqlNotIn($nonumeroVente_client) ; }
			if(!empty($objetVente_client)){ $sql .= " AND objetVente_client ".sqlIn($objetVente_client) ; }
			if(!empty($noobjetVente_client)){ $sql .= " AND objetVente_client ".sqlNotIn($noobjetVente_client) ; }
			if(!empty($dateCreationVente_client)){ $sql .= " AND dateCreationVente_client ".sqlIn($dateCreationVente_client) ; }
			if(!empty($nodateCreationVente_client)){ $sql .= " AND dateCreationVente_client ".sqlNotIn($nodateCreationVente_client) ; }
			if(!empty($dateDebutVente_client)){ $sql .= " AND dateDebutVente_client ".sqlIn($dateDebutVente_client) ; }
			if(!empty($nodateDebutVente_client)){ $sql .= " AND dateDebutVente_client ".sqlNotIn($nodateDebutVente_client) ; }
			if(!empty($dateFinVente_client)){ $sql .= " AND dateFinVente_client ".sqlIn($dateFinVente_client) ; }
			if(!empty($nodateFinVente_client)){ $sql .= " AND dateFinVente_client ".sqlNotIn($nodateFinVente_client) ; }
			if(!empty($dateClotureVente_client)){ $sql .= " AND dateClotureVente_client ".sqlIn($dateClotureVente_client) ; }
			if(!empty($nodateClotureVente_client)){ $sql .= " AND dateClotureVente_client ".sqlNotIn($nodateClotureVente_client) ; }
			if(!empty($commentaireVente_client)){ $sql .= " AND commentaireVente_client ".sqlIn($commentaireVente_client) ; }
			if(!empty($nocommentaireVente_client)){ $sql .= " AND commentaireVente_client ".sqlNotIn($nocommentaireVente_client) ; }
			if(!empty($ordreVente_client)){ $sql .= " AND ordreVente_client ".sqlIn($ordreVente_client) ; }
			if(!empty($noordreVente_client)){ $sql .= " AND ordreVente_client ".sqlNotIn($noordreVente_client) ; }
			if(!empty($nomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlIn($nomType_critere_ligne_vente) ; }
			if(!empty($nonomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlNotIn($nonomType_critere_ligne_vente) ; }
			if(!empty($uniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlIn($uniteType_critere_ligne_vente) ; }
			if(!empty($nouniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlNotIn($nouniteType_critere_ligne_vente) ; }
			if(!empty($codeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlIn($codeType_critere_ligne_vente) ; }
			if(!empty($nocodeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlNotIn($nocodeType_critere_ligne_vente) ; }
			if(!empty($ordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlIn($ordreType_critere_ligne_vente) ; }
			if(!empty($noordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlNotIn($noordreType_critere_ligne_vente) ; }
			if(!empty($valeurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlIn($valeurCritere_ligne_vente_client) ; }
			if(!empty($novaleurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlNotIn($novaleurCritere_ligne_vente_client) ; }
			if(!empty($idcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlIn($idcritere_ligne_vente_client) ; }
			if(!empty($noidcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlNotIn($noidcritere_ligne_vente_client) ; }
			if(!empty($idtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlIn($idtype_critere_ligne_vente) ; }
			if(!empty($noidtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlNotIn($noidtype_critere_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneLigneVenteClient($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_ligne_vente_client where 1 " ;
			if(!empty($idligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlSearch($idligne_vente_client) ; }
			if(!empty($noidligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlNotSearch($noidligne_vente_client) ; }
			if(!empty($type_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlSearch($type_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($notype_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlNotSearch($notype_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($vente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlSearch($vente_client_idvente_client) ; }
			if(!empty($novente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlNotSearch($novente_client_idvente_client) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlSearch($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotSearch($noproduit_idproduit) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlSearch($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotSearch($nomateriel_idmateriel) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlSearch($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotSearch($nolocalisation_idlocalisation) ; }
			if(!empty($dateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlSearch($dateCreationLigne_vente_client) ; }
			if(!empty($nodateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlNotSearch($nodateCreationLigne_vente_client) ; }
			if(!empty($dateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlSearch($dateDebutLigne_vente_client) ; }
			if(!empty($nodateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlNotSearch($nodateDebutLigne_vente_client) ; }
			if(!empty($dateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlSearch($dateFinLigne_vente_client) ; }
			if(!empty($nodateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlNotSearch($nodateFinLigne_vente_client) ; }
			if(!empty($dateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlSearch($dateClotureLigne_vente_client) ; }
			if(!empty($nodateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlNotSearch($nodateClotureLigne_vente_client) ; }
			if(!empty($pvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlSearch($pvhtLigne_vente_client) ; }
			if(!empty($nopvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlNotSearch($nopvhtLigne_vente_client) ; }
			if(!empty($commentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlSearch($commentaireLigne_vente_client) ; }
			if(!empty($nocommentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlNotSearch($nocommentaireLigne_vente_client) ; }
			if(!empty($quantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlSearch($quantiteLigne_vente_client) ; }
			if(!empty($noquantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlNotSearch($noquantiteLigne_vente_client) ; }
			if(!empty($ordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlSearch($ordreLigne_vente_client) ; }
			if(!empty($noordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlNotSearch($noordreLigne_vente_client) ; }
			if(!empty($pahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlSearch($pahtLigne_vente_client) ; }
			if(!empty($nopahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlNotSearch($nopahtLigne_vente_client) ; }
			if(!empty($idvente_client)){ $sql .= " AND idvente_client ".sqlSearch($idvente_client) ; }
			if(!empty($noidvente_client)){ $sql .= " AND idvente_client ".sqlNotSearch($noidvente_client) ; }
			if(!empty($agent_idagent)){ $sql .= " AND agent_idagent ".sqlSearch($agent_idagent) ; }
			if(!empty($noagent_idagent)){ $sql .= " AND agent_idagent ".sqlNotSearch($noagent_idagent) ; }
			if(!empty($type_vente_client_idtype_vente_client)){ $sql .= " AND type_vente_client_idtype_vente_client ".sqlSearch($type_vente_client_idtype_vente_client) ; }
			if(!empty($notype_vente_client_idtype_vente_client)){ $sql .= " AND type_vente_client_idtype_vente_client ".sqlNotSearch($notype_vente_client_idtype_vente_client) ; }
			if(!empty($client_idclient)){ $sql .= " AND client_idclient ".sqlSearch($client_idclient) ; }
			if(!empty($noclient_idclient)){ $sql .= " AND client_idclient ".sqlNotSearch($noclient_idclient) ; }
			if(!empty($numeroVente_client)){ $sql .= " AND numeroVente_client ".sqlSearch($numeroVente_client) ; }
			if(!empty($nonumeroVente_client)){ $sql .= " AND numeroVente_client ".sqlNotSearch($nonumeroVente_client) ; }
			if(!empty($objetVente_client)){ $sql .= " AND objetVente_client ".sqlSearch($objetVente_client) ; }
			if(!empty($noobjetVente_client)){ $sql .= " AND objetVente_client ".sqlNotSearch($noobjetVente_client) ; }
			if(!empty($dateCreationVente_client)){ $sql .= " AND dateCreationVente_client ".sqlSearch($dateCreationVente_client) ; }
			if(!empty($nodateCreationVente_client)){ $sql .= " AND dateCreationVente_client ".sqlNotSearch($nodateCreationVente_client) ; }
			if(!empty($dateDebutVente_client)){ $sql .= " AND dateDebutVente_client ".sqlSearch($dateDebutVente_client) ; }
			if(!empty($nodateDebutVente_client)){ $sql .= " AND dateDebutVente_client ".sqlNotSearch($nodateDebutVente_client) ; }
			if(!empty($dateFinVente_client)){ $sql .= " AND dateFinVente_client ".sqlSearch($dateFinVente_client) ; }
			if(!empty($nodateFinVente_client)){ $sql .= " AND dateFinVente_client ".sqlNotSearch($nodateFinVente_client) ; }
			if(!empty($dateClotureVente_client)){ $sql .= " AND dateClotureVente_client ".sqlSearch($dateClotureVente_client) ; }
			if(!empty($nodateClotureVente_client)){ $sql .= " AND dateClotureVente_client ".sqlNotSearch($nodateClotureVente_client) ; }
			if(!empty($commentaireVente_client)){ $sql .= " AND commentaireVente_client ".sqlSearch($commentaireVente_client) ; }
			if(!empty($nocommentaireVente_client)){ $sql .= " AND commentaireVente_client ".sqlNotSearch($nocommentaireVente_client) ; }
			if(!empty($ordreVente_client)){ $sql .= " AND ordreVente_client ".sqlSearch($ordreVente_client) ; }
			if(!empty($noordreVente_client)){ $sql .= " AND ordreVente_client ".sqlNotSearch($noordreVente_client) ; }
			if(!empty($nomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlSearch($nomType_critere_ligne_vente) ; }
			if(!empty($nonomType_critere_ligne_vente)){ $sql .= " AND nomType_critere_ligne_vente ".sqlNotSearch($nonomType_critere_ligne_vente) ; }
			if(!empty($uniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlSearch($uniteType_critere_ligne_vente) ; }
			if(!empty($nouniteType_critere_ligne_vente)){ $sql .= " AND uniteType_critere_ligne_vente ".sqlNotSearch($nouniteType_critere_ligne_vente) ; }
			if(!empty($codeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlSearch($codeType_critere_ligne_vente) ; }
			if(!empty($nocodeType_critere_ligne_vente)){ $sql .= " AND codeType_critere_ligne_vente ".sqlNotSearch($nocodeType_critere_ligne_vente) ; }
			if(!empty($ordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlSearch($ordreType_critere_ligne_vente) ; }
			if(!empty($noordreType_critere_ligne_vente)){ $sql .= " AND ordreType_critere_ligne_vente ".sqlNotSearch($noordreType_critere_ligne_vente) ; }
			if(!empty($valeurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlSearch($valeurCritere_ligne_vente_client) ; }
			if(!empty($novaleurCritere_ligne_vente_client)){ $sql .= " AND valeurCritere_ligne_vente_client ".sqlNotSearch($novaleurCritere_ligne_vente_client) ; }
			if(!empty($idcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlSearch($idcritere_ligne_vente_client) ; }
			if(!empty($noidcritere_ligne_vente_client)){ $sql .= " AND idcritere_ligne_vente_client ".sqlNotSearch($noidcritere_ligne_vente_client) ; }
			if(!empty($idtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlSearch($idtype_critere_ligne_vente) ; }
			if(!empty($noidtype_critere_ligne_vente)){ $sql .= " AND idtype_critere_ligne_vente ".sqlNotSearch($noidtype_critere_ligne_vente) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllLigneVenteClient(){ 
		$sql="SELECT * FROM  ligne_vente_client"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneLigneVenteClient($name="idligne_vente_client",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_vente_client , idligne_vente_client FROM WHERE 1 ";  
			if(!empty($idligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlIn($idligne_vente_client) ; }
			if(!empty($noidligne_vente_client)){ $sql .= " AND idligne_vente_client ".sqlNotIn($noidligne_vente_client) ; }
			if(!empty($type_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlIn($type_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($notype_ligne_vente_idtype_ligne_vente)){ $sql .= " AND type_ligne_vente_idtype_ligne_vente ".sqlNotIn($notype_ligne_vente_idtype_ligne_vente) ; }
			if(!empty($vente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlIn($vente_client_idvente_client) ; }
			if(!empty($novente_client_idvente_client)){ $sql .= " AND vente_client_idvente_client ".sqlNotIn($novente_client_idvente_client) ; }
			if(!empty($produit_idproduit)){ $sql .= " AND produit_idproduit ".sqlIn($produit_idproduit) ; }
			if(!empty($noproduit_idproduit)){ $sql .= " AND produit_idproduit ".sqlNotIn($noproduit_idproduit) ; }
			if(!empty($materiel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlIn($materiel_idmateriel) ; }
			if(!empty($nomateriel_idmateriel)){ $sql .= " AND materiel_idmateriel ".sqlNotIn($nomateriel_idmateriel) ; }
			if(!empty($localisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlIn($localisation_idlocalisation) ; }
			if(!empty($nolocalisation_idlocalisation)){ $sql .= " AND localisation_idlocalisation ".sqlNotIn($nolocalisation_idlocalisation) ; }
			if(!empty($dateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlIn($dateCreationLigne_vente_client) ; }
			if(!empty($nodateCreationLigne_vente_client)){ $sql .= " AND dateCreationLigne_vente_client ".sqlNotIn($nodateCreationLigne_vente_client) ; }
			if(!empty($dateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlIn($dateDebutLigne_vente_client) ; }
			if(!empty($nodateDebutLigne_vente_client)){ $sql .= " AND dateDebutLigne_vente_client ".sqlNotIn($nodateDebutLigne_vente_client) ; }
			if(!empty($dateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlIn($dateFinLigne_vente_client) ; }
			if(!empty($nodateFinLigne_vente_client)){ $sql .= " AND dateFinLigne_vente_client ".sqlNotIn($nodateFinLigne_vente_client) ; }
			if(!empty($dateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlIn($dateClotureLigne_vente_client) ; }
			if(!empty($nodateClotureLigne_vente_client)){ $sql .= " AND dateClotureLigne_vente_client ".sqlNotIn($nodateClotureLigne_vente_client) ; }
			if(!empty($pvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlIn($pvhtLigne_vente_client) ; }
			if(!empty($nopvhtLigne_vente_client)){ $sql .= " AND pvhtLigne_vente_client ".sqlNotIn($nopvhtLigne_vente_client) ; }
			if(!empty($commentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlIn($commentaireLigne_vente_client) ; }
			if(!empty($nocommentaireLigne_vente_client)){ $sql .= " AND commentaireLigne_vente_client ".sqlNotIn($nocommentaireLigne_vente_client) ; }
			if(!empty($quantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlIn($quantiteLigne_vente_client) ; }
			if(!empty($noquantiteLigne_vente_client)){ $sql .= " AND quantiteLigne_vente_client ".sqlNotIn($noquantiteLigne_vente_client) ; }
			if(!empty($ordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlIn($ordreLigne_vente_client) ; }
			if(!empty($noordreLigne_vente_client)){ $sql .= " AND ordreLigne_vente_client ".sqlNotIn($noordreLigne_vente_client) ; }
			if(!empty($pahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlIn($pahtLigne_vente_client) ; }
			if(!empty($nopahtLigne_vente_client)){ $sql .= " AND pahtLigne_vente_client ".sqlNotIn($nopahtLigne_vente_client) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_vente_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectLigneVenteClient($name="idligne_vente_client",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_vente_client , idligne_vente_client FROM ligne_vente_client ORDER BY nomLigne_vente_client ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassLigneVenteClient = new LigneVenteClient(); ?>