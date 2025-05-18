<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 08/11/2016
	 * Time: 09:05
	 */

	include_once($_SERVER['CONF_INC']);

	return;
	$Table    = 'App_version_file';
	$APP_FILE = new App('app_version_file');
	global $exclude_dir, $Table, $export;
	$exclude_dir = ['images', 'images_base'];
	$export      = [];
	version_scan(SITEPATH);

	foreach ($export as $key => $value) {
		$APP_FILE->create_update(['nom' . $Table => $value['nom' . $Table]], $value);
	}

	function version_scan($dir_main) {
		global $exclude_dir, $export, $Table;
		$scanned_directory = array_diff(scandir($dir_main), ['..', '.']);

		foreach ($scanned_directory as $dir) {
			$obj_name = $dir_main .'/'.  $dir;
			$time     = filemtime($obj_name);
			$date     = date('Y-m-d', $time);
			$heure    = date('H:i:s', $time);

			//   $files = file($dir);
			//   vardump_async($dir,true);
			//   include_once($conf_module . $file);
			if (is_dir($obj_name)) {
				version_scan($obj_name);
			} else {
				$export[$obj_name] = ['ENVIRONEMENT'=>ENVIRONEMENT,'time' . $Table => $time, 'date' . $Table => $date, 'heure' . $Table => $heure, 'nom' . $Table => $obj_name, 'code' . $Table => $dir];
			}
		}

		// return $export;
	}