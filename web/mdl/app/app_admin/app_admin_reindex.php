<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/06/14
	 * Time: 00:22
	 */

	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
	$APP_SCH           = new APP('appscheme');
	$APP_SCH_TY        = new APP('appscheme_type');
	$APP_SCH_FIELD     = new APP('appscheme_field');
	$APP_SCH_HAS_FIELD = new APP('appscheme_has_field');

	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);

	$time = time();

	if (empty($_POST['run'])):
		?>
		<div style="width:350px;overflow:hidden;">
			<div class="titre_entete flex_h flex_align_middle">
				<div style="width:90px;text-align:center">
					<i class="fa fa-key fa-2x"></i></div>
				<div class="texterouge">
					Reindexation de la Base
				</div>
			</div>
			<div class="padding margin">
				<progress value="0" id="auto_first_job" style="display:none;"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('app/app_admin/app_admin_reindex','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
		</div>
		<?
		return;
	endif;

	$con  = $APP->plug_base('sitebase_image');
	$grid = $con->getGridFs();
	$grid->ensureIndex(['filename'=>1]);

	MongoCursor::$timeout = -1;
	$time                 = time();
	$APP                  = new App();
	$ARR_SCH              = $APP->get_schemes();
	$ARR_Bool             = $APP->get_array_field_bool();
	$ARR_SORT             = $APP->get_sort_fields();

	foreach ($ARR_SCH as $key => $arr_sh):
		if(empty($arr_sh)) continue;
		if(empty($arr_sh['codeAppscheme'])) continue;
		if(empty($arr_sh['codeAppscheme_base'])) continue;

		$i++;
		$base            = $arr_sh['codeAppscheme_base'];
		$table           = $arr_sh['codeAppscheme'];
		$Table           = ucfirst($table);
		$APP_TMP         = new App($table);
		$arr_sch         = $APP->get_table_scheme($table);
		$arr_fk          = $APP->get_grille_fk();
		$arr_rfk         = $APP->get_reverse_grille_fk($table);

		$ARR_FIELD_DATE = $APP_SCH_FIELD->distinct_all('idappscheme_field', ['codeAppscheme_field_group' => ['$in'=>['descriptif','image']]]);
		$ARR_HAS        = $APP_SCH_HAS_FIELD->distinct_all('codeAppscheme_has_field', ['codeAppscheme' => $table, 'idappscheme_field' => ['$nin' => $ARR_FIELD_DATE]]);
		//$RS_FIELD       = $APP_SCH_FIELD->find(['idappscheme_field' => ['$in' => $ARR_HAS]])->sort(['ordreAppscheme_field' => 1]);

		$response = $APP->plug($base, $table)->deleteIndexes();
		// print_r($response);

		/*vardump($table);
		vardump($ARR_HAS);
		vardump(array_keys($arr_fk));*/
		// print_r($response);
		$arr_ty = ['type','statut','categorie','group'];
		$ARR_TY = [];
		foreach($arr_ty as $key_type){
			$KEY_TYPE = 'has'.ucfirst($key_type).'Scheme';
			if($APP_TMP->app_table_one[$KEY_TYPE]) $ARR_TY['id'.$table.'_'.$key_type]=1;
		}

		$arr_indexes = array_merge($ARR_HAS,array_column(array_values($arr_fk),'idtable_fk'),array_keys($ARR_TY));

		skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
		                                   'progress_value'   => $i,
		                                   'progress_max'     => sizeof($ARR_SCH),
		                                   'progress_message' => 'Traitement ' . $table . ' début'], session_id());
		//
		$APP->plug($base, $table)->ensureIndex(['id' . $table => 1]);


		foreach ($arr_indexes as $KEY):
			try {
				 $APP->plug($base, $table)->ensureIndex([$KEY  => 1]);
			} catch (Exception $e) {
				//	echo 'Caught exception: ', $e->getMessage(), "\n";
				skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
				                                   'progress_value'   => $i,
				                                   'progress_max'     => sizeof($ARR_SCH),
				                                   'progress_log'     => $table . ' erreur index '. $e->getMessage(),
				                                   'progress_message' => 'Traitement ' . $table . ' ... '], session_id());
			}

		endforeach;


		$arr_index = [];
		foreach ($arr_rfk as $keyfk => $valuefk) {
			$APP_FK      = new App($valuefk['table']);
			$ct          = $APP_FK->find()->sort(['id' . $table => -1])->getNext();
			$arr_index[] = (int)$ct['id' . $table];
		}
		if (sizeof($arr_rfk) != 0) {
			rsort($arr_index);
		}

		$ct     = $APP_TMP->find()->sort(['id' . $table => -1]);
		$arr_ct = $ct->getNext();
		if ($ct->count() != 0 && empty($arr_ct['id' . $table])) {
			while ($arr_ct = $ct->getNext()) {
				$_id = new MongoId($arr_ct['_id']);
				$APP_TMP->update(['_id' => $_id], ['id' . $table => $APP->getNext('id' . $table)]);
				$zou++;
				skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
				                                   'progress_value'   => $zou,
				                                   'progress_max'     => $ct->count(),
				                                   'progress_message' => 'Création index ' . $table . ' ... '], session_id());
			}
		}
		$arr_index[] = (int)$arr_ct['id' . $table];
		rsort($arr_index);
		$idnext     = $APP->readNext('id' . $table);
		$set_idnext = (int)$arr_index[0];
		if ($set_idnext > $idnext) {
			$new_index = ($set_idnext > $idnext) ? $set_idnext : $idnext;
			skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
			                                   'progress_value'   => $i,
			                                   'progress_max'     => sizeof($ARR_SCH),
			                                   'progress_log'     => $table . ' : ' . $arr_index[0] . ' - ' . $idnext . ' = ' . $new_index,
			                                   'progress_message' => 'Traitement ' . $table . ' ... '], session_id());
			$APP_TMP->setNext('id' . $table, $new_index);

		}
		/*skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
		                                        'progress_value'   => $i,
		                                        'progress_max'     => sizeof($ARR_SCH),
		                                        'progress_log'     => $table.' : '.$arr_index[0].' = '.$idnext,
		                                        'progress_message' => 'Traitement ' . $table . ' terminé'), session_id());*/

	endforeach;
	skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
	                                   'progress_value'   => sizeof($ARR_SCH),
	                                   'progress_max'     => sizeof($ARR_SCH),
	                                   'progress_message' => 'Traitement terminé '], session_id());
?>