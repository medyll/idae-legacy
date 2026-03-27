<?php
/**
 * actions.php — Main AJAX action entry point.
 * Dispatches F_action to internal handlers then includes postAction.php.
 *
 * Date: 07/07/14
 * Modified: 2026-03-15 — <?php tag, CSRF validation, exit→return, English comments
 */
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/appclasses/appcommon/CsrfGuard.php');
	use AppCommon\CsrfGuard;

	if ( isset($_POST['F_action']) ) {
		$F_action = $_POST['F_action'];
	} else {
		return;
	}

	// CSRF validation — skip for safe/read-only actions and login
	$csrf_exempt = ['identificationAgent', 'debug'];
	if (!in_array($F_action, $csrf_exempt, true) && !CsrfGuard::check()) {
		http_response_code(403);
		echo json_encode(['error' => 'CSRF token invalid']);
		return;
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

			return;
			break;
	}
//
	include_once(DOCUMENTROOT . '/postAction.php');
