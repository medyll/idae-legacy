<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();

	$local_rep = XMLDIR . 'msc/';

	$real_file_name  = $_POST['real_file_name'];
	$collection_name = str_replace('.csv', '', $real_file_name);

	if ($handle = fopen($local_rep . $real_file_name, "r")) {

		//
		$APP_TMP = $APP->plug('sitebase_csv', $collection_name);
		$APP_TMP->drop();
		$fields       = fgetcsv($handle, 4096, ';');
		$modulo       = 100;
		$modulo_count = 0;

		$arr_indexes = ['TYPE', 'CODE', 'CATEGORY', 'SAILING-PORT', 'TERMINATION-PORT', 'ITEMS', 'DEP-PORT', 'ARR-PORT', 'AREA/DEST', 'ITIN-CD', 'CRUISE-ID', 'DISC-CLASS', 'DISC-RATE-TYPE', 'PORT-CD', 'COUNTRY-CD', 'CITY-CD', 'FARE-CD', 'CATG', 'SHIP-CD', 'REG-CD'];
		foreach ($arr_indexes as $key => $value) {
			$APP_TMP->ensureIndex([$value => 1]);
		}

		$tab_ligne = file($local_rep . $real_file_name);

		// skelMdl::send_cmd('act_notify', ['msg' => 'xml test ' . $real_file_name, 'options' => ['sticky' => 1]], session_id());
		$i     = 0;
		$total = count($tab_ligne) - 1;
		while (($data = fgetcsv($handle, 4096, ';')) !== FALSE) {

			$i++;

			$row = safeArrayCombine($fields, $data);
			$out = array_filter_key($row, 'strlen');

			if (sizeof($out) != 0 && sizeof(array_keys($out) != 0)) $APP_TMP->insert($out);

			if (modulo_progress($i, 500, $total)) {
				skelMdl::send_cmd('act_progress', ['progress_parent'    => 'read_msc' ,
				                                   'progress_name'    => 'xml_job_' . $collection_name,
				                                   'progress_text'    => ' XML . ' . $collection_name,
				                                   'progress_message' => $i . ' / ' . $total,
				                                   'progress_max' =>   $total,
				                                   'progress_value'   => $i], session_id());
			}


		}
		fclose($handle);

	} else {
		skelMdl::send_cmd('act_progress', ['progress_parent'    => 'read_msc' ,
		                                   'progress_name'    => 'xml_job_' . $collection_name,
		                                   'progress_text'    => ' XML . ' . $collection_name,
		                                   'progress_message' => ' ERREUR '], session_id());
	}

	function array_filter_key(array $array, $callback = null) {
		$matchedKeys = array_filter(array_keys($array), $callback);

		return array_intersect_key($array, array_flip($matchedKeys));
	}

?>