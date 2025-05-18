<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 27/02/15
	 * Time: 23:22
	 */
	include_once($_SERVER['CONF_INC']);

	$id = (int)$_POST['idagent'];
	// ROOMREQUESTER
	$APPID = $_POST['APPID'] = $_POST['ROOMREQUESTER'];
	//echo http_build_query($_POST);
	$APP_ONLINE = new App('agent');
	$arr_online = $APP_ONLINE->query_one(['idagent' => $id]);

	$nom = $arr_online['nomAgent'] . ' ' . $arr_online['prenomAgent'];
	$pseudo = $arr_online['loginAgent'];
?>
<div class="relative blanc" data-appid="<?= $APPID ?>" data-idagent="<?= $id ?>">
	<div class="flex_h flex_main bordert blanc" style="width:100%;">
		<div class="aligncenter padding">
			<img src="<?= Act::imgApp('agent', $id, 'squary') ?>">
		</div>
		<div class="flex_main" style="width:100%;overflow: hidden;">
			<div class="titre_entete applink applinkblock">
				<?= $nom ?>
			</div>
			<div class="appchat_msgzone"></div>
			<div class="titre_entete applink" style="width: 100%;">
				<a class="app_chat_accept closer" ><i class="fa fa-comment"></i> Accepter </a>

			</div>
		</div>
	</div>
</div>