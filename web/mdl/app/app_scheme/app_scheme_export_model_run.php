<?php
	include_once($_SERVER['CONF_INC']);

	$APP_SCHEME           = new App('appscheme');
	$APP_SCHEME_HAS_FIELD = new App('appscheme_has_field');
	$rs                   = $APP_SCHEME->find(['$or' => [['codeAppscheme_type' => 'CONF'], ['codeAppscheme' => 'agent'], ['codeAppscheme' => 'agent_groupe'], ['codeAppscheme' => 'agent_groupe_droit']]])->sort(['codeAppscheme_base' => 1, 'codeAppscheme' => 1]);
	$arr_type             = ['type', 'group', 'groupe', 'statut', 'ligne'];
	$FINAL                = [];
	$i                    = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$exp_scheme                               = [];
		$table                                    = $arr['codeAppscheme'];
		$exp_scheme[$table]['idappscheme']        = $arr['idappscheme'];
		$exp_scheme[$table]['idappscheme_base']        = $arr['idappscheme_base'];
		$exp_scheme[$table]['codeAppscheme_base'] = $arr['codeAppscheme_base'];
		$exp_scheme[$table]['nomAppscheme_base']  = $arr['nomAppscheme_base'];
		$exp_scheme[$table]['codeAppscheme']      = $arr['codeAppscheme'];
		$exp_scheme[$table]['nomAppscheme']       = $arr['nomAppscheme'];

		if (!empty($arr['iconAppscheme'])) $exp_scheme[$table]['iconAppscheme'] = $arr['iconAppscheme'];
		if (!empty($arr['colorAppscheme'])) $exp_scheme[$table]['colorAppscheme'] = $arr['iconAppscheme'];
		if (!empty($arr['sortFieldName'])) $exp_scheme[$table]['sortFieldName'] = $arr['sortFieldName'];
		if (!empty($arr['sortFieldOrder'])) $exp_scheme[$table]['sortFieldOrder'] = $arr['sortFieldOrder'];

		$Table = ucfirst($table);
		if ($table == 'app_version') continue;
		if ($table == 'app_version_file') continue;

		echo $Table. "<br>";
		if (!empty($arr['grilleFK']))
			$exp_scheme[$table]['grilleFK'] = array_column(array_values($arr['grilleFK']), 'table');
		if (!empty($arr['grilleCount']))
			$exp_scheme[$table]['grilleCount'] = array_keys($arr['grilleCount']);

		foreach ($arr_type as $key_type => $value_type):
			if (!empty($arr['has' . ucfirst($value_type) . 'Scheme'])) {
				$exp_scheme[$table]['has'][] = $value_type;
				// echo " has $value_type ";
			}

		endforeach;
		//  values
		$APP_TMP                      = new APP($table);
		$rs_tmp                       = $APP_TMP->find();
		$arr_fields                   = $APP_SCHEME_HAS_FIELD->distinct_all('codeAppscheme_field', ['idappscheme' => (int)$arr['idappscheme']]);
		$exp_scheme[$table]['fields'] = array_values($arr_fields);
		// pas de values pour appscheme
		//if ($arr['codeAppscheme'] != 'appscheme') {
		// vardump($exp_scheme[$table]);
		echo "$table ";

		if (!in_array($table, ['agent', 'agent_history', 'agent_groupe', 'agent_groupe_droit', 'app_conf'])) {
			echo " export data <br>";
			$name_id = "id$table";
			while ($arr_tmp = $rs_tmp->getNext()) {
				/*if($table=='agent_groupe' && $arr_tmp['code'.$Table] != 'ADMIN' ) continue;
				if($table=='agent_groupe_droit' && $arr_tmp['codeAgent_groupe'] != 'ADMIN' ) continue;*/
				$tmp = $arr_tmp;
				unset($tmp['updated_fields'],$tmp['_id'],$tmp['m_mode']);
				/*foreach ($exp_scheme[$table]['fields'] as $key_f => $node) {
					if (isset($arr_tmp[$node . $Table])){
						$tmp[$node . $Table] = $arr_tmp[$node . $Table];
						$tmp[$name_id] = (int)$arr_tmp[$name_id];}
				}*/
				if (!empty($exp_scheme[$table]['grilleFK']))
					foreach ($exp_scheme[$table]['grilleFK'] as $key_f => $node) {
						if (isset($arr_tmp['code' . ucfirst($node)]))
							$tmp['code' . ucfirst($node)] = $arr_tmp['code' . ucfirst($node)];
					}

				$exp_scheme[$table]['values'][] = $tmp;
			}
			//vardump($exp_scheme[$table]['values']);
		}
		echo "<br>";
		// };
		$FINAL[] = $exp_scheme[$table];

		skelMdl::send_cmd('act_progress', ['progress_name'    => 'export_job',
		                                   'progress_value'   => $i,
		                                   'progress_log'     => $table,
		                                   'progress_max'     => ($rs->count()),
		                                   'progress_message' => "En cours "], session_id());
	}

	// echo   json_encode($FINAL);
	$final_file = APPCONFDIR . '/models/' . BUSINESS . '/idae_scheme.json';
	if (!file_exists(APPCONFDIR . '/models/')) mkdir(APPCONFDIR . '/models/');
	if (!file_exists(APPCONFDIR . '/models/' . BUSINESS)) mkdir(APPCONFDIR . '/models/' . BUSINESS);
	file_put_contents($final_file, json_encode($FINAL));