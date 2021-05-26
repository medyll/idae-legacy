<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
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
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$top = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom = 'nom' . ucfirst($table);


?>
<div style = "width: 800px;"
     class = "relative">
	<img style = "position: absolute;right:5px;top:5px;"
	     src = "<?= Act::imageSite($table, $table_value, 'mini') ?>"/>
	<div act_defer
	     mdl = "app/app/app_menu"
	     vars = "act_from=meta_update&table=<?= $table ?>&table_value=<?= $table_value ?>"></div>
	<div class = "table"
	     style = "width: 100%;">
	</div>
	<form action = "<?= ACTIONMDL ?>app/actions.php"
	      onsubmit = "ajaxFormValidation(this);return false;">
		<input type = "hidden"
		       name = "F_action"
		       value = "app_update"/>
		<input type = "hidden"
		       name = "table"
		       value = "<?= $table ?>"/>
		<input type = "hidden"
		       name = "table_value"
		       value = "<?= $table_value ?>"/>
		<input type = "hidden"
		       name = "scope"
		       value = "<?= $id ?>"/>
		<input type = "hidden"
		       name = "<?= $id ?>"
		       value = "<?= $table_value ?>"/>

		<div class = "table tabletop"
		     style = "width: 100%;">
			<div class = "cell padding">
				<table class = "table_form">
					<? foreach ($arrFieldsMeta as $field):
						$input_name = "vars[$field" . ucfirst($table) . "]";
						?>
						<tr>
							<td><?= ucfirst(idioma($field)) ?></td>
							<td class = "justify">
								<?
									switch ($field):
										case  'description':
											?>
											<textarea name = "<?= $input_name ?>"
											          style = "width:100%; height:100px;"><?= nl2br($ARR[$field . ucfirst($table)]) ?>
											</textarea>
											<?
											break;
										default:
											?>
												<textarea name = "<?= $input_name ?>" class="border4"
														  style = "width:550px; height:39px;overflow:hidden;"><?= nl2br($ARR[$field . ucfirst($table)]) ?></textarea>
											<?
											break;
									endswitch; ?>
							</td>
						</tr>
					<? endforeach; ?>
				</table>

			</div>
		</div>
		<div class = "buttonZone">
			<input class="valid_button" type = "submit"
			       value = "<?= idioma('Valider') ?>">
			<input type = "button"
			       class = "cancelClose"
			       value = "<?= idioma('Fermer') ?>"></div>
	</form>
</div>
<div class = "titreFor">
	<?= idioma('Mise à jour') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div>
<div class="footerFor">ici</div>
<style>
	.valid_button {border:1px solid #0000FF;}
	.trash_button {  border : 1px solid #B81900; }
	.trash_button:before {
		content:'\f014';
		font-family: FontAwesome;
		color  : #B81900;
		margin : 0 5px;
	}
</style>