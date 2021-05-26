<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 30/01/15
 * Time: 16:54
 */
	include_once($_SERVER['CONF_INC']);
	$_SESSION['reindex']=date('H-i-s');
	header("Location: index.php");
exit;