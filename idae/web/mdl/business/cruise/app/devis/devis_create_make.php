<?php
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/' . BUSINESS . '/app/devis/';

	$APP_DV = new App('devis');
	$APP_PR = new App('produit');
	// var_dump($_POST);
	$time = time();
	ini_set('display_errors', 55);
	if (!empty($_POST['idclient'])) {
		$idclient           = (int)$_POST['idclient'];
		$rs = $APP_DV->query(array('idclient' => $idclient), 0, 1);
		$arrP               = $rs->getNext();
		$_POST['idproduit'] = ['idproduit'];
	}

	if (!empty($_POST['idproduit'])) {
		$idproduit = $_POST['idproduit'];
	}
	$arrProduit = $APP_PR->query_one(array('idproduit' => (int)$idproduit));
?>
<div class="titre_entete">
	<button onclick="dcm_el('div_produit_liste_devis').toggleContent();return false;"><?= idioma('Annuler') ?></button>
	<?= $idproduit ?>
	&nbsp;|&nbsp;
	<?= $arrProduit['nomProduit'] ?>
	<?= $arrProduit['nomFournisseur'] ?>
</div>
<input type="hidden" name="vars[idproduit]" value="<?= $idproduit ?>">
<input type="hidden" name="vars[idfournisseur]" value="<?= $arrProduit['idfournisseur'] ?>">
<input type="hidden" name="vars[idtransport]" value="<?= $arrProduit['idtransport'] ?>">
<input type="hidden" name="vars[idtransport_gamme]" id="idtransport_gamme" value="">
<input type="hidden" name="nomTransport_gamme" id="nomTransport_gamme" value="">
<div class="margin padding flex_h  ">

	<div class="padding"><br><br>
		<button type="submit" value="valider ce devis">
			<i class="fa fa-file"></i> <?=idioma('valider ce devis')?>

		</button>
	</div>
	<div main_auto_tree class="flex_main" style="width:50%;">
		<div auto_tree>
			<div class="trait bold"><?= idioma('Passagers') ?></div>
		</div>
		<div class="autoBlock">
			<br>
			<table class="table_form">
				<tr>
					<td><?= idioma('adultes') ?>
					<td>
						<select name="vars[nbreAdulteDevis]">
							<option value="1">1 adulte</option>
							<option value="2">2 adultes</option>
							<option value="3">3 adultes</option>
							<option value="4">4 adultes</option>
							<option value="5">5 adultes</option>
							<option value="6">6 adultes</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?= idioma('Enfants') ?>
					<td>
						<select name="vars[nbreEnfantDevis]" id="nombreEnfantDevis">
							<option value="">aucun enfant</option>
							<option value="1">1 enfant</option>
							<option value="2">2 enfants</option>
							<option value="3">3 enfants</option>
							<option value="4">4 enfants</option>
							<option value="5">5 enfants</option>
							<option value="6">6 enfants</option>
						</select>
					</td>
				</tr>
			</table>
			<label class="nolabel" id="elem_enf"></label>
		</div>
		<div auto_tree>
			<div class="trait bold"><?= idioma('Date de départ') ?></div>
		</div>
		<div class="autoBlock">
			<br>
			<div class="applink">
				<a onClick="ajaxMdl('production/produittarif/produit_tarif_create','Ajouter une date de départ','idproduit=<?= $idproduit ?>')">
					<i class="fa fa-plus"></i>
					Ajouter
				</a>
			</div>
			<div class="retrait">
				<?= skelMdl::cf_module($path_to_devis . 'devis_create_date', array('idproduit' => (int)$idproduit), $_SESSION['idagent']) ?>
			</div>
			<br>
		</div>
		<div auto_tree>
			<div class="trait bold"><?= idioma('Cabine') ?></div>
		</div>
		<div class="autoBlock">
			<br>
			<div class="retrait">
				<?= skelMdl::cf_module($path_to_devis . 'devis_create_cabine', array('idproduit' => $idproduit)) ?>
			</div>
		</div>
	</div>
</div>
<div id="devis_spy"></div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, new Element, .update, .show, .hide) to native DOM, with file-local
	 * `dcm_` helpers. toggleContent is not a shim call: engine/methods.js puts
	 * it on HTMLElement.prototype.
	 */
	function dcm_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function dcm_show(node) { if (node) node.style.display = ''; return node; }
	function dcm_hide(node) { if (node) node.style.display = 'none'; return node; }

	/**
	 * Prototype's `new Element(tag, attributes)`. `className` and `style` are
	 * attribute *aliases* there, not properties, so they are written through
	 * setAttribute like the rest — 'className' mapping to the class attribute.
	 */
	function dcm_create(tag, attributes) {
		var node = document.createElement(tag);
		for (var name in attributes) {
			if (!Object.prototype.hasOwnProperty.call(attributes, name)) continue;
			node.setAttribute(name === 'className' ? 'class' : name, attributes[name]);
		}
		return node;
	}

	monitor_enf = function (val) {
		var zone = dcm_el('elem_enf');
		zone.innerHTML = "";
		if (val > 0) {
			var enf = dcm_create('input', {type: 'checkbox', value: '1', name: 'partageCabineDevis', 'checked': 'checked'});
			var ccenf = dcm_create('span', {className: 'margin inline borderl', 'style': 'vertical-align:middle;'});
			ccenf.innerHTML = '&nbsp;Chambre partagée&nbsp;'
			zone.appendChild(enf)
			dcm_show(zone).appendChild(ccenf)
		} else {
			// `.hide().update()` — update with no argument empties the node.
			dcm_hide(zone).innerHTML = ''
		}
	}
	reloadTarif = function (id) {
		reloadModule('app/app_custom/devis/devis_create_cabine', '*', 'idproduit=<?=$idproduit?>&idproduit_tarif=' + id)
	}
	//reloadTarif(dcm_el('idproduit_tarif').value);
	monitor_enf(dcm_el('nbreEnfantDevis').value);
</script>
<style>
	.demi {
		width: 50%;
	}

	table.tabledevis tr td {
		padding: 5px !important;
	}
</style>
