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
		<span class="flex_main"><i class="fa fa-cloud"></i> site keepon</span>
		<span class="keepon_client_count"></span>
		<span class="alignright toggler">
			<a class="keepon_connected autoToggle"><i class="fa fa-chain"></i></a>
			<a class="keepon_disconnected  autoToggle"><i class="fa fa-chain-broken"></i></a>
		</span>
		<span>
			<a onclick="reloadModule('app/app_keepon/app_keepon_panel','*');"><i class="fa fa-refresh"></i> </a>
		</span>
	</div>
	<div style="overflow:auto;" class="flex_main" id="socket_keep_on_log">

	</div>
</div>
<script>


	keep_on_init = function () {
		keepon_agent_state_retrieve();
		if(keepon_get_key('keepon_connected')) keepon_connect_agent();
	}

	keepon_get_key = function (key) {
		// if (!localStorage.keepon) localStorage.keepon = {};
		value =  localStorage.getItem('keepon_'+key);

		return value && JSON.parse(value);
	}
	keepon_store_key = function (key, value) {
		// if (!localStorage.keepon) localStorage.keepon = {};
		localStorage.setItem('keepon_'+key,JSON.stringify(value));
	}
	keep_on_panel = function (state) {
		// keepon_agent_state_retrieve();
		if (keepon_get_key('keepon_show_panel')==true) {

		}
	}

	keepon_agent_state_retrieve = function () {
		if (keepon_get_key('keepon_connected')==true) {
			$$('.keepon_connected').invoke('addClassName', 'active');
			$$('.keepon_disconnected').invoke('removeClassName', 'active');
		} else {
			$$('.keepon_connected').invoke('removeClassName', 'active');
			$$('.keepon_disconnected').invoke('addClassName', 'active');
		}
	}
	keepon_disconnect_agent = function () {
		// agent présent pour affichage bouton sur site
		socket_keep_on.emit('undeclare_glue', {APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});
		// statut bouton
		$$('.keepon_connected').invoke('removeClassName', 'active');
		$$('.keepon_disconnected').invoke('addClassName', 'active');
		// keepon_store_key
		keepon_store_key('keepon_connected', false);
	}
	keepon_connect_agent = function () {
		// agent présent pour affichage bouton sur site
		socket_keep_on.emit('declare_glue', {APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});
		// statut bouton
		$$('.keepon_connected').invoke('addClassName', 'active');
		$$('.keepon_disconnected').invoke('removeClassName', 'active');
		// keepon_store_key
		keepon_store_key('keepon_connected', true);
	}

	keep_on_init();

</script>