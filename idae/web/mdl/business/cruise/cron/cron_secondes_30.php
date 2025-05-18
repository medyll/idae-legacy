<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
	skelMdl::run($PATH . 'notify', ['file'  => 'red',
	                                'url'   => 'la suite et fin ',
	                                'delay' => 10,
	                                'run'   => 1]);