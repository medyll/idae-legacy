<? 
// ob_start();
	include_once($_SERVER['CONF_INC']);
	require_once __DIR__ . '/appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;
	$APP = new App();
	$arr_G = explode('.',$_GET['_id']) ;
	$_id = $arr_G[0];
	// ob_end_clean();
	$base 	= $APP->plug_base('sitebase_image')->getGridFs('wallpaper');
	$dsp 	= $base->findOne(array('_id'=>MongoCompat::toObjectId($_id)));

	$imgsrc = $dsp->getBytes();

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: cache, must-revalidate');
	header('Pragma: public');
	header('Content-type: image/jpg');
	header('Content-transfer-encoding: binary');
	// header('Content-Length: '.$aSize);
	//
	$ip 	= $_SERVER['REMOTE_ADDR'];
	$ref 	= $_SERVER['HTTP_REFERER'];
	// ob_clean();
	echo $imgsrc;exit;