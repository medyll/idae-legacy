<?   
class Tache
{
	var $conn = null;
	function Tache(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function createTache($params){ 
		$this->conn->AutoExecute("tache", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("tache", $params, "UPDATE", "idtache = ".$idtache); 
	}
	
	function deleteTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  tache WHERE idtache = $idtache"; 
		return $this->conn->Execute($sql); 	
	}
	function truncate(){	
		$sql="TRUNCATE TABLE tache"; 
		return $this->conn->Execute($sql); 	
	}
	function getOneTache($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from vue_tache where 1 " ;
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
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(isset($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($dateClotureTache)){ $sql .= " AND dateClotureTache ".sqlIn($dateClotureTache) ; }
			if(!empty($nodateClotureTache)){ $sql .= " AND dateClotureTache ".sqlNotIn($nodateClotureTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($nomType_tache)){ $sql .= " AND nomType_tache ".sqlIn($nomType_tache) ; }
			if(!empty($nonomType_tache)){ $sql .= " AND nomType_tache ".sqlNotIn($nonomType_tache) ; }
			if(!empty($ordreType_tache)){ $sql .= " AND ordreType_tache ".sqlIn($ordreType_tache) ; }
			if(!empty($noordreType_tache)){ $sql .= " AND ordreType_tache ".sqlNotIn($noordreType_tache) ; }
			if(!empty($idtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlIn($idtache_has_personne) ; }
			if(!empty($noidtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlNotIn($noidtache_has_personne) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlIn($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotIn($nopersonne_idpersonne) ; }
			if(!empty($idtype_tache)){ $sql .= " AND idtype_tache ".sqlIn($idtype_tache) ; }
			if(!empty($noidtype_tache)){ $sql .= " AND idtype_tache ".sqlNotIn($noidtype_tache) ; }
			if(!empty($commentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlIn($commentaireType_tache) ; }
			if(!empty($nocommentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlNotIn($nocommentaireType_tache) ; }
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlIn($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotIn($noidagent_has_tache) ; }
			//
			if(!empty($oldDateDebutTache)){ $sql .= " AND dateDebutTache < ".$oldDateDebutTache ; }
			//
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlIn($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotIn($noproprietaireAgent_has_tache) ; }
			if(!empty($idstatut_tache)){ $sql .= " AND idstatut_tache ".sqlIn($idstatut_tache) ; }
			if(!empty($noidstatut_tache)){ $sql .= " AND idstatut_tache ".sqlNotIn($noidstatut_tache) ; }
			if(!empty($nomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlIn($nomStatut_tache) ; }
			if(!empty($nonomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlNotIn($nonomStatut_tache) ; }
			if(!empty($valeurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlIn($valeurStatut_tache) ; }
			if(!empty($novaleurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlNotIn($novaleurStatut_tache) ; }
			if(!empty($commentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlIn($commentaireStatut_tache) ; }
			if(!empty($nocommentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlNotIn($nocommentaireStatut_tache) ; }
			if(!empty($ordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlIn($ordreStatut_tache) ; }
			if(!empty($noordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlNotIn($noordreStatut_tache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlIn($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotIn($nomigrateidtacheTech) ; }
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlIn($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotIn($noidsuivi) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlIn($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotIn($nocodeType_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlIn($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotIn($notype_suivi_idtype_suivi) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlIn($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotIn($noheureCreationTache) ; }
			if(!empty($codeType_tache)){ $sql .= " AND codeType_tache ".sqlIn($codeType_tache) ; }
			if(!empty($nocodeType_tache)){ $sql .= " AND codeType_tache ".sqlNotIn($nocodeType_tache) ; }
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlIn($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotIn($nocodeStatut_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneTache($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from vue_tache where 1 " ;
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
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlSearch($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotSearch($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlSearch($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotSearch($noheureDebutTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlSearch($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotSearch($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlSearch($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotSearch($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlSearch($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotSearch($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlSearch($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotSearch($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlSearch($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotSearch($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlSearch($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotSearch($noresultatTache) ; }
			if(!empty($nomType_tache)){ $sql .= " AND nomType_tache ".sqlSearch($nomType_tache) ; }
			if(!empty($nonomType_tache)){ $sql .= " AND nomType_tache ".sqlNotSearch($nonomType_tache) ; }
			if(!empty($ordreType_tache)){ $sql .= " AND ordreType_tache ".sqlSearch($ordreType_tache) ; }
			if(!empty($noordreType_tache)){ $sql .= " AND ordreType_tache ".sqlNotSearch($noordreType_tache) ; }
			if(!empty($idtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlSearch($idtache_has_personne) ; }
			if(!empty($noidtache_has_personne)){ $sql .= " AND idtache_has_personne ".sqlNotSearch($noidtache_has_personne) ; }
			if(!empty($personne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlSearch($personne_idpersonne) ; }
			if(!empty($nopersonne_idpersonne)){ $sql .= " AND personne_idpersonne ".sqlNotSearch($nopersonne_idpersonne) ; }
			if(!empty($idtype_tache)){ $sql .= " AND idtype_tache ".sqlSearch($idtype_tache) ; }
			if(!empty($noidtype_tache)){ $sql .= " AND idtype_tache ".sqlNotSearch($noidtype_tache) ; }
			if(!empty($commentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlSearch($commentaireType_tache) ; }
			if(!empty($nocommentaireType_tache)){ $sql .= " AND commentaireType_tache ".sqlNotSearch($nocommentaireType_tache) ; }
			if(!empty($idagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlSearch($idagent_has_tache) ; }
			if(!empty($noidagent_has_tache)){ $sql .= " AND idagent_has_tache ".sqlNotSearch($noidagent_has_tache) ; }
			 
			if(!empty($proprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlSearch($proprietaireAgent_has_tache) ; }
			if(!empty($noproprietaireAgent_has_tache)){ $sql .= " AND proprietaireAgent_has_tache ".sqlNotSearch($noproprietaireAgent_has_tache) ; }
			if(!empty($idstatut_tache)){ $sql .= " AND idstatut_tache ".sqlSearch($idstatut_tache) ; }
			if(!empty($noidstatut_tache)){ $sql .= " AND idstatut_tache ".sqlNotSearch($noidstatut_tache) ; }
			if(!empty($nomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlSearch($nomStatut_tache) ; }
			if(!empty($nonomStatut_tache)){ $sql .= " AND nomStatut_tache ".sqlNotSearch($nonomStatut_tache) ; }
			if(!empty($valeurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlSearch($valeurStatut_tache) ; }
			if(!empty($novaleurStatut_tache)){ $sql .= " AND valeurStatut_tache ".sqlNotSearch($novaleurStatut_tache) ; }
			if(!empty($commentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlSearch($commentaireStatut_tache) ; }
			if(!empty($nocommentaireStatut_tache)){ $sql .= " AND commentaireStatut_tache ".sqlNotSearch($nocommentaireStatut_tache) ; }
			if(!empty($ordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlSearch($ordreStatut_tache) ; }
			if(!empty($noordreStatut_tache)){ $sql .= " AND ordreStatut_tache ".sqlNotSearch($noordreStatut_tache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlSearch($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotSearch($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlSearch($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotSearch($nomigrateidtacheTech) ; }
			if(!empty($idsuivi)){ $sql .= " AND idsuivi ".sqlSearch($idsuivi) ; }
			if(!empty($noidsuivi)){ $sql .= " AND idsuivi ".sqlNotSearch($noidsuivi) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlSearch($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotSearch($noidagent_writer) ; }
			if(!empty($codeType_suivi)){ $sql .= " AND codeType_suivi ".sqlSearch($codeType_suivi) ; }
			if(!empty($nocodeType_suivi)){ $sql .= " AND codeType_suivi ".sqlNotSearch($nocodeType_suivi) ; }
			if(!empty($type_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlSearch($type_suivi_idtype_suivi) ; }
			if(!empty($notype_suivi_idtype_suivi)){ $sql .= " AND type_suivi_idtype_suivi ".sqlNotSearch($notype_suivi_idtype_suivi) ; }
			if(!empty($heureCreationTache)){ $sql .= " AND heureCreationTache ".sqlSearch($heureCreationTache) ; }
			if(!empty($noheureCreationTache)){ $sql .= " AND heureCreationTache ".sqlNotSearch($noheureCreationTache) ; }
			if(!empty($codeType_tache)){ $sql .= " AND codeType_tache ".sqlSearch($codeType_tache) ; }
			if(!empty($nocodeType_tache)){ $sql .= " AND codeType_tache ".sqlNotSearch($nocodeType_tache) ; }
			if(!empty($codeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlSearch($codeStatut_tache) ; }
			if(!empty($nocodeStatut_tache)){ $sql .= " AND codeStatut_tache ".sqlNotSearch($nocodeStatut_tache) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	 
	function getAllTache(){ 
		$sql="SELECT * FROM  tache"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneTache($name="idtache",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomTache , idtache FROM tache WHERE  1 ";  
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
			if(!empty($dateCreationTache)){ $sql .= " AND dateCreationTache ".sqlIn($dateCreationTache) ; }
			if(!empty($nodateCreationTache)){ $sql .= " AND dateCreationTache ".sqlNotIn($nodateCreationTache) ; }
			if(!empty($heureDebutTache)){ $sql .= " AND heureDebutTache ".sqlIn($heureDebutTache) ; }
			if(!empty($noheureDebutTache)){ $sql .= " AND heureDebutTache ".sqlNotIn($noheureDebutTache) ; }
			if(!empty($dateDebutTache)){ $sql .= " AND dateDebutTache ".sqlIn($dateDebutTache) ; }
			if(!empty($nodateDebutTache)){ $sql .= " AND dateDebutTache ".sqlNotIn($nodateDebutTache) ; }
			if(!empty($heureFinTache)){ $sql .= " AND heureFinTache ".sqlIn($heureFinTache) ; }
			if(!empty($noheureFinTache)){ $sql .= " AND heureFinTache ".sqlNotIn($noheureFinTache) ; }
			if(!empty($dateFinTache)){ $sql .= " AND dateFinTache ".sqlIn($dateFinTache) ; }
			if(!empty($nodateFinTache)){ $sql .= " AND dateFinTache ".sqlNotIn($nodateFinTache) ; }
			if(!empty($commentaireTache)){ $sql .= " AND commentaireTache ".sqlIn($commentaireTache) ; }
			if(!empty($nocommentaireTache)){ $sql .= " AND commentaireTache ".sqlNotIn($nocommentaireTache) ; }
			if(!empty($valeurTache)){ $sql .= " AND valeurTache ".sqlIn($valeurTache) ; }
			if(!empty($novaleurTache)){ $sql .= " AND valeurTache ".sqlNotIn($novaleurTache) ; }
			if(!empty($resultatTache)){ $sql .= " AND resultatTache ".sqlIn($resultatTache) ; }
			if(!empty($noresultatTache)){ $sql .= " AND resultatTache ".sqlNotIn($noresultatTache) ; }
			if(!empty($migrateidtache)){ $sql .= " AND migrateidtache ".sqlIn($migrateidtache) ; }
			if(!empty($nomigrateidtache)){ $sql .= " AND migrateidtache ".sqlNotIn($nomigrateidtache) ; }
			if(!empty($migrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlIn($migrateidtacheTech) ; }
			if(!empty($nomigrateidtacheTech)){ $sql .= " AND migrateidtacheTech ".sqlNotIn($nomigrateidtacheTech) ; }
			if(!empty($idagent_writer)){ $sql .= " AND idagent_writer ".sqlIn($idagent_writer) ; }
			if(!empty($noidagent_writer)){ $sql .= " AND idagent_writer ".sqlNotIn($noidagent_writer) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomTache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectTache($name="idtache",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomTache , idtache FROM tache ORDER BY nomTache ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassTache = new Tache(); ?>