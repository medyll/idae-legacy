<? 

class Calcul
{
	var $conn = null;
	
	
	function Calcul()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function getca_nbParc($idParc)
	{
	$sql = "select ROUND(SUM(cout_copie_noir * cv_trim),2)  as tot  
			from ligne_parcs where parcs_idparcs = ".$idParc ;
	$rstot = $this->conn->execute($sql);
	$tot = $rstot->fields['tot'];
	return $tot;
	}
	function getca_couleurParc($idParc)
	{
	$sql = "select ROUND(SUM(cout_copie_couleur * cv_trim_couleur),2) as tot from ligne_parcs where parcs_idparcs = ".$idParc ;
	$rstot = $this->conn->execute($sql);
	$tot = $rstot->fields['tot'];
	return $tot;
	}
	function getca_nbLignesParc($idLigne)
	{
	$sql = "select ROUND((cout_copie_noir * cv_trim),2) as tot from ligne_parcs where idligne_parcs = ".$idLigne ;
	//// echo $sql;
	$rstot = $this->conn->execute($sql);
	$tot = $rstot->fields['tot'];
	return $tot;
	}
	function getca_couleurLignesParc($idLigne)
	{
	$sql = "select ROUND((cout_copie_couleur * cv_trim_couleur),2) as tot from ligne_parcs where idligne_parcs = ".$idLigne ;
	//// echo $sql;
	$rstot = $this->conn->execute($sql);
	$tot = $rstot->fields['tot'];
	return $tot;
	}
	function getloyer_trimLignesParc($idLigne)
	{
	$sql = "select () as tot from ligne_parcs where idLigne_parcs = ".$idLigne ;
	}
	function getDateFinContrat($date,$trim)
	{
	$trim = $trim;// * 91.25 ;
	$sql= " SELECT DATE_FORMAT(DATE_ADD('$date', INTERVAL  $trim QUARTER ),'%d/%m/%Y') as finContrat ";
	$rstot = $this->conn->execute($sql);
	$finContrat = $rstot->fields['finContrat'];
	return $finContrat;
	}
	
}
$ClassCalcul = new Calcul();
?>