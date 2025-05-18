<?
	include_once($_SERVER['CONF_INC']);

	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'] , 1);
	$APP = new App('opportunite_ligne');
	$APPS = new App('opportunite_statut');

	$time = time();
	//
	$table = 'opportunite_ligne';

?>

<div class = "border4 ededed">
	<div class="">
		<form class="Form" action = "<?= ACTIONMDL ?>app/actions.php"  onsubmit = "ajaxFormValidation(this);return false;" auto_reset="auto_reset">
			<input type = "hidden" name = "F_action" id = "F_action" value = "app_create"/>
			<input type = "hidden" name = "table" value = "opportunite_ligne"/>
			<? foreach ($vars as $key => $input): ?>
				<input type = "hidden" name = "vars[<?= $key ?>]" value = "<?= $input ?>">
			<? endforeach; ?>
			<input type = "hidden" name = "idagent_writer" value = "<?= $_SESSION['idagent']; ?>"/>
			<div class="paddin retrait">
				<table>
					<tr>
						<td><?= idioma("nbre") ?></td>
						<td><?= idioma("Produit") ?></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td><input autofocus="autofocus" class = "inputTiny" type = "text" name = "vars[quantiteOpportunite_ligne]" value = "<?= $ARR['quantiteOpportunite_ligne'] ?>" placeholder = "quantité"/></td>
						<td><input datalist_input_name = "vars[idproduit]" datalist_input_value = "" datalist = "app/app_select" populate name = "vars[nomProduit]" paramName = "search" vars = "table=produit" type = "text"/></td>
					<td><button type="submit">ok</button></td>
					<td><a class="cancelRemove blanc inline padding  border4 margin">fermer</a></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>