<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 02/02/15
	 * Time: 16:35
	 */
	include_once($_SERVER['CONF_INC']);
	set_time_limit(0);
	ignore_user_abort(true);

	$session_id = $_POST['session_id'];

	if (empty($session_id)) {
		//	exit;
	}

	// exit;
	$AP       = new App();
	$APP      = $AP->init_scheme('sitebase_sockets', 'daemon', ['fields' => ['code', 'nom', 'valeur', 'dateDebut', 'heureDebut']]);
	$iddaemon = (int)$APP->create_update(['codeDaemon' => 'DEFAULT']);
	// $APP->update(array('iddaemon' => $iddaemon), array('valeurDaemon' => 3)); exit();
	// test valeurDaemon
	$test = $APP->findOne(array('iddaemon' => $iddaemon, 'valeurDaemon' => 1));
	if (!empty($test['iddaemon'])) { // deja en route
		exit();
	}
	// register run
	$APP->update(array('iddaemon' => $iddaemon), array('valeurDaemon' => 1, 'dateDebutDaemon' => date('Y-m-d'), 'heureDebutDaemon' => date('h:i:s')));
	//
	$i = 0;
	while (1):
		$test = $APP->find(array('iddaemon' => $iddaemon, 'valeurDaemon' => 3));
		if ($test->count() != 0) { // need stop
			$APP->update(array('iddaemon' => $iddaemon), array('valeurDaemon' => 0));
			sleep(2);
			exit();
		}
		// register running
		$APP->update(array('iddaemon' => $iddaemon), array('dateDaemon' => date('Y-m-d'), 'heureDaemon' => date('h:i:s')));
		// execution
		if (($i % 6) == 0) { // toutes les minutes
			// skelMdl::send_cmd('act_notify', array('options'=>['sticky'=>true],'msg' => ($i%6).' '.date('h:i:s',($i*10)) ));
			skelMdl::runSocketModule('dyn/dyn_ged');
			// break;
		}
		if ($i != 0 && ($i % 12) == 0) {
			$i=0;
			// $APP->update(array('iddaemon' => $iddaemon), array('valeurDaemon' => 3));
		}
		//
		sleep(10);
		$i++;

	endwhile;