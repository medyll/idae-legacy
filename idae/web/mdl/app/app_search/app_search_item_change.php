<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/11/2015
	 * Time: 14:15
	 */

	include_once($_SERVER['CONF_INC']);
	$target = $_POST['target'];
	$arr = ['free'=>'Saisie','select'=>'Liste','text'=>'champ libre','check'=>'Multiple'];
	unset($arr[$_POST['from']]);
	unset($_POST['target']);
	?>
<div class="padding applink applinkblock">
	<? foreach ($arr as $key=>$value):

		?>
		<a onclick="$('<?=$target?>').socketModule('app/app_search/search_item_<?=$key?>','<?=http_build_query($_POST)?>')"><i class="fa fa-caret-right"></i> <?=$value?></a>
		<?
	endforeach; ?>
</div>
