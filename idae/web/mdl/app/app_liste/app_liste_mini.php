<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$table_value = $_POST['table_value'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$nbRows = empty($_POST['nbRows']) ? 50 : $_POST['nbRows'];
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$id = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	//
	$zone = 'app_mini_liste_'.$table . implode('_', array_keys($vars)) . '_' . implode('_', array_values($vars));
	//
?>
<div class="flex_v" style="width:100%;" id="explo_<?= $table ?>">
	<div class="flex_main" id="<?= $zone ?>" data-dsp="line"></div>
</div>
<script>
	load_table_in_zone('groupBy=<?=$groupBy?>&table=<?=$table?>&<?=$APP->translate_vars($vars)?>&nbRows=<?=$nbRows?>', '<?=$zone?>');
</script>