<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/05/14
	 * Time: 18:58
	 */
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table']) || empty($_POST['table_value']))
		return;
	$table = $_POST['table'];
	$table_value = (int)$_POST['table_value'];

	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;

	//
	$id = 'id' . $table;
	$nom = 'nom' . ucfirst($table);
	$ARR = $APP->query_one([$id => $table_value]);

?>


<div class="ededed" style="width:450px;">
	<form action="<?= ACTIONMDL ?>app/actions.php"
	      onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
	<input type="hidden"
	       name="F_action"
	       value="app_delete"/>
	<input type="hidden"
	       name="table"
	       value="<?= $table ?>" />
	<input type="hidden"
	       name="table_value"
	       value="
	       <?= $table_value ?>"/>
<div class="fond_noir color_fond_noir">
	<table style="width:100%">
		<tr>
			<td style="width:90px;text-align:center"><br>
				<i class="fa fa-trash-o fa-5x fa-aligncenter"></i>
			</td>
			<td class="texterouge"><br>
				<?= idioma('Voulez vous supprimer') ?><br>
				<?= $table ?> <span class="bold"> "<?= $ARR['nom' . ucfirst($table)] ?>"</span> ?<br>
				<br></td>
		</tr>
	</table></div>
	<div class="buttonZone">
		<button class="trash_button"
		        type="submit"><?= idioma('Supprimer') ?></button>
		<input type="button"
		       value="<?= idioma('Annuler') ?>"
		       class="cancelClose">
	</div>
	</form>
</div>
<div class="titreFor">
	<div class="textrouge"> <?= idioma('Suppression') ?> <?= $table ?> "<?= $ARR[$nom] ?>"</div>
</div>
