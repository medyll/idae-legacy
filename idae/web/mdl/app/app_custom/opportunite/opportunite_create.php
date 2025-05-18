<?
	include_once($_SERVER['CONF_INC']);
	$APP  = new App('opportunite');
	$APPS = new App('opportunite_statut');

	$time = time();
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
	// ?
	$rsTT     = $APPS->find()->sort(['ordreOpportunite_statut' => 1]);
	$selectST = fonctionsProduction::getSelectMongo('vars[idopportunite_statut]', $rsTT, 'idopportunite_statut', ['nomOpportunite_statut'], 1);

	$rsA     = $APP->plug('sitebase_base', 'agent')->find()->sort(['nomAgent' => 1]);
	$selectA = fonctionsProduction::getSelectMongo('vars[idagent]', $rsA, 'idagent', 'prenomAgent', $_SESSION['idagent']);

	//
	foreach ($vars as $key => $input):
		if ($key == 'idclient') {
			$_POST['add_table']       = 'client';
			$_POST['add_table_value'] = (int)$input;
		}
		if ($key == 'idprospect') {
			$_POST['add_table']       = 'prospect';
			$_POST['add_table_value'] = (int)$input;
		}
		//	$_POST['add_table']       = $key;
		//	$_POST['add_table_value'] = (int)$input;
	endforeach;
?>
<div class="blanc">
	<form action="<?= ACTIONMDL ?>app/actions.php" id="formCreateOpportunite<?= $time ?>" name="formCreateOpportunite<?= $time ?>" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" id="F_action" value="app_create"/>
		<input type="hidden" name="table" value="opportunite"/>
		<input type="hidden" name="editAfter" value="opportunite"/>
		<input type="hidden" name="dateCreationOpportunite" value="<?= date('d-m-Y') ?>">
		<input type="hidden" name="heureCreationOpportunite" value="<?= date('H:i') ?>:00">
		<input type="hidden" name="proprietaireAgent" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="idagent_writer" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="reloadModule[app/app_planning/app_planning_opportunite_reload]" value="*"/>
		<? if (!empty($_POST['add_table']) && !empty($_POST['add_table_value'])) { ?>
			<input type="hidden" name="vars[id<?= $_POST['add_table'] ?>]" value="<?= $_POST['add_table_value'] ?>">
		<? } ?>
		<? foreach ($vars as $key => $input): ?>
			<input type="hidden" name="vars[<?= $key ?>]" value="<?= $input ?>">
		<? endforeach; ?>
		<div class=" ">
			<div class="relative nth2">
				<div class="flex_h flex_align_top flex_margin nth2">
					<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-user"></i></div>
					<div class="borderb flex_main">
						<table class="table_form tablepadding margin">
							<tr>
								<td>
									<?= idioma('Agent') ?>
								</td>
								<td><?= $selectA ?></td>
							</tr>
							<? if (empty($_POST['add_table']) && (empty($_POST['vars']['idclient']) || empty($_POST['vars']['idprospect']))) { ?>
								<tr>
									<td colspan="2" style="padding:0!important;">
										<?= skelMdl::cf_module('app/app_field_add', array('field' => ['prospect', 'client'],'vars'=>['idagent'=>$_SESSION['idagent']])) ?>
									</td>
								</tr>
							<? } ?></table>
					</div>
				</div> 
				<div class="flex_h flex_align_top flex_margin nth2">
					<div class="padding aligncenter"><i class="textgris margin fa-2x fa fa-euro"></i></div>
					<div class="borderb flex_main">
						<table class="table_form tablepadding margin">
							<tr>
								<td> <?= idioma("CCN Proposé") ?> </td>
								<td>
									<input class="" name="vars[ccnOpportunite]" value="" type="text">
								</td>
								<td> <?= idioma("vmmNB estimé") ?> </td>
								<td>
									<input class="" name="vars[vmmNBOpportunite]" value="" type="text">
								</td>
							</tr>
							<tr>
								<td> <?= idioma("CCCOUL Proposé") ?> </td>
								<td>
									<input class="" name="vars[cccoulOpportunite]" value="" type="text">
								</td>
								<td> <?= idioma("vmmCouleur estimé") ?> </td>
								<td>
									<input class="" name="vars[vmmCouleurOpportunite]" value="" type="text">
								</td>
							</tr>
							<tr>
								<td> <?= idioma("CA attendu") ?> </td>
								<td>
									<input type="text" name="vars[montantOpportunite]" value="" placeholder="Valeur en euros">
								</td>
								<td>
									<?= idioma("Rachat") ?>
								</td>
								<td>
									<input required="required" type="text" name="vars[montantRachatOpportunite]" value="" placeholder="Valeur en euros">
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
									<input class="validate-date-au" type="text" id="dateDebutOpportunite<?= $rand ?>" value="<?= date('mm/dd/Y') ?>" name="vars[dateDebutOpportunite]">
								</td>
								<td> <?= idioma("Echéance") ?> </td>
								<td colspan="3">
									<input required class="validate-date-au" type="text" id="dateFinOpportunite<?= $rand ?>" name="vars[dateFinOpportunite]">
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
									<input class="inline" style="width:250px;" type="range" max="4" value="1" name="vars[rangOpportunite]" onchange="$(this).next().value=value">
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
								<td colspan="3"><?//=str_replace(APPMDL,'',__DIR__)?>
									<textarea class="inputLarge" name="vars[descriptionOpportunite]" onkeyup="$('oppo_zone').loadModule('app/app_custom/opportunite/opportunite_create_log','table=opportunite&descriptionOpportunite='+this.value)"></textarea>
								<div id="oppo_zone"></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="Valider">
			<input type="button" value="Annuler" class="cancelClose">
		</div>
	</form>
</div>
<div class="enteteFor">
	<? if (!empty($_POST['add_table']) && !empty($_POST['add_table_value'])) {
		$APP_TMP = new App($_POST['add_table']);
		$arr_tmp = $APP_TMP->findOne(['id'.$_POST['add_table']=>(int)$_POST['add_table_value']]);
		?>
		<div class=" "><?= skelMdl::cf_module('app/app/app_menu', ['table' => $_POST['add_table'], 'table_value' => $_POST['add_table_value']]) ?></div>
	<? } ?>
	<div class="titre_entete fond_noir color_fond_noir "><?= idioma('Nouvelle opportunité').' '.$arr_tmp['nom'.ucfirst($_POST['add_table'])] ?></div>
</div>
<script>
	$('dateDebutOpportunite<?=$rand?>').observe('focus', function () {
		$('dateDebutOpportunite<?=$rand?>').up('form').dateFinOpportunite.value = $('dateDebutOpportunite<?=$rand?>').value
	}.bind(this))
	$('dateDebutOpportunite<?=$rand?>').observe('blur', function () {
		$('dateDebutOpportunite<?=$rand?>').up('form').dateFinOpportunite.value = $('dateDebutOpportunite<?=$rand?>').value
	}.bind(this))
</script>
<script>
	addContact = function () {
		idsociete = $('formCreateOpportunite<?=$time?>').select('[name=societe_idsociete]').first().value
		ajaxMdl('societehaspersonne/mdlSocieteHasPersonneCreate', '<?=idioma('Ajouter un contact')?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {
			value: idsociete,
			insertion: Insertion.Top
		});
	}
	launchContact = function () {
		idsociete = $('formCreateOpportunite<?=$time?>').select('[name=societe_idsociete]').first().value
		ajaxInMdl('societehaspersonne/mdlSocieteHasPersonneAdd', 'add_taskOnPersonne<?=$time?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {
			value: idsociete,
			insertion: Insertion.Top
		});
	}
</script>
