<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 01/03/15
	 * Time: 18:51
	 */

	include_once($_SERVER['CONF_INC']);
	$APP = new App();
?>
<div class="flex_v" style="height: 100%;width:100%;">
	<div class="titre_entete flex_h applink" style="width:100%;">
		<span class="flex_main"><i class="fa fa-cloud"></i> site appchat</span>
		<span class="appchat_client_count"></span>
		<span class="alignright toggler">
			<a class="appchat_connected autoToggle"><i class="fa fa-chain"></i></a>
			<a class="appchat_disconnected  autoToggle"><i class="fa fa-chain-broken"></i></a>
		</span>
		<span>
			<a onclick="appchat_init();"><i class="fa fa-rocket"></i> </a>
		</span>
		<span>
			<a onclick="reloadModule('app/app_chat/app_chat_panel','*');"><i class="fa fa-refresh"></i> </a>
		</span>
		<div ><a class="app_chat_button" ><i class="fa fa-comments-o"></i> </a></div>
	</div>
	<div style="overflow:auto;" class="flex_main" id="socket_appchat_log">

	</div>
</div>
<script>
	appchat_init();
</script>