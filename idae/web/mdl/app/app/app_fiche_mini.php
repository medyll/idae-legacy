<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$table = $_POST['table'];
	if(empty($this)) {
		$Idae = new Idae($table);
		echo $Idae->module('app/app/fiche_mini',$_POST);
		return;
	}

	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche_mini.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche_mini', $_POST);

		return;
	}

	$table       = $this->HTTP_VARS['table'];
	$table_value = (int)$this->HTTP_VARS['table_value'];

	$Idae            = new Idae($table);
	$APP             = new App($table);
	$ARR_COLLECT     = $Idae->get_table_fields($table_value, $this->HTTP_VARS);

	if ($Idae->has_field('adresse')) {
		$html_map_link = 'table=' . $table . '&table_value=' . $table_value;
	}
	if (!empty($this->HTTP_VARS['hide_empty'])) {
		$ARR_COLLECT = array_filter($ARR_COLLECT, function ($atr) { return sizeof($atr['appscheme_fields']) != 0; });
	}
	$tpl['nomAppscheme']     = $APP->nomAppscheme;
	$tpl['iconAppscheme']    = $APP->iconAppscheme;
	$tpl['colorAppscheme']   = $APP->colorAppscheme;
	$tpl['tpl_table_fields'] = $Idae->module('app/app/fiche_fields', ['table'         => $table,
	                                                                  'in_mini_fiche' => 1,
	                                                                  'hide_empty'    => 1,
	                                                                  'table_value'   => $table_value]);
	echo AppTemplate::cf_template('app/app_fiche/app_fiche_mini', array_merge($tpl, $this->HTTP_VARS, ['ARR_FIELDS' => $ARR_COLLECT]));

