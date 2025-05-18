<?

	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);

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
					APP migration 1
				</div>
			</div>
			<div class="padding">
				<progress value="0" id="auto_first_job"></progress>
				<progress value="0" id="auto_first_injob"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('app/app_admin/app_migration_first','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
		</div>
		<?
		return;
	endif;
	MongoCursor::$timeout = -1;
	$time = time();
// 
 $arr_tables = get_artis_table();

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

		$WHERE = [];
		$limit = 6000;
		$sort = 'T_COMPUTED_UPDATE_TIME';
		switch ($name_table):
			case "t_couvbienparserv":
				do_artis_index($name_table, 'B_ACTIF_COUVBIENPARSERV_IDX');do_artis_index($name_table, 'N_SOURCE_BIEN_ID');do_artis_index('t_solservclt', 'D_DATEDEFININITIALE_SOLSERVCLT');
			 	// $WHERE = ['N_SOURCE_BIEN_ID' => '54909'];// ['$ne' => 'Non']
				$limit = 40000;
				break;
			case "t_solservclt":
				  $WHERE = ['D_DATEDEFININITIALE_SOLSERVCLT'=>['$gte'=>date('Y-m-d')]];
				break;
			case "t_classif":
				  $WHERE = ['N_ID'=>'109'];
				break;
			case "t_categprofilclt":
				//	$limit = 1000;
				//  $WHERE = ['N_TARGET_PROFILCLT_ID'=>'8927'];
				break;
			case "t_servclt":
				$limit = 40000;

				break;
			case "t_cptcpta":
				// $WHERE['C_NUMERO_CPTCPTA'] = '93030227';
				$limit = 10000;
				break;
			case "t_cmde":
				// $WHERE['C_NUMERO_CPTCPTA'] = '93030227';
				$limit = 2011;
				break;
			case "t_inter":
				$limit = 10000;
				break;
			case "t_dmdeinter":
				$limit = 10000;
				break;
			case "t_interinfoprincipales":
				$limit = 10000;
				break;
			case "t_interplanifiee":
				$limit = 10000;
				break;
			case "t_lieubien":
				// Chez le client
				$WHERE = ['N_APPARTIENTAUSTOCKDUDEPOT_DEPOTSTOCKAGE_ID'=>''];
				$limit = 15000;
				break;
			case "t_bien":
                 $limit = 20000;
				break;
			case "t_infocontact":
                 $limit = 12000;
				break;
			case "t_volumemoyenmensuel":

				$sort = 'T_CREATE_DATE';
				$limit = 45000;
				break;
			case "t_partie":

				break;
		endswitch;
		$i++;
		$res = $BASE_SYNC->$name_table->find($WHERE)->sort([$sort => -1])->limit($limit);
		//
		skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
		                                        'progress_value'   => $i,
		                                        'progress_max'     => sizeof($arr_tables),
		                                        'progress_message' => 'Traitement ' . $name_table . ' en cours'), session_id());

		skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_injob',
		                                        'progress_value'   => 50,
		                                        'progress_max'     => $res->count(),
		                                        'progress_message' => 'Démarrage ---- '), session_id());//
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
				case "t_solservclt":
					if (strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']) < $time) continue;
					$ROW['D_DATEDEFININITIALE_SOLSERVCLT'] = date('Y-m-d',strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']));
					$ROW['D_DATEDEDEBUT_SOLSERVCLT'] = date('Y-m-d',strtotime($ROW['D_DATEDEDEBUT_SOLSERVCLT']));
					break;
				case "t_cptcpta":
					if (empty($ROW['C_CODEENCOMPTABILITE_CPTCPTA'])) continue;
					break;
			endswitch;
			//
			array_walk_recursive($ROW, 'encode_items');
			// si N_ID
			if ($ROW['N_ID']) {
				 $msg = do_artis_rows($name_table, $ROW);
			}
			//
			if ($z % 25 == 0 || $z == $res->count()) {
				skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_injob',
				                                        'progress_value'   => $z,
				                                        'progress_max'     => $res->count(),
				                                        'progress_log'     => $msg,
				                                        'progress_message' => 'en cours ' . $z), session_id());//
				echo " ";
				flush();
				ob_flush();
			}

		}
	}

	skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_injob',
	                                        'progress_value'   => 100,
	                                        'progress_max'     => 100,
	                                        'progress_message' => 'termine 100% '.$z), session_id());
//
	skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
	                                        'progress_value'   => 100,
	                                        'progress_max'     => 100,
	                                        'progress_message' => 'Traitemen termine'), session_id());

	function encode_items(&$item, $key) {
		$item = iconv('Windows-1251', 'UTF-8//IGNORE', $item);
		$item = iconv('', 'UTF-8', $item);
	}

?>