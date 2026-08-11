<?php
	include_once($_SERVER['CONF_INC']);
	$APP     = new App('tache');
	$idtache = (int)$_POST['table_value'];
	$arr     = $APP->findOne(['idtache' => $idtache]);
	$time    = time();
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
	<div class="titre_entete">
		<?= idioma('Relancer une tache') ?>
	</div>
	<div class="barre_entete"><?= $arr['nomTache'] ?></div>
	<form action="<?= ACTIONMDL ?>app/actions.php" id="formCreateTache<?= $time ?>" name="formCreateTache<?= $time ?>" auto_close="auto_close" onsubmit="ajaxFormValidation(this);return false;" auto_close="true">
		<input type="hidden" name="F_action" id="F_action" value="app_create"/>
		<input type="hidden" name="table" value="tache"/>
		<input type="hidden" name="vars[idtache_link]" value="<?= $idtache ?>"/>
		<input type="hidden" name="vars[idagent]" value="<?= $arr['idagent'] ?>"/>
		<input type="hidden" name="vars[idprospect]" value="<?= $arr['idprospect'] ?>"/>
		<input type="hidden" name="vars[idclient]" value="<?= $arr['idclient'] ?>"/>
		<input type="hidden" name="vars[idcontact]" value="<?= $arr['idcontact'] ?>"/>
		<input type="hidden" name="reloadModule[app/app_planning/app_planning_tache_reload]" value="*"/>
		<input type="hidden" name="vars[m_mode]" value="1"/>
		<div class="flex_h">
			<div class="ededed borderr">
					<div class="aligncenter">
						<input type="text" id="movespy" name="vars[dateDebutTache]" class="titre2 aligncenter" readonly required="required">
					</div>
					<div class="padding">
						<div class="ededed" id="cal_<?= $time ?>">
							<?= skelMdl::cf_module('app/app_calendrier/app_calendrier') ?>
						</div>
					</div>
			</div>
			<div class=" padding">
				<div>
					<label class="padding label"><?= idioma("Objet") ?></label>
					<div class="retrait">
						<input type="text" name="vars[nomTache]" value="fwd: <?= $arr['nomTache'] ?>" class="inputLarge" required>
					</div>
				</div>
				<div>
					<label class="padding label"><?= idioma("Type") ?></label>
					<div class="retrait"><?= $selectTT ?></div>
				</div>
				<div>
					<label class="  label">
						<?= idioma("Commentaires") ?>
					</label>
					<div class="retrait">
						<textarea class="inputLarge" name="vars[descriptionTache]"><?= $arr['descriptionTache'] ?></textarea>
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
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .observe, .on, .up, .select, .first) to native DOM, with file-local
	 * `tf_` helpers.
	 *
	 * Insertion.Top is deliberately left: it is passed through to ajaxMdl /
	 * ajaxInMdl as an option value, so replacing it means changing those
	 * consumers too — out of scope for this file.
	 */
	function tf_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function tf_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	tf_el('cal_<?=$time?>').addEventListener('dom:act_click', function (event) {
		moveTache(event.memo.value)
	})
	moveTache = function (date) {
		tf_el('movespy').value = date;
	}
</script>
<script>
	// Two arguments, no selector: never delegation — the shim fell through to
	// Event.observe, so this is a plain listener.
	tf_el('tache_maker_first').addEventListener('dom:act_change', function (e) {
		reloadModule('app/app_field_add', '456', 'run=1&add_field=contact&module_value=456&field[]=contact&vars[id' + e.memo.table + ']=' + e.memo.id)
	})
	tf_el('dateDebutTache<?=$rand?>').addEventListener('focus', function () {
		tf_up(tf_el('dateDebutTache<?=$rand?>'), 'form').dateFinTache.value = tf_el('dateDebutTache<?=$rand?>').value
	})
	tf_el('dateDebutTache<?=$rand?>').addEventListener('blur', function () {
		tf_up(tf_el('dateDebutTache<?=$rand?>'), 'form').dateFinTache.value = tf_el('dateDebutTache<?=$rand?>').value
	})
</script>
<script>
	addContact = function () {
		var idsociete = tf_el('formCreateTache<?=$time?>').querySelector('[name=societe_idsociete]').value
		ajaxMdl('societehaspersonne/mdlSocieteHasPersonneCreate', '<?=idioma('Ajouter un contact')?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: Insertion.Top});
	}
	launchContact = function () {
		var idsociete = tf_el('formCreateTache<?=$time?>').querySelector('[name=societe_idsociete]').value
		ajaxInMdl('societehaspersonne/mdlSocieteHasPersonneAdd', 'add_taskOnPersonne<?=$time?>', 'valueModule=<?=$time?>&reloaded=true&societe_idsociete=' + idsociete, {value: idsociete, insertion: Insertion.Top});
	}
</script>
