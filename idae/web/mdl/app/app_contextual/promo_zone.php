<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 14/01/15
 * Time: 13:18
 */
	include_once($_SERVER['CONF_INC']);
	$APP = new App('promo_zone');
	$arr        =  $APP->query_one(array('idpromo_zone'=>(int) $_POST['idpromo_zone']));
	if(!empty($MAIN_APP)) echo "main";
?>
<a onclick="ajaxInMdl('app/app_promo_zone/app_promo_zone_build','tmp_derfeamL_frame','idpromo_zone=<?= $_POST['idpromo_zone'] ?>',{onglet:'<?= niceUrl($arr['nomPromo_zone']) ?>'});"><img
		src="<?= ICONPATH ?>fiche16.png"/>Modifier <?= $arr['nomPromo_zone'] ?></a>