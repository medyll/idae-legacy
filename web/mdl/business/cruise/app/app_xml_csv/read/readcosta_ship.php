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
	$APP_VILLE           = new App('ville');
	$APP_PAYS            = new App('pays');
	$APP_GAMME           = new App('gamme');
	$APP_TRANSPORT       = new App('transport');
	$APP_TRANSPORT_GAMME = new App('transport_gamme');

	$ARR_FOURNISSEUR = $APP_FOURNISSEUR->findOne([ 'codeFournisseur' => $CODE_FOURNISSEUR ]);
	$idfournisseur   = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$i            = 0;
	$non_attr     = 0;
	$orphans      = 0;
	$collect_ship = [ ];

	$ARR_GAMME = [ ];
	$RS_GAMME  = $APP_GAMME->find();
	while ($arr_gamme = $RS_GAMME->getNext()) {
		$ARR_GAMME[$arr_gamme['codeGamme']] = $arr_gamme;
	}

	$local_rep = XMLDIR . 'costa/';
	$xml       = simplexml_load_file($local_rep . 'ships.xml');
	$xml_p     = $xml->Ships;


	if ( empty($_POST['run']) ) {
		?>
		<div class = "padding"><?= sizeof($xml->Ships->Ship) ?> Navires</div><?

	} else {
		foreach ($xml_p as $key => $Ships) {
			$total = sizeof($Ships);
			foreach ($Ships as $key_iti => $Ship) {
				$attr = $Ship->attributes();

				$SHIP_CD   = (string)$attr['CCN_COD_NAVE'];
				$SHIP_NAME = (string)$attr['DisplayName']; // Description ImgUrl   Url

				$idtransport = null;
				$msg         = '';
				$arr_insert  = [ ];
				//
				if ( !empty($collect_ship[$SHIP_CD]) ) {
					$TEST_TRANSP['idtransport']   = $collect_ship[$SHIP_CD]['idtransport'];
					$TEST_TRANSP['nomTransport']  = $collect_ship[$SHIP_CD]['nomTransport'];
					$TEST_TRANSP['codeTransport'] = $collect_ship[$SHIP_CD]['codeTransport'];
				} else {
					$TEST_TRANSP = $APP_TRANSPORT->findOne([ 'codeTransport' => $SHIP_CD , 'idfournisseur' => (int)$idfournisseur ]);
				}
				//
				$nom_transport_msc = $SHIP_NAME;

				if ( !empty($TEST_TRANSP['idtransport']) ):
					$idtransport   = (int)$TEST_TRANSP['idtransport'];
					$nomTransport  = $TEST_TRANSP['nomTransport'];
					$codeTransport = $TEST_TRANSP['codeTransport'];
					$msg .= $idtransport . ' => ' . $codeTransport . ' ' . $nomTransport . "  ok<br>";

				else  :
					// création ship en base
					$non_attr ++;
					$msg = $nom_transport_msc . ' => ' . " à insérer dans transport<br>";
					//
					$arr_insert = [ 'idfournisseur'   => (int)$idfournisseur ,
					                'codeFournisseur' => $CODE_FOURNISSEUR ,
					                'codeTransport'   => $SHIP_CD ,
					                'nomTransport'    => $SHIP_NAME ];

					$idtransport   = $APP_TRANSPORT->insert($arr_insert);
					$nomTransport  = $SHIP_NAME;
					$codeTransport = $SHIP_CD;

				endif;
				if ( !empty($idtransport) ):
					$collect_ship[$SHIP_CD]['idtransport']   = $idtransport;
					$collect_ship[$SHIP_CD]['nomTransport']  = $nomTransport;
					$collect_ship[$SHIP_CD]['codeTransport'] = $codeTransport;
				endif;
				if ( !empty($idtransport) ) {


					foreach ($Ship->Categories->Category as $key_cat => $Category) {
						$attr                 = $Category->attributes();
						$codeCategorie        = (string)$attr['CategoryCode'];
						$nomCategorie         = (string)$attr['CategoryName'];
						$descriptionCategorie = (string)$attr['CategoryDescription'];
						$videoCategorie       = (string)$attr['QuickTimeUrl'];

						$codeTransport_gamme = $codeCategorie;
						$nomTransport_gamme  = $nomCategorie;

						$ARR_TG = $APP_TRANSPORT_GAMME->findOne([ 'idtransport' => $idtransport , 'codeTransport_gamme' => $codeTransport_gamme ]);
						if ( empty($ARR_TG['idtransport_gamme']) ) {
							$msg               = $nomTransport . ' ' . $codeTransport_gamme . " => cabine à inserer  <br>";
							$idtransport_gamme = $APP_TRANSPORT_GAMME->insert([ 'codeTransport_gamme' => $codeTransport_gamme , 'idtransport' => $idtransport ]);
							$ARR_TG            = $APP_TRANSPORT_GAMME->findOne([ 'idtransport' => $idtransport , 'codeTransport_gamme' => $codeTransport_gamme ]);
						} else {
							$idtransport_gamme = (int)$ARR_TG['idtransport_gamme'];
						}

						if ( $ARR_TG['nomTransport_gamme'] != $nomTransport_gamme ) {
							$msg = $nomTransport . ' ' . $idtransport_gamme . ' : ' . $codeTransport_gamme . " => MAJ nom  <br>";
							$APP_TRANSPORT_GAMME->update([ 'idtransport_gamme' => (int)$idtransport_gamme ] , [ 'nomTransport_gamme' => $nomTransport_gamme ]);
						}
						// la gamme
						if ( empty($ARR_TG['idgamme']) ) {
							if ( stripos($nomTransport_gamme , 'interieure') !== false ) {
								$idgamme = $ARR_GAMME['I']['idgamme'];
							} // I
							if ( stripos($nomTransport_gamme , 'vue mer') !== false ) {
								$idgamme = $ARR_GAMME['O']['idgamme'];
							} // O
							if ( stripos($nomTransport_gamme , 'balcon') !== false ) {
								$idgamme = $ARR_GAMME['B']['idgamme'];
							} // B
							if ( stripos($nomTransport_gamme , 'suite') !== false ) {
								$idgamme = $ARR_GAMME['S']['idgamme'];
							} // S
							if ( !empty($idgamme) ) {
								$APP_TRANSPORT_GAMME->update([ 'idtransport_gamme' => (int)$idtransport_gamme ] , [ 'idgamme' => (int)$idgamme ]);
							}
						} else {
							$idgamme = $ARR_TG['idgamme'];
						}
					}
				}
				$arr_progr = [ 'progress_parent'  => 'ship_costa' ,
				               'progress_name'    => 'xml_job_transport_costa' ,
				               'progress_text'    => " XML . $total transports " ,
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
		//
		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
		skelMdl::run($PATH . 'read/readcosta_datedepart' , [ 'file'  => 'red' ,
		                                                     'url'   => 'la suite et fin ' ,
		                                                     'delay' => 10 ,
		                                                     'run'   => 1 ]);

	}