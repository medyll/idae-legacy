<?php
	include_once($_SERVER['CONF_INC']);
	$APP  = new App('tache');
	$idtache = (int)$_POST['table_value'];
	$arr = $APP->findOne(['idtache'=>$idtache]);
	$time = time();
	// ?
	$APP_ST = new App('tache_statut');
	$APP_TY = new App('tache_type');

	$rsTT     = $APP_TY->find();
	$selectTT = fonctionsProduction::getSelectMongo('vars[idtache_type]', $rsTT, 'idtache_type', 'nomTache_type');
	$rsST     = $APP_ST->find(['codeTache_statut' => 'END'])->sort(array('ordreTache_statut' => 1));
	$selectST = fonctionsProduction::getSelectMongo('vars[idtache_statut]', $rsST, 'idtache_statut', 'nomTache_statut', false, true);
	$rsA      = $APP->plug('sitebase_base', 'agent')->find()->sort(array('nomAgent' => 1));
	$selectA  = fonctionsProduction::getSelectMongo('vars[idagent]', $rsA, 'idagent', 'prenomAgent', $_SESSION['idagent']);
	//
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
?>

<div class="blanc">
	<div class="barre_entete">
		<?= idioma('Fermer une tache') ?>
	</div>
	<div class="titre_entete_menu"><?=$arr['nomTache']?></div>
	<form action="<?= ACTIONMDL ?>app/actions.php" id="formCreateTache<?= $time ?>" name="formCreateTache<?= $time ?>" onsubmit="ajaxFormValidation(this);return false;" auto_close="true">
		<input type="hidden" name="F_action" id="F_action" value="app_update"/>
		<input type="hidden" name="table" value="tache"/>
		<input type="hidden" name="table_value" value="<?=$idtache?>"/>

		<input type="hidden" name="reloadModule[app/app_planning/app_planning_tache_reload]" value="*"/>

		<input type="hidden" name="vars[m_mode]" value="1"/>


		<div class=" ">
			<div class=" maingui">
				<table class="table_form tablemiddle">
					<tr>
						<td><?= idioma("Statut") ?></td>
						<td><?= $selectST ?></td>
					</tr>
					<tr>
						<td>
							<?= idioma("Commentaires") ?>
						</td>
						<td>
							<textarea class="inputLarge" name="vars[descriptionTache]"><?=$arr['descriptionTache']?></textarea></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="Valider">
			<input type="button" value="Annuler" class="cancelClose">
		</div>
	</form>
</div>


<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .observe, .on, .up, .select, .first) to native DOM, with file-local
	 * `tcl_` helpers.
	 *
	 * app_socket only tests `options.insertion` for truthiness, then performs
	 * the top insertion itself. The old Insertion.Top function was never called.
	 */
	function tcl_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function tcl_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	// Two arguments, no selector: never delegation — the shim fell through to
	// Event.observe, so this is a plain listener.
	tcl_el('tache_maker_first').addEventListener('dom:act_change', function (e) {
		reloadModule('app/app_field_add', '456', 'run=1&add_field=contact&module_value=456&field[]=contact&vars[id' + e.memo.table + ']=' + e.memo.id)
	})
	tcl_el('dateDebutTache<?=$rand?>').addEventListener('focus', function () {
		tcl_up(tcl_el('dateDebutTache<?=$rand?>'), 'form').dateFinTache.value = tcl_el('dateDebutTache<?=$rand?>').value
	})
	tcl_el('dateDebutTache<?=$rand?>').addEventListener('blur', function () {
		tcl_up(tcl_el('dateDebutTache<?=$rand?>'), 'form').dateFinTache.value = tcl_el('dateDebutTache<?=$rand?>').value
	})
</script>
<script>
	addContact = function () {
		var idsociete = tcl_el('formCreateTache<?=$time?>').querySelector('[name=societe_idsociete]').value
		ajaxMdl('societehaspersonne/mdlSocieteHasPersonneCreate', '<?=idioma('Ajouter un contact')?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: true});
	}
	launchContact = function () {
		var idsociete = tcl_el('formCreateTache<?=$time?>').querySelector('[name=societe_idsociete]').value
		ajaxInMdl('societehaspersonne/mdlSocieteHasPersonneAdd', 'add_taskOnPersonne<?=$time?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: true});
	}
</script>
