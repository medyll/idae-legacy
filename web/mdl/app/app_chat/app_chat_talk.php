<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 27/02/15
	 * Time: 23:22
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$id = (int)$_POST['idagent'];
	$APPID = $_POST['APPID'];
	//echo http_build_query($_POST);

	$APP_ONLINE = new App('agent');
	$arr_online = $APP_ONLINE->query_one(['idagent' => $id]);

	$nom = $arr_online['nomAgent'] . ' ' . $arr_online['prenomAgent'];
	$pseudo = $arr_online['loginAgent'];
?>
<div class="relative" data-appid="<?= $APPID ?>" data-appchatbox="1">
	<div class="glue_phone bold titre aligncenter" style="display:none;"></div>
	<div class="flex_h flex_main bordert blanc" style="width:100%;">
		<div class="aligncenter padding fond_noir color_fond_noir">
			<img src="<?= Act::imgApp('agent', $id, 'squary') ?>">
			<br><?= $pseudo ?>
			<div class="glue_requested padding applink flex_h" style="display:none;width: 100%;">
				<div class="relative flex_main alignright">
					<a class="app_chat_accept" title="<?= idioma('Accepter contact'); ?>"><i class="fa fa-bell"></i>
						accepter</a>
				</div>
				<div class="relative alignright">
					<a class="glue_busy" style="display: none;"><i class="fa fa-rss"></i></a>
				</div>
			</div>
		</div>
		<div class="flex_main" style="width:100%;overflow: hidden;">
			<div class="titre_entete flex_h applink fond_noir color_fond_noir" style="width: 100%;">
				<div class="flex_main" style="width:100%"><?= $nom ?></div>
				<div class="relative alignright glue_busy">
					<i class="fa fa-rss"></i>
				</div>
			</div>
			<div class="padding appchat_msgzone" style="min-height: 150px;max-height: 450px;overflow: auto;max-width: 450px;"></div>
			<div class="padding ededed">
				<form
					onsubmit="appchat_talk_agent(this);appchat_msgzone_update('<?= $APPID ?>',this.MSGTXT.value);this.reset(); return false;">
					<input type="hidden" name="ROOMREQUESTED" value="<?= $APPID ?>">
					<input type="text" name="MSGTXT" class="inputMedium" required >
					<button type="submit" class="inputTiny"><i class="fa fa-send"></i></button>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	//appchat_get_key('appchat_last_talk.<?= $APPID ?>');
	//appchat_get_key('appchat_last_self_talk.<?= $APPID ?>');
</script>