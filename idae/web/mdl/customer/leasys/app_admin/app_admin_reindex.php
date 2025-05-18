<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/06/14
	 * Time: 00:22
	 */


	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
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
					Reindexation de la Base Idae
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

	MongoCursor::$timeout = -1;
	$time = time();
	$APP = new App();
	$ARR_SCH = $APP->get_schemes();
	$ARR_Bool = $APP->get_array_field_bool();
	$ARR_SORT = $APP->get_sort_fields();


	foreach ($ARR_SCH as $key => $arr_sh):
		$i++;
		$base            = $arr_sh['codeAppscheme_base'];
		$table           = $arr_sh['codeAppscheme'];
		$arr_sch         = $APP->get_table_scheme($table);
		$arr_fk          = $APP->get_grille_fk();
		$APP_SORT_FIELDS = $APP->get_date_fields($table);

		skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
		                                        'progress_value'   => $i,
		                                        'progress_max'     => sizeof($ARR_SCH),
		                                        'progress_message' => 'Traitement ' . $table . ' en cours'), session_id());

		$APP->plug($base, $table)->ensureIndex(['N_ID' => 1]);
		$APP->plug($base, $table)->ensureIndex(['id' . $table => 1]);
		$APP->plug($base, $table)->ensureIndex(['nom' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['code' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['ordre' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['range' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['numero' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['reference' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['dateDebut' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['date' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['heureDebut' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['timeDebut' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['dateCreation' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['dateFin' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['timeFin' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['valeur' . ucfirst($table) => 1]);
		$APP->plug($base, $table)->ensureIndex(['prix' . ucfirst($table) => 1]);

		if (!empty($arr_sh['hasTypeScheme'])):
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex(['id' . $table . '_type' => 1]);
		endif;
		foreach ($arr_fk as $key => $fields):
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex([$fields['idtable_fk'] => 1]);
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex([$fields['nomtable_fk'] => 1]);
		endforeach;
		foreach ($ARR_SORT as $key => $fields):
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex([$key . ucfirst($table) => 1]);
		endforeach;
		foreach ($ARR_Bool as $key => $fields):
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex([$key . ucfirst($table) => 1]);
		endforeach;
		foreach ($APP_SORT_FIELDS as $key => $fields):
			$APP->plug($arr_sch['codeAppscheme_base'], $arr_sch['codeAppscheme'])->ensureIndex([$key . ucfirst($table) => 1]);
		endforeach;


	endforeach;
	skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
	                                        'progress_value'   => sizeof($ARR_SCH),
	                                        'progress_max'     => sizeof($ARR_SCH),
	                                        'progress_message' => 'Traitement terminé '), session_id());
?>