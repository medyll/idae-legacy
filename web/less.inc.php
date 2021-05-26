<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 23/09/2016
	 * Time: 21:33
	 */



	$less = new lessc;

	$less->setImportDir(array("assets/less/", "assets/bootstrap"));
	$less->setFormatter("compressed");
	$less->setPreserveComments(true);


	$less->checkedCompile("input.less", "output.css");

	exit;