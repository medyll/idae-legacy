<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 23/09/2016
	 * Time: 21:33
	 */
	include_once($_SERVER['CONF_INC']);

	$less = new lessc;

	$Directory = new RecursiveDirectoryIterator(APPLESS.'generated');
	$Iterator  = new RecursiveIteratorIterator($Directory);
	$Regex     = new RegexIterator($Iterator, '/^.+\.css/i', RecursiveRegexIterator::ALL_MATCHES);

	$out = '';
	foreach ($Regex as $less_iter => $object) {
		$less_dir = explode('/', $less_iter);
		$less_file = array_pop($less_dir);

		//vardump($less_iter);
		// vardump($less_file);
		$less_import =  HTTPAPP.$less_iter;
		//$less->setFormatter("compressed");
		//$less->setPreserveComments(true);

		// $less->checkedCompile($less_iter, APPLESS.'generated/'.str_replace('.less','.css',$less_file));
		// $out .=  "@import url(\"$less_import\");\r\n";
		$out .=  "<link type='text/css' rel='stylesheet' href='$less_import' >\r\n";
	}

	// echo "<style type='text/css' rel='stylesheet' >$out</style>";
	echo $out;
