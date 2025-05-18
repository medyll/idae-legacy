<?

	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);
	ini_set('max_execution_time', 0);
	set_time_limit(0);

	$time = time();

?>
<?
	if (empty($_POST['run'])):
		?>
		<div style="width:950px;">
			<div class="titre_entete flex_h flex_align_middle">
				<div style="width:90px;text-align:center">
					<i class="fa fa-database fa-2x"></i></div>
				<div class="texterouge">
					Import données Artis
				</div>
			</div>
			<div class="padding">
				<progress value="0" id="auto_first_job"></progress>
				<progress value="0" id="auto_first_injob"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('customer/<?= CUSTOMERNAME ?>/app_admin/app_migration_first','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
		</div>
		<?
		return;
	endif;
	MongoCursor::$timeout = -1;
	$time                 = time();
// 
	$arr_tables = get_artis_table();
//	$arr_tables = ['t_lgncmde'];
 // 	$arr_tables = ['t_lieubien'];

// 	$arr_tables = ['t_categprofilclt'];
// $arr_tables = ['t_cout'];
// $arr_tables = ['t_reglefactuclt'];
// $arr_tables = ['t_volumemoyenmensuel'];
// $arr_tables = ['t_valori'];
//$arr_tables = ['t_solservclt'];
//$arr_tables = ['t_solservclt'];
// $arr_tables = ['t_lieufonction'];
// $arr_tables = ['t_couvbienparserv'];
// $arr_tables = ['t_fact','t_lgnfact'];
// $arr_tables = ['t_bien','t_servclt','t_solservclt','t_servclt','t_couvbienparserv','t_lieubien'];

//	$arr_tables = ['t_dmdeinter']; // t_partie.C_CODE_ORG

//  $arr_tables = ['t_bien','t_solservclt','t_servclt','t_couvbienparserv'];
//	$arr_tables = [ 't_typprod','t_bien','t_solservclt','t_servclt','t_couvbienparserv'];
//  $arr_tables = [ 't_statutsolservclt'];
//	$arr_tables = ['t_volumemoyenmensuel'];
//  $arr_tables = ['t_dmdeinter' , 't_inter','t_interplanifiee'];  // ,'t_interplanifiee','t_financement','t_lgnfinancee','t_echeancierfinancement' ,'t_dmdeinter' , 't_inter','t_interinfoprincipales','t_interplanifiee'
//  t_dmdeinter t_inter t_interinfoprincipales t_interplanifiee
//  unset($arr_tables['t_valori'],$arr_tables['t_volumemoyenmensuel'],$arr_tables['t_grandeur']);

	foreach ($arr_tables as $key => $name_table) {
		//
		do_artis_index($name_table, 'N_ID');
		do_artis_index($name_table, 'T_UPDATE_DATE');
		do_artis_index($name_table, 'T_UPDATE_TIME');
		do_artis_index($name_table, 'T_COMPUTED_UPDATE_DATE');
		do_artis_index($name_table, 'T_COMPUTED_UPDATE_TIME');
		do_artis_index($name_table, 'T_CREATE_DATE');

		$WHERE      = [];
		$hard_limit = 15000;
		$limit      = 18000;
		$sort       = 'T_COMPUTED_UPDATE_TIME';
		switch ($name_table):
			case "t_couvbienparserv":
				do_artis_index($name_table, 'B_ACTIF_COUVBIENPARSERV_IDX');
				do_artis_index($name_table, 'N_SOURCE_BIEN_ID');
				do_artis_index('t_solservclt', 'D_DATEDEFININITIALE_SOLSERVCLT');
				//	$WHERE = ['N_SOURCE_BIEN_ID' => '64886'];// ['$ne' => 'Non']
				$limit = 30000;
				break;
			case "t_solservclt":
				// $WHERE = ['D_DATEDEFININITIALE_SOLSERVCLT'=>['$gte'=>date('Y-m-d')]];
				do_artis_index('t_moduleservclt', 'D_DATEDEFIN_MODULESERVCLT');

				break;
			case "t_classif":
				$WHERE = ['N_ID' => '109'];
				break;
			case "t_categprofilclt":
				$limit = 1000;
				//  $WHERE = ['N_TARGET_PROFILCLT_ID'=>'8927'];
				break;
			case "t_servclt":
				$limit = 7000;

				break;
			case "t_cptcpta":
				// $WHERE['C_NUMERO_CPTCPTA'] = 'C0000205';
				$limit = 5000;
				break;
			case "t_cmde":
				// $WHERE['C_NUMERO_CPTCPTA'] = '93030227';
				$limit = 30000;
				break;
			case "t_lgncmde":
				// $WHERE['C_NUMERO_CPTCPTA'] = '93030227';
				$limit = 80000;
				break;
			case "t_inter":
				$limit = 5000;
				break;
			case "t_dmdeinter":
				$limit = 3000;
				break;
			case "t_interinfoprincipales":
				$limit = 5000;
				break;
			case "t_interplanifiee":
				$limit = 5000;
				break;
			case "t_lieubien":
				// Chez le client
				do_artis_index($name_table, 'D_DATEFIN_LIEUBIEN');
				do_artis_index($name_table, 'D_DATEDEBUT_LIEUBIEN');
				do_artis_index($name_table, 'N_ESTLIEUDU_BIEN_ID');
				do_artis_index($name_table, 'N_APPARTIENTAUSTOCKDUDEPOT_DEPOTSTOCKAGE_ID');
				//	'N_ESTLIEUDU_BIEN_ID' => '48419',
				// $WHERE = ['D_DATEFIN_LIEUBIEN' => ['$in' => [null, '']], 'N_APPARTIENTAUSTOCKDUDEPOT_DEPOTSTOCKAGE_ID' => ['$in' => [null, '','322']]];
				$sort  = 'D_DATEDEBUT_LIEUBIEN';
				//$limit = 17000;
				//$hard_limit = 17000;
				break;
			case "t_lieufonction":
				$limit = 20000;
				break;
			case "t_bien":
				// $WHERE = ['N_ID'=>'57024'];
				$limit = 14000;
				break;
			case "t_infocontact":
				$limit = 12000;
				break;
			case "t_volumemoyenmensuel":

				$sort  = 'T_CREATE_DATE';
				$WHERE = ['N_PERMETESTIMER_GRANDEUR_ID' => '23553'];
				$limit = 16000;
				break;
			case "t_valori":
				$BASE_MV = $APP->plug_base('sitebase_base');
				$BASE_MV->materiel_compteur->createIndex(['N_ID' => 1]);

				do_artis_index($name_table, 'T_RELEVEELE_VALORI');
				$WHERE = ['T_RELEVEELE_VALORI' => ['$gt' => '2016-07-30']];
				// $WHERE = ['N_ID'=>'717688'];

				$sort  = 'T_RELEVEELE_VALORI';
				$limit = 15000;
				break;
			case "t_partie":
				$limit = 5000;
				//$WHERE = ['C_CODE_ORG'=>'CWTT0970'];

				break;
			case "t_echeancierfinancement":
				//$WHERE = ['N_ESTECHEANCIER_FINANCEMENT_ID'=>'1844'];

				break;
			case "t_siteorg":
				// $WHERE = ['N_TARGET_ORG_ID'=>'20712'];
				$limit = 4000;
				break;
			case "t_fact":
				do_artis_index('t_fact', 'D_DATEDEFACTURE_FACT');
				$sort = 'D_DATEDEFACTURE_FACT';
				// $WHERE = ['N_TARGET_ORG_ID'=>'20712'];
				$limit = 14000;
				break;
			case "t_lgnfact":
				$sort = 'N_ID';
				// $WHERE = ['N_TARGET_ORG_ID'=>'20712'];
				$limit = 25000;
				break;
			case "t_site":
				// $WHERE = ['N_TARGET_ORG_ID'=>'20712'];
				$limit = 9000;
				break;
			case "t_cout":
				$WHERE = ['N_ESTFACTUREA_LIEUFONCTION_ID' => '40236'];
				$limit = 1000;
				break;
			case "t_financement":
				//  $WHERE = ['N_ID'=>'3807'];
				////$limit = 1000;
				break;
			case "t_lgnfinancee":
				//  $WHERE = ['N_ESTINCLUSEDS_FINANCEMENTCLT_ID'=>'3'];
				$limit = 5000;
				break;
			case "t_adrmedia":
				// $WHERE = ['N_ID'=>'61935'];
				$limit = 10000;
				break;
			case "t_reglefactuclt":
				//$WHERE = ['N_ESTFACTUREEPARDEFA_LIEUFONCTION_ID'=>['$in'=>['42879']]];
				$WHERE = ['N_ESTFACTUREEPARDEFA_LIEUFONCTION_ID' => '59980'];
				//  $sort = 'N_ID';
				do_artis_index('t_statutmoduleservclt', 'N_SEREFEREA_MODULESERVCLT_ID');
				do_artis_index('t_cout', 'N_DEPEND_RFCUC_ID');
				do_artis_index('t_cout', 'N_REGUL_RFCFRNONGLOBALISEE_ID');
				$limit = 20000;
				break;
		endswitch;
		//
		$i++;
		$res = $BASE_SYNC->$name_table->find($WHERE)->sort([$sort => -1])->limit($hard_limit);
		//
		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
		                                   'progress_value'   => $i,
		                                   'progress_max'     => sizeof($arr_tables),
		                                   'progress_message' => 'Traitement ' . $name_table . ' en cours'], session_id());

		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_injob',
		                                   'progress_value'   => 50,
		                                   'progress_max'     => $res->count(),
		                                   'progress_message' => 'Démarrage ---- '], session_id());//
		$z = 0;
		while ($ROW = $res->getNext()) {
			foreach ($ROW as $key_r => $value_r) {
				if (is_numeric($key_r)) unset($ROW[$key_r]);
			}
			//
			$z++;
			// exceptions
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
					$ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE'] = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE']));
					$ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']   = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']));
					break;
				case "t_grandeur":
					$ROW['D_DERNFACTURATIONLE_GRANDEUR'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNFACTURATIONLE_GRANDEUR']));
					break;
				case "t_reglefactuclt":
					$ROW['D_DERNREELLELE_REGLEFACTUCLT']    = date('Y-m-d H:i:s', strtotime($ROW['D_DERNREELLELE_REGLEFACTUCLT']));
					$ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT']));
					break;
				case "t_lieubien":
					$ROW['D_DATEDEBUT_LIEUBIEN'] = date('Y-m-d', strtotime($ROW['D_DATEDEBUT_LIEUBIEN']));
					if(!empty($ROW['D_DATEFIN_LIEUBIEN'])){
						$ROW['D_DATEFIN_LIEUBIEN'] = date('Y-m-d', strtotime($ROW['D_DATEFIN_LIEUBIEN']));
					}else{
						$ROW['D_DATEFIN_LIEUBIEN'] = "";
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
			//
			array_walk_recursive($ROW, 'encode_items');
			// si N_ID
			if ($ROW['N_ID']) {
				//skelMdl::runModule('customer/'.CUSTOMERNAME.'/app_admin/app_migration_first_wrk',['name_table'=>$name_table,'ROW'=>$ROW]);

				$msg = do_artis_rows($name_table, $ROW);
			}
			//
			if ($z % 25 == 0 || $z == $res->count()) {
				skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_injob',
				                                   'progress_value'   => $z,
				                                   'progress_max'     => $res->count(true),
				                                   'progress_log'     => $msg,
				                                   'progress_message' => 'en cours ' . $z], session_id());//
				echo " ";
				flush();
				ob_flush();
			}

		}
	}

	skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_injob',
	                                   'progress_value'   => 100,
	                                   'progress_max'     => 100,
	                                   'progress_message' => 'termine 100% ' . $z], session_id());
//
	skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
	                                   'progress_value'   => 100,
	                                   'progress_max'     => 100,
	                                   'progress_message' => 'Traitemen termine'], session_id());

	function encode_items(&$item, $key) {
		//	echo mb_detect_encoding($item, mb_detect_order(), true);
		$item = iconv('Windows-1251', 'UTF-8//IGNORE', $item);
		$item = iconv('', 'UTF-8//IGNORE', $item);
	}

?>