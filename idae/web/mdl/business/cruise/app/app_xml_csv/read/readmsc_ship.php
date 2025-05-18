<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	set_time_limit(0);

	$CODE_FOURNISSEUR = 'MSC';
	$idfournisseur    = 21;

	$APP                 = new App();
	$APP_GAMME           = new App('gamme');
	$APP_TRANSPORT       = new App('transport');
	$APP_TRANSPORT_GAMME = new App('transport_gamme');

	// $APP_TRANSPORT->consolidate_scheme();
	//  $APP_TRANSPORT_GAMME->consolidate_scheme();

	$APP_CSV = $APP->plug('sitebase_csv', 'flatshpdetl_fra_fra');
	$rs_csv  = $APP_CSV->find()->sort(['SHIP-CD' => 1]);

	if (empty($_POST['run'])) {
		?>
		<div class="padding"><?= $rs_csv->count() ?> Navires et cabines</div>
		<?

	} else {

		$total        = $rs_csv->count();
		$i            = 0;
		$non_attr     = 0;
		$orphans      = 0;
		$collect_ship = [];

		$ARR_GAMME = [];
		$RS_GAMME  = $APP_GAMME->find();
		while ($arr_gamme = $RS_GAMME->getNext()) {
			$ARR_GAMME[$arr_gamme['codeGamme']] = $arr_gamme;
		}

		while ($arr_csv_ship = $rs_csv->getNext()) {
			$i++;
			$idtransport = null;
			$msg         = '';
			$arr_insert  = [];
			$SHIP_CD     = $arr_csv_ship['SHIP-CD'];
			//
			if (!empty($collect_ship[$SHIP_CD])) {
				$TEST_TRANSP['idtransport']   = $collect_ship[$SHIP_CD]['idtransport'];
				$TEST_TRANSP['nomTransport']  = $collect_ship[$SHIP_CD]['nomTransport'];
				$TEST_TRANSP['codeTransport'] = $collect_ship[$SHIP_CD]['codeTransport'];
			} else {
				$TEST_TRANSP = $APP_TRANSPORT->findOne(['codeTransport' => $arr_csv_ship['SHIP-CD'],'idfournisseur'=>(int)$idfournisseur]);
			}
			//
			$nom_transport_msc = $arr_csv_ship['SHIP-NAME'];

			if (!empty($TEST_TRANSP['idtransport'])):
				$idtransport   = (int)$TEST_TRANSP['idtransport'];
				$nomTransport  = $TEST_TRANSP['nomTransport'];
				$codeTransport = $TEST_TRANSP['codeTransport'];
			// $msg           = $idtransport . ' => ' . $codeTransport . ' ' . $nomTransport . "  ok<br>";

			else  :
				// création ship en base
				$non_attr++;
				$msg = $nom_transport_msc . ' => ' . " à insérer dans transport<br>";
				//
				$arr_insert = ['idfournisseur'   => (int)$idfournisseur,
				               'codeFournisseur' => $CODE_FOURNISSEUR,
				               'codeTransport'   => $arr_csv_ship['SHIP-CD'],
				               'nomTransport'    => $arr_csv_ship['SHIP-NAME']];

				$idtransport   = $APP_TRANSPORT->insert($arr_insert);
				$nomTransport  = $arr_csv_ship['SHIP-NAME'];
				$codeTransport = $arr_csv_ship['SHIP-CD'];

			endif;
			if (!empty($idtransport)):
				$collect_ship[$SHIP_CD]['idtransport']  = $idtransport;
				$collect_ship[$SHIP_CD]['nomTransport'] = $nomTransport;
				$collect_ship[$SHIP_CD]['codeTrasport'] = $codeTransport;
			endif;
			if (!empty($idtransport)):

				$codeTransport_gamme = $arr_csv_ship['CATEGORY'];
				$nomTransport_gamme  = $arr_csv_ship['CATG DESC'];

				$ARR_TG = $APP_TRANSPORT_GAMME->findOne(['idtransport' => $idtransport, 'codeTransport_gamme' => $codeTransport_gamme]);
				if (empty($ARR_TG['idtransport_gamme'])) {
					$msg               = $nomTransport . ' ' . $codeTransport_gamme . " => cabine à insérer  <br>";
					$idtransport_gamme = $APP_TRANSPORT_GAMME->insert(['codeTransport_gamme' => $codeTransport_gamme, 'idtransport' => $idtransport]);
					$ARR_TG            = $APP_TRANSPORT_GAMME->findOne(['idtransport' => $idtransport, 'codeTransport_gamme' => $codeTransport_gamme]);
				} else {
					$idtransport_gamme = (int)$ARR_TG['idtransport_gamme'];
				}

				if ($ARR_TG['nomTransport_gamme'] != $nomTransport_gamme) {
					$msg = $idtransport_gamme . ' : ' . $codeTransport_gamme . " => MAJ nom  <br>";
					$APP_TRANSPORT_GAMME->update(['idtransport_gamme' => (int)$idtransport_gamme], ['nomTransport_gamme' => $nomTransport_gamme]);
				}
				// la gamme
				if (empty($ARR_TG['idgamme'])) {
					if (stripos($nomTransport_gamme, 'interieure') !== false) {
						$idgamme = $ARR_GAMME['I']['idgamme'];
					} // I
					if (stripos($nomTransport_gamme, 'vue mer') !== false) {
						$idgamme = $ARR_GAMME['O']['idgamme'];
					} // O
					if (stripos($nomTransport_gamme, 'balcon') !== false) {
						$idgamme = $ARR_GAMME['B']['idgamme'];
					} // B
					if (stripos($nomTransport_gamme, 'suite') !== false) {
						$idgamme = $ARR_GAMME['S']['idgamme'];
					} // S
					if(!empty($idgamme)){
						$APP_TRANSPORT_GAMME->update(['idtransport_gamme' => (int)$idtransport_gamme], ['idgamme' => (int)$idgamme]);
					}
				}else{
					$idgamme = $ARR_TG['idgamme'];
				}
			endif;
			$arr_progr = ['progress_parent'  => 'ship_msc',
			              'progress_name'    => 'xml_job_transport',
			              'progress_text'    => " XML . $total transports ",
			              'progress_message' => $i . ' / ' . $total . "  - $non_attr non attr.",
			              'progress_max'     => $total,
			              'progress_value'   => $i];
			//
			if (!empty($msg)) $arr_progr['progress_log'] = $msg;
			//
			skelMdl::send_cmd('act_progress', $arr_progr);
		}
		$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

		skelMdl::run($PATH . 'read/readmsc_cruise' , [ 'file'  => 'red' ,
		                                             'url'   => 'la suite et fin ' ,
		                                             'delay' => 10 ,
		                                             'run'   => 1 ]);
	}

