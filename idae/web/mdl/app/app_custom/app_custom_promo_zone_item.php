<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 16/01/15
 * Time: 00:41
 */
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$name_id = 'id'.$table;
	$Table = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$APP = new App($table);
	$arrFields = $APP->get_basic_fields();

	$arr = $APP->query_one(array($name_id=>$table_value));

	$srcF = $table.'-large-'.$table_value;

?>
<div class="relative" style="width:650px;overflow:hidden;height:430px;" table="<?=$table?>" table_value="<?=$table_value?>">
	<img style="width:100%" src="<?=Act::imgSrc($srcF);?>?date_cahe=<?=$arr['timeModification'.$Table]?>" data-src="<?=Act::imgSrc($srcF);?>"  class="relative">
	<? if(!empty($arr['atout'.$Table])): ?>
	<div class="absolute padding  " style="z-index:100;right:1em;bottom:200px;">
		<h4 style="color:<?=$arr['color'.$Table]?>" field_name="<?='atout'.$Table?>" field_name_raw="atout"><?=$arr['atout'.$Table]?></h4>
	</div>
	<? endif; ?>
	<? if(!empty($arr['description'.$Table])): ?>
		<div class="absolute padding borderb blanc" style="z-index:100;left:1em;top:1em;width:33%;">
			<span field_name="<?='atout'.$Table?>" field_name_raw="atout"><?=$arr['description'.$Table]?></span>
		</div>
	<? endif; ?>
</div>