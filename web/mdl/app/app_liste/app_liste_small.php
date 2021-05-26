<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$table_value = $_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$nbRows      = empty($_POST['nbRows']) ? 50 : $_POST['nbRows'];
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$id  = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	//
	if (!empty($_POST['data-dsp'])) {
		$string_settings = " data-dsp='" . $_POST['data-dsp'] . "' data-dsp-mdl=''";
	}
	if (!empty($_POST['data-dsp-mdl'])) {
		$string_settings = " data-dsp='mdl' data-dsp-mdl='" . $_POST['data-dsp-mdl'] . "'";
	}
	if (!empty($_POST['data-dsp-className'])) {
		$string_settings .= " data-dsp-className='" . $_POST['data-dsp-className'] . "' ";
	}

	$string_settings .= empty($settings_data_button_className) ? '' : " data-dsp-className='$settings_data_button_className'";

	$zone = 'app_mini_small_liste_' . $table . implode('_', array_keys($vars)) . '_' . implode('_', array_values($vars));

	if (!empty($_POST['data_model'])) $data_model = $_POST['data_model'];
	if (!empty($_POST['groupBy'])) $data_classname = 'table_groupe';
	if($_POST['mode']!='integrated')  $integrated = 'overflow:hidden;height:100%;';
?>
<div style="height:100%;" class="flex_v" app_gui_explorer>
	<div class="padding"><?= $APP->get_full_titre_vars($vars) ?></div>
	<div class="relative" style="z-index:1;"><?= skelMdl::cf_module('app/app_liste/app_liste_menu_small', ['table' => $table, 'table_value' => $table_value,'vars'=>$vars]) ?></div>
	<div class="flex_main" style="<?=$integrated?>width:100%;" expl_file_zone>
		<div style="height:100%;overflow:hidden;" id="<?= $zone ?>" <?= $string_settings ?> data-classname="<?= $data_classname ?>" data-data_model="<?= $data_model ?>" expl_file_list></div>
	</div>
</div>
<script>
	load_table_in_zone('groupBy=<?=$groupBy?>&table=<?=$table?>&<?=$APP->translate_vars($vars)?>&nbRows=<?=$nbRows?>', '<?=$zone?>');
</script>