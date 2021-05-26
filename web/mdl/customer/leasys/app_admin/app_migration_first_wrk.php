<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	set_time_limit(0);
	ini_set('display_errors', 55);

	$name_table = $_POST['name_table'];
	$ROW        = $_POST['ROW'];

	// var_dump($_POST);
	//exit;
	do_artis_rows($name_table, $ROW);



