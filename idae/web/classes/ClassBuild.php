<?
function buildC($tableName,$className=''){ 
$ClassForm = new Form(); 
if($className==''){$className= $tableName;}
$conn =""; 
$params= '<?   
class '.ucfirst($className).'
{
	var $conn = null; 
	function '.ucfirst($className).'(){ 
		$this->conn = &ADONewConnection("mysql");  
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	}
	
	function getParseSelect($field,$params){  
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			if(is_array($params[$field])){$params = array_filter($params[$field]); }
		}
		'.$ClassForm->getFunctionSql($tableName).'				
	}
	
	function getParseSearch($field,$params){ 
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			if(is_array($params[$field])){$params = array_filter($params[$field]); }
		}
		'.$ClassForm->getFunctionSqlSearch($tableName).'				
	}
	
	function create'.ucfirst($className).'($params){ 
		$this->conn->AutoExecute("'.$tableName.'", $params,"INSERT"); 
		return $this->conn->Insert_ID();				
	}
	
	function update'.ucfirst($className).'($params){
		extract($params,EXTR_OVERWRITE) ;
		$this->conn->AutoExecute("'.$tableName.'", $params, "UPDATE", "id'.$tableName.' = ".$id'.$tableName.'); 
	}
	
	function delete'.ucfirst($className).'($params){
		extract($params,EXTR_OVERWRITE) ;	
		$sql="DELETE FROM  '.$tableName.' WHERE id'.$tableName.' = $id'.$tableName.'"; 
		return $this->conn->Execute($sql); 	
	}
	
	function getOne'.ucfirst($className).'($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		'.$ClassForm->getSelectSql($tableName).'
	}
	function searchOne'.ucfirst($className).'($params){
		extract($params,EXTR_OVERWRITE) ;	
		$all = (empty($all))? " * " : $all  ; 
		'.$ClassForm->getSearchSql($tableName).'
	}
	function getAll'.ucfirst($className).'(){ 
		$sql="SELECT * FROM  '.$tableName.'"; 
		return $this->conn->Execute($sql) ;	
	}
	function getSelectOne'.ucfirst($className).'($name="id'.$tableName.'",$id="",$allowEmpty= false,$params)
	{ 
		extract($params,EXTR_OVERWRITE) ;	
		$sql = "SELECT nom'.ucfirst($tableName).' , id'.$tableName.' FROM WHERE 1 ";'.$ClassForm->getSelectOneSql($tableName).' 
		$sql .=" ORDER BY nom'.ucfirst($tableName).' ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
	function getSelect'.ucfirst($className).'($name="id'.$tableName.'",$id="",$allowEmpty= false)
	{ 
		$sql = "SELECT nom'.ucfirst($tableName).' , id'.$tableName.' FROM '.$tableName.' ORDER BY nom'.ucfirst($tableName).' ";
		$rs = $this->conn->Execute($sql) ; 
		return $rs->GetMenu2($name,$id,$allowEmpty); 
	}
}
$Class'.ucfirst($className).' = new '.ucfirst($className).'(); ?>'   ;
return $params;
}
?>
