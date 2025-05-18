<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 27/02/15
	 * Time: 23:22
	 */
	include_once($_SERVER['CONF_INC']);

	$id = (int)$_POST['idagent'];
	$APPID = $_POST['APPID'];
	//echo http_build_query($_POST);

	$APP_ONLINE = new App('agent');
	$arr_online = $APP_ONLINE->query_one(['idagent' => $id]);

	$nom = $arr_online['nomAgent'] . ' ' . $arr_online['prenomAgent'];
	$pseudo = $arr_online['loginAgent'];
?>
<div class="relative">

	<div class="glue_phone bold titre aligncenter" style="display:none;"></div>
	<div class="flex_h flex_main bordert blanc" style="width:100%;">
		<div class="aligncenter padding">
			<img src="<?= Act::imgApp('agent', $id, 'squary') ?>">

			<div class="glue_requested padding applink flex_h" style="display:none;width: 100%;">
				<span class="relative flex_main alignright">

				</span>
				<span class="relative alignright">
					 <a class="glue_busy" style="display: none;"><i class="fa fa-rss"></i></a>
				</span>
			</div>
		</div>
		<div class="flex_main" style="width:100%;overflow: hidden;">
			<div class=" applink" style="width: 100%;">
				<div class="titre_entete"><?= $nom ?></div>
				<div><a style="display:none;" class="app_chat_accept" act_chrome_gui="app/app_chat/app_chat_talk"
				        vars="<?= http_build_query($_POST) ?>"><i class="fa fa-comment"></i> Accepter</a></div>
				<div class="bordert applink applinkblock">
					<div class="app_chat_ask_contact applink applinkblock"><a><i class="fa fa-comments"></i> Demande de
							contact</a></div>
					<div class="app_chat_ask_contact_wait applink applinkblock" style="display:none;"><a><i
								class="fa fa-spinner fa-spin"></i>
							Demande en attente</a>
					</div>
					<div class="app_chat_ask_contact_ok applink applinkblock" style="display:none;"><a><i
								class="fa fa-comments-o"></i> contact ok</a></div>
				</div>
				<? //= skelMdl::cf_module('app/app/app_fiche_mini', ['table' => 'agent', 'table_value' => $arr_online['idagent']]) ?>
			</div>
		</div>
	</div>