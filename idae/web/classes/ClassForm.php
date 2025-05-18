<? 
class Form
{
	var $conn = null;
	var $connWrite = null;
	function Form() 
	{
		$this->conn = &ADONewConnection("mysql");   
		$this->conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,SQL_BDD); 
	} 
	
	function getForm ($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql); 
		echo '<form action="mdl/'.$form.'/actions.php"  onsubmit="ajaxFormValidation(this);return false;" />';
		echo '<input type="hidden" name="F_action" value="create'.$form.'" />';
		while (!$rs->EOF) { 
			//for ($i = 0; $i < $rs->FieldCount(); $i++) {
				 $fld = $rs->fields['Field']; 
					echo '<ul class="ulform">';
					echo '<li><label><?=idioma("'.$fld.'")?></label><input type="text" name="'.$fld.'" value=""></li>';
					echo '</ul>';
			//} 
			
		$rs->MoveNext(); }
		echo ' <div class="buttonZone">
				<input type="submit" value="Valider"  >
				<input type="reset" value="Annuler" class="cancelButton" >
			  </div>';
		echo "</form>";
	}
	function getFiche ($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql); 
			while (!$rs->EOF) { 
			//for ($i = 0; $i < $rs->FieldCount(); $i++) {
				 $fld = $rs->fields['Field']; 
					echo '
							<ul class="ulform">';
					echo '
							<li>
								<label><?=idioma("'.$fld.'")?></label>
									<?=$arr["'.$fld.'"]?>
							</li>';
					echo '
							</ul>';
			//} 
			
		$rs->MoveNext(); } 
	}
	
	function getFormUpdate ($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql); 
		echo '<form action="mdl/'.$form.'/actions.php"  onsubmit="ajaxFormValidation(this);return false;" />
			';
		echo '<input type="hidden" name="F_action" value="update'.$form.'" />
			';
		while (!$rs->EOF) { 
			//for ($i = 0; $i < $rs->FieldCount(); $i++) {
				 $fld = $rs->fields['Field']; 
					echo '<ul class="ulform">
								';
					echo '<li><label><?=idioma("'.$fld.'")?></label><input type="text" name="'.$fld.'" value="<?=$arr["'.$fld.'"]?>"></li>';
					echo '
					</ul>';
			//} 
			
		$rs->MoveNext(); }
		echo "</form>";
	}
	function getSelectOneSql($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql);
	$output =  '$sql = "";
	';
	while (!$rs->EOF) { 
		$fld = $rs->fields['Field']; 
		$output .='
		$sql .= $this->getParseSelect("'.$fld.'",$params);'; 
		$rs->MoveNext();
	}
	$output.= '
			if(!empty($groupBy)){ $sql .= " group by $groupBy " ; }';
	return ' '.$output;
	}
	function getFunctionSql($field){
		$output =  '$sql = "";
		';
		$output .= 'if(!empty($params[$field])) 		$sql .=  " AND $field ".sqlIn($params[$field]);';
		$output .= '
		if(!empty($params["no".$field]))		$sql .=  " AND $field ".sqlNotIn($params["no".$field]); ';
		$output .= '
		if(!empty($params["lt_".$field])) 		$sql .=  " AND $field <   ".$params["lt_".$field];';
		$output .= '
		if(!empty($params["lte_".$field]))		$sql .=  " AND $field <=  ".$params["lte_".$field];';
		$output .= '
		if(!empty($params["gt_".$field]))		$sql .=  " AND $field >   ".$params["gt_".$field];';
		$output .= '
		if(!empty($params["gte_".$field]))		$sql .=  " AND $field >=  ".$params["gte_".$field];';
		$output .= '
		if(!empty($params["or_".$field]))		$sql .=  " OR $field =  ".$params["or_".$field];';
		$output .= '
		if(!empty($params["nor_".$field]))		$sql .=  " OR $field <>  ".$params["nor_".$field];';
		$output .= '
		return $sql;';
		return $output;
	}
	function getFunctionSqlSearch($field){
		$output =  '$sql = "";
		';
		$output .= 'if(!empty($params[$field]))	$sql .=  " AND $field ".sqlSearch($params[$field],$field) ;';
		$output .= '
		if(!empty($params["no".$field]))	$sql .=  " AND $field ".sqlNotSearch($params["no".$field],$field) ; ';
		$output .= '
		if(!empty($params["lt_".$field]))	$sql .=  " AND $field <   ".$params["lt_".$field] ;';
		$output .= '
		if(!empty($params["lte_".$field]))	$sql .=  " AND $field <=  ".$params["lte_".$field] ;';
		$output .= '
		if(!empty($params["gt_".$field]))	$sql .=  " AND $field >   ".$params["gt_".$field] ;';
		$output .= '
		if(!empty($params["gte_".$field]))	$sql .=  " AND $field >=  ".$params["gte_".$field] ;';
		$output .= '
		if(!empty($params["or_".$field]))	$sql .=  " OR $field =  ".$params["or_".$field] ;';
		$output .= '
		if(!empty($params["nor_".$field]))	$sql .=  " OR $field <>  ".$params["nor_".$field] ;';
		$output .= '
		return $sql;';
		return $output;
	}
	
	function getSelectSql($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql); 
	$output =  '" select ".$all." from '.$form.' where 1 " ;';
	while (!$rs->EOF) { 
		$fld = $rs->fields['Field']; 
		$output .='
			$sql .= $this->getParseSelect("'.$fld.'",$params);'; 
		$rs->MoveNext();
	}
	$output.= '
			if(!empty($groupBy)){ $sql .= " group by $groupBy";  }';
	$output.= '
			if(!empty($orderBy)){ $sql .= " order by $orderBy";  }';
	$output.= '
			if(!empty($debug)){ echo $sql;   }';
	$output.= '
			if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}';
	return '$sql='.$output.'
	return $this->conn->Execute($sql) ;	';
	}
	function getSearchSql($form){
	$sql="show fields  from  ".$form;
	$rs = $this->conn->Execute($sql);
	$output =  '" select ".$all." from '.$form.' where 1 " ;';
	while (!$rs->EOF) { 
		$fld = $rs->fields['Field']; 
		$output .='
		$sql .= $this->getParseSearch("'.$fld.'",$params);'; 
		$rs->MoveNext();
	}
	$output.= '
		if(!empty($groupBy)){ $sql .= " group by $groupBy";  }';
	$output.= '
		if(!empty($orderBy)){ $sql .= " order by $orderBy";  }';
	$output.= '
		if(!empty($debug)){ echo $sql;   }';
	$output.= '
		if(!empty($nbRows) && !empty($page) ){ return $this->conn ->PageExecute($sql,$nbRows,$page);}';
	return '$sql='.$output.'
	return $this->conn->Execute($sql) ;	';
	}
}
$ClassForm = new Form(); 
?>