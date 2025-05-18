<?
	include_once('conf.inc.php');
	ini_set('display_errors', 55);
	$APP = new App();

	echo $importedCSVFile = APPPATH . 'web/idae_artis_2.csv';
	$assocSSC = csv2array($importedCSVFile);
	$max      = sizeof($assocSSC);

	$ClassClient = new Client();

	set_time_limit(0);

	$done  = 0;
	$empty = 0;

	foreach ($assocSSC as $key => $line) {
		if (!empty($line['codeclient'])) {
			$rs  = $ClassClient->getOneClient(['estClientClient' => 1, 'groupBy' => 'idclient', 'nomSociete' => addslashes($line['nomclient'])]);
			$arr = $rs->fetchRow();
			if (!empty($arr['idclient']) && empty($arr['numeroClient'])) {
				echo "<br>";
				print_r($line);
			 	$ClassClient->updateClient(['idclient'=>$arr['idclient'],'numeroClient'=>$line['codeclient']]);
				$done++;

			} else {
				$empty++;
			}
		} else {
			$empty++;
		}
	}

	echo "<br>Done => " . $done;
	echo "<br>Vides => " . $empty;

	function csv2array($importedCSVFile) { // tomongo !!
		//
		/*$APP = new App();
		$db_tmp = $APP->plug('sitebase_tmp','tmp_csv');
		$rs_tmp = $db_tmp->find(array('idcsv'=>1));

		if($rs_tmp->count()!=0):
			return $rs_tmp;

		endif;*/

		$assocData  = array();
		$assocDataM = array();

		if (($handle = fopen($importedCSVFile, "r")) !== FALSE) {
			$rowCounter = 0;
			while (($rowData = fgetcsv($handle, 0, ";")) !== FALSE) {
				if (0 === $rowCounter) {
					$headerRecord = $rowData;
				} else {
					$assocDataM = array('idcsv' => 1);
					foreach ($rowData as $key => $value) {
						$key                              = niceUrl(utf8_encode($headerRecord[$key]));
						$md_key                           = str_replace('.', '', $key);
						$assocData[$rowCounter - 1][$key] = utf8_encode($value);
						$assocDataM[$md_key]              = utf8_encode($value);
					}
					// $db_tmp->insert($assocDataM);

				}
				//
				$rowCounter++;
			}

			fclose($handle);
		}

		return $assocData;
	}

?>
FIN