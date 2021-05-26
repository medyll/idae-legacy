<?
	include_once($_SERVER['CONF_INC']);

	$time = time();
	$APP = new App();

	$base_cache = $APP->plug('sitebase_cache','cache');

	// echo $base_cache->drop();
exec('forever restart app_cruise.app.js');
	skelMdl::send_cmd('act_notify',array('msg'=>'Cache vidé ... '.date('d/m/Y H:i:s')));