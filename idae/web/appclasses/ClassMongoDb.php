<?  


class MongoDb extends daoSkel
{
	var $conn = null; 
	
	function __construct($table=''){ 
		$this->con = new Mongo("mongodb://{root}:{malaterre654}@{localhost}");
		$this->db  = $con->selectDB('sitebase'); 
	}
	
	function groupBy ($id,$condition=array()){ 			
		$initial = array("group" => array(),"retval" => array()); 
		$reduce = "function (obj, prev) { prev.group = obj.".$id.",prev.retval = obj}";
		// On groupe
		$rs = $collection -> group(array($id=>1),$initial,$reduce,$condition); 
	}
	
	 
}
?>