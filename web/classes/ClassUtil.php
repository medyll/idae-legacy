<?   
class Util
{
	var $conn = null;
	function Util()
	{
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD);
	}
	
	function getTotalVenteClient($params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql="select sum(quantiteLigne_vente_client * pvhtLigne_vente_client) as total from ligne_vente_client where vente_client_idvente_client = $idvente_client";
		$rs = $this->conn->Execute($sql); 	
		return 	$rs->fields['total'];		
	}
	
}
$ClassUtil = new Util();  
?>