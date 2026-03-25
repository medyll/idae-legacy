<?  


class MongoDb extends daoSkel
{
	var $conn = null; 
	
	function __construct($table=''){
		// Build MongoDB URI from environment or constants
		$mongoHost = getenv('MDB_HOST') ?: (defined('MDB_HOST') ? MDB_HOST : '127.0.0.1');
		$mongoUser = getenv('MDB_USER') ?: (defined('MDB_USER') ? MDB_USER : '');
		$mongoPass = getenv('MDB_PASSWORD') ?: (defined('MDB_PASSWORD') ? MDB_PASSWORD : '');
		if (!empty($mongoUser) && !empty($mongoPass)) {
			$uri = 'mongodb://' . $mongoUser . ':' . $mongoPass . '@' . $mongoHost . ':27017';
		} else {
			$uri = 'mongodb://' . $mongoHost . ':27017';
		}
		$options = array('db' => 'sitebase');
		$this->con = new MongoClient($uri, $options);
		$this->db = $this->con->selectDB('sitebase');
	}
	
	function groupBy ($id,$condition=array()){ 			
		$initial = array("group" => array(),"retval" => array()); 
		$reduce = "function (obj, prev) { prev.group = obj.".$id.",prev.retval = obj}";
		// On groupe
		$rs = $collection -> group(array($id=>1),$initial,$reduce,$condition); 
	}
	
	 
}
?>