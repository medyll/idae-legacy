<?php
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$Table          = ucfirst($table);
	$table_value    = empty($_POST['table_value']) ? '' : (int)$_POST['table_value'];
	$act_chrome_gui = empty($_POST['act_chrome_gui']) ? '' : 'act_chrome_gui=' . $_POST['act_chrome_gui'];
	//
	$APP = new App($table);
	//
	/** @var  $EXTRACTS_VARS */
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);

	$BASE_APP = $APP_TABLE['codeAppscheme_base'];
	//
	$id = 'id' . $table;
	if (!empty($table_value)) $vars[$id] = (int)$table_value;

	$ARR = $APP->findOne($vars);
?>
<div  class="flex_h">
	<?
		$arr_has = ['statut', 'type', 'categorie', 'group'];
		foreach ($arr_has as $key => $value):
			$APPTMP = new App($value);
			$Value  = ucfirst($value);
			$_table = $table . '_' . $value;
			$_Table = ucfirst($_table);
			$_id    = 'id' . $_table;
			$_nom   = 'nom' . $_Table;
			if (!empty($ARR[$_nom])): ?>
				<div class="padding  flex_h    ">
					<div class="label"><span class="bold"><?= ucfirst(idioma($Value)) ?></span></div>
					<div><?= $ARR[$_nom] ?></div>
					<div><i class="fa fa-<?= $APPTMP->iconAppscheme ?>"></i></div>
				</div>
			<? endif; ?>
		<? endforeach; ?>
</div>
