<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/05/14
	 * Time: 18:58
	 */
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['idappscheme']) || empty($_POST['uid']))
		return;
	$table       = "appscheme";
	$idappscheme = (int)$_POST['idappscheme'];

	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;

	//
	$id  = 'id' . $table;
	$nom = 'nom' . ucfirst($table);
	$ARR = $APP->query_one([$id => $idappscheme]);
	$GRILLE_FK = $APP->get_grille_fk($ARR['codeAppscheme']);


?>
<div class="ededed" style="width:450px;">
	<form action="<?= ACTIONMDL ?>app/app_scheme/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="deleteFK">
		<input type="hidden" name="idappscheme" value="<?=$idappscheme?>">
		<input type="hidden" name="uid" value="<?=$_POST['uid']?>">
		<div class="flex_h">
			<div class="borderr" style="width:90px;text-align:center">
				<br>
				<i class="fa fa-trash-o fa-5x textrouge"></i>
			</div>
			<div class="flex_main padding">
				<table style="width:100%">
					<tr>
						<td class="texterouge">
							<br>
							<?= idioma('Voulez vous supprimer cette entrée') ?>
							<br>
							<?= $table ?> <span class="bold"> "<?= $_POST['nomInput'] ?>"</span> ?
							<br>
							<br>
						</td>
					</tr>
				</table>
			</div>
		</div>
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
	<?= idioma('Suppression') ?>
</div>
