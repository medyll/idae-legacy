<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 04/08/2015
 * Time: 10:04
 */

include_once($_SERVER['CONF_INC']);

 echo json_encode(get_artis_table(),JSON_FORCE_OBJECT);