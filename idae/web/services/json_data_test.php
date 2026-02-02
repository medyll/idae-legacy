<?
	include_once($_SERVER['CONF_INC']);

	$_POST = array_merge($_GET, $_POST);
	if (empty($_POST['table'])) {
		return;
	}
	if (!empty($_POST['stream_to'])) {
		if (!empty($_POST['url_data'])) {
			$_POST['url_data'] .='&stream_to='.$_POST['stream_to'];
		}
	}
	if (!empty($_POST['url_data'])) {
		 parse_str($_POST['url_data'],$_POST);
	}
	$uniqid = uniqid();
	//
	$table = $_POST['table'];
	$Table = ucfirst($_POST['table']);
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
	$vars    = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars']), 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page    = (!isset($_POST['page'])) ? 0 : $_POST['page'] ;
	$nbRows  = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
	//  vars_date
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
	//
	$FIELDS = array_merge(array_keys($APP_DATE_FIELDS), array_values($APP_FIELD_BOOL));
	//
	$where = array();
	if (!empty($_POST['search'])) {
		$search_escaped = MongoCompat::escapeRegex($_POST['search']);
		$regexp         = MongoCompat::toRegex(".*" . $search_escaped . "*.", 'i');
		$where['$or'][] = array($nom => $regexp);
		$where['$or'][] = array($id => (int)$_POST['search']);
		// tourne ds fk
		if (sizeof($GRILLE_FK) != 0) {
			foreach ($GRILLE_FK as $field):
				$nom_fk         = 'nom' . ucfirst($field['table_fk']);
				$regexp         = MongoCompat::toRegex("." . $nom_fk . "*.", 'i');
				$where['$or'][] = array($nom_fk => $regexp);
			endforeach;
		}
	}
	// SUr sort
	if (!empty($APP_TABLE['hasOrdreScheme'])):
		$sortBy = 'ordre'.$Table;
		$sortDir = 1;
	endif;
	//
	$rs = $APP->query($vars + $where);
	$maxcount = $rs->count();
	$rs = $APP->query($vars + $where,$page,$nbRows)->sort(array($sortBy => $sortDir));
	//
	if (!empty($groupBy)):
		$rs_dist    = $APP->distinct($groupBy, $vars + $where);
		//$dist_count = $rs_dist->count();
		//
		//$nbRows = (ceil($nbRows / $dist_count));
		//$rs_dist->limit(20)->skip($page * 20);
		// $maxcount = 2000;
	endif;
	//
	//$rs->limit($nbRows)->skip($page * $nbRows);
	// DEPRECATED  $columnModel
	$columnModel   = array();
	$columnModel[] = array('field_name' => '', 'title' => '<input type = "checkbox" onclick = "doClickto(this);" >', 'width' => '', 'className' => '');
	$columnModel[] = array('field_name' => 'id'.$table,'field_name_raw' => 'id'.$table, 'title' => 'id', 'width' => '', 'className' => 'alignright');

	foreach ($arrFieldsBool as $bool => $arr_ico):
		$columnModel[] = array('field_name' => $bool.$Table,'field_name_raw' => $bool, 'title' => '<i class="fa fa-' . $arr_ico[0] . '"></i>', 'width' => '', 'className' => '');
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
		//if(empty($vars['id'. $fk['table_fk']])):
		$columnModel[] = array('field_name' => 'nom'.ucfirst($fk['table_fk']),'field_name_raw' => $fk['table_fk'], 'title' => $fk['table_fk']);
		//endif;
	endforeach;
	if (!empty($APP_TABLE['hasOrdreScheme'])):
		$columnModel[] = array('field_name' => 'ordre'.$Table,'field_name_raw' => 'ordre', 'title' => idioma('ordre'));
	endif;
	if (!empty($APP_TABLE['hasPrixScheme'])):
		$columnModel[] = array('field_name' => 'prix'.$Table,'field_name_raw' => 'prix', 'title' => idioma('Prix'), 'width' => '', 'className' => '');
	endif;
	if (!empty($APP_TABLE['hasDateScheme'])):
		$columnModel[] = array('field_name' => 'dateDebut'.$Table,'field_name_raw' => 'date', 'title' => idioma('date'));
	endif;
	if (!empty($APP_TABLE['hasHeureScheme'])):
		$columnModel[] = array('field_name' => 'heureDebut'.$Table,'field_name_raw' => 'heureDebut', 'title' => idioma('début'));
		$columnModel[] = array('field_name' => 'heureFin'.$Table,'field_name_raw' => 'heureFin', 'title' => idioma('fin'));
	endif;
	// MAIN_DATA
	if (!empty($groupBy)): 
		$data_main = array();
		$strm = array();

		$i=0;
		foreach ($rs_dist as $arr_dist):
			$i++;

			$vars['id' . $groupBy] = (int)$arr_dist['id' . $groupBy];
			$rs                    = $APP->query($vars + $where)->sort(array($sortBy => $sortDir));
			echo vardump($vars);
			//
			$vars_rfk['vars']        = ['id' . $table => $table_value];
			$vars_rfk['table']       = $arr_fk['table'];
			$vars_rfk['table_value'] = $arr_fk['table_value'];

			$vars_rfk['vars'] = ['id' . $groupBy => $arr_dist['id' . $groupBy]];

			$vars_rfk['table']       = $table;
			$vars_rfk['table_value'] = $arr_dist['id' . $groupBy];
			$vars_rfk['groupBy']     = '';

			$data_out = array();
			/// datas du groupby colspan

			$z           = '<a act_chrome_gui = "app/app_liste/app_liste_gui" vars = "' . http_build_query($vars_rfk) . '" >' . $arr_dist['nom' . ucfirst($groupBy)].' '.$i . '</a >';
			$data_main[] = array('html' => $z, 'vars' => $vars_rfk, 'groupBy' => 1);
			$strm[]      = array('html' => $z, 'vars' => $vars_rfk,'groupBy'=>1);

