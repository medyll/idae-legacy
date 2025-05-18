<?
	include_once($_SERVER['CONF_INC']);

	$table       = $this->HTTP_VARS['table'];
	$table_value = (int)$this->HTTP_VARS['table_value'];

	$Idae        = new Idae($table);
	$ARR_COLLECT = $Idae->get_table_fields($table_value, $this->HTTP_VARS);
	if ($Idae->has_field('adresse')) {
		$html_map_link = 'table=' . $table . '&table_value=' . $table_value;
	}
	if (isset($this->HTTP_VARS['hide_empty']) && !empty($this->HTTP_VARS['hide_empty'])) {
		$ARR_COLLECT = array_filter($ARR_COLLECT,function($atr){return sizeof($atr['appscheme_fields']) !=0;});
	}
	echo AppTemplate::cf_template('app/app_fiche_fields/app_fiche_fields',array_merge($this->HTTP_VARS,['ARR_FIELDS'=>$ARR_COLLECT]));
