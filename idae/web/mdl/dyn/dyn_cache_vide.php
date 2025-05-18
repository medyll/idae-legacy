<?
	include_once($_SERVER['CONF_INC']);

	$time = time();
	$APP = new App();
// MDB_PREFIX.'sitebase_cache'
	$base_cache = $APP->plug('sitebase_cache','fs.files');//plug();
	$base_chunks = $APP->plug('sitebase_cache','fs.chunks');//plug();

	$a = $base_cache->find();
	echo '<i class="fa fa-trash-o"></i> Cache: ';
	echo $a->count() ; echo " élément(s) ";
	$base_cache->drop();
	$base_chunks->drop();

	//vardump_async(MDB_PREFIX.'sitebase_cache',true);
	//vardump_async($base_cache,true);

	skelMdl::send_cmd('act_notify',array('msg'=>'<i class="fa fa-trash-o"></i> Cache vidé ... '.date('d/m/Y H:i:s')));

	echo ' ok ';