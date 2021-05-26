<?

	include_once($_SERVER['CONF_INC']);
	$table = $_POST['table'];
	$Table = ucfirst($table);

	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];

	// CONVERSION
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history' || $table == 'agent_table'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
		$Table       = ucfirst($table);
	endif;

	$APP = new App($table);
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);
	//
	$BASE_APP = $APP_TABLE['codeAppscheme_base'];
	$NAME_APP = $APP_TABLE['nomAppscheme_base'];
	//
	//
	$arrFields = $APP->get_basic_fields();
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$top   = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom   = 'nom' . ucfirst($table);

?>
<div onclick="ajaxInMdl('app/app/app_explorer','app_explorer_<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Espace ' . $table) ?>'});" class="tile"
     data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>">
	<div class="tile_icon_main">
		<i class="tile_background fa fa-database fa-2x textgris"></i>
		<div class="tile_icon">
			<i class="fa fa-<?= $APP->iconAppscheme; ?> fa-2x" style="color:<?= $APP->colorAppscheme; ?>!important"></i>
		</div>
	</div>
	<div class="tile_bottom" style="overflow:hidden;">
		<div class="tile_text tile_text_desk ellipsis"> <?= $APP->nomAppscheme; ?></div>
	</div>
</div>