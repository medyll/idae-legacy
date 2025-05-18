<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 31/05/14
	 * Time: 16:18
	 */
	include_once($_SERVER['CONF_INC']);
	$time = time();
	$arr_id = $_POST['id'];
	$F_action = $_POST['F_action'];
	//
	$table = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;

	//
	$id = 'id' . $table;
	//


	//
	$arrFieldsBool = $APP->get_array_field_bool();
	// vardump($_POST);
?>

<div style = "width:450px;">
	<div class = "titre_entete">
		<i class = "fa fa-question-circle"></i> <?= idioma('Création de liste ') ?><?= $table ?>
	</div>


	<form action = "<?= ACTIONMDL ?>app/actions.php"
	      onsubmit = "ajaxFormValidation(this);return false;" auto_close="auto_close">

		<input type = "hidden"
		       name = "F_action"
		       value = "app_save_liste_multi"/><input type = "hidden"
		                                          name = "table"
		                                          value = "<?= $table ?>"/>
		<? foreach ($arr_id as $value) { ?>
			<input name = "arr_id[]"
			       type = "hidden"
			       value = "<?= $value ?>">
		<? } ?>
		<div class = "table" style="width:100%;">
			<div class = "cell">
				<div class = "margin padding  ">
						<div class = "padding"><?= ucfirst(idioma('Liste')) ?></div>
						<div class = "padding retrait bold">
							<input datalist_input_name = "vars[idagent_liste]"
							       datalist_input_value = ""
							       datalist = "app/app_select"
							       populate
							       name = "vars[<?= $nom_type ?>]"
							       paramName = "search"
							       vars = "table=agent_liste&vars[idagent]=<?=$_SESSION['idagent']?>&vars[codeAgent_liste]=<?=$table?>"
							       value = ""
							       class = "inputMedium"/>
						</div>

				</div>
			</div>

		</div>

		<div class = "buttonZone">
			<input type = "submit"
			       value = "Valider">
			<input type = "button"
			       value = "Annuler"
			       class = "cancelClose">
		</div>
	</form>
</div>