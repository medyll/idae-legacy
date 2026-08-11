<?php
	include_once($_SERVER['CONF_INC']);

	$APP    = new App('conge');
	$APP_A  = new App('agent');
	$APP_T  = new App('conge_type');
	$APP_ST = new App('conge_statut');
	$APP_TY = new App('conge_type');

	$idconge = (int)$_POST['table_value'];

	$arr_c = $APP->query_one(array('idconge' => $idconge));

	$time = time();
	// ?
	$rsTT     = $APP_T->find();
	$selectTT = fonctionsProduction::getSelectMongo('vars[idconge_type]', $rsTT, 'idconge_type', 'nomConge_type', $arr_c['idconge_type'], true);
	$rsA      = $APP_A->find()->sort(array('nomAgent' => 1));
	$selectA  = fonctionsProduction::getSelectMongo('idagent', $rsA, 'idagent', 'prenomAgent', $_SESSION['idagent']);
	$rsST     = $APP_ST->find(['codeConge_statut' => ['$ne' => 'END']])->sort(array('ordreConge_statut' => 1));
	$selectST = fonctionsProduction::getSelectRadio('vars[idconge_statut]', $rsST, 'idconge_statut', 'nomConge_statut', $arr_c['idconge_statut'], true);
	//
	$dateDebutConge = (empty($_POST['vars']['dateDebutConge'])) ? date('Y-m-d', time() + 86400) : $_POST['vars']['dateDebutConge'];
	$dateFinConge   = (empty($_POST['vars']['dateFinConge'])) ? '' : $_POST['vars']['dateFinConge'];

	if ($arr_c['codeConge_statut'] == 'END') {
		?>
		<div class="padding_more blanc"> Ce congé est déja validé</div>
		<?php return;
	}

?>
<div style="width:450px;">
	<form data-dyn_datetime action="<?= ACTIONMDL ?>app/actions.php" id="formCreateConge" name="formCreateConge" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" id="F_action" value="app_update"/>
		<input type="hidden" name="reloadModule[app/app_conge/app_conge_reload]" value="*"/>
		<input type="hidden" name="table" value="conge"/>
		<input type="hidden" name="table_value" value="<?= $idconge ?>"/>
		<div class="ededed ">
			<div class="padding margin border4 ededed">
				<table class="tablemiddle tablepadding" style="width:100%;table-layout:auto">
					<tr>
						<td style="width:80px;"><?= idioma("Type") ?></td>
						<td><?= $selectTT ?></td>
					</tr>
					<?php if ($arr_c['idagent'] == $_SESSION['idagent']) { ?>
						<tr>
							<td style="width:80px;"><?= idioma("Statut") ?></td>
							<td><?= $selectST ?></td>
						</tr>
					<?php } ?>
				</table>
				<table class="tablemiddle tablepadding" style="width:100%;table-layout:auto">
					<tr>
						<td style="width:80px;"><?= idioma("Début") ?></td>
						<td>
							<input onchange="" class="validate-date-au" type="text" id="dateDebut" name="vars[dateDebutConge]" value="<?= date_fr($arr_c['dateDebutConge']) ?>">
						</td>
						<td><i class="fa fa-clock-o"></i>&nbsp;
							<select id="heureDebut" name="vars[heureDebutConge]">
								<option value="AM">AM</option>
								<option value="PM">PM</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= idioma("Fin") ?></td>
						<td>
							<input class="validate-date-au" type="text" id="dateFin" name="vars[dateFinConge]" value="<?= date_fr($arr_c['dateFinConge']) ?>">
						</td>
						<td><i class="fa fa-clock-o"></i>&nbsp;
							<select id="heureFin" name="vars[heureFinConge]">
								<option value="AM">AM</option>
								<option value="PM" selected="selected">PM</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= idioma("Duree") ?></td>
						<td>
							<input class="inputSmall" type="text" id="duree" name="vars[dureeConge]" value="<?= $arr_c['dureeConge'] ?>">
						</td>
						<td>
						</td>
					</tr>
				</table>
				<div class="maingui   margin">
					<table class="tableGui tablepadding" style="width:100%">
						<tr>
							<td>
								<label>
									<i class="fa fa-comments-o"></i>&nbsp;<?= idioma("Commentaires") ?>
								</label>
								<br/>
								<textarea style="width:100%" name="vars[commentaireConge]"></textarea>
							</td>
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
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .select, .first) to native DOM, with file-local `cgu_` helpers.
	 */
	function cgu_one(node, selector) {
		return node ? node.querySelector(selector) : null;
	}

	getDuree = function (node) { // father
		var date_deb = cgu_one(node, '#dateDebut');
		var date_fin = cgu_one(node, '#dateFin');
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
		var heureDebut = cgu_one(node, '#heureDebut');
		var heureFin = cgu_one(node, '#heureFin');
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
		var duree = cgu_one(node, '#duree');
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
