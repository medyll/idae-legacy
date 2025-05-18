<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 14/01/15
 * Time: 13:18
 */
	include_once($_SERVER['CONF_INC']);
	$APP = new App('newsletter');
	$arr        =  $APP->query_one(array('idnewsletter'=>(int) $_POST['idnewsletter']));
	if(!empty($MAIN_APP)) echo "main";
?>
<a onclick="ajaxInMdl('app/app_newsletter/app_newsletter_build','tmp_derfeasxmL_frame','idnewsletter=<?= $_POST['idnewsletter'] ?>',{onglet:'News <?= niceUrl($arr['nomNewsletter']) ?>'});">
	<i class="fa fa-cog"></i> Modifier <?= $arr['nomNewsletter'] ?></a>