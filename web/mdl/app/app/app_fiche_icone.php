<?

	include_once($_SERVER['CONF_INC']);
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//

	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche_icone.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche_icone', $_POST);

		return;
	}

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


	//
	$arrFieldsBool = $APP->get_array_field_bool();
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE');

	//
	$DEFAULT_FIELDS = $APP->app_default_fields;

?>
<div onclick="<?= fonctionsJs::app_fiche($table, $table_value) ?>" class="tile margin" title="<?= $APP->nomAppscheme; ?>"
     data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>">
	<div class="aligncenter mastershow" style=padding:0.25em;">
		<div class="tile_icon_main">
			<div class="tile_background textgris"><i class=" fa fa-file-o fa-2x " style="color:<?= $ICON_COLOR ?>"></i></div>
			<div class="tile_icon">
				<i class="fa fa-<?= $ICON ?> fa-2x"></i>
			</div>
		</div>
		<div class="tile_bottom" style="overflow:hidden;">
			<? if (!empty($ARR['color' . $Table . '_type']) || !empty($ARR['color' . $Table . '_statut'])) { ?>
				<div class="tile_text aligncenter ellipsis">
					<i class="fa fa-<?= $ARR['icon' . $Table . '_type'] ?>" style="color:<?= $ARR['color' . $Table . '_type'] ?>"></i>
					<i class="fa fa-<?= $ARR['icon' . $Table . '_statut'] ?>" style="color:<?= $ARR['color' . $Table . '_statut'] ?>"></i>
				</div>
			<? } ?>
			<div class="tile_text aligncenter ellipsis ucfirst"><?= strtolower($ARR['nom' . ucfirst($table)]) ?></div>
			<div class="tile_text aligncenter ellipsis"><span class="bordert textgrisfonce">  <?= $ARR['code' . ucfirst($table)] ?></span></div>
		</div>
	</div>
</div>