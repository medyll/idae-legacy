<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];

	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	//print_r($_POST['vars']);
	$table_main = empty($_POST['table_main']) ? $table : $_POST['table_main'];
	$table_from = empty($_POST['table_from']) ? $table : $_POST['table_from'];
	$input_name = empty($_POST['input_name']) ? 'vars' : $_POST['input_name'];

	$vars      = empty($_POST['vars']) ? array() : $_POST['vars'];
	$vars      = fonctionsProduction::cleanPostMongo($vars, true);

	$APP_TABLE = new App($table);
	$APP_MAIN  = new App($table_main);
	$APP_FROM  = new App($table_from);

	$vars = array_filter($vars);
	$filter       = ['id' . $table => ['$exists' => true]];
	$fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter+$vars); // seulement ceux étant dans item_main


	if ($table_from == $table):

	else:
		// $fitered_data = $APP_MAIN->distinct_all('id' . $table, $filter+$vars); // seulement ceux étant dans item_main

	endif;

	///
	$rs = $APP->find(array('id' . $table => array('$in' => $fitered_data)));
	$target = uniqid('r');
?><div id="<?=$target?>">
<div class="searchMdl padding">
	<label class="padding none"><?= $APP_TABLE->app_field_name_nom ?></label>
	<div class="flex_h   blanc">
		<div class="aligncenter padding   cursor" data-menu="data-menu"><i class="fa fa-<?= $APP->iconAppscheme ?> textgris"></i> </div>
		<div class="contextmenu" style="position:absolute;display:none;" act_defer mdl="app/app_search/app_search_item_change" vars="from=select&target=<?=$target?>&<?=http_build_query($_POST);?>"></div>
		<div class="flex_main">
			<select name="<?=$input_name?>[<?= 'id' . $table ?>]" class="inputFull border4"  >
				<option value=""><?= $APP_TABLE->nomAppscheme ?></option> <?
					while ($arr = $rs->getNext()):

						?>
						<option value="<?= $arr['id' . $table] ?>"><?= $arr['nom' . $Table] ?></option>					<?
					endwhile; ?>
			</select>

		</div>
	</div>
</div></div>
