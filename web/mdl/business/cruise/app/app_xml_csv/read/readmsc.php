<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	ini_set('display_errors', 55);

	$APP = new App();

	// Définition de quelques variables
	$local_rep = XMLDIR . 'msc/';
	$arrFile   = ['FRA/flatact_fra_fra.zip',
		/*'FRA/flatprices_fra_fra.zip',*/
		'FRA/itinff_fra_fra.zip',
		'FRA/flatitems_fra_fra.zip',
		'GBL/flatportdetl_fra.zip',
		'GBL/flatcitydetl_fra.zip',
		'FRA/flatfile_fra_air.zip',
		'FRA/flatshpdetl_fra_fra.zip',
		'FRA/flatcabdetl_fra_fra.zip',
		'GBL/flatregion_fra.zip'];

	$index_file = 0;

	if (!empty($_POST['run'])) {

		$index_file = 0;

		$page = !isset($_POST['page']) ? 0 : (int)$_POST['page'];
		$row_count = !isset($_POST['row_count']) ? 0 : (int)$_POST['row_count'];

		$file            = $arrFile[$page];
		$real_file_name  = explode('/', $file)[1];
		$real_file_name  = str_replace('.zip', '.csv', $real_file_name);
		$collection_name = str_replace('.csv', '', $real_file_name);

		// le traitement ici
		// sleep(5);
		copy_data($local_rep, $real_file_name, $collection_name,$row_count);
		$page = $page + 1;
		// skelMdl::send_cmd('act_notify', ['msg' => $page . '-' . $_POST['page'] . '-' . $real_file_name . ' ' . $collection_name]);
		// fin traitement
		echo "$page/ ".sizeof($arrFile);
		echo "<br>";
		if ($page <= sizeof($arrFile)) {
			 	skelMdl::runModule($PATH . 'read/readmsc', ['run'=>1,'page' => $page]);
		}else{
			$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

			skelMdl::run($PATH . 'read/readmsc_ville' , [ 'file'  => 'red' ,
	                                                    'url'   => 'la suite et fin ' ,
	                                                    'delay' => 10 ,
	                                                    'run'   => 1 ]);
		}

	} else {
		echo "fichiers " . sizeof($arrFile);
	}

	function copy_data($local_rep, $real_file_name, $collection_name,$row_count) {

		if ($handle = fopen($local_rep . $real_file_name, "r")) {
			global $APP;
			global $PATH;
			global $page;

			fseek($handle,0,SEEK_END) ;
			$max_file_size =ftell($handle);
			fseek($handle,0) ;
			//
			$APP_TMP = $APP->plug('sitebase_csv', $collection_name);
			$APP_TMP->drop();
			$fields       = fgetcsv($handle, 4096, ';');
			$modulo       = 100;
			$modulo_count = 0;

			if($row_count!=0){
				fseek($handle, $row_count,SEEK_SET);
			}

			$arr_indexes = ['TYPE', 'CODE', 'CATEGORY', 'SAILING-PORT', 'TERMINATION-PORT', 'ITEMS', 'DEP-PORT', 'ARR-PORT', 'AREA/DEST', 'ITIN-CD', 'CRUISE-ID', 'DISC-CLASS', 'DISC-RATE-TYPE', 'PORT-CD', 'COUNTRY-CD', 'CITY-CD', 'FARE-CD', 'CATG', 'SHIP-CD', 'REG-CD'];
			foreach ($arr_indexes as $key => $value) {
				$APP_TMP->ensureIndex([$value => 1]);
			}

			$tab_ligne = file($local_rep . $real_file_name);

			// skelMdl::send_cmd('act_notify', ['msg' => 'xml test ' . $real_file_name, 'options' => ['sticky' => 1]], session_id());
			$i     = empty($_POST['i_row'])? 0 : (int)$_POST['i_row'];
			$total = count($tab_ligne) - 1;
			while (($data = fgetcsv($handle, 4096, ';')) !== FALSE) {

				$row_count = ftell($handle);
				$i++;

				$row = safeArrayCombine($fields, $data);
				$out = array_filter_key($row, 'strlen');

				if (sizeof($out) != 0 && sizeof(array_keys($out) != 0)) $APP_TMP->insert($out);

				if (modulo_progress($i, 1200, $total)) {
					skelMdl::send_cmd('act_progress', ['progress_parent'  => 'read_msc',
					                                   'progress_name'    => 'xml_job_' . $collection_name,
					                                   'progress_text'    => ' XML . ' . $collection_name,
					                                   'progress_message' => $i . ' / ' . $total,
					                                   'progress_max'     => $total,
					                                   'progress_log'     => $row_count,
					                                   'progress_value'   => $i]);
				}
				if (($i % 10000) == 0 ) {
					fclose($handle);
					skelMdl::runModule($PATH . 'read/readmsc', ['run'=>1,'page' => $page,'row_count'=>$row_count,'i_row'=>$i]);// same page
					die();
				}

			}
			fclose($handle);

		} else {
			skelMdl::send_cmd('act_progress', ['progress_parent'  => 'read_msc',
			                                   'progress_name'    => 'xml_job_' . $collection_name,
			                                   'progress_text'    => ' XML . ' . $collection_name,
			                                   'progress_message' => ' ERREUR ']);
		}

	}

	function array_filter_key(array $array, $callback = null) {
		$matchedKeys = array_filter(array_keys($array), $callback);

		return array_intersect_key($array, array_flip($matchedKeys));
	}
