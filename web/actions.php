<?
	include_once($_SERVER['CONF_INC']);

	if ( isset($_POST['F_action']) ) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}


	array_walk_recursive($_POST , 'CleanStr' , $_POST);

	$APP = new App();

	switch ($F_action) {
		case "debug" :
			var_dump($_POST);
			break;
		case "quitter" :
			session_start();
			session_destroy();
			session_write_close();
			if (isset($_SERVER['HTTP_COOKIE'])) {
				$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				foreach($cookies as $cookie) {
					$parts = explode('=', $cookie);
					$name = trim($parts[0]);
					setcookie($name, '', time()-1000);
					setcookie($name, '', time()-1000, '/');
				}
			}
			break;
		case "unstream_to" :
			echo 'unstream_to '.$_POST['stream_to'] ;
			$APP_SOCKET = $APP->plug('sitebase_sockets' , 'stream_to');

			$APP_SOCKET->update(array( 'nomStream_to' => $_POST['stream_to'] ),array('$set'=>array('stop'=>1)),array('upsert'=>true,'safe'=>1));

			exit;
			break;
	}
//
	include_once(DOCUMENTROOT . '/postAction.php');