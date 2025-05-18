<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('opportunite');
	$APPS = new App('opportunite_statut');

	$time = time();
	//
	$idopportunite = (int)$_POST['table_value'];
	$ARR = $APP->query_one(['idopportunite' => $idopportunite]);
	// ?
	$rsTT = $APPS->find()->sort(['ordreOpportunite_statut' => 1]);
	$selectST = fonctionsProduction::getSelectMongo('vars[idopportunite_statut]', $rsTT, 'idopportunite_statut', 'nomOpportunite_statut', $ARR['idopportunite_statut']);

	$rsA = $APP->plug('sitebase_base', 'agent')->find()->sort(['nomAgent' => 1]);
	$selectA = fonctionsProduction::getSelectMongo('vars[idagent]', $rsA, 'idagent', 'prenomAgent', $ARR['idagent']);
	//
?>

<div style="width:950px;" class="blanc" >





		<div class="flex_h" style="width:100%;" >

			<div class="flex_main borderr padding" >
				<form class="nth2 " action="<?= ACTIONMDL ?>app/actions.php" id="formCreateOpportunite<?= $time ?>" name="formCreateOpportunite<?= $time ?>" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
					<input type="hidden" name="F_action" id="F_action" value="app_update" />
					<input type="hidden" name="table" value="opportunite" />
					<input type="hidden" name="table_value" value="<?= $idopportunite ?>" />
					<input type="hidden" name="idagent_writer" value="<?= $_SESSION['idagent']; ?>" />
					<div class="flex_h flex_align_top flex_margin nth2">
						<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-euro"></i></div>
						<div class="borderb flex_main">
							<table class="table_form tablepadding margin">
								<tr>
									<td> <?= idioma("CCN Proposé") ?> </td>
									<td>
										<input class="" name="vars[ccnOpportunite]" value="<?= $ARR['ccnOpportunite'] ?>" type="text">
									</td>
									<td> <?= idioma("vmmNB estimé") ?> </td>
									<td>
										<input class="" name="vars[vmmNBOpportunite]" value="<?= $ARR['vmmNBOpportunite'] ?>" type="text">
									</td>
								</tr>
								<tr>
									<td> <?= idioma("CCCOUL Proposé") ?> </td>
									<td>
										<input class="" name="vars[cccoulOpportunite]" value="<?= $ARR['cccoulOpportunite'] ?>" type="text">
									</td>
									<td> <?= idioma("vmmCouleur estimé") ?> </td>
									<td>
										<input class="" name="vars[vmmCouleurOpportunite]" value="<?= $ARR['vmmCouleurOpportunite'] ?>" type="text">
									</td>
								</tr>
								<tr>
									<td> <?= idioma("CA attendu") ?> </td>
									<td>
										<input type="text" name="vars[montantOpportunite]" value="<?= $ARR['montantOpportunite'] ?>" placeholder="Valeur en euros">
									</td>
									<td>
										<?= idioma("Rachat") ?>
									</td>
									<td>
										<input required="required" type="text" name="vars[montantRachatOpportunite]" value="<?= $ARR['montantRachatOpportunite'] ?>" placeholder="Valeur en euros">
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="flex_h flex_align_top flex_margin nth2">
						<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-calendar"></i></div>
						<div class="borderb flex_main">
							<table class="table_form tablepadding margin">
								<tr>
									<td> <?= idioma("Début négociation") ?> </td>
									<td>
										<input class="validate-date-au" type="text" id="dateDebutOpportunite<?= $rand ?>" value="<?= $ARR['dateDebutOpportunite'] ?>" name="vars[dateDebutOpportunite]">
									</td>
									<td> <?= idioma("Echéance") ?> </td>
									<td colspan="3">
										<input required class="validate-date-au" type="text" id="dateFinOpportunite<?= $rand ?>" value="<?= $ARR['dateFinOpportunite'] ?>" name="vars[dateFinOpportunite]">
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="flex_h flex_align_top flex_margin nth2">
						<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-graduation-cap"></i></div>
						<div class="borderb flex_main">
							<table class="table_form tablepadding margin">
								<tr>
									<td>
										<?= idioma("Statut") ?>
									</td>
									<td colspan="3"><?= $selectST ?></td>
								</tr>
								<tr>
									<td>
										<?= idioma("Probabilité") ?>
									</td>
									<td colspan="3">
										<input class="inline" style="width:250px;" type="range" max="4" value="<?= $ARR['rangOpportunite'] ?>" name="vars[rangOpportunite]" onchange="$(this).next().value=value">
										<output class="inline"></output>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="flex_h flex_align_top flex_margin nth2">
						<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-comment"></i></div>
						<div class="borderb flex_main">
							<table class="table_form tablepadding margin">
								<tr>
									<td>
										<?= idioma("Commentaires") ?>
									</td>
									<td colspan="3">
										<textarea class="inputLarge" name="vars[descriptionOpportunite]"><?= $ARR['descriptionOpportunite'] ?> </textarea>
									</td>
								</tr>
							</table>
						</div>
					</div>

					<div class="buttonZone" >
						<input type="submit" value="Valider" >
						<input type="button" value="Annuler" class="cancelClose" >
					</div >
				</form >
			</div >

			<div style="width:30%;height:100%;overflow:hidden;" class="flex_v"  >
				<div class="titre_entete applink alignright" >
					<a act_target="ss_zone_p<?= $idopportunite ?>" mdl="app/app/app_create" vars="table=opportunite_ligne&vars[idopportunite]=<?= $idopportunite ?>" options="{scope:'opportunite_ligne'}" >
						<i class="fa fa-plus-circle" ></i > <?= idioma('Ajouter composantes') ?>
					</a >
				</div >
				<div id="ss_zone_p<?= $idopportunite ?>" ></div >
				<div class="ededed flex_main" id="zone_p<?= $idopportunite ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_micro" > </div >
			</div >
		</div >
</div >

<? if (!empty($ARR['idclient'])) {
	$add_table = 'client';
	$add_value = $ARR['idclient'];
} elseif (!empty($ARR['idprospect'])) {
	$add_table = 'prospect';
	$add_value = $ARR['idprospect'];
} elseif (!empty($ARR['idcontact'])) {
	$add_table = 'contact';
	$add_value = $ARR['idcontact'];
} ?>
<div class="enteteFor" >
	<? if (!empty($add_table)) { ?>
		<div class=" " ><?= skelMdl::cf_module('app/app/app_menu', ['table' => $add_table, 'table_value' => $add_value]) ?>

		</div >
	<? } ?>
</div >
<div class="titreFor">
	<?=$ARR['nomOpportunite']?> <?=$ARR['nomClient']?>
</div>
<script >
	load_table_in_zone('table=opportunite_ligne&vars[idopportunite]=<?=$idopportunite?>', 'zone_p<?=$idopportunite?>');
</script >

