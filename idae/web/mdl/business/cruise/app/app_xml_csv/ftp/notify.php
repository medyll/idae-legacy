<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 03/12/2017
	 * Time: 12:36
	 */

	include_once($_SERVER['CONF_INC']);

	$headers  = get_headers($_POST['url'] , TRUE);
	$filesize = $headers['Content-Length'];
