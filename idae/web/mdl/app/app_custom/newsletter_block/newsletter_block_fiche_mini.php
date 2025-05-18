<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 15/01/2016
	 * Time: 10:18
	 */
	include($_SERVER['CONF_INC']);

	global $buildArr;
	global $IMG_SIZE_ARR;

	$APP      = new App('newsletter_block');
	$APP_ITEM = new App('newsletter_item');

	$table       = $_POST['table'];
	$Table = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$arr         = $APP->findOne(array('idnewsletter_block' => (int)$table_value));
	$rs_item     = $APP_ITEM->find(['idnewsletter_block' => $table_value])->sort(['ordreNewsletter_item' => 1]);
?>
<div class="blanc  padding  borderb" sort_zone_drag="true">
	<div class="bordertb padding ededed">
		<?= $APP->draw_field(['field_name_raw' => 'color', 'table' => $table, 'field_value' => $arr['color' . $Table]]) ?></a>
		<?= $APP->draw_field(['field_name_raw' => 'bgcolor', 'table' => $table, 'field_value' => $arr['bgcolor' . $Table]]) ?></a>
	</div>
	<div class="flex_h">
	<? while ($arr_item = $rs_item->getNext()) {
		$img_show = '';
		switch ($rs_item->count()):
			case 1:
				$img_size = 'large';
				break;
			case 2:
				$img_size = 'largy';
				$img_show = 'largy';
				break;
			case 3:
				$img_size = 'small';
				break;
			case 4:
				$img_size = 'square';
				break;
		endswitch;
		$img_show = empty($img_show) ? $img_size : $img_show;
		//
		$width  = $IMG_SIZE_ARR[$img_show][0];
		$height = $IMG_SIZE_ARR[$img_show][1];
		if (empty($width)):
			$width  = $buildArr[$img_show][0];
			$height = $buildArr[$img_show][1];
		endif;
		?>
	<div style="width:<?= $width ?>px;overflow:hidden;" draggable="true" data-sort_element="true" data-table="newsletter_item" data-table_value="<?= $arr_item['idnewsletter_item'] ?>" class="flex_main margin blanc">
		<div class="">
			<div class="aligncenter   boxshadow" style="z-index:1;bottom:20px;right:240px;" act_defer mdl="app/app_img/image_dyn"
			     vars="table=newsletter_item&table_value=<?= $arr_item['idnewsletter_item'] ?>&codeTailleImage=<?= $img_size ?>&show=<?= $img_show ?>" scope="app_img"
			     value="newsletter_item-<?= $img_size ?>-<?= $arr_item['idnewsletter_item'] ?>"></div>
			<div><?= skelMdl::cf_module('app/app/app_fiche_micro', ['table' => 'newsletter_item', 'table_value' => $arr_item['idnewsletter_item']]) ?></div>
		</div>
		</div><? } ?></div>
</div>

