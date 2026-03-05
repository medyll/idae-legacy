<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	$vars    = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$table   = $_POST['table'];
	$APP     = new App($table);
	$tr_vars = $APP->translate_vars($vars);
	//

	$table_fk = $_POST['table_fk'];
	$Table_fk = ucfirst($table_fk);
	$idtable_fk =  'id'.$table_fk;

	$APP_TMP  = new App($table_fk);
	//
	if (!empty($_POST['search'])) { // un champ de recherche unique
		$regexp         = MongoCompat::toRegex(MongoCompat::escapeRegex($_POST['search']), 'i');

		if(is_int($_POST['search'])) $vars['$or'][] = [$idtable_fk => (int)$_POST['search']];

		if ($APP->has_field('nom'))     $vars['$or'][] = array('nom' . $Table_fk => $regexp);
		if ($APP->has_field('prenom'))  $vars['$or'][] = array('prenom' . $Table_fk => $regexp);
		if ($APP->has_field('email'))   $vars['$or'][] = array('email' . $Table_fk => $regexp);
		if ($APP->has_field('code'))    $vars['$or'][] = array('code' . $Table_fk => $regexp);
		if ($APP->has_field('reference')) $vars['$or'][] = array('reference' . $Table_fk => $regexp);
		if ($APP->has_field('telephone')) $vars['$or'][] = array('telephone' . $Table_fk => MongoCompat::toRegex(MongoCompat::escapeRegex(cleanTel($_POST['search'])), 'i'));



		// vardump($where);exit;
	}

	$rs_dist  = $APP->distinct($table_fk, $vars);

	while ($arr_dist = $rs_dist->getNext()) {
		?>
		<a app_button="<?= $table ?>" vars="table=<?= $table ?>&vars[id<?= $table_fk ?>]=<?= $arr_dist['id' . $table_fk] ?>&<?= $tr_vars ?>"><?= $arr_dist['nom' . ucfirst($table_fk)] ?></a>
	<? } ?>
