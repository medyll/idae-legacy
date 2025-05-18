<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$table = $_POST['table'];

	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	//print_r($_POST['vars']);
	$table_main = empty($_POST['table_main']) ? $table : $_POST['table_main'];
	$table_from = empty($_POST['table_from']) ? $table : $_POST['table_from'];
	$input_name = empty($_POST['input_name']) ? 'vars_search' : $_POST['input_name'];

	$vars      = empty($_POST['vars']) ? [] : $_POST['vars'];
	$vars      = fonctionsProduction::cleanPostMongo($vars, true);
	$APP_MAIN  = new App($table_main);

	$vars         = array_filter($vars);
	$filter       = ['id' . $table => ['$exists' => true]];
	$fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter + $vars); // seulement ceux étant dans item_main

	if ($table_from == $table):

	else:
		// $fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter+$vars); // seulement ceux étant dans item_main

	endif;

	///
	$rs        = $APP->find(['id' . $table => ['$in' => $fitered_data]]);
	$HTTP_VARS = 'vars_in[id' . $table . '][$in]=' . json_encode($fitered_data);
	$target    = uniqid('r');
?>
<div id="<?= $target ?>" class=" ">
	<div class="searchMdl padding">
		<label class="padding none"><?= $table ?></label>
		<div class="flex_h flex_align_middle    ">
			<div class="aligncenter padding   cursor" data-menu="data-menu"><i class="fa fa-<?= $APP->iconAppscheme ?>  " style="color:<?= $APP->colorAppscheme ?>"></i></div>
			<div class="contextmenu" style="position:absolute;display:none;" act_defer mdl="app/app_search/app_search_item_change" vars="from=free&target=<?= $target ?>&<?= http_build_query($_POST); ?>"></div>
			<div class="flex_main">
				<input type="text" class="inputFull" placeholder="<?= $APP->nomAppscheme ?>" name="<?= $input_name ?>[<?= $table ?>]"  />
			</div>
		</div>
	</div>
</div>