<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$vars = array_filter($vars);
	$tr_vars = App::translate_vars($vars);
	$table = $_POST['table'];
	$table_value = $_POST['table_value'];
	$id = 'id' . $_POST['table'];
	$nom = 'nom' . ucfirst($table);
?>
<div>
<input datalist_input_name = "vars[<?=$id?>]"
       datalist = "app/app_select"
       name     = "vars[<?=$nom?>]"
       paramName = "search"
       vars     = "table=<?=$table?>&<?=$tr_vars?>"
       value    = "<?=$table_value?>"
       class    = "inputMedium"
       populate /> choisir</div>