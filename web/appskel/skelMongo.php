<?   
class skelMongo extends Mongo { 
	
	public function connectBase($base='sitebase_production'){ 
		if(empty($base)) return 'choisir une base';
			if(!defined('MDB_USER')){
					$con = new Mongo(); }else{
					$con = new Mongo('mongodb://'.MDB_USER.':'.MDB_PASSWORD.'@localhost');
				}
			$db  = $con->$base; 
			return $db;
		}
		
	public function connect($table='produit',$base='sitebase_production'){
			if(empty($table)) return 'choisir une base';
			if(!defined('MDB_USER')){
					$con = new Mongo(); }else{
					$con = new Mongo('mongodb://'.MDB_USER.':'.MDB_PASSWORD.'@localhost');
				}
			$db  = $con->$base;
			$collection = $db->$table;
			return $collection;
		}
	public function connectFs($base='produit'){
			if(empty($base)) return 'choisir une base';
			if(!defined('MDB_USER')){
					$con = new Mongo(); }else{
					$con = new Mongo('mongodb://'.MDB_USER.':'.MDB_PASSWORD.'@localhost');
				}
			$db  = $con->$base;
			$collection = $db->getGridFS();
			return $collection;
		}
	static function getNext($id,$min=1){
			if(!defined('MDB_USER')){
				$con = new Mongo(); }else{
				$con = new Mongo('mongodb://'.MDB_USER.':'.MDB_PASSWORD.'@localhost');
			} 
			if(!empty($min)){
			$test = $con->sitebase_increment->auto_increment->findOne(array('_id'=>$id));
				if(!empty($test['value'])){
					if($test['value']<$min){ 
						$con->sitebase_increment->auto_increment->update(array('_id'=>$id),array('value'=>(int)$min), array("upsert" => true));
						}
				}
			}
			$con->sitebase_increment->auto_increment->update(array('_id'=>$id),array('$inc'=>array('value'=>1)), array("upsert" => true));
			$ret = $con->sitebase_increment->auto_increment->findOne(array('_id'=>$id));
			return $ret['value'];
		}

}  ?>