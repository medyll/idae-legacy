<?
	include_once($_SERVER['CONF_INC']);



	$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

	skelMdl::run( $PATH . 'ftp/costa_catalog' , [
		'run'   => 1 ,
		'delay' => 1000 ]);