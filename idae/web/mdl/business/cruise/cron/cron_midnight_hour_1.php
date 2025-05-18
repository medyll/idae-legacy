<?
	include_once($_SERVER['CONF_INC']);

	vardump_async('une heure du mat',true);

	skelMdl::run( $PATH . 'ftp/msc' , [
		'run'   => 1 ,
		'delay' => 1000 ]);