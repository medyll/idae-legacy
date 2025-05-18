<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$TABLE = empty($_POST['table']) ? 'client' : $_POST['table'];
	$name_id = 'id'.$TABLE;
	$Table = ucfirst($TABLE);
	$APP = new App($TABLE);
	$APP_TABLE = $APP->app_table_one;
	$ARR_SCH = $APP->get_schemes();
	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-30 day');
	$DADATE = $dateStart->format('Y-m-d');

	$col_activity = $APP->plug('sitebase_base', 'activity');

	$rs_table = $col_activity->find(['table' => $TABLE, 'dateActivite' => ['$gte' => $DADATE], 'idagent' => (int)$_SESSION['idagent']]);
	$arrTable = $col_activity->distinct('timeActivite', ['table' => $TABLE, 'dateActivite' => ['$gte' => $DADATE], 'idagent' => (int)$_SESSION['idagent']]);
	$arrDate = $col_activity->distinct('dateActivite', ['dateActivite' => ['$gte' => $DADATE], 'idagent' => (int)$_SESSION['idagent']]);

	$COL = [];

	$final_array = [];
	while ($arr = $rs_table->getNext()):
		$final_array[$arr['table_value']]++;
	endwhile;

	// $zer = array_count_values($final_array);
	arsort($final_array,SORT_REGULAR);
	$output = array_slice($final_array,0,15,true);

?>
<div class="margin padding titre1 blanc"><i class="fa fa-caret-right"></i><i class="fa fa-<?= $APP->app_table_icon?>"></i> <?=strtoupper($TABLE)?></div>
<div class="margin applink applinkblock blanc" >
	<?
		foreach ($output as $key => $value):
			$ARR = $APP->query_one([$name_id=>(int)$key]);

			?>
			<div  data-contextual = "table=<?= $TABLE ?>&table_value=<?= $key ?>" class="flex_h">
				<div class="padding block flex_main ellipsis"><a  act_chrome_gui = "app/app/app_fiche" vars = "table=<?= $TABLE ?>&table_value=<?= $key ?>" ><?=strtolower($ARR['nom'.$Table])?></a></div>
				<div class="padding block alignright"> <?=$ARR['code'.$Table]?></div>
			</div>
		<?
		endforeach;
	?>
</div>
