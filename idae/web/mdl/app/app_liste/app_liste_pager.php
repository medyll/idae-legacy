<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 03:24
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];
	$nom   = 'nom' . ucfirst($table);

	$vars    = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? $nom : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? 1 : (int)$_POST['sortDir'];

	//
	$APP = new App($table);
	//

	$HTTP_VARS = $APP->translate_vars($vars);
	//
	$HTTP_BASE_VARS = http_build_query(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);
	//
	$settings_button_group = empty($groupBy) ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_group', $table) : $groupBy;
	$settings_button_sort  = empty($sortBy) ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_sort', $table) : $sortBy;
	//
	$arr_page = ['50', '100', '200', '500', '3000', '5000'];
?>
<div class="padding relative toggler applink applink_block">
	<a class="" data-menu="data-menu"><i class="fa fa-sort-numeric-asc"></i></a>
	<div class="contextmenu" style="display:none;">
		<? if (!empty($table)): ?>
			<? foreach ($arr_page as $key => $value): ?>
				<a class="autoToggle" app_button="app_button" onclick="save_settings('list_data_button_nbRows_<?= $table ?>','<?= $table ?>');" vars="<?= $HTTP_BASE_VARS ?>&nbRows=<?= $value ?>&<?= $HTTP_VARS ?>">
					<?= $value ?>
				</a>
			<? endforeach; ?>
		<? endif; ?>
	</div>
</div>