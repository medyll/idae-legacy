<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 22/12/14
 * Time: 23:55
 */

	include_once($_SERVER['CONF_INC']);

	$img = Act::imgSrc($_GET['image']);

	header("Location: ".$img);exit;