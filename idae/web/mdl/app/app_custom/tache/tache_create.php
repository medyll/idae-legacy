<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();
	$time = time();
	// ?
	$APP_ST = new App('tache_statut');
	$APP_TY = new App('tache_type');
	if(!empty($_POST['add_table'])  ):
		$_POST['vars'][ 'id'.$_POST['add_table']] = $_POST['add_table_value'];


	endif;

	$rsTT = $APP_TY->find();
	$selectTT = fonctionsProduction::getSelectMongo('vars[idtache_type]', $rsTT, 'idtache_type', 'nomTache_type');
	$rsST = $APP_ST->find()->sort(array('ordreTache_statut' => 1));
	$selectST = fonctionsProduction::getSelectMongo('vars[idtache_statut]', $rsST, 'idtache_statut', 'nomTache_statut', false, true);
	$rsA = $APP->plug('sitebase_base', 'agent')->find()->sort(array('nomAgent' => 1));
	$selectA = fonctionsProduction::getSelectMongo('vars[idagent]', $rsA, 'idagent', 'prenomAgent', $_SESSION['idagent']);
	//
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];

?>

<div style="width:850px;" class="blanc" id="tache_maker">
	<div class="titre_entete ededed">
		<?=idioma('Nouvelle tache')?>
	</div>
	<div class="barre_entete">
		<?=$APP->vars_to_titre($vars)?>
	</div>
	<form action="<?= ACTIONMDL ?>app/actions.php" id="formCreateTache<?= $time ?>" name="formCreateTache<?= $time ?>"
	      onsubmit="ajaxFormValidation(this);return false;" auto_close="true">
		<input type="hidden" name="F_action" id="F_action" value="app_create"/>
		<input type="hidden" name="table" value="tache"/>
		<input type="hidden" name="proprietaireAgent" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="idagent_writer" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="reloadModule[app/app_planning/app_planning_tache_reload]" value="*"/>
		<input type="hidden" name="afterAction[app/app_custom/tache/tache_create]" value="close"/>
		<input type="hidden"    name = "vars[m_mode]"   value ="1"/>
		<? if(!empty($_POST['add_table'])): ?>
			<input type="hidden" name="add_table" value="<?= $_POST['add_table']; ?>"/>
			<input type="hidden" name="add_table_value" value="<?= $_POST['add_table_value']; ?>"/>
			<input type="hidden" name="vars[id<?= $_POST['add_table']; ?>]" value="<?= $_POST['add_table_value']; ?>"/>
		<? endif; ?>

		<? foreach ($vars as $key => $input): ?>
			<input type = "hidden" name = "vars[<?= $key ?>]" value = "<?= $input ?>" >
		<? endforeach; ?>
		<div class="none">
			<table class="tablemiddle" style="width:100%;table-layout:auto">
				<tr>
					<td style="width:80px;"><label>Rappel</label></td>
					<td style="width:120px;"><select name="timeRappelTache">
							<option>aucun</option>
							<option value="5">5 min</option>
							<option value="15">15 min</option>
							<option value="20">20 min</option>
							<option value="30">30 min</option>
							<option value="60">1 heure</option>
							<option value="120">2 heures</option>
							<option value="180">3 heures</option>
							<option value="3600">1 jour</option>
							<option value="7200">2 jours</option>
						</select></td>
					<td style="width:120px;"><label class="nolabel">
							<input type="checkbox" name="codeRappelTache" value="MAIL">
							Rappel par mail</label></td>
					<td onclick="$('mdlTachePeriodicite<?= $time ?>').toggle();" class="alignright  cursor applink "><a>
							<i class="fa fa-caret-down"></i>
							&nbsp;Périodicité</a></td>
				</tr>
			</table>
			<div id="mdlTachePeriodicite<?= $time ?>" style="display:none" class="borderb bordert ededed">
				<?= skelMdl::cf_module('app/app_custom/tache/tache_periode', $_POST); ?>
			</div>
		</div>
		<? if(!empty($_POST['add_table'])):
			$APP_TMP = new App($_POST['add_table']);
			$ARR_TMP = $APP_TMP->findOne(['id'.$_POST['add_table']=>(int)$_POST['add_table_value']]);
		?>
				<div class="padding borderb ededed none">
					<?=$ARR_TMP['nom'.ucfirst($_POST['add_table'])]?>
				</div>
			<?
			endif;
			?>
		<div class=" ">
			<? if (!empty($_POST['vars']['heureDebutTache'])) { ?>
				<input type="hidden" name="vars[heureDebutTache]" value="<?= $_POST['vars']['heureDebutTache'] ?>"/>
			<? } ?>
			<? if (!empty($_POST['vars']['dateDebutTache'])) { ?>
				<input type="hidden" name="vars[dateDebutTache]" value="<?= date_mysql($_POST['vars']['dateDebutTache']) ?>"/>
			<? } ?>
			<div class=" maingui">
				<table class="table_form tablemiddle" style="width:100%;table-layout:auto">

					<? if (empty($_POST['add_table']) && empty($_POST['vars']['idclient'])) { ?>
						<tr id="tache_maker_first">
							<td class="ededed borderb paddingb" colspan="8" style="padding:0!important;">
								<?=skelMdl::cf_module('app/app_field_add',array('module_value'=>123,'field'=>['prospect','client']),123)?>
							</td>
						</tr>
					<? } ?>
					<? if ( empty($_POST['vars']['idclient'])) { ?>
						<tr>
							<td class="ededed borderb paddingb" colspan="8" style="padding:0!important;">
								<?=skelMdl::cf_module('app/app_field_add',array('module_value'=>456,'field'=>['contact']),456)?>
							</td>
						</tr>
					<? } else{ ?>
						<tr>
							<td class="ededed borderb paddingb" colspan="8" style="padding:0!important;">
								<?=skelMdl::cf_module('app/app_field_add',array('vars'=>['idclient'=>$_POST['vars']['idclient']],'module_value'=>456,'field'=>['contact']),456)?>
							</td>
						</tr>

					<? } ?>
					<tr>
						<td style="width:80px;"><label>
								<?= idioma("Objet") ?>
							</label></td>
						<td colspan="7"><input required="required" style="width:100%" type="text" name="vars[nomTache]" value=""></td>
					</tr>

					<tr>
						<td style="width:80px;"><label>
								<?= idioma("Début") ?>
							</label></td>
						<td><? if (empty($_POST['vars']['dateDebutTache'])) { ?>
								<input  class="validate-date-au" type="text" id="dateDebutTache<?= $rand ?>"
								       name="vars[dateDebutTache]" value="<?= date('d/m/Y', time() + 86400); ?>">
							<?
							}
							else {
								?>
								<?= date_fr($_POST['vars']['dateDebutTache']); ?>
							<? } ?></td>
						<td><i class="fa fa-clock-o"></i>&nbsp;<? if (empty($_POST['vars']['heureDebutTache'])) { ?>
								<input type="text" class="heure inputSmall" id="heureDebutTache<?= $rand ?>"
								       name="vars[heureDebutTache]" value="09:00:00">
							<?
							}
							else {
								?>
								<?= maskHeure($_POST['vars']['heureDebutTache']); ?>
							<? } ?></td>
						<td style="width:70px;" class="alignright"><label>
								<?= idioma("Type") ?>
							</label></td>
						<td style="width:130px;"><?= $selectTT ?></td>
						<td style="width:70px;"><label>
								<?= idioma('Agent') ?>
							</label></td>
						<td style="width:120px;"><?= $selectA ?></td>
					</tr>
					<tr>
						<td><label>
								<?= idioma("Fin") ?>
							</label></td>
						<td><? if (empty($_POST['vars']['dateDebutTache'])) { ?>
								<input  class="validate-date-au" type="text" id="dateFinTache<?= $rand ?>"
								       name="vars[dateFinTache]" value="">
							<?
							}
							else {
								?>
								<input  class="validate-date-au" type="text" id="dateFinTache<?= $rand ?>"
								       name="vars[dateFinTache]" value="<?= date_fr($_POST['vars']['dateDebutTache']); ?>">
							<? } ?></td>
						<td><i class="fa fa-clock-o"></i>&nbsp;<input class="inputSmall heure" type="text" name="vars[heureFinTache]" value=""></td>
						<td class="alignright"><label>
								<?= idioma("Statut") ?>
							</label></td>
						<td><?= $selectST ?></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<div class="maingui   margin">
					<table class="tableGui" style="width:100%">
						<tr>
							<td><label>
									<?= idioma("Commentaires") ?>
								</label>
								<br/>
								<textarea style="width:100%" name="vars[descriptionTache]"></textarea></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="Valider">
			<input type="button" value="Annuler" class="cancelClose">
		</div>
	</form>
