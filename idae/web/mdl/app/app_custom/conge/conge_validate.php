<?php
	include_once($_SERVER['CONF_INC']);

	$APP     = new App('conge');
	$APP_A   = new App('agent');
	$APP_T   = new App('conge_type');
	$APP_ST  = new App('conge_statut');
	$idconge = (int)$_POST['table_value'];

	$arr_c = $APP->query_one(array('idconge' => $idconge));

	$time = time();
	// ?
	ini_set('display_errors', 0);
	$rsTT     = $APP_T->find();
	$selectTT = fonctionsProduction::getSelectMongo('vars[idconge_type]', $rsTT, 'idconge_type', 'nomConge_type',$arr_c['idconge_type']);
	$rsA      = $APP_A->find()->sort(array('nomAgent' => 1));

	$rsST     = $APP_ST->find(['codeConge_statut' => ['$ne' => 'BEFORE']])->sort(array('ordreConge_statut' => 1));
	$selectST = fonctionsProduction::getSelectRadio('vars[idconge_statut]', $rsST, 'idconge_statut', 'nomConge_statut', $arr_c['idconge_statut'], true);
	//
	$dateDebutConge = (empty($_POST['vars']['dateDebutConge'])) ? date('Y-m-d', time() + 86400) : $_POST['vars']['dateDebutConge'];
	$dateFinConge   = (empty($_POST['vars']['dateFinConge'])) ? '' : $_POST['vars']['dateFinConge'];
?>
<div style="width:450px;">
	<form   action="<?= ACTIONMDL ?>app/actions.php" id="formCreateConge" name="formCreateConge"
	      onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" id="F_action" value="app_update"/>
		<input type="hidden" name="reloadModule[app/app_conge/app_conge_reload]" value="*"/>
		<input type="hidden" name="table" value="conge"/>
		<input type="hidden" name="table_value" value="<?= $idconge ?>"/>
		<div class="padding borderb" >
			<div class="   "><?= skelMdl::cf_module('app/app/app_fiche_entete', ['table' => 'conge', 'table_value' => $idconge]) ?></div>
		</div>
		<div class="">
			<div class=" margin">
				<div class="padding margin">
					<label class="padding bold">
						<i class="fa fa-check textorange"></i>&nbsp;<?= idioma("Validation du congé") ?>
					</label>
					<div class="padding"><?= $selectST ?></div>
				</div>
				<div class="padding margin">
					<div class="padding">
						<label ><?= idioma("Type") ?></label>
						<div class="padding"><?= $selectTT ?></div>
					</div>
				</div>
				<div class="padding margin">
					<label class="padding">
						<i class="fa fa-comments-o"></i>&nbsp;<?= idioma("Commentaires") ?>
					</label>
					<br/>
					<textarea style="width:100%" name="vars[commentaireConge]"></textarea>
				</div>
			</div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="Valider">
			<input type="button" value="Annuler" class="cancelClose">
		</div>
	</form>
</div>
<div class="titreFor">
	<?= idioma('Valider congé') ?>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .select, .first) to native DOM, with file-local `cgv_` helpers.
	 */
	function cgv_one(node, selector) {
		return node ? node.querySelector(selector) : null;
	}

	getDuree = function (node) { // father
		var date_deb = cgv_one(node, '#dateDebut');
		var date_fin = cgv_one(node, '#dateFin');
		if (!date_deb || !date_fin) return;

		var val = 0;
		var dd = getDate(date_deb.value);
		var df = getDate(date_fin.value);

		var a = Number(dayDiff(date_deb.value, date_fin.value));

		// Was `if (node.select('#heureDebut') && node.select('#heureFin'))`.
		// Element#select returns an Array, so both operands were truthy even
		// when empty and the guard never guarded anything — the `.first().value`
		// two lines down then threw on a form without those fields. Now an
		// actual presence check.
		var heureDebut = cgv_one(node, '#heureDebut');
		var heureFin = cgv_one(node, '#heureFin');
		if (heureDebut && heureFin) {

			var h = heureDebut.value;
			var f = heureFin.value;
			if (h == 'PM') {
				val = -0.5
			}
			if (f == 'AM') {
				val = -0.5
			}
		}

		//

		while (dd < df) {
			var newDate = dd.setDate(dd.getDate() + 1);
			dd = new Date(newDate);

			if (dd.getDay() == 0) { // Sunday
				a -= 1;
			}

		}
		// Same broken-guard shape as above.
		var duree = cgv_one(node, '#duree');
		if (duree) {
			duree.value = a + val;
		}

	}
	getDate = function (strDate) {
		var day = strDate.substring(0, 2);
		var month = strDate.substring(3, 5);
		var year = strDate.substring(6, 10);
		var d = new Date();
		d.setDate(day);
		d.setMonth(month - 1);
		d.setFullYear(year);

		console.log(strDate, d)
		return d;
	}
	dayDiff = function (d1, d2) {
		d1 = getDate(d1)
		d2 = getDate(d2)
		d1 = d1.getTime() / 86400000;
		d2 = (86400000 + d2.getTime()) / 86400000;
		return new Number(d2 - d1).toFixed(0);
	}
</script>
