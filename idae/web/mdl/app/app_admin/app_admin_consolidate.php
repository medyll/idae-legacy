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
					Consolidation de la Base de la Base
				</div>
			</div>
			<div class="padding margin">
				<progress value="0" id="auto_first_job" style="display:none;"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('app/app_admin/app_admin_consolidate','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
		</div>
		<?
		return;
	endif;

	MongoCursor::$timeout = -1;
	$time                 = time();
	$APP                  = new App();
	$ARR_SCH              = $APP->get_schemes();

	foreach ($ARR_SCH as $key => $arr_sh):
		$i++;
		$base            = $arr_sh['codeAppscheme_base'];
		$table           = $arr_sh['codeAppscheme'];


		$APP->consolidate_app_scheme($table);

			skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
			                                   'progress_value'   => $i,
			                                   'progress_max'     => sizeof($ARR_SCH),
			                                   'progress_log'     => $table . ' : ' ,
			                                   'progress_message' => 'Traitement ' . $table . ' ... '], session_id());



	endforeach;
	skelMdl::send_cmd('act_progress', ['progress_name'    => 'first_job',
	                                   'progress_value'   => sizeof($ARR_SCH),
	                                   'progress_max'     => sizeof($ARR_SCH),
	                                   'progress_message' => 'Traitement terminé '], session_id());
?>