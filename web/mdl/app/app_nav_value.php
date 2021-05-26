<?
	include_once($_SERVER['CONF_INC']);
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$table = $_POST['table'];

	$APP = new App($table);

	$id = 'id' . $table;
	$top = 'estTop' . ucfirst($table);
	$nom = 'nom' . ucfirst($table);
	// $rs_count = skelMongo::connect($table)->find($vars);
	$rs = $APP->query($vars,'',50)->sort(array($top => -1, $nom => 1));
?>
<div> nav value
	<?
		while ($arr = $rs->getNext()) {
			?>
			<a class = "autoToggle ellipsis"
			   app_button = "app_button"
			   vars = "table=<?= $table ?>&table_value=<?= $arr[$id] ?>" >
				<?= $arr[$nom] ?>
			</a>
		<? } ?></div>