<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors' , 55);

	$CODE_FOURNISSEUR = 'MSC';
	$idfournisseur    = 21;

	$APP           = new App();
	$APP_XML_VILLE = new App('xml_ville');
	$APP_VILLE     = new App('ville');
	$APP_PAYS      = new App('pays');

	// $APP_XML_VILLE->consolidate_scheme();

	$APP_CSV = $APP->plug('sitebase_csv' , 'flatportdetl_fra');
	$rs_csv  = $APP_CSV->find()->sort([ 'PORT-NAME' => 1 ]);

	if ( empty($_POST['run']) ) {
		?>
		<div class = "padding"><?= $rs_csv->count() ?> Ports</div>
		<?

	} else {

		$total    = $rs_csv->count();
		$i        = 0;
		$non_attr = 0;
		while ($arr_csv_ville = $rs_csv->getNext()) {
			$i ++;
			// VILLE
			$ARR_XML_VILLE = $APP_XML_VILLE->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR , 'codeXml_ville' => trim($arr_csv_ville['PORT-CD']) ]);
			$test_V        = $APP_VILLE->findOne([ 'codeVille' => $arr_csv_ville['PORT-CD'] ]);
			//
			$msg        = '';
			$arr_insert = [ ];
			//
			$arr_tmp       = explode(',' , $arr_csv_ville['PORT-NAME']);
			$nom_ville_msc = $arr_csv_ville['PORT-NAME'];
			$nomVille_tmp  = $arr_tmp[0];
			$nomPays_tmp   = $arr_tmp[1];

			$test_V2 = $APP_VILLE->findOne([ 'nomVille' => new MongoRegex("/" . $nomVille_tmp . "/i") ]);
			//  idxml_ville dans xml_ville avec idville null
			if ( !empty($ARR_XML_VILLE['idxml_ville']) && !empty($ARR_XML_VILLE['idville']) ):
				$ARR_VILLE_TEST = $APP_VILLE->findOne([ 'idville' => (int)$ARR_XML_VILLE['idville'] ]);
				if ( empty($ARR_VILLE_TEST['idville']) ) :
					//
					$APP_XML_VILLE->update([ 'idxml_ville' => (int)$ARR_XML_VILLE['idxml_ville'] ] , [ 'idville' => null ]);
					$msg = "non trouvé";
				endif;

			//  idxml_ville dans xml_ville avec idville null
			elseif ( !empty($ARR_XML_VILLE['idxml_ville']) && empty($ARR_XML_VILLE['idville']) ):
				if ( !empty($test_V2['idville']) ):
					$idville   = (int)$test_V2['idville'];
					$nomVille  = $test_V2['nomVille'];
					$codeVille = $test_V2['codeVille'];

					$msg = $arr_csv_ville['PORT-CD'] . ' ' . $nom_ville_msc . " / $nomVille  =>  $idville  ville correspondante<br>";
					//
					$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
					                'idville'         => (int)$idville ,
					                'codeFournisseur' => $CODE_FOURNISSEUR ,
					                'nomVille'        => $nomVille ,
					                'codeVille'       => $codeVille ,
					                'codeXml_ville'   => $arr_csv_ville['PORT-CD'] ,
					                'nomXml_ville'    => $arr_csv_ville['PORT-NAME'] ];
					//
					$APP_XML_VILLE->update([ 'idxml_ville' => (int)$ARR_XML_VILLE['idxml_ville'] ] , $arr_insert);

				else:

					$msg = $arr_csv_ville['PORT-CD'] . ' ' . $nomVille_tmp . ' => ' . " ville non attribuée / inexistante<br>";
					$non_attr ++;
				endif;

			//  pas dans xml_ville et ville trouvée
			elseif ( empty($ARR_XML_VILLE['idxml_ville']) && !(empty($test_V['idville'])) ):
				$idville   = (int)$test_V['idville'];
				$nomVille  = $test_V['nomVille'];
				$codeVille = $test_V['codeVille'];
				$msg       = $nomVille_tmp . ' => ' . $codeVille . ' ' . $nomVille . " à insérer dans xml_ville<br>";
				//
				$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
				                'idville'         => $idville ,
				                'codeFournisseur' => $CODE_FOURNISSEUR ,
				                'codeVille'       => $codeVille ,
				                'nomVille'        => $nomVille ,
				                'codeXml_ville'   => $arr_csv_ville['PORT-CD'] ,
				                'nomXml_ville'    => $arr_csv_ville['PORT-NAME'] ];

			// pas idville dans xml_ville et ville non trouvée
			elseif ( empty($ARR_XML_VILLE['idxml_ville']) && empty($test_V['idville']) ):
				$msg = $nomVille_tmp . ' => ' . " à insérer dans xml_ville sans ville<br>";
				//
				$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
				                'codeFournisseur' => $CODE_FOURNISSEUR ,
				                'codeXml_ville'   => $arr_csv_ville['PORT-CD'] ,
				                'nomXml_ville'    => $arr_csv_ville['PORT-NAME'] ];
			endif;
			//
			if ( sizeof($arr_insert) != 0 ) {
				$APP_XML_VILLE->insert($arr_insert);
			}
			//
			$arr_progr = [ 'progress_parent'  => 'ville_msc' ,
			               'progress_name'    => 'xml_job_ville' ,
			               'progress_text'    => " XML . $total ports " ,
			               'progress_message' => $i . ' / ' . $total . "  - $non_attr non attr." ,
			               'progress_max'     => $total ,
			               'progress_value'   => $i ];
			//
			if ( !empty($msg) ) {
				$arr_progr['progress_log'] = $msg;
			}
			//
			skelMdl::send_cmd('act_progress' , $arr_progr );
		}

		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		skelMdl::run($PATH . 'read/readmsc_destination' , [ 'file'  => 'red' ,
		                                                    'url'   => 'la suite et fin ' ,
		                                                    'delay' => 10 ,
		                                                    'run'   => 1 ]);
	}

