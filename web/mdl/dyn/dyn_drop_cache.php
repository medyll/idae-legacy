<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 19/05/15
 * Time: 11:17
 */
	include_once($_SERVER['CONF_INC']);

	skelMdl::dropCache();

	skelMdl::send_cmd('act_notify',array('msg'=>'Cache vidé ... '.date('d/m/Y H:i:s')));