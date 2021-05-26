<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 30/11/2017
	 * Time: 19:13
	 */

	include_once($_SERVER['CONF_INC']);

	$CODE_FOURNISSEUR = 'COSTA';

	$APP                 = new App();
	$APP_FOURNISSEUR     = new App('fournisseur');
	$APP_XML_VILLE       = new App('xml_ville');
	$APP_XML_DESTINATION = new App('xml_destination');
	$APP_DESTINATION     = new App('destination');
	$APP_PAYS            = new App('pays');
	$APP_VILLE           = new App('ville');
	$ARR_FOURNISSEUR     = $APP_FOURNISSEUR->findOne(array( 'codeFournisseur' => $CODE_FOURNISSEUR ));
	$idfournisseur       = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$i        = 0;
	$non_attr = 0;
	$orphans  = 0;

	$local_rep = XMLDIR . 'costa/';

	$xml = simplexml_load_file($local_rep . 'catalog.xml');

	if ( empty($_POST['run']) ) {
		?>
		<div class = "padding"><?= sizeof($xml->CostaCruiseCatalog->Destination) ?> destinations</div><?

	} else {
		foreach ($xml->CostaCruiseCatalog->Destination as $key => $Destination) {

			$attr             = $Destination->attributes();
			$CODE_DESTINATION = strtoupper((string)$attr['Code']);
			$NOM_DESTINATION  = (string)$attr['DisplayName'];

			$ARR_XML_DESTINATION = $APP_XML_DESTINATION->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR , 'codeXml_destination' => trim($CODE_DESTINATION) ]);
			$test_V              = $APP_DESTINATION->findOne([ 'codeDestination' => $CODE_DESTINATION ]);
			//
			$msg        = '';
			$arr_insert = [ ];
			//
			$arr_tmp             = explode(',' , $NOM_DESTINATION);
			$nom_destination_msc = $NOM_DESTINATION;
			$nomDestination_tmp  = $arr_tmp[0];
			$nomPays_tmp         = $arr_tmp[1];

			$test_V2 = $APP_DESTINATION->findOne([ 'nomDestination' => new MongoRegex("/" . $nomDestination_tmp . "/i") ]);

			//  idxml_destination dans xml_destination avec iddestination null
			if ( !empty($ARR_XML_DESTINATION['idxml_destination']) && !empty($ARR_XML_DESTINATION['iddestination']) ):
				$ARR_DESTINATION_TEST = $APP_DESTINATION->findOne([ 'iddestination' => (int)$ARR_XML_DESTINATION['iddestination'] ]);
				if ( empty($ARR_DESTINATION_TEST['iddestination']) ) :
					//
					//$APP_XML_DESTINATION->update(['idxml_destination' => (int)$ARR_XML_DESTINATION['idxml_destination']], ['iddestination' => null]);
					$msg = "$nom_destination_msc non trouv�";
				endif;

			//  idxml_destination dans xml_destination avec iddestination null
			elseif ( !empty($ARR_XML_DESTINATION['idxml_destination']) && empty($ARR_XML_DESTINATION['iddestination']) ):
				if ( !empty($test_V2['iddestination']) ):
					$iddestination   = (int)$test_V2['iddestination'];
					$nomDestination  = $test_V2['nomDestination'];
					$codeDestination = $test_V2['codeDestination'];

					$msg = $CODE_DESTINATION . ' ' . $nom_destination_msc . " / $nomDestination  =>  $iddestination  destination correspondante<br>";
					//
					$arr_insert = [ 'idfournisseur'       => (int)$idfournisseur ,
					                'iddestination'       => (int)$iddestination ,
					                'codeFournisseur'     => $CODE_FOURNISSEUR ,
					                'nomDestination'      => $nomDestination ,
					                'codeDestination'     => $codeDestination ,
					                'codeXml_destination' => $CODE_DESTINATION ,
					                'nomXml_destination'  => $NOM_DESTINATION ];
				//
				//	$APP_XML_DESTINATION->update(['idxml_destination' => (int)$ARR_XML_DESTINATION['idxml_destination']], $arr_insert);

				else:

					$msg = $CODE_DESTINATION . ' ' . $nomDestination_tmp . ' => ' . " destination non attribu�e / inexistante<br>";
					$non_attr ++;
				endif;

			//  pas dans xml_destination et destination trouvée
			elseif ( empty($ARR_XML_DESTINATION['idxml_destination']) && !(empty($test_V['iddestination'])) ):
				$iddestination   = (int)$test_V['iddestination'];
				$nomDestination  = $test_V['nomDestination'];
				$codeDestination = $test_V['codeDestination'];
				$msg             = $nomDestination_tmp . ' => ' . $codeDestination . ' ' . $nomDestination . " � ins�rer dans xml_destination<br>";
				//
				$arr_insert = [ 'idfournisseur'       => (int)$idfournisseur ,
				                'iddestination'       => $iddestination ,
				                'codeFournisseur'     => $CODE_FOURNISSEUR ,
				                'codeDestination'     => $codeDestination ,
				                'nomDestination'      => $nomDestination ,
				                'codeXml_destination' => $CODE_DESTINATION ,
				                'nomXml_destination'  => $NOM_DESTINATION ];

			// pas iddestination dans xml_destination et destination non trouv�e
			elseif ( empty($ARR_XML_DESTINATION['idxml_destination']) && empty($test_V['iddestination']) ):
				$msg = $nomDestination_tmp . ' => ' . " � ins�rer dans xml_destination sans destination<br>";
				//
				$arr_insert = [ 'idfournisseur'       => (int)$idfournisseur ,
				                'codeFournisseur'     => $CODE_FOURNISSEUR ,
				                'codeXml_destination' => $CODE_DESTINATION ,
				                'nomXml_destination'  => $NOM_DESTINATION ];
			endif;
			//
			if ( sizeof($arr_insert) != 0 ) {
				$APP_XML_DESTINATION->insert($arr_insert);
			}
			//
			$arr_progr = [ 'progress_parent'  => 'destination_costa' ,
			               'progress_name'    => 'xml_job_destination_costa' ,
			               'progress_text'    => " XML . $total destinations " ,
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
		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		skelMdl::run($PATH . 'read/readcosta_ship' , [ 'file'  => 'red' ,
		                                                    'url'   => 'la suite et fin ' ,
		                                                    'delay' => 10 ,
		                                                    'run'   => 1 ]);
	}