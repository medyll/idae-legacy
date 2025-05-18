<?   
class IdaeHistoStock
{
	var $conn = null;
	function IdaeHistoStock(){
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD_IDAE);
	}
	
	function createIdaeHistoStock($params){ 
		$this->conn->AutoExecute("histomachine", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function updateIdaeHistoStock($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("histomachine", $params, "UPDATE", "idhistomachine = ".$idhistomachine); 
	}
	
	function deleteIdaeHistoStock($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  histomachine WHERE idhistomachine = $idhistomachine"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOneIdaeHistoStock($params){
		extract($params,EXTR_OVERWRITE) ;
		$all = (empty($all))? "*" : $all;	
		$sql="select ".$all." from histomachine where 1 " ;
			if(!empty($idhistoMachine)){ $sql .= " AND idhistoMachine ".sqlIn($idhistoMachine) ; }
			if(!empty($noidhistoMachine)){ $sql .= " AND idhistoMachine ".sqlNotIn($noidhistoMachine) ; }
			if(!empty($lt_idhistoMachine)){ $sql .= " AND idhistoMachine < '".$lt_idhistoMachine."'" ; }
			if(!empty($gt_idhistoMachine)){ $sql .= " AND idhistoMachine > '".$gt_idhistoMachine."'" ; }
			if(!empty($machine_idmachine)){ $sql .= " AND machine_idmachine ".sqlIn($machine_idmachine) ; }
			if(!empty($nomachine_idmachine)){ $sql .= " AND machine_idmachine ".sqlNotIn($nomachine_idmachine) ; }
			if(!empty($lt_machine_idmachine)){ $sql .= " AND machine_idmachine < '".$lt_machine_idmachine."'" ; }
			if(!empty($gt_machine_idmachine)){ $sql .= " AND machine_idmachine > '".$gt_machine_idmachine."'" ; }
			if(!empty($machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlIn($machine_stockMachine_idstockMachine) ; }
			if(!empty($nomachine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlNotIn($nomachine_stockMachine_idstockMachine) ; }
			if(!empty($lt_machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine < '".$lt_machine_stockMachine_idstockMachine."'" ; }
			if(!empty($gt_machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine > '".$gt_machine_stockMachine_idstockMachine."'" ; }
			if(!empty($machine_produits_id)){ $sql .= " AND machine_produits_id ".sqlIn($machine_produits_id) ; }
			if(!empty($nomachine_produits_id)){ $sql .= " AND machine_produits_id ".sqlNotIn($nomachine_produits_id) ; }
			if(!empty($lt_machine_produits_id)){ $sql .= " AND machine_produits_id < '".$lt_machine_produits_id."'" ; }
			if(!empty($gt_machine_produits_id)){ $sql .= " AND machine_produits_id > '".$gt_machine_produits_id."'" ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($lt_idclient)){ $sql .= " AND idclient < '".$lt_idclient."'" ; }
			if(!empty($gt_idclient)){ $sql .= " AND idclient > '".$gt_idclient."'" ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($dateInstall)){ $sql .= " AND dateInstall ".sqlIn($dateInstall) ; }
			if(!empty($nodateInstall)){ $sql .= " AND dateInstall ".sqlNotIn($nodateInstall) ; }
			if(!empty($lt_dateInstall)){ $sql .= " AND dateInstall < '".$lt_dateInstall."'" ; }
			if(!empty($gt_dateInstall)){ $sql .= " AND dateInstall > '".$gt_dateInstall."'" ; }
			if(!empty($dateRetrait)){ $sql .= " AND dateRetrait ".sqlIn($dateRetrait) ; }
			if(!empty($nodateRetrait)){ $sql .= " AND dateRetrait ".sqlNotIn($nodateRetrait) ; }
			if(!empty($lt_dateRetrait)){ $sql .= " AND dateRetrait < '".$lt_dateRetrait."'" ; }
			if(!empty($gt_dateRetrait)){ $sql .= " AND dateRetrait > '".$gt_dateRetrait."'" ; }
			if(!empty($commentaireInstall)){ $sql .= " AND commentaireInstall ".sqlIn($commentaireInstall) ; }
			if(!empty($nocommentaireInstall)){ $sql .= " AND commentaireInstall ".sqlNotIn($nocommentaireInstall) ; }
			if(!empty($lt_commentaireInstall)){ $sql .= " AND commentaireInstall < '".$lt_commentaireInstall."'" ; }
			if(!empty($gt_commentaireInstall)){ $sql .= " AND commentaireInstall > '".$gt_commentaireInstall."'" ; }
			if(!empty($commentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlIn($commentaireRetrait) ; }
			if(!empty($nocommentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlNotIn($nocommentaireRetrait) ; }
			if(!empty($lt_commentaireRetrait)){ $sql .= " AND commentaireRetrait < '".$lt_commentaireRetrait."'" ; }
			if(!empty($gt_commentaireRetrait)){ $sql .= " AND commentaireRetrait > '".$gt_commentaireRetrait."'" ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlIn($compteurNBRetrait) ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotIn($nocompteurNBRetrait) ; }
			if(!empty($lt_compteurNBRetrait)){ $sql .= " AND compteurNBRetrait < '".$lt_compteurNBRetrait."'" ; }
			if(!empty($gt_compteurNBRetrait)){ $sql .= " AND compteurNBRetrait > '".$gt_compteurNBRetrait."'" ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlIn($compteurCoulRetrait) ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotIn($nocompteurCoulRetrait) ; }
			if(!empty($lt_compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait < '".$lt_compteurCoulRetrait."'" ; }
			if(!empty($gt_compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait > '".$gt_compteurCoulRetrait."'" ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlIn($pvHt) ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotIn($nopvHt) ; }
			if(!empty($lt_pvHt)){ $sql .= " AND pvHt < '".$lt_pvHt."'" ; }
			if(!empty($gt_pvHt)){ $sql .= " AND pvHt > '".$gt_pvHt."'" ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlIn($paHt) ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotIn($nopaHt) ; }
			if(!empty($lt_paHt)){ $sql .= " AND paHt < '".$lt_paHt."'" ; }
			if(!empty($gt_paHt)){ $sql .= " AND paHt > '".$gt_paHt."'" ; }
			if(!empty($idconseiller)){ $sql .= " AND idconseiller ".sqlIn($idconseiller) ; }
			if(!empty($noidconseiller)){ $sql .= " AND idconseiller ".sqlNotIn($noidconseiller) ; }
			if(!empty($lt_idconseiller)){ $sql .= " AND idconseiller < '".$lt_idconseiller."'" ; }
			if(!empty($gt_idconseiller)){ $sql .= " AND idconseiller > '".$gt_idconseiller."'" ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function searchOneIdaeHistoStock($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select * from histomachine where 1 " ;
			if(!empty($idhistoMachine)){ $sql .= " AND idhistoMachine ".sqlSearch($idhistoMachine,"idhistoMachine") ; }
			if(!empty($lt_idhistoMachine)){ $sql .= " AND idhistoMachine < '".$lt_idhistoMachine."'" ; }
			if(!empty($gt_idhistoMachine)){ $sql .= " AND idhistoMachine > '".$gt_idhistoMachine."'" ; }
			if(!empty($noidhistoMachine)){ $sql .= " AND idhistoMachine ".sqlNotSearch($noidhistoMachine) ; }
			if(!empty($machine_idmachine)){ $sql .= " AND machine_idmachine ".sqlSearch($machine_idmachine,"machine_idmachine") ; }
			if(!empty($lt_machine_idmachine)){ $sql .= " AND machine_idmachine < '".$lt_machine_idmachine."'" ; }
			if(!empty($gt_machine_idmachine)){ $sql .= " AND machine_idmachine > '".$gt_machine_idmachine."'" ; }
			if(!empty($nomachine_idmachine)){ $sql .= " AND machine_idmachine ".sqlNotSearch($nomachine_idmachine) ; }
			if(!empty($machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlSearch($machine_stockMachine_idstockMachine,"machine_stockMachine_idstockMachine") ; }
			if(!empty($lt_machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine < '".$lt_machine_stockMachine_idstockMachine."'" ; }
			if(!empty($gt_machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine > '".$gt_machine_stockMachine_idstockMachine."'" ; }
			if(!empty($nomachine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlNotSearch($nomachine_stockMachine_idstockMachine) ; }
			if(!empty($machine_produits_id)){ $sql .= " AND machine_produits_id ".sqlSearch($machine_produits_id,"machine_produits_id") ; }
			if(!empty($lt_machine_produits_id)){ $sql .= " AND machine_produits_id < '".$lt_machine_produits_id."'" ; }
			if(!empty($gt_machine_produits_id)){ $sql .= " AND machine_produits_id > '".$gt_machine_produits_id."'" ; }
			if(!empty($nomachine_produits_id)){ $sql .= " AND machine_produits_id ".sqlNotSearch($nomachine_produits_id) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlSearch($idclient,"idclient") ; }
			if(!empty($lt_idclient)){ $sql .= " AND idclient < '".$lt_idclient."'" ; }
			if(!empty($gt_idclient)){ $sql .= " AND idclient > '".$gt_idclient."'" ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotSearch($noidclient) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlSearch($nomSociete,"nomSociete") ; }
			if(!empty($lt_nomSociete)){ $sql .= " AND nomSociete < '".$lt_nomSociete."'" ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > '".$gt_nomSociete."'" ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotSearch($nonomSociete) ; }
			if(!empty($dateInstall)){ $sql .= " AND dateInstall ".sqlSearch($dateInstall,"dateInstall") ; }
			if(!empty($lt_dateInstall)){ $sql .= " AND dateInstall < '".$lt_dateInstall."'" ; }
			if(!empty($gt_dateInstall)){ $sql .= " AND dateInstall > '".$gt_dateInstall."'" ; }
			if(!empty($nodateInstall)){ $sql .= " AND dateInstall ".sqlNotSearch($nodateInstall) ; }
			if(!empty($dateRetrait)){ $sql .= " AND dateRetrait ".sqlSearch($dateRetrait,"dateRetrait") ; }
			if(!empty($lt_dateRetrait)){ $sql .= " AND dateRetrait < '".$lt_dateRetrait."'" ; }
			if(!empty($gt_dateRetrait)){ $sql .= " AND dateRetrait > '".$gt_dateRetrait."'" ; }
			if(!empty($nodateRetrait)){ $sql .= " AND dateRetrait ".sqlNotSearch($nodateRetrait) ; }
			if(!empty($commentaireInstall)){ $sql .= " AND commentaireInstall ".sqlSearch($commentaireInstall,"commentaireInstall") ; }
			if(!empty($lt_commentaireInstall)){ $sql .= " AND commentaireInstall < '".$lt_commentaireInstall."'" ; }
			if(!empty($gt_commentaireInstall)){ $sql .= " AND commentaireInstall > '".$gt_commentaireInstall."'" ; }
			if(!empty($nocommentaireInstall)){ $sql .= " AND commentaireInstall ".sqlNotSearch($nocommentaireInstall) ; }
			if(!empty($commentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlSearch($commentaireRetrait,"commentaireRetrait") ; }
			if(!empty($lt_commentaireRetrait)){ $sql .= " AND commentaireRetrait < '".$lt_commentaireRetrait."'" ; }
			if(!empty($gt_commentaireRetrait)){ $sql .= " AND commentaireRetrait > '".$gt_commentaireRetrait."'" ; }
			if(!empty($nocommentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlNotSearch($nocommentaireRetrait) ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlSearch($compteurNBRetrait,"compteurNBRetrait") ; }
			if(!empty($lt_compteurNBRetrait)){ $sql .= " AND compteurNBRetrait < '".$lt_compteurNBRetrait."'" ; }
			if(!empty($gt_compteurNBRetrait)){ $sql .= " AND compteurNBRetrait > '".$gt_compteurNBRetrait."'" ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotSearch($nocompteurNBRetrait) ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlSearch($compteurCoulRetrait,"compteurCoulRetrait") ; }
			if(!empty($lt_compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait < '".$lt_compteurCoulRetrait."'" ; }
			if(!empty($gt_compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait > '".$gt_compteurCoulRetrait."'" ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotSearch($nocompteurCoulRetrait) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlSearch($pvHt,"pvHt") ; }
			if(!empty($lt_pvHt)){ $sql .= " AND pvHt < '".$lt_pvHt."'" ; }
			if(!empty($gt_pvHt)){ $sql .= " AND pvHt > '".$gt_pvHt."'" ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotSearch($nopvHt) ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlSearch($paHt,"paHt") ; }
			if(!empty($lt_paHt)){ $sql .= " AND paHt < '".$lt_paHt."'" ; }
			if(!empty($gt_paHt)){ $sql .= " AND paHt > '".$gt_paHt."'" ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotSearch($nopaHt) ; }
			if(!empty($idconseiller)){ $sql .= " AND idconseiller ".sqlSearch($idconseiller,"idconseiller") ; }
			if(!empty($lt_idconseiller)){ $sql .= " AND idconseiller < '".$lt_idconseiller."'" ; }
			if(!empty($gt_idconseiller)){ $sql .= " AND idconseiller > '".$gt_idconseiller."'" ; }
			if(!empty($noidconseiller)){ $sql .= " AND idconseiller ".sqlNotSearch($noidconseiller) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }
			if(!empty($debug)){ echo $sql;   }
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}
	return $this->conn->Execute($sql) ;	
	}
	function getAllIdaeHistoStock(){ 
		$sql="SELECT * FROM  histomachine"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOneIdaeHistoStock($name="idhistomachine",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nomHistomachine , idhistomachine FROM histomachine WHERE  1 ";  
			if(!empty($idhistoMachine)){ $sql .= " AND idhistoMachine ".sqlIn($idhistoMachine) ; }
			if(!empty($gt_idhistoMachine)){ $sql .= " AND idhistoMachine > ".$gt_idhistoMachine ; }
			if(!empty($noidhistoMachine)){ $sql .= " AND idhistoMachine ".sqlNotIn($noidhistoMachine) ; }
			if(!empty($machine_idmachine)){ $sql .= " AND machine_idmachine ".sqlIn($machine_idmachine) ; }
			if(!empty($gt_machine_idmachine)){ $sql .= " AND machine_idmachine > ".$gt_machine_idmachine ; }
			if(!empty($nomachine_idmachine)){ $sql .= " AND machine_idmachine ".sqlNotIn($nomachine_idmachine) ; }
			if(!empty($machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlIn($machine_stockMachine_idstockMachine) ; }
			if(!empty($gt_machine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine > ".$gt_machine_stockMachine_idstockMachine ; }
			if(!empty($nomachine_stockMachine_idstockMachine)){ $sql .= " AND machine_stockMachine_idstockMachine ".sqlNotIn($nomachine_stockMachine_idstockMachine) ; }
			if(!empty($machine_produits_id)){ $sql .= " AND machine_produits_id ".sqlIn($machine_produits_id) ; }
			if(!empty($gt_machine_produits_id)){ $sql .= " AND machine_produits_id > ".$gt_machine_produits_id ; }
			if(!empty($nomachine_produits_id)){ $sql .= " AND machine_produits_id ".sqlNotIn($nomachine_produits_id) ; }
			if(!empty($idclient)){ $sql .= " AND idclient ".sqlIn($idclient) ; }
			if(!empty($gt_idclient)){ $sql .= " AND idclient > ".$gt_idclient ; }
			if(!empty($noidclient)){ $sql .= " AND idclient ".sqlNotIn($noidclient) ; }
			if(!empty($nomSociete)){ $sql .= " AND nomSociete ".sqlIn($nomSociete) ; }
			if(!empty($gt_nomSociete)){ $sql .= " AND nomSociete > ".$gt_nomSociete ; }
			if(!empty($nonomSociete)){ $sql .= " AND nomSociete ".sqlNotIn($nonomSociete) ; }
			if(!empty($dateInstall)){ $sql .= " AND dateInstall ".sqlIn($dateInstall) ; }
			if(!empty($gt_dateInstall)){ $sql .= " AND dateInstall > ".$gt_dateInstall ; }
			if(!empty($nodateInstall)){ $sql .= " AND dateInstall ".sqlNotIn($nodateInstall) ; }
			if(!empty($dateRetrait)){ $sql .= " AND dateRetrait ".sqlIn($dateRetrait) ; }
			if(!empty($gt_dateRetrait)){ $sql .= " AND dateRetrait > ".$gt_dateRetrait ; }
			if(!empty($nodateRetrait)){ $sql .= " AND dateRetrait ".sqlNotIn($nodateRetrait) ; }
			if(!empty($commentaireInstall)){ $sql .= " AND commentaireInstall ".sqlIn($commentaireInstall) ; }
			if(!empty($gt_commentaireInstall)){ $sql .= " AND commentaireInstall > ".$gt_commentaireInstall ; }
			if(!empty($nocommentaireInstall)){ $sql .= " AND commentaireInstall ".sqlNotIn($nocommentaireInstall) ; }
			if(!empty($commentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlIn($commentaireRetrait) ; }
			if(!empty($gt_commentaireRetrait)){ $sql .= " AND commentaireRetrait > ".$gt_commentaireRetrait ; }
			if(!empty($nocommentaireRetrait)){ $sql .= " AND commentaireRetrait ".sqlNotIn($nocommentaireRetrait) ; }
			if(!empty($compteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlIn($compteurNBRetrait) ; }
			if(!empty($gt_compteurNBRetrait)){ $sql .= " AND compteurNBRetrait > ".$gt_compteurNBRetrait ; }
			if(!empty($nocompteurNBRetrait)){ $sql .= " AND compteurNBRetrait ".sqlNotIn($nocompteurNBRetrait) ; }
			if(!empty($compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlIn($compteurCoulRetrait) ; }
			if(!empty($gt_compteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait > ".$gt_compteurCoulRetrait ; }
			if(!empty($nocompteurCoulRetrait)){ $sql .= " AND compteurCoulRetrait ".sqlNotIn($nocompteurCoulRetrait) ; }
			if(!empty($pvHt)){ $sql .= " AND pvHt ".sqlIn($pvHt) ; }
			if(!empty($gt_pvHt)){ $sql .= " AND pvHt > ".$gt_pvHt ; }
			if(!empty($nopvHt)){ $sql .= " AND pvHt ".sqlNotIn($nopvHt) ; }
			if(!empty($paHt)){ $sql .= " AND paHt ".sqlIn($paHt) ; }
			if(!empty($gt_paHt)){ $sql .= " AND paHt > ".$gt_paHt ; }
			if(!empty($nopaHt)){ $sql .= " AND paHt ".sqlNotIn($nopaHt) ; }
			if(!empty($idconseiller)){ $sql .= " AND idconseiller ".sqlIn($idconseiller) ; }
			if(!empty($gt_idconseiller)){ $sql .= " AND idconseiller > ".$gt_idconseiller ; }
			if(!empty($noidconseiller)){ $sql .= " AND idconseiller ".sqlNotIn($noidconseiller) ; }
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; } 
		$sql .=" ORDER BY nomHistomachine ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelectIdaeHistoStock($name="idhistomachine",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nomHistomachine , idhistomachine FROM histomachine ORDER BY nomHistomachine ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$ClassIdaeHistoStock = new IdaeHistoStock(); ?>