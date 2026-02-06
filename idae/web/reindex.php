<?php
  // session_start();
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 30/01/15
 * Time: 16:54
 * Modified: 2026-02-06 - Added redirect loop protection + session validation (Agent)
 */
	include_once($_SERVER['CONF_INC']);
	
	// Verify session is working before redirecting
	if (session_status() !== PHP_SESSION_ACTIVE) {
		die('<h1>Session Error</h1><p>session_start() failed. Check ClassSession.php and MongoDB connection.</p>');
	}
	
	$_SESSION['reindex'] = date('H-i-s');
	
	// Verify write succeeded
	if (empty($_SESSION['reindex'])) {
		die('<h1>Session Error</h1><p>Cannot write to session. Check SESSION_PATH permissions or MongoDB.</p>');
	}
	
	// Forward retry parameter if exists
	$retry = isset($_GET['retry']) ? '?retry=' . (int)$_GET['retry'] : '';
	header("Location: index.php$retry");
exit;