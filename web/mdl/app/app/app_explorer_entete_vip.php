<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);


	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$table = empty($_POST['table']) ? 'client' : $_POST['table'];

	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	$name_id = 'id' . $table;
	$Table   = ucfirst($table);
	//
	$APP     = new App($table);
	//
	$APPOBJ  = $APP->appobj();
	//
	$APP_TABLE  = $APP->app_table_one;
	$GRILLE_FK  = $APPOBJ->GRILLE_FK;
	$GRILLE_RFK = $APPOBJ->R_FK;

	$HTTP_VARS         = $APP->translate_vars($vars);
	$HTTP_VARS_NOAGENT = $APP->translate_vars($vars_noagent);
	$TEST_AGENT        = $APP->has_agent();
	$ARR_SCH           = $APP->get_schemes();
	$dateStart         = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$DADATE       = $dateStart->format('Y-m-d');
	$nomAppscheme = $APP->nomAppscheme;
	$COL          = [];

	$zone = uniqid($table);

	$arr_rfk  = $vars;
	$RSSCHEME = $APP->get_table_scheme($table);
	// SECURITE CONF
	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
	//	$vars['idagent'] = (int)$_SESSION['idagent'];
	endif;
	print_r($vars);
	// RS
	$RS = $APP->find($vars);
?>
<div id="<?= $zone ?>" style="width:100%; " class=" ">.. **
	<?
		while ($ARR = $RS->getNext()) {
			$main_id = (int)$ARR[$APPOBJ->NAME_ID];
			foreach ($GRILLE_RFK as $key => $arr_fk) {
				$table_k = $arr_fk['codeAppscheme'];
				$APP_FK  = new App($table_k);
				// SECURITE CONF
				if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
					//$vars['idagent'] = (int)$_SESSION['idagent'];
				endif;
				if (!$APP_FK->has_field('dateDebut')) continue;
				$RS_FK = $APP_FK->find($vars + [$APPOBJ->NAME_ID => $main_id]);
				if($RS_FK->count()==0) continue;
				// echo $table_k . ' ' . $RS_FK->count();

			}

		}
	?>
*-*-*-*-
</div>
