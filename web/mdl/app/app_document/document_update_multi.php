<?
	include_once($_SERVER['CONF_INC']);

	if (empty($_POST['base']) || empty($_POST['collection']))
		return;
	$time = time();
	$_id = $_POST['_id'];
	$F_action = $_POST['F_action'];
	$base = $_POST['base'];
	$collection = $_POST['collection'];
?>

<div style = "width:450px;">
	<div class = "titre_entete uppercase fond_noir color_fond_noir">
		<?= idioma('Modification de liste') ?>
	</div>
	<form action = "<?= ACTIONMDL ?>document/actions.php"
	      onsubmit = "ajaxFormValidation(this);return false;"
	      auto_close = "auto_close">

		<input type = "hidden"
		       name = "F_action"
		       value = "multiDoc"/>
		<input type = "hidden"
		       name = "scope"
		       value = "document"/>
		<input type = "hidden"
		       name = "base"
		       value = "<?= $base ?>"/>
		<input type = "hidden"
		       name = "collection"
		       value = "<?= $collection ?>"/>
		<? foreach ($_id as $value) { ?>
			<input name = "_id[]"
			       type = "hidden"
			       value = "<?= $value ?>">
		<? } ?>
		<table class = "tabletop">
			<tr>
				<td style = "width:90px;text-align:center"><br>
					<img src = "<?= ICONPATH ?>alert32.png"/></td>
				<td class = "texterouge"><br>
					Êtes vous sur de vouloir modifier ces<br>
					<strong>
						<?= sizeof($_id) ?>
						document(s)</strong> ?
					<hr>
					<? switch ($_POST['F_action']):
					case "desc":
						?>
						<label>
							<?= idioma("Descriptif") ?>
						</label>
					<input type = "text"
					       name = "vars[descriptionProduit]"
					       value = ""
					       class = "inputLarge">
					<?
						break;
						case "suppr":
					?>
						<label>
							<input type = "checkbox"
							       name = "SUPPRIMER"
							       value = "1"/>
							&nbsp;
							<?= idioma('SUPPRIMER DEFINITIVEMENT') ?>
						</label>
					<?
						break;
						case "setmetadata":
					?>
						<label>
							<input placeholder = "Par client"
							       target = "treg"
							       type = "text"
							       name = "searchDataClient"
							       value = ""
							       class = "inputMedium"
							       datalist = "document/document_data_select">
							<input placeholder = "par devis ou dossier"
							       target = "treg"
							       type = "text"
							       name = "searchDataDevis"
							       value = ""
							       class = "inputMedium"
							       datalist = "document/document_data_select">
						</label>
						<div id = "treg"></div>
						<script>
							//   new myddeDatalist($('id
							<?=$uniqid?>'));
						</script>
						<?
						break;
					endswitch; ?></td>
			</tr>
		</table>
		<br/>

		<div class = "buttonZone">
			<input type = "submit"
			       value = "Valider">
			<input type = "reset"
			       value = "Annuler"
			       class = "cancelClose">
		</div>
	</form>
</div>
