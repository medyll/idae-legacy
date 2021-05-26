<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	$CODE_FOURNISSEUR = 'MSC';
	$idfournisseur    = 21;

	$APP                 = new App();
	$APP_XML_DESTINATION = new App('xml_destination');
	$APP_DESTINATION     = new App('destination');
	$APP_PAYS            = new App('pays');

	// $APP_XML_DESTINATION->consolidate_scheme();

	$APP_CSV = $APP->plug('sitebase_csv', 'flatregion_fra');
	$rs_csv  = $APP_CSV->find()->sort(['REG-DESC' => 1]);

	if (empty($_POST['run'])) {
		?>
		<div class="padding"><?= $rs_csv->count() ?> destinations</div>
		<?

	} else {

		$total    = $rs_csv->count();
		$i        = 0;
		$non_attr = 0;
		$orphans = 0;

		while ($arr_csv_destination = $rs_csv->getNext()) {
			$i++;
			// DESTINATION
			$ARR_XML_DESTINATION = $APP_XML_DESTINATION->findOne(['codeFournisseur' => $CODE_FOURNISSEUR, 'codeXml_destination' => trim($arr_csv_destination['REG-CD'])]);
			$test_V              = $APP_DESTINATION->findOne(['codeDestination' => $arr_csv_destination['REG-CD']]);
			//
			$msg        = '';
			$arr_insert = [];
			//
			$arr_tmp             = explode(',', $arr_csv_destination['REG-DESC']);
			$nom_destination_msc = $arr_csv_destination['REG-DESC'];
			$nomDestination_tmp  = $arr_tmp[0];
			$nomPays_tmp         = $arr_tmp[1];

			$test_V2 = $APP_DESTINATION->findOne(['nomDestination' => new MongoRegex("/" . $nomDestination_tmp . "/i")]);
			//  idxml_destination dans xml_destination avec iddestination null
			if (!empty($ARR_XML_DESTINATION['idxml_destination']) && !empty($ARR_XML_DESTINATION['iddestination'])):
				$ARR_DESTINATION_TEST = $APP_DESTINATION->findOne(['iddestination' => (int)$ARR_XML_DESTINATION['iddestination']]);
				if (empty($ARR_DESTINATION_TEST['iddestination'])) :
					//
					//$APP_XML_DESTINATION->update(['idxml_destination' => (int)$ARR_XML_DESTINATION['idxml_destination']], ['iddestination' => null]);
					$msg = "$nom_destination_msc non trouvé";
				endif;

			//  idxml_destination dans xml_destination avec iddestination null
			elseif (!empty($ARR_XML_DESTINATION['idxml_destination']) && empty($ARR_XML_DESTINATION['iddestination'])):
				if (!empty($test_V2['iddestination'])):
					$iddestination   = (int)$test_V2['iddestination'];
					$nomDestination  = $test_V2['nomDestination'];
					$codeDestination = $test_V2['codeDestination'];

					$msg = $arr_csv_destination['REG-CD'] . ' ' . $nom_destination_msc . " / $nomDestination  =>  $iddestination  destination correspondante<br>";
					//
					$arr_insert = ['idfournisseur'       => (int)$idfournisseur,
					               'iddestination'       => (int)$iddestination,
					               'codeFournisseur'     => $CODE_FOURNISSEUR,
					               'nomDestination'      => $nomDestination,
					               'codeDestination'     => $codeDestination,
					               'codeXml_destination' => $arr_csv_destination['REG-CD'],
					               'nomXml_destination'  => $arr_csv_destination['REG-DESC']];
					//
				//	$APP_XML_DESTINATION->update(['idxml_destination' => (int)$ARR_XML_DESTINATION['idxml_destination']], $arr_insert);

				else:

					$msg = $arr_csv_destination['REG-CD'] . ' ' . $nomDestination_tmp . ' => ' . " destination non attribuée / inexistante<br>";
					$non_attr++;
				endif;

			//  pas dans xml_destination et destination trouvée
			elseif (empty($ARR_XML_DESTINATION['idxml_destination']) && !(empty($test_V['iddestination']))):
				$iddestination   = (int)$test_V['iddestination'];
				$nomDestination  = $test_V['nomDestination'];
				$codeDestination = $test_V['codeDestination'];
				$msg             = $nomDestination_tmp . ' => ' . $codeDestination . ' ' . $nomDestination . " à insérer dans xml_destination<br>";
				//
				$arr_insert = ['idfournisseur'       => (int)$idfournisseur,
				               'iddestination'       => $iddestination,
				               'codeFournisseur'     => $CODE_FOURNISSEUR,
				               'codeDestination'     => $codeDestination,
				               'nomDestination'      => $nomDestination,
				               'codeXml_destination' => $arr_csv_destination['REG-CD'],
				               'nomXml_destination'  => $arr_csv_destination['REG-DESC']];

			// pas iddestination dans xml_destination et destination non trouvée
			elseif (empty($ARR_XML_DESTINATION['idxml_destination']) && empty($test_V['iddestination'])):
				$msg = $nomDestination_tmp . ' => ' . " à insérer dans xml_destination sans destination<br>";
				//
				$arr_insert = ['idfournisseur'       => (int)$idfournisseur,
				               'codeFournisseur'     => $CODE_FOURNISSEUR,
				               'codeXml_destination' => $arr_csv_destination['REG-CD'],
				               'nomXml_destination'  => $arr_csv_destination['REG-DESC']];
			endif;
			//
			if (sizeof($arr_insert) != 0) {
				$APP_XML_DESTINATION->insert($arr_insert);
			}
			//
			$arr_progr = ['progress_parent'  => 'destination_msc',
			              'progress_name'    => 'xml_job_destination',
			              'progress_text'    => " XML . $total destinations ",
			              'progress_message' => $i . ' / ' . $total . "  - $non_attr non attr.",
			              'progress_max'     => $total,
			              'progress_value'   => $i];
			//
			if (!empty($msg)) $arr_progr['progress_log'] = $msg;
			//
			skelMdl::send_cmd('act_progress', $arr_progr );
		}

		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		skelMdl::run($PATH . 'read/readmsc_ship' , [ 'file'  => 'red' ,
		                                                    'url'   => 'la suite et fin ' ,
		                                                    'delay' => 10 ,
		                                                    'run'   => 1 ]);
	}

