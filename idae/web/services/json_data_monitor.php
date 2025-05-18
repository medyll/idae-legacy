<?
	ob_end_clean();
	include_once($_SERVER['CONF_INC']);
	$_POST = array_merge($_GET, $_POST);
	header("Connection: close");
	ignore_user_abort(true);

	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush();
	flush();

	ini_set('display_errors', 55);

	if (empty($_POST['table'])) {
		echo "no table ";
		// vardump($_POST);
		exit;
	}

	$APP            = new App();
	$APP_SYNC       = new App('sync_log');
	$APP_SYNC_TABLE = new App('sync_table');
	$_POST          = array_merge($_GET, $_POST);
	$name_table     = $_POST['table'];

	$COLL = $APP->plug('sitebase_sync', $name_table);
	// $APP->plug('sitebase_sync',$name_table)->createIndex(array('table'=>1,'N_ID'=>1));

	//

	//

	// 1ER tour , en base mongo
	foreach ($_POST['rows'] as $key => $ROW) {
		array_walk_recursive($ROW, 'encode_items');
		switch ($name_table):
			case "t_couvbienparserv":
				// if ($ROW['B_ACTIF_COUVBIENPARSERV_IDX'] == 'Non') continue;
				break;
			case "t_financement":
				$ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT'] = date('Y-m-d', strtotime($ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT']));
				break;
			case "t_cmde":
				$ROW['D_DATEDECOMMANDE_CMDE'] = date('Y-m-d H:i:s', strtotime($ROW['D_DATEDECOMMANDE_CMDE']));
				break;
			case "t_interplanifiee":
				$ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE']   = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE']));
				$ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']     = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']));
				break;
			case "t_grandeur":
				$ROW['D_DERNFACTURATIONLE_GRANDEUR'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNFACTURATIONLE_GRANDEUR']));
				break;
			case "t_reglefactuclt":
				$ROW['D_DERNREELLELE_REGLEFACTUCLT']    = date('Y-m-d H:i:s', strtotime($ROW['D_DERNREELLELE_REGLEFACTUCLT']));
				$ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT']));
				break;
			case "t_lieubien":
				$ROW['D_DATEDEBUT_LIEUBIEN'] = date('Y-m-d',strtotime($ROW['D_DATEDEBUT_LIEUBIEN']));
				if(!empty($ROW['D_DATEFIN_LIEUBIEN'])){
					$ROW['D_DATEFIN_LIEUBIEN'] = date('Y-m-d', strtotime($ROW['D_DATEFIN_LIEUBIEN']));
				}else{
					$ROW['D_DATEFIN_LIEUBIEN']='';
				}
				break;
			case "t_solservclt":
				if (strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']) < $time) continue;
				$ROW['D_DATEDEFININITIALE_SOLSERVCLT'] = date('Y-m-d', strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']));
				$ROW['D_DATEDEDEBUT_SOLSERVCLT']       = date('Y-m-d', strtotime($ROW['D_DATEDEDEBUT_SOLSERVCLT']));
				break;
			case "t_cptcpta":
				if (empty($ROW['C_CODEENCOMPTABILITE_CPTCPTA'])) continue;
				break;
		endswitch;

		// T_UPDATE_DATE T_CREATE_DATE
		if (!empty($ROW['T_CREATE_DATE'])) $ROW['T_CREATE_DATE'] = date('Y-m-d', strtotime($ROW['T_CREATE_DATE']));
		if (!empty($ROW['T_UPDATE_DATE'])) $ROW['T_UPDATE_DATE'] = date('Y-m-d', strtotime($ROW['T_UPDATE_DATE']));
		if (!empty($ROW['T_COMPUTED_UPDATE_DATE'])) $ROW['T_COMPUTED_UPDATE_DATE'] = date('Y-m-d H:i:s', strtotime($ROW['T_COMPUTED_UPDATE_DATE']));
		// test changes
		$test   = $APP->plug('sitebase_sync', $name_table)->findOne(['N_ID' => $ROW['N_ID']]);
		$result = array_diff($ROW, $test);
		$sutlre = array_diff($test, $ROW);

		if (sizeof($result) != 0) {
			// si N_ID
			if ($ROW['N_ID']) {
				$test   = $APP->plug('sitebase_sync', $name_table)->findOne(['N_ID' => $ROW['N_ID']]);
				$result = array_diff($ROW, $test);
				if (sizeof($result) != 0) {
					$APP->plug('sitebase_sync', $name_table)->update(['table' => $name_table, 'N_ID' => $ROW['N_ID']],
						['$set' => $ROW + ['date' => date('d/M/Y'), 'time' => date('H:i:s'), 'table' => $name_table, 'N_ID' => $ROW['N_ID']]],
						['upsert' => true]);

				}
			} else {
				$APP->plug('sitebase_sync', $name_table)->insert($ROW + ['date' => date('d/M/Y'), 'time' => date('H:i:s'), 'table' => $name_table, 'N_ID' => 'NO_N_ID']);
			}

			//if (sizeof($result) != 0) {
			$out['value'] = '*';
			$out['html']  = json_encode($name_table, JSON_PRETTY_PRINT) . ' taille ' . sizeof($result);
			$out['mdl']   = 'app/app_test';

			//}
			//skelMdl::send_cmd('act_update_mdl', $out);
			$OLD_SYNC = $APP_SYNC->findOne(['codeSync_log' => $name_table . '-' . $ROW['N_ID']]);
			$idsync_table                    = $APP_SYNC_TABLE->create_update(['codeSync_table' => $name_table], ['nomSync_table' => $name_table, 'dateSync_table' => date('Y-m-d'), 'heureSync_table' => date('H:i:s')]);
			$out_sync                        = ['idsync_table' => $idsync_table, 'nomSync_log' => $name_table . ' ' . $ROW['N_ID'] . ' ' . sizeof($result), 'dateCreationSync_log' => date('Y-m-d'), 'heureCreationSync_log' => date('H:i:s')];
			$out_sync['descriptionSync_log'] = json_encode($result, JSON_PRETTY_PRINT) . '<br>' . json_encode($sutlre, JSON_PRETTY_PRINT).'<br>________<br>'.$OLD_SYNC['descriptionSync_log'];

			$APP_SYNC->create_update(['codeSync_log' => $name_table . '-' . $ROW['N_ID']], $out_sync);
		}
	}

	// 2nd tour, boucle rows function
	foreach ($_POST['rows'] as $key => $ROW) {
		do_artis_rows($name_table, $ROW);
	}

	// keep url_data
	// faire une synchro avec mysql mongodb ....
	//   skelMdl::send_cmd('act_notify', ['msg' => 'Artis run ' . $name_table . '   ' . json_encode(($_POST['rows']))]);

	function encode_items(&$item, $key) {
		//	echo mb_detect_encoding($item, mb_detect_order(), true);
		$item = iconv('Windows-1251', 'UTF-8//IGNORE', $item);
		$item = iconv('', 'UTF-8//IGNORE', $item);
	}
	
	