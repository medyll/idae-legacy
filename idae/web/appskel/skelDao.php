<?   
class daoSkel
{ 
	function daoSkel(){ 
		 
	}
	function daoPrv(){  
	
	}
	function getParseSelect($field,$params){  
		// si $params[$field] est un array;
		if(!empty($params[$field])){
			//if(is_array($params[$field])){$params = array_filter($params[$field]); }
		}
		$sql = "";
		if(isset($params[$field])) 				$sql .=  " AND $field ".sqlIn($params[$field]);
		if(isset($params["no".$field]))			$sql .=  " AND $field ".sqlNotIn($params["no".$field]); 
		if(isset($params["no_".$field]))		$sql .=  " AND $field ".sqlNotIn($params["no_".$field]); 
		if(isset($params["null_".$field]))		$sql .=  " AND $field IS NULL";
		if(isset($params["notnull_".$field]))	$sql .=  " AND $field IS NOT NULL";
		if(!empty($params["lt_".$field])) 		$sql .=  " AND $field <   ".$params["lt_".$field];
		if(!empty($params["lte_".$field]))		$sql .=  " AND $field <=  ".$params["lte_".$field];
		if(!empty($params["gt_".$field]))		$sql .=  " AND $field >   ".$params["gt_".$field];
		if(!empty($params["gte_".$field]))		$sql .=  " AND $field >=  ".$params["gte_".$field];
		if(!empty($params["or_".$field]))		$sql .=  " OR  $field =   ".$params["or_".$field];
		if(!empty($params["nor_".$field]))		$sql .=  " OR  $field <>  ".$params["nor_".$field];
		if(!empty($params["month_".$field]))	$sql .=  " AND  MONTH($field) ".sqlIn($params["month_".$field]);
		if(!empty($params["year_".$field]))		$sql .=  " AND  YEAR($field) ".sqlIn($params["year_".$field]);
		
		return $sql;				
	}
	
	function getParseSearch($field,$params){   
		if(!empty($params[$field])){
			//if(is_array($params[$field])){$params = array_filter($params[$field]); }
		}
		$sql = "";  
		if(!empty($params["debug_".$field])) $sql .=  " AND  ".sqlSearchDebug($params["debug_".$field],$field) ;
		if(!empty($params[$field]))			$sql .=  " AND $field ".sqlSearch($params[$field],$field) ;
		if(!empty($params["no".$field]))	$sql .=  " AND $field ".sqlNotSearch($params["no".$field],$field) ; 
		if(isset($params["no_".$field]))	$sql .=  " AND $field ".sqlNotIn($params["no_".$field]); 
		if(!empty($params["lt_".$field]))	$sql .=  " AND $field <   ".$params["lt_".$field] ;
		if(!empty($params["lte_".$field]))	$sql .=  " AND $field <=  ".$params["lte_".$field] ;
		if(!empty($params["gt_".$field]))	$sql .=  " AND $field >   ".$params["gt_".$field] ;
		if(!empty($params["gte_".$field]))	$sql .=  " AND $field >=  ".$params["gte_".$field] ;
		if(!empty($params["or_".$field]))	$sql .=  " OR  $field =   ".$params["or_".$field] ;
		if(!empty($params["nor_".$field]))	$sql .=  " OR  $field <>  ".$params["nor_".$field] ; 
		if(!empty($params["month_".$field]))	$sql .=  " AND  MONTH($field) ".sqlIn($params["month_".$field]);
		if(!empty($params["year_".$field]))		$sql .=  " AND  YEAR($field) ".sqlIn($params["year_".$field]);
		return $sql;				
	}  
}  ?>