echo "<hr> ".$arr_dist['nom' . ucfirst($groupBy)]." ".($rs->count())."<br>";
			while ($arr = $rs->getNext()) {
				$i++;
				$data_out = array();
				// variables pour le mdl_tr
				$trvars['id' . $table] = $arr[$id];
				$trvars['_id']         = (string)$arr['_id'];
				$trvars['table']       = $table;
				$trvars['table_value'] = $arr[$id];
				$trvars['sortBy']      = $sortBy;
				$trvars['key_date']    = $key_date;
				//
				$prod_vars = Act::decodeVars([$id => (int)$arr[$id]]);
				$ct_prod = $APP->plug('sitebase_production', 'produit')->find($prod_vars)->count();
				//
				$data_out['chk'] = '<input type = "checkbox" value = "' . $arr[$id] . '" name = "id[]" />';
				$data_out[$id] = $arr[$id];

				foreach ($arrFieldsBool as $bool => $arr_ico):
					$name       = $bool . ucfirst($table);
					$set_value  = empty($arr[$name]);
					$css        = empty($arr[$name]) ? 'textvert' : '';
					$uri        = "table=$table&table_value=$arr[$id]&vars[$name]=$set_value&scope=$id";
					$data_out[$bool . ucfirst($table)] = '<a class = "' . $css . '" onclick = "ajaxValidation(\'app_update\',\'mdl/app/\',\'' . $uri . '\');" ><i class = "fa fa-' . $arr_ico[(int)$set_value] . '" ></i ></a >';
				endforeach;
				$data_out['prod'] = ($ct_prod == 0) ? '' : $ct_prod; //  $arr['ordre' . ucfirst($fk['table'])];
				if (!empty($key_date)):
					$data_out[$key_date] = '';
				endif;
				if (!empty($APP_TABLE['hasCodeScheme'])):
					$data_out['code' . ucfirst($table)] = $arr['code' . ucfirst($table)];
				endif;
				$data_out[$nom] = '<a act_chrome_gui = "app/app/app_fiche" vars = "table=' . $table . '&table_value=' . $arr[$id] . '" scope = "id' . $table . '" options = "{ident:\'id' . $table . '\',value:\'' . $arr[$id] . '\',scope:\'id' . $table . '\'}" >' . $arr[$nom] .' '.$i. '</a>';
				foreach ($GRILLE_FK as $field):
					$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
					$id_fk = $field['idtable_fk'];
					//
					$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
					$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
					//
					$data_out['nom' . ucfirst($field['table_fk'])] = $dsp_name;
				endforeach;

				echo $arr[$id]."<br>";

				if (!empty($APP_TABLE['hasOrdreScheme'])):
					$data_out['ordre' . ucfirst($table)] = $arr['ordre' . ucfirst($table)];
				endif;
				if (!empty($APP_TABLE['hasPrixScheme'])):
					$data_out['prix' . ucfirst($table)] = maskNbre($arr['prix' . ucfirst($table)]);

				endif;
				if (!empty($APP_TABLE['hasDateScheme'])):
					$data_out['dateDebut' . ucfirst($table)] = date_fr($arr['dateDebut' . ucfirst($table)]);

				endif;
				if (!empty($APP_TABLE['hasHeureScheme'])):
					$data_out['heureDebut' . ucfirst($table)] = maskHeure($arr['heureDebut' . ucfirst($table)]);
					$data_out['heureFin' . ucfirst($table)] = maskHeure($arr['heureFin' . ucfirst($table)]);
				endif;
				$data_main[] = array('html' => $data_out, 'vars' => $trvars,'value'=>$arr[$id]);
				$strm[]     = array('html' => $data_out, 'vars' => $trvars,'value'=>$arr[$id]);
				// progress
				if(($i % 100)==0 || $i==1){
					$vars = array('progress_name' => 'progress_'.$table, 'progress_value' =>($nbRows*$page)+$i,'progress_max' =>$maxcount);
					skelMdl::send_cmd('act_progress',$vars,session_id());
				}
				// stream
				if(($i % 20)==0 || $i==1){
					if(!empty($_POST['stream_to'])):
						$out_model = array('columnModel' => $columnModel, 'data_main' => $strm,'maxcount'=>$maxcount);

						$strm_vars = array('stream_to' => $_POST['stream_to'],'data'=>$out_model,'data_size'=>sizeof($strm));

						skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)),session_id());
						$strm = array();
					endif;
				}

			}
			// stream
			if(($i % 20)==0 || $i==1){
				if(!empty($_POST['stream_to'])):
					$out_model = array('columnModel' => $columnModel, 'data_main' => $strm,'maxcount'=>$maxcount);

					$strm_vars = array('stream_to' => $_POST['stream_to'],'data'=>$out_model,'data_size'=>sizeof($strm));

					skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)),session_id());
					$strm = array();
				endif;
			}
		endforeach;
		if(sizeof($strm)!=0):
			if(!empty($_POST['stream_to'])):
				$out_model = array('columnModel' => $columnModel, 'data_main' => $strm,'maxcount'=>$maxcount);

				$strm_vars = array('stream_to' => $_POST['stream_to'],'data'=>$out_model,'data_size'=>sizeof($strm));

				skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)),session_id());
				$strm = array();
			endif;
		endif;
	else:

		$data_main = array();
		$i=0;
		//
		while ($arr = $rs->getNext()) {
			$i++;
			$data_out = array();
			// variables pour le mdl_tr
			$trvars['id' . $table] = $arr[$id];
			$trvars['_id']         = (string)$arr['_id'];
			$trvars['table']       = $table;
			$trvars['table_value'] = $arr[$id];
			$trvars['sortBy']      = $sortBy;
			$trvars['key_date']    = $key_date;
			//
			$prod_vars = Act::decodeVars([$id => (int)$arr[$id]]);
			$ct_prod = $APP->plug('sitebase_production', 'produit')->find($prod_vars)->count();
			//
			$data_out['chk'] = '<input type = "checkbox" value = "' . $arr[$id] . '" name = "id[]" />';
			$data_out[$id] = $arr[$id];

			foreach ($arrFieldsBool as $bool => $arr_ico):
				$name       = $bool . ucfirst($table);
				$set_value  = empty($arr[$name]);
				$css        = empty($arr[$name]) ? 'textvert' : '';
				$uri        = "table=$table&table_value=$arr[$id]&vars[$name]=$set_value&scope=$id";
				$data_out[$bool . ucfirst($table)] = '<a class = "' . $css . '" onclick = "ajaxValidation(\'app_update\',\'mdl/app/\',\'' . $uri . '\');" ><i class = "fa fa-' . $arr_ico[(int)$set_value] . '" ></i ></a >';
			endforeach;
			$data_out['prod'] = ($ct_prod == 0) ? '' : $ct_prod;
			if (!empty($key_date)):
				$data_out[$key_date] = '';
			endif;
			if (!empty($APP_TABLE['hasCodeScheme'])):
				$data_out['code' . ucfirst($table)] = $arr['code' . ucfirst($table)];
			endif;
			$data_out[$nom] = '<a act_chrome_gui = "app/app/app_fiche" vars = "table=' . $table . '&table_value=' . $arr[$id] . '" scope = "id' . $table . '" options = "{ident:\'id' . $table . '\',value:\'' . $arr[$id] . '\',scope:\'id' . $table . '\'}" >' . $arr[$nom] . '</a>';
			foreach ($GRILLE_FK as $field):
				$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
				$id_fk = $field['idtable_fk'];

				$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
				$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];

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
			if (!empty($APP_TABLE['hasHeureScheme'])):
				$data_out['heureDebut' . ucfirst($table)] = maskHeure($arr['heureDebut' . ucfirst($table)]);
				$data_out['heureFin' . ucfirst($table)] = maskHeure($arr['heureFin' . ucfirst($table)]);
			endif;
			$data_main[] = array('html' => $data_out, 'vars' => $trvars,'value'=>$arr[$id]);
			$strm[]  = array('html' => $data_out, 'vars' => $trvars,'value'=>$arr[$id]);
			// progress
			if(($i % 100)==0 || $i==1){
				$vars = array('progress_name' => 'progress_'.$table, 'progress_value' =>($nbRows*$page)+$i,'progress_max' =>$maxcount);
				skelMdl::send_cmd('act_progress',$vars,session_id());
			}
			// stream
			if(($i % 20)==0 || $i==1){
				if(!empty($_POST['stream_to'])):
					$out_model = array('columnModel' => $columnModel, 'data_main' => $strm,'maxcount'=>$maxcount);

					$strm_vars = array('stream_to' => $_POST['stream_to'],'data'=>$out_model,'data_size'=>sizeof($strm));

					skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)),session_id());
					$strm = array();
				endif;
			}



		}

		$vars = array('progress_name' => 'progress_'.$table, 'progress_value' =>($nbRows*$page)+$i,'progress_max' =>$maxcount);
		skelMdl::send_cmd('act_progress', $vars,session_id());

	endif;
	if(sizeof($strm)!=0):
		if(!empty($_POST['stream_to'])):
			$out_model = array('columnModel' => $columnModel, 'data_main' => $strm,'maxcount'=>$maxcount);

			$strm_vars = array('stream_to' => $_POST['stream_to'],'data'=>$out_model,'data_size'=>sizeof($strm));

			skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)),session_id());
			$strm = array();
		endif;
	endif;

	$out_model = array('columnModel' => $columnModel, 'data_main' => $data_main,'maxcount'=>$maxcount);
	//
	if(empty($_POST['stream_to'])):
	//	echo trim(json_encode($out_model));
	endif;

function dotr($arr){

}