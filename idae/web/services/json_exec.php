<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 18/09/2015
	 * Time: 09:33
	 */


	execInBackground('php /var/www/web26/web/server/app_server.php');

	function execInBackground($cmd) {
		if (substr(php_uname(), 0, 7) == "Windows"){
			pclose(popen("start /B ". $cmd, "r"));
		}
		else {
			exec($cmd . " > /dev/null &");
		}
	}