<?php
	include_once($_SERVER['CONF_INC']);//
	//
	$output = shell_exec('/bin/bash ./deploy.sh');
	echo "<pre>$output</pre>";
	echo "<pre>Démarrage ok</pre>";
