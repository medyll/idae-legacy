<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('opportunite_ligne');
	$APPS = new App('opportunite_statut');

	$time = time();
	//
	$idopportunite_ligne = (int)$_POST['table_value'];
	$ARR = $APP->query_one(['idopportunite_ligne' => $idopportunite_ligne]);

?>

<div style="width:350px;" class="ededed" >

	<form action="<?= ACTIONMDL ?>app/actions.php" id="formCreateOpportunite<?= $time ?>" name="formCreateOpportunite<?= $time ?>" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
		<input type="hidden" name="F_action" id="F_action" value="app_update" />
		<input type="hidden" name="table" value="opportunite_ligne" />
		<input type="hidden" name="table_value" value="<?= $idopportunite_ligne ?>" />
		<input type="hidden" name="idagent_writer" value="<?= $_SESSION['idagent']; ?>" />
		<input id="lg2" name="vars[nomOpportunite_ligne]" type="hidden">
		<div class="flex_h" style="width:100%;" >
			<div class="padding bordertb ededed">
				<table class=""   >
					<tr >
						<td > <?= idioma("Qte") ?> </td >
						<td >
							<input type="text" class="inputTiny" name="vars[quantiteOpportunite_ligne]" value="<?= $ARR['quantiteOpportunite_ligne'] ?>" placeholder="quantité" >
						</td >
						<td > <?= idioma("Produit") ?> </td >
						<td >
							<input  id="lg1" required  datalist_input_name = "vars[idproduit]" datalist_input_value = "<?= $ARR['idproduit'] ?>" datalist = "app/app_select" populate name = "vars[nomProduit]" value="<?= $ARR['nomProduit'] ?>" paramName = "search" vars = "table=produit" type = "text"/>
						</td >
					</tr >
				</table ></div>
			</div >
		<div class="buttonZone" >
			<input type="submit" value="Valider" >
			<input type="button" value="Annuler" class="cancelClose" >
		</div >
	</form >
</div >
<script>
	$('lg1').cloneCopy($('lg2'),{spy:'lg1'})
</script>
<? if (!empty($ARR['idclient'])) {
	$add_table = 'client';
	$add_value = $ARR['idclient'];
} elseif (!empty($ARR['idprodspect'])) {
	$add_table = 'prospect';
	$add_value = $ARR['idprospect'];
} elseif (!empty($ARR['idcontact'])) {
	$add_table = 'contact';
	$add_value = $ARR['idcontact'];
} ?>
<? if (!empty($add_table)) { ?>
	<div class="enteteFor" >
		<div style="width:750px;"
		     class="fond_noir color_fond_noir borderb shadow inline" ><?= skelMdl::cf_module('app/app/app_fiche_mini', ['table' => $add_table, 'table_value' => $add_value]) ?></div >
	</div >
<? } ?>
