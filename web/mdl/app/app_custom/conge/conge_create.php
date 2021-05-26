<?
	include_once($_SERVER['CONF_INC']);
	$APP    = new App('conge');
	$APP_T  = new App('conge_type');
	$APP_ST = new App('conge_statut');
	$time   = time();
	// ?

	$rsTT     = $APP_T->find();
	$selectTT = fonctionsProduction::getSelectMongo('vars[idconge_type]', $rsTT, 'idconge_type', 'nomConge_type', '');

	$rsST     = $APP_ST->find(['codeConge_statut' => ['$ne' => 'END']])->sort(array('ordreConge_statut' => 1));
	$selectST = fonctionsProduction::getSelectRadio('vars[idconge_statut]', $rsST, 'idconge_statut', 'nomConge_statut', '', true,'required');

	$rsA     = $APP->plug('sitebase_base', 'agent')->find()->sort(array('nomAgent' => 1));
	$selectA = fonctionsProduction::getSelectMongo('vars[idagent]', $rsA, 'idagent', ['prenomAgent', 'nomAgent'], $_SESSION['idagent'], true);

	//
	$dateDebutConge = (empty($_POST['vars']['dateDebutConge'])) ? date('Y-m-d', time() + 86400) : $_POST['vars']['dateDebutConge'];
	$dateFinConge   = (empty($_POST['vars']['dateFinConge'])) ? '' : $_POST['vars']['dateFinConge'];
?>
<div style="width:450px;">
	<div class="titre_entete"><?= idioma('Demande de congé') ?></div>
	<form class="form" data-dyn_datetime action="<?= ACTIONMDL ?>app/actions.php" id="formCreateConge" name="formCreateConge" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" id="F_action" value="app_create"/>
		<input type="hidden" name="proprietaireAgent" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="vars[idagent_writer]" value="<?= $_SESSION['idagent']; ?>"/>
		<input type="hidden" name="vars[estActifConge]" value="0"/>
		<? if (!empty($_POST['vars']['idagent'])) { ?>
			<input type="hidden" name="vars[idagent]" value="<?= $_POST['vars']['idagent']; ?>"/>
		<? } ?>
		<input type="hidden" name="reloadModule[app/app_conge/app_conge_reload]" value="*"/>
		<input type="hidden" name="afterAction[app/app/app_create]" value="close"/>
		<input type="hidden" name="table" value="conge"/>
		<div class=" ededed ">
			<div class="padding margin border4  blanc">
				<? if (empty($_POST['vars']['idagent'])) { ?>
					<table class="tablemiddle tablepadding" style="width:100%;table-layout:auto">
						<tr>
							<td style="width:80px;"><?= idioma("Agent") ?></td>
							<td><?= $selectA ?></td>
						</tr>
					</table>
				<? } ?>
				<table class="tablemiddle tablepadding" style="width:100%;table-layout:auto">
					<tr>
						<td style="width:80px;"><?= idioma("Type") ?></td>
						<td><?= $selectTT ?></td>
					</tr>
					<tr>
						<td style="width:80px;"><?= idioma("Statut") ?></td>
						<td><?= $selectST ?></td>
					</tr>
				</table>
				<table class="tablemiddle tablepadding" style="width:100%;table-layout:auto">
					<tr class="padding">
						<td style="width:80px;"><?= idioma("Début") ?></td>
						<td>
							<input class="validate-date-au" required="required" type="text" id="dateDebut" name="vars[dateDebutConge]" value="">
						</td>
						<td><i class="fa fa-clock-o"></i>&nbsp;
							<select id="heureDebut" name="vars[heureDebutConge]">
								<option value="AM">AM</option>
								<option value="PM">PM</option>
							</select>
						</td>
					</tr>
					<tr class="padding">
						<td><?= idioma("Fin") ?></td>
						<td>
							<input  class="validate-date-au" required="required"  type="text" id="dateFin" name="vars[dateFinConge]" value="">
						</td>
						<td><i class="fa fa-clock-o"></i>&nbsp;
							<select id="heureFin" name="vars[heureFinConge]">
								<option value="AM">AM</option>
								<option value="PM" selected="selected">PM</option>
							</select>
						</td>
					</tr>
					<tr class="padding">
						<td><?= idioma("Duree") ?></td>
						<td>
							<input class="inputSmall" type="text" id="duree" name="vars[dureeConge]" value="">
						</td>
						<td>
						</td>
					</tr>
				</table>
				<div class="maingui   margin">
					<table class="tableGui tablepadding" style="width:100%">
						<tr class="padding">
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
	getDuree = function (node) { // father
		date_deb = node.select('#dateDebut').first();
		date_fin = node.select('#dateFin').first();

		val = 0;
		dd = getDate($(date_deb).value);
		df = getDate($(date_fin).value);

		a = eval(dayDiff($(date_deb).value, $(date_fin).value));

		if (node.select('#heureDebut') && node.select('#heureFin')) {

			h = node.select('#heureDebut').first().value;
			f = node.select('#heureFin').first().value;
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
		if (node.select('#duree')) {
			node.select('#duree').first().value = eval(a) + eval(val);
		}

	}
	getDate = function (strDate) {
		day = strDate.substring(0, 2);
		month = strDate.substring(3, 5);
		year = strDate.substring(6, 10);
		d = new Date();
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
