<?

	include_once($_SERVER['CONF_INC']);
	$APP                  = new App();
	$_POST                = array_merge($_GET, $_POST);
	$COLLECT = ['PHPSESSID'=>'','SESSID'=>'','idagent'=>''];

	if(empty($_COOKIE['PHPSESSID'])){
		echo trim(json_encode($COLLECT));
		exit;
	}

/*	$COLLECT['PHPSESSID'] = session_id();
	$COLLECT['SESSID']    = (int)$_SESSION['idagent'];
	$COLLECT['idagent']   = (int)$_SESSION['idagent'];*/

	$COLLECT['PHPSESSID'] = $_COOKIE['PHPSESSID'];
	$COLLECT['SESSID']    = (int)$_SESSION['idagent'];
	$COLLECT['idagent']   = (int)$_SESSION['idagent'];


	echo trim(json_encode($COLLECT));
	// vardump_async(['JSON_SSID',$COLLECT ],true);
	exit;
	$COLLECT['ver_filemttime'] = mostRecentModifiedFileTime(APPPATH . 'web/', true, ['images_base', 'tmp', 'app_install']);
	if (!empty($_SESSION['idagent']) && $COLLECT['ver_filemttime'] != $_SESSION['ver_filemttime']) {

		$init_date = strtotime('2016-11-01');
		$col       = $APP->plug('sitebase_app', 'app_version_user');
		$arr       = $col->findOne(['idagent' => (int)$_SESSION['idagent']]);

		if (!empty($arr['timeLastApp_version_user'])) $_SESSION['ver_filemttime'] = $arr['timeLastApp_version_user'];
		if (empty($_SESSION['ver_filemttime'])) $_SESSION['ver_filemttime'] = $arr['timeLastApp_version_user'];
		if (empty($_SESSION['ver_filemttime'])) $_SESSION['ver_filemttime'] = $init_date;

		$time = time();
		if (empty($_SESSION['time_check'])) $_SESSION['time_check'] = $time;

		if (($_SESSION['time_check'] == $time) || ($time - $_SESSION['time_check']) > 360) {
			if ($COLLECT['ver_filemttime'] != $_SESSION['ver_filemttime']) {
				$ver_filemtetime_date = date('Y-m-d', $COLLECT['ver_filemttime']);
				$ver_filemtetime_time = date('H:i:s', $COLLECT['ver_filemttime']);
				// vardump_async([$COLLECT['ver_filemttime'] , $_SESSION['ver_filemttime']],true);
				skelMdl::send_cmd('act_notify', [/*'msg' => '<br> nada ' . $COLLECT['ver_filemttime'] . ' ' . $_SESSION['ver_filemttime'],*/
					'options' => ['sticky' => 1, 'mdl' => 'app/app_version/app_version_update', 'id' => 'json_version']], $_COOKIE['PHPSESSID']);

			} else {
				// skelMdl::send_cmd('act_notify', ['options' => ['id' => 'json_version']],$_COOKIE['PHPSESSID']);

			}

			$_SESSION['time_check'] = time();
		}
	}