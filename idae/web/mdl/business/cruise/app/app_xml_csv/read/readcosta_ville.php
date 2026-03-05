<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 30/11/2017
	 * Time: 19:13
	 */

	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../../../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	$CODE_FOURNISSEUR = 'COSTA';

	$APP             = new App();
	$APP_FOURNISSEUR = new App('fournisseur');
	$APP_XML_VILLE   = new App('xml_ville');
	$APP_VILLE       = new App('ville');
	$APP_PAYS        = new App('pays');
	$ARR_FOURNISSEUR = $APP_FOURNISSEUR->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR ]);

	$idfournisseur = (int)$ARR_FOURNISSEUR['idfournisseur'];
	$local_rep     = XMLDIR . 'costa/';

	$xml   = simplexml_load_file($local_rep . 'ports.xml');
	$xml_p = $xml->Ports;
	if ( empty($_POST['run']) ) {
		?>
		<div class = "padding"><?= sizeof($xml->Ports->Port) ?> Ports</div>
		<?

	} else {
		foreach ($xml_p as $key => $Port) {
			$total = sizeof($Port);
			foreach ($Port as $key_iti => $item) {
				$attr          = $item->attributes();
				$codeXml_ville = (string)$attr['Code'];
				$nomXml_ville  = (string)$attr['Description']; // ShortDescription LongDescription   VeryLongDescription
				// echo $codeXml_ville;
				// echo $nomXml_ville;
				// continue;
				// VILLE
				$ARR_XML_VILLE = $APP_XML_VILLE->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR , 'codeXml_ville' => trim($codeXml_ville) ]);
				$test_V        = $APP_VILLE->findOne([ 'codeVille' => $codeXml_ville ]);
				$test_V2       = $APP_VILLE->findOne([ 'nomVille' => MongoCompat::toRegex(MongoCompat::escapeRegex($nomXml_ville), 'i') ]);
				//
				$msg        = '';
				$arr_insert = [ ];
//
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

						$msg = $codeXml_ville . " / $nomVille  =>  $idville  ville correspondante<br>";
						//
						$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
						                'idville'         => (int)$idville ,
						                'codeFournisseur' => $CODE_FOURNISSEUR ,
						                'nomVille'        => $nomVille ,
						                'codeVille'       => $codeVille ,
						                'codeXml_ville'   => $codeXml_ville ,
						                'nomXml_ville'    => $nomXml_ville ];
						//
						$APP_XML_VILLE->update([ 'idxml_ville' => (int)$ARR_XML_VILLE['idxml_ville'] ] , $arr_insert);

					else:

						$msg = $codeXml_ville . ' => ' . " ville non attribuée / inexistante<br>";
						$non_attr ++;
					endif;

				//  pas dans xml_ville et ville trouvée
				elseif ( empty($ARR_XML_VILLE['idxml_ville']) && !(empty($test_V['idville'])) ):
					$idville   = (int)$test_V['idville'];
					$nomVille  = $test_V['nomVille'];
					$codeVille = $test_V['codeVille'];
					$msg       = ' => ' . $codeVille . ' ' . $nomVille . " à insérer dans xml_ville<br>";
					//
					$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
					                'idville'         => $idville ,
					                'codeFournisseur' => $CODE_FOURNISSEUR ,
					                'codeVille'       => $codeVille ,
					                'nomVille'        => $nomVille ,
					                'codeXml_ville'   => $codeXml_ville ,
					                'nomXml_ville'    => $nomXml_ville ];

				// pas idville dans xml_ville et ville non trouvée
				elseif ( empty($ARR_XML_VILLE['idxml_ville']) && empty($test_V['idville']) ):
					$msg = "$codeXml_ville $nomXml_ville insérer dans xml_ville sans ville<br>";
					//
					$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
					                'codeFournisseur' => $CODE_FOURNISSEUR ,
					                'codeXml_ville'   => $codeXml_ville ,
					                'nomXml_ville'    => $nomXml_ville ];

				endif;
				//
				if ( sizeof($arr_insert) != 0 ) {
					$APP_XML_VILLE->insert($arr_insert);
				}
				//
				$arr_progr = [ 'progress_parent'  => 'ville_costa' ,
				               'progress_name'    => 'xml_job_ville_costa' ,
				               'progress_text'    => " XML . $total ports " ,
				               'progress_message' => $i . ' / ' . $total . "  - $non_attr non attr." ,
				               'progress_max'     => $total ,
				               'progress_value'   => $i ];
				//
				if ( !empty($msg) ) {
					$arr_progr['progress_log'] = $msg;
				}
				//
				skelMdl::send_cmd('act_progress' , $arr_progr);
			}
		}
		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		// retry in one second
		skelMdl::run( $PATH . 'read/readcosta_destination' , [
				'run'   => 1 ,
				'delay' => 1000 ]);

	}