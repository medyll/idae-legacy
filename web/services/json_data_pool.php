<?
	// menu , non 
	//ob_end_clean();
	include_once($_SERVER['CONF_INC']);
	//header("Connection: close");
	$_POST = array_merge($_GET, $_POST);
	ignore_user_abort(true);

	$size = ob_get_length();
	//header("Content-Length: $size");
	//ob_end_flush();
	//flush();

	ini_set('display_errors', 55);
	$APP     = new App();
	$APP_CLI = new App('client');
	$_POST   = array_merge($_GET, $_POST);

	skelDaemon::tacheDaemon();

//
	$ms = [0, 15, 30, 45];
	$m  = mt_rand(date('m'), 12);
	$d  = mt_rand(1, 30);
	$h  = mt_rand(9, 17);
	$hm = $ms[mt_rand(0, 3)];
//
	$str   = $d . '/' . $m . '/2015';
	$heure = $h . ':' . $hm . ':00';

	$ti = [$heure, 'AM', 'PM'];
	echo $time = $ti[mt_rand(0, 2)];

	$dist             = $APP_CLI->distinct('client');
	$i_cli            = mt_rand(1, sizeof($dist));
	$vars['idclient'] = $dist[$i_cli];
	$vars             = ['nomTache' => 'random ' . $heure, 'dateDebutTache' => $str, 'heureDebutTache' => $time, 'idagent' => 41];
	skelMdl::run('mdl/app/actions', ['table' => 'tache', 'F_action' => 'app_create', 'vars' => $vars]);
	skelMdl::send_cmd('act_notify', ['msg' => $heure . ' ' . $str . ' Cyclique pool ' . date('H:i:s')]);
	
	