<?   
class IdaeLigneParcProspect
{
	var $conn = null;
	function IdaeLigneParcProspect(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeLigneParcProspect($params){ 
		$this->conn->AutoExecute("ligne_parc_prospect", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeLigneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("ligne_parc_prospect", $params, "UPDATE", "idligne_parc_prospect = ".$idligne_parc_prospect); 
	}
	
	function deleteIdaeLigneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  ligne_parc_prospect WHERE idligne_parc_prospect = $idligne_parc_prospect"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeLigneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from ligne_parc_prospect where 1 " ;
			if(!empty($idligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlIn($idligne_parc_prospect) ; }
			if(!empty($noidligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlNotIn($noidligne_parc_prospect) ; }
			if(!empty($parc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlIn($parc_prospect_idparc_prospect) ; }
			if(!empty($noparc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlNotIn($noparc_prospect_idparc_prospect) ; }
			if(!empty($quantite)){ $sql .= " AND quantite ".sqlIn($quantite) ; }
			if(!empty($noquantite)){ $sql .= " AND quantite ".sqlNotIn($noquantite) ; }
			if(!empty($segment)){ $sql .= " AND segment ".sqlIn($segment) ; }
			if(!empty($nosegment)){ $sql .= " AND segment ".sqlNotIn($nosegment) ; }
			if(!empty($fax)){ $sql .= " AND fax ".sqlIn($fax) ; }
			if(!empty($nofax)){ $sql .= " AND fax ".sqlNotIn($nofax) ; }
			if(!empty($trieuse)){ $sql .= " AND trieuse ".sqlIn($trieuse) ; }
			if(!empty($notrieuse)){ $sql .= " AND trieuse ".sqlNotIn($notrieuse) ; }
			if(!empty($agrapheuse)){ $sql .= " AND agrapheuse ".sqlIn($agrapheuse) ; }
			if(!empty($noagrapheuse)){ $sql .= " AND agrapheuse ".sqlNotIn($noagrapheuse) ; }
			if(!empty($scan)){ $sql .= " AND scan ".sqlIn($scan) ; }
			if(!empty($noscan)){ $sql .= " AND scan ".sqlNotIn($noscan) ; }
			if(!empty($prix_noir)){ $sql .= " AND prix_noir ".sqlIn($prix_noir) ; }
			if(!empty($noprix_noir)){ $sql .= " AND prix_noir ".sqlNotIn($noprix_noir) ; }
			if(!empty($prix_couleur)){ $sql .= " AND prix_couleur ".sqlIn($prix_couleur) ; }
			if(!empty($noprix_couleur)){ $sql .= " AND prix_couleur ".sqlNotIn($noprix_couleur) ; }
			if(!empty($cvTrimNb)){ $sql .= " AND cvTrimNb ".sqlIn($cvTrimNb) ; }
			if(!empty($nocvTrimNb)){ $sql .= " AND cvTrimNb ".sqlNotIn($nocvTrimNb) ; }
			if(!empty($cvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlIn($cvTrimCouleur) ; }
			if(!empty($nocvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlNotIn($nocvTrimCouleur) ; }
			if(!empty($contrat_location)){ $sql .= " AND contrat_location ".sqlIn($contrat_location) ; }
			if(!empty($nocontrat_location)){ $sql .= " AND contrat_location ".sqlNotIn($nocontrat_location) ; }
			if(!empty($contrat_vente)){ $sql .= " AND contrat_vente ".sqlIn($contrat_vente) ; }
			if(!empty($nocontrat_vente)){ $sql .= " AND contrat_vente ".sqlNotIn($nocontrat_vente) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlIn($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotIn($noleaser) ; }
			if(!empty($duree_location)){ $sql .= " AND duree_location ".sqlIn($duree_location) ; }
			if(!empty($noduree_location)){ $sql .= " AND duree_location ".sqlNotIn($noduree_location) ; }
			if(!empty($periode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlIn($periode_facturation_location) ; }
			if(!empty($noperiode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlNotIn($noperiode_facturation_location) ; }
			if(!empty($VFL)){ $sql .= " AND VFL ".sqlIn($VFL) ; }
			if(!empty($noVFL)){ $sql .= " AND VFL ".sqlNotIn($noVFL) ; }
			if(!empty($loyer)){ $sql .= " AND loyer ".sqlIn($loyer) ; }
			if(!empty($noloyer)){ $sql .= " AND loyer ".sqlNotIn($noloyer) ; }
			if(!empty($montantVente)){ $sql .= " AND montantVente ".sqlIn($montantVente) ; }
			if(!empty($nomontantVente)){ $sql .= " AND montantVente ".sqlNotIn($nomontantVente) ; }
			if(!empty($neuf)){ $sql .= " AND neuf ".sqlIn($neuf) ; }
			if(!empty($noneuf)){ $sql .= " AND neuf ".sqlNotIn($noneuf) ; }
			if(!empty($occasion)){ $sql .= " AND occasion ".sqlIn($occasion) ; }
			if(!empty($nooccasion)){ $sql .= " AND occasion ".sqlNotIn($nooccasion) ; }
			if(!empty($machine)){ $sql .= " AND machine ".sqlIn($machine) ; }
			if(!empty($nomachine)){ $sql .= " AND machine ".sqlNotIn($nomachine) ; }
			if(!empty($couleur)){ $sql .= " AND couleur ".sqlIn($couleur) ; }
			if(!empty($nocouleur)){ $sql .= " AND couleur ".sqlNotIn($nocouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeLigneParcProspect($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from ligne_parc_prospect where 1 " ;
			if(!empty($idligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlSearch($idligne_parc_prospect) ; }
			if(!empty($noidligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlNotSearch($noidligne_parc_prospect) ; }
			if(!empty($parc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlSearch($parc_prospect_idparc_prospect) ; }
			if(!empty($noparc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlNotSearch($noparc_prospect_idparc_prospect) ; }
			if(!empty($quantite)){ $sql .= " AND quantite ".sqlSearch($quantite) ; }
			if(!empty($noquantite)){ $sql .= " AND quantite ".sqlNotSearch($noquantite) ; }
			if(!empty($segment)){ $sql .= " AND segment ".sqlSearch($segment) ; }
			if(!empty($nosegment)){ $sql .= " AND segment ".sqlNotSearch($nosegment) ; }
			if(!empty($fax)){ $sql .= " AND fax ".sqlSearch($fax) ; }
			if(!empty($nofax)){ $sql .= " AND fax ".sqlNotSearch($nofax) ; }
			if(!empty($trieuse)){ $sql .= " AND trieuse ".sqlSearch($trieuse) ; }
			if(!empty($notrieuse)){ $sql .= " AND trieuse ".sqlNotSearch($notrieuse) ; }
			if(!empty($agrapheuse)){ $sql .= " AND agrapheuse ".sqlSearch($agrapheuse) ; }
			if(!empty($noagrapheuse)){ $sql .= " AND agrapheuse ".sqlNotSearch($noagrapheuse) ; }
			if(!empty($scan)){ $sql .= " AND scan ".sqlSearch($scan) ; }
			if(!empty($noscan)){ $sql .= " AND scan ".sqlNotSearch($noscan) ; }
			if(!empty($prix_noir)){ $sql .= " AND prix_noir ".sqlSearch($prix_noir) ; }
			if(!empty($noprix_noir)){ $sql .= " AND prix_noir ".sqlNotSearch($noprix_noir) ; }
			if(!empty($prix_couleur)){ $sql .= " AND prix_couleur ".sqlSearch($prix_couleur) ; }
			if(!empty($noprix_couleur)){ $sql .= " AND prix_couleur ".sqlNotSearch($noprix_couleur) ; }
			if(!empty($cvTrimNb)){ $sql .= " AND cvTrimNb ".sqlSearch($cvTrimNb) ; }
			if(!empty($nocvTrimNb)){ $sql .= " AND cvTrimNb ".sqlNotSearch($nocvTrimNb) ; }
			if(!empty($cvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlSearch($cvTrimCouleur) ; }
			if(!empty($nocvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlNotSearch($nocvTrimCouleur) ; }
			if(!empty($contrat_location)){ $sql .= " AND contrat_location ".sqlSearch($contrat_location) ; }
			if(!empty($nocontrat_location)){ $sql .= " AND contrat_location ".sqlNotSearch($nocontrat_location) ; }
			if(!empty($contrat_vente)){ $sql .= " AND contrat_vente ".sqlSearch($contrat_vente) ; }
			if(!empty($nocontrat_vente)){ $sql .= " AND contrat_vente ".sqlNotSearch($nocontrat_vente) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlSearch($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotSearch($noleaser) ; }
			if(!empty($duree_location)){ $sql .= " AND duree_location ".sqlSearch($duree_location) ; }
			if(!empty($noduree_location)){ $sql .= " AND duree_location ".sqlNotSearch($noduree_location) ; }
			if(!empty($periode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlSearch($periode_facturation_location) ; }
			if(!empty($noperiode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlNotSearch($noperiode_facturation_location) ; }
			if(!empty($VFL)){ $sql .= " AND VFL ".sqlSearch($VFL) ; }
			if(!empty($noVFL)){ $sql .= " AND VFL ".sqlNotSearch($noVFL) ; }
			if(!empty($loyer)){ $sql .= " AND loyer ".sqlSearch($loyer) ; }
			if(!empty($noloyer)){ $sql .= " AND loyer ".sqlNotSearch($noloyer) ; }
			if(!empty($montantVente)){ $sql .= " AND montantVente ".sqlSearch($montantVente) ; }
			if(!empty($nomontantVente)){ $sql .= " AND montantVente ".sqlNotSearch($nomontantVente) ; }
			if(!empty($neuf)){ $sql .= " AND neuf ".sqlSearch($neuf) ; }
			if(!empty($noneuf)){ $sql .= " AND neuf ".sqlNotSearch($noneuf) ; }
			if(!empty($occasion)){ $sql .= " AND occasion ".sqlSearch($occasion) ; }
			if(!empty($nooccasion)){ $sql .= " AND occasion ".sqlNotSearch($nooccasion) ; }
			if(!empty($machine)){ $sql .= " AND machine ".sqlSearch($machine) ; }
			if(!empty($nomachine)){ $sql .= " AND machine ".sqlNotSearch($nomachine) ; }
			if(!empty($couleur)){ $sql .= " AND couleur ".sqlSearch($couleur) ; }
			if(!empty($nocouleur)){ $sql .= " AND couleur ".sqlNotSearch($nocouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeLigneParcProspect(){ 
		$sql="SELECT * FROM  ligne_parc_prospect"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeLigneParcProspect($name="idligne_parc_prospect",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomLigne_parc_prospect , idligne_parc_prospect FROM ligne_parc_prospect WHERE  1 ";  
			if(!empty($idligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlIn($idligne_parc_prospect) ; }
			if(!empty($noidligne_parc_prospect)){ $sql .= " AND idligne_parc_prospect ".sqlNotIn($noidligne_parc_prospect) ; }
			if(!empty($parc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlIn($parc_prospect_idparc_prospect) ; }
			if(!empty($noparc_prospect_idparc_prospect)){ $sql .= " AND parc_prospect_idparc_prospect ".sqlNotIn($noparc_prospect_idparc_prospect) ; }
			if(!empty($quantite)){ $sql .= " AND quantite ".sqlIn($quantite) ; }
			if(!empty($noquantite)){ $sql .= " AND quantite ".sqlNotIn($noquantite) ; }
			if(!empty($segment)){ $sql .= " AND segment ".sqlIn($segment) ; }
			if(!empty($nosegment)){ $sql .= " AND segment ".sqlNotIn($nosegment) ; }
			if(!empty($fax)){ $sql .= " AND fax ".sqlIn($fax) ; }
			if(!empty($nofax)){ $sql .= " AND fax ".sqlNotIn($nofax) ; }
			if(!empty($trieuse)){ $sql .= " AND trieuse ".sqlIn($trieuse) ; }
			if(!empty($notrieuse)){ $sql .= " AND trieuse ".sqlNotIn($notrieuse) ; }
			if(!empty($agrapheuse)){ $sql .= " AND agrapheuse ".sqlIn($agrapheuse) ; }
			if(!empty($noagrapheuse)){ $sql .= " AND agrapheuse ".sqlNotIn($noagrapheuse) ; }
			if(!empty($scan)){ $sql .= " AND scan ".sqlIn($scan) ; }
			if(!empty($noscan)){ $sql .= " AND scan ".sqlNotIn($noscan) ; }
			if(!empty($prix_noir)){ $sql .= " AND prix_noir ".sqlIn($prix_noir) ; }
			if(!empty($noprix_noir)){ $sql .= " AND prix_noir ".sqlNotIn($noprix_noir) ; }
			if(!empty($prix_couleur)){ $sql .= " AND prix_couleur ".sqlIn($prix_couleur) ; }
			if(!empty($noprix_couleur)){ $sql .= " AND prix_couleur ".sqlNotIn($noprix_couleur) ; }
			if(!empty($cvTrimNb)){ $sql .= " AND cvTrimNb ".sqlIn($cvTrimNb) ; }
			if(!empty($nocvTrimNb)){ $sql .= " AND cvTrimNb ".sqlNotIn($nocvTrimNb) ; }
			if(!empty($cvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlIn($cvTrimCouleur) ; }
			if(!empty($nocvTrimCouleur)){ $sql .= " AND cvTrimCouleur ".sqlNotIn($nocvTrimCouleur) ; }
			if(!empty($contrat_location)){ $sql .= " AND contrat_location ".sqlIn($contrat_location) ; }
			if(!empty($nocontrat_location)){ $sql .= " AND contrat_location ".sqlNotIn($nocontrat_location) ; }
			if(!empty($contrat_vente)){ $sql .= " AND contrat_vente ".sqlIn($contrat_vente) ; }
			if(!empty($nocontrat_vente)){ $sql .= " AND contrat_vente ".sqlNotIn($nocontrat_vente) ; }
			if(!empty($leaser)){ $sql .= " AND leaser ".sqlIn($leaser) ; }
			if(!empty($noleaser)){ $sql .= " AND leaser ".sqlNotIn($noleaser) ; }
			if(!empty($duree_location)){ $sql .= " AND duree_location ".sqlIn($duree_location) ; }
			if(!empty($noduree_location)){ $sql .= " AND duree_location ".sqlNotIn($noduree_location) ; }
			if(!empty($periode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlIn($periode_facturation_location) ; }
			if(!empty($noperiode_facturation_location)){ $sql .= " AND periode_facturation_location ".sqlNotIn($noperiode_facturation_location) ; }
			if(!empty($VFL)){ $sql .= " AND VFL ".sqlIn($VFL) ; }
			if(!empty($noVFL)){ $sql .= " AND VFL ".sqlNotIn($noVFL) ; }
			if(!empty($loyer)){ $sql .= " AND loyer ".sqlIn($loyer) ; }
			if(!empty($noloyer)){ $sql .= " AND loyer ".sqlNotIn($noloyer) ; }
			if(!empty($montantVente)){ $sql .= " AND montantVente ".sqlIn($montantVente) ; }
			if(!empty($nomontantVente)){ $sql .= " AND montantVente ".sqlNotIn($nomontantVente) ; }
			if(!empty($neuf)){ $sql .= " AND neuf ".sqlIn($neuf) ; }
			if(!empty($noneuf)){ $sql .= " AND neuf ".sqlNotIn($noneuf) ; }
			if(!empty($occasion)){ $sql .= " AND occasion ".sqlIn($occasion) ; }
			if(!empty($nooccasion)){ $sql .= " AND occasion ".sqlNotIn($nooccasion) ; }
			if(!empty($machine)){ $sql .= " AND machine ".sqlIn($machine) ; }
			if(!empty($nomachine)){ $sql .= " AND machine ".sqlNotIn($nomachine) ; }
			if(!empty($couleur)){ $sql .= " AND couleur ".sqlIn($couleur) ; }
			if(!empty($nocouleur)){ $sql .= " AND couleur ".sqlNotIn($nocouleur) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomLigne_parc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeLigneParcProspect($name="idligne_parc_prospect",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomLigne_parc_prospect , idligne_parc_prospect FROM ligne_parc_prospect ORDER BY nomLigne_parc_prospect ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeLigneParcProspect = new IdaeLigneParcProspect(); ?>