</div>


<script>
	$('tache_maker_first').on('dom:act_change',function(e){
		reloadModule('app/app_field_add','456','run=1&add_field=contact&module_value=456&field[]=contact&vars[id'+e.memo.table+']='+ e.memo.id)
	})
	$('dateDebutTache<?=$rand?>').observe('focus', function () {
		$('dateDebutTache<?=$rand?>').up('form').dateFinTache.value = $('dateDebutTache<?=$rand?>').value
	}.bind(this))
	$('dateDebutTache<?=$rand?>').observe('blur', function () {
		$('dateDebutTache<?=$rand?>').up('form').dateFinTache.value = $('dateDebutTache<?=$rand?>').value
	}.bind(this))
</script>
<script>
	addContact = function () {
		idsociete = $('formCreateTache<?=$time?>').select('[name=societe_idsociete]').first().value
		ajaxMdl('societehaspersonne/mdlSocieteHasPersonneCreate', '<?=idioma('Ajouter un contact')?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: Insertion.Top});
	}
	launchContact = function () {
		idsociete = $('formCreateTache<?=$time?>').select('[name=societe_idsociete]').first().value
		ajaxInMdl('societehaspersonne/mdlSocieteHasPersonneAdd', 'add_taskOnPersonne<?=$time?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: Insertion.Top});
	}
</script>
