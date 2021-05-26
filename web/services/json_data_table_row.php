<?
	include_once($_SERVER['CONF_INC']);
	$_POST = array_merge($_GET, $_POST);
	if (empty($_POST['table'])) {
		return;
	}
	if (!empty($_POST['url_data'])) {
		 parse_str($_POST['url_data'],$_POST);
	}
	$uniqid = uniqid();
	//
	$table = $_POST['table'];
	//
	$APP = new App($table);
	//
	$id       = 'id' . $table;
	$nom      = 'nom' . ucfirst($table);
	$id_type  = 'id' . $table . '_type';
	$nom_type = 'nom' . ucfirst($table) . '_type';
	$top      = 'estTop' . ucfirst($table);
	$actif    = 'estActif' . ucfirst($table);
	$visible  = 'estVisible' . ucfirst($table);
	//
	$vars    = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page    = (empty($_POST['page'])) ? 0 : $_POST['page'] - 1;
	$nbRows  = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
	//
	if (!empty($_POST['table_value'])):
		$vars[$id]        = (int)$_POST['table_value'];
	endif;	//  vars_date
	if (!empty($_POST['vars_date'])):
		$key_date        = $_POST['vars_date'] ['name_key'];
		$vars[$key_date] = $_POST['vars_date'][$key_date];
	endif;
	// SETTINGS       ne pas faire ici mais par javascript ....
	// $APP->set_settings($_SESSION['idagent'], ['sortBy_' . $table => $sortBy, 'sortDir_' . $table => $sortDir, 'groupBy_' . $table => $groupBy, 'nbRows_' . $table => $nbRows]);
	//
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	$HTTP_VARS       = $APP->translate_vars($vars);
	$RS_APP          = $APP->query($vars, 0);
	$APP_DATE_FIELDS = $APP->get_date_fields($table);
	$GRILLE_SORT     = array_keys($APP->get_sort_fields($table));
	$arrFieldsBool   = $APP->get_array_field_bool();
	$APP_FIELD_BOOL  = $APP->get_bool_fields($table);
	$arrFields       = $APP->get_basic_fields_nude($table);
	//
	$FIELDS = array_merge(array_keys($APP_DATE_FIELDS), array_values($APP_FIELD_BOOL));
	//
	$where = array();
	if (!empty($_POST['search'])) {
		$regexp         = new MongoRegex("/.*" . $_POST['search'] . "*./i");
		$where['$or'][] = array($nom => $regexp);
		$where['$or'][] = array($id => (int)$_POST['search']);
		// tourne ds fk
		if (sizeof($GRILLE_FK) != 0) {
			foreach ($GRILLE_FK as $field):
				$nom_fk         = 'nom' . ucfirst($field['table_fk']);
				$regexp         = new MongoRegex("/." . $nom_fk . "*./i");
				$where['$or'][] = array($nom_fk => $regexp);
			endforeach;
		}
	}
	//
	$rs = $APP->query($vars + $where);
	$maxcount = $rs->count();
	$rs = $APP->query($vars + $where,$page,$nbRows)->sort(array($sortBy => $sortDir));
	//

	//
	//$rs->limit($nbRows)->skip($page * $nbRows);
	// deprecated
	$columnModel   = array();
	$columnModel[] = array('field_name' => '', 'title' => '<input type = "checkbox" onclick = "doClickto(this);" >', 'width' => '', 'className' => '');
	$columnModel[] = array('field_name' => 'id'.$table,'field_name_raw' => 'id'.$table, 'title' => 'id', 'width' => '', 'className' => 'alignright');

	foreach ($arrFieldsBool as $bool => $arr_ico):
		$columnModel[] = array('field_name' => $bool.$Table,'field_name_raw' => $bool, 'title' => '<i class="fa fa-' . $arr_ico[0] . '"></i>', 'width' => '', 'className' => 'fk');
	endforeach;
	$columnModel[] = array('field_name' => '','field_name_raw' => '', 'title' => '<i class = "fa fa-cubes" ></i >', 'width' => '', 'className' => 'aligncenter');
	if (!empty($key_date)):
		$columnModel[] = array('field_name' => $key_date.$Table,'field_name_raw' => $key_date, 'title' => $key_date, 'width' => '', 'className' => '');
	endif;
	if (!empty($APP_TABLE['hasCodeScheme'])):
		$columnModel[] = array('field_name' => 'code'.$Table,'field_name_raw' => 'code', 'title' => idioma('code'));
	endif;
	$columnModel[] = array('field_name' => 'nom'.$Table,'field_name_raw' => 'nom', 'title' => $table, 'width' => '', 'className' => '');
	foreach ($GRILLE_FK as $fk):
		$columnModel[] = array('field_name' => $fk['table_fk'],'field_name_raw' => $fk['table_fk'], 'title' => $fk['table_fk'], 'width' => '', 'className' => '');
	endforeach;
	if (!empty($APP_TABLE['hasOrdreScheme'])):
		$columnModel[] = array('field_name' => 'ordre'.$Table,'field_name_raw' => 'ordre', 'title' => idioma('ordre'));
	endif;
	if (!empty($APP_TABLE['hasPrixScheme'])):
		$columnModel[] = array('field_name' => 'prix'.$Table,'field_name_raw' => 'prix', 'title' => idioma('Prix'));
	endif;
	if (!empty($APP_TABLE['hasDateScheme'])):
		$columnModel[] = array('field_name' => 'dateDebut'.$Table,'field_name_raw' => 'date', 'title' => idioma('date'));
	endif;
	if (!empty($APP_TABLE['hasHeureScheme'])):
		$data_out['heureDebut' . ucfirst($table)] = maskHeure($arr['heureDebut' . ucfirst($table)]);
		$data_out['heureFin' . ucfirst($table)] = maskHeure($arr['heureFin' . ucfirst($table)]);
	endif;
	// MAIN_DATA


		$data_main = array();


		while ($arr = $rs->getNext()) {
			$data_out = $arr;
			// variables pour le mdl_tr
			$trvars['id' . $table] = $arr[$id];
			$trvars['_id']         = (string)$arr['_id'];
			$trvars['table']       = $table;
			$trvars['table_value'] = $arr[$id];
			$trvars['sortBy']      = $sortBy;
			$trvars['key_date']    = $key_date;
			//
			//
			$data_out[] = '<input type = "checkbox" value = "' . $arr[$id] . '" name = "id[]" />';
			$data_out[$id] = $arr[$id];

			foreach ($arrFields as $key_f => $value_f):
				//
				$data_out[$key_f. ucfirst($table)] = $arr[$key_f];
			endforeach;
			foreach ($arrFieldsBool as $bool => $arr_ico):
				$name       = $bool . ucfirst($table);
				$set_value  = empty($arr[$name]);
				$css        = empty($arr[$name]) ? '' : '';
				$uri        = "table=$table&table_value=$arr[$id]&vars[$name]=$set_value&scope=$id";
				$data_out[$bool . ucfirst($table)] = '<span class = "' . $css . ' cursor" onclick = "ajaxValidation(\'app_update\',\'mdl/app/\',\'' . $uri . '\');" ><i class = "fa fa-' . $arr_ico[(int)$set_value] . '" ></i ></span >';
			endforeach;


			if (!empty($key_date)):
				$data_out[$key_date] = '';
			endif;
			$data_out[$nom] = '<a act_chrome_gui = "app/app/app_fiche" vars = "table=' . $table . '&table_value=' . $arr[$id] . '" scope = "id' . $table . '" options = "{ident:\'id' . $table . '\',value:\'' . $arr[$id] . '\',scope:\'id' . $table . '\'}" >' . $arr[$nom] . '</a>';
			foreach ($GRILLE_FK as $field):
				$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
				$id_fk = $field['idtable_fk'];
				//
				$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
				$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
				//
				$data_out['nom' . ucfirst($field['table_fk'])] = $dsp_name;
			endforeach;
			if (!empty($APP_TABLE['hasOrdreScheme'])):
				$data_out['ordre' . ucfirst($table)] = $arr['ordre' . ucfirst($table)];
			endif;
			if (!empty($APP_TABLE['hasPrixScheme'])):
				$data_out['prix' . ucfirst($table)] = maskNbre($arr['prix' . ucfirst($table)]);

			endif;
			if (!empty($APP_TABLE['hasDateScheme'])):
				$data_out['dateDebut' . ucfirst($table)] = date_fr($arr['dateDebut' . ucfirst($table)]);
			endif;


			$data_main[] = array('html' => $data_out, 'vars' => $trvars,'value'=>$arr[$id]);
		}


	$out_model = array( 'data_main' => $data_main,'maxcount'=>$maxcount); // 'columnModel' => $columnModel,
	//
	echo trim(json_encode($out_model));