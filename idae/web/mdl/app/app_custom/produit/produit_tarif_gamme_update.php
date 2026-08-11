<?php
	include_once($_SERVER['CONF_INC']);

	$APP_PROD = new App('produit');
	$APP_GAMME = new App('gamme');
	$APP_T_G = new App('transport_gamme');
	$APP_PT = new App('produit_tarif');
	//
	$uniquid = uniqid();
	$body = 'body' . $uniquid;

	$idproduit = (int)$_POST['idproduit'];
	$arrP = $APP_PROD->query_one(array('idproduit' => $idproduit));
	$GRDATE = $arrP['grilleDateProduit'];
	//
	$idproduit_type = (int)$arrP['idproduit_type'];


	$rsT = $APP_GAMME->query([], 0, 1); // array( 'idproduit_type' => (int)$idproduit_type )
	$rsPT = $APP_PT->query(array('idproduit' => (int)$idproduit), 0, 250);

	if (!empty($arrP['idtransport'])) {
		$rsT = $APP_T_G->query(array('idtransport' => (int)$arrP['idtransport']))->sort(array('ordreTransport_gamme' => 1,
		                                                                                      'ordreGamme'           => 1));
	}

	//
	$colspan = $rsT->count();
?>

<div class="flex_v" style="width: 100%;">
	<div class="padding applink ">
		<div class="inline padding ededed">
			<a act_chrome_gui="app/app_create"
			   vars="table=produit_tarif&vars[idproduit]=<?= $idproduit ?>"
			   options="{scope:'produit_tarif'}">
				<i class="fa fa-calendar"></i> <?= idioma('Ajouter une date de départ') ?>
			</a></div>
	</div>
	<div class="flex_main" style="overflow: hidden;">
		<div class="flex_h" style="width:100%;height:100%;">
			<div class="padding ededed shadowbox aligncenter borderr">
				<div class="padding">
					<i class="fa fa-calendar"></i><br><i class="fa fa-euro"></i>
				</div>
			</div>
			<div class="flex_main" style="overflow: auto;width:100%;">
				<?php while ($arr_pt = $rsPT->getNext()):
					$idproduit_tarif = (int)$arr_pt['idproduit_tarif'];
					?>
					<div class="titre_entete borderb  bold"><i class="fa fa-calendar"></i> <?= date_fr($arr_pt['dateDebutProduit_tarif']) ?></div>
					<div class="padding">
						<div class="  padding borderb flex_h flex_align_stretch toggler">
							<a class="bold active">
								<i class="fa fa-euro"></i> <?= idioma('Ajouter un prix') ?>
							</a>
							<?php while ($arrT = $rsT->getNext()) {
								$nomGamme          = !empty($arrP['idtransport']) ? $arrT["nomTransport_gamme"] : $arrT["nomGamme"];
								$idgamme           = $arrT["idgamme"];
								$idtransport_gamme = $arrT["idtransport_gamme"];
								?>
								<a class="autoToggle" title=" <?= $arrT['codeTransport_gamme'] ?>"
								   act_chrome_gui="app/app/app_create"
								   vars="table=produit_tarif_gamme&vars[idgamme]=<?= $idgamme ?>&vars[idtransport_gamme]=<?= $idtransport_gamme ?>&vars[idproduit]=<?= $idproduit ?>&vars[idproduit_tarif]=<?= $idproduit_tarif ?>"
								   options="{scope:'produit_tarif_gamme'}"><?= $nomGamme ?></a>

							<?php
							}
								$rsT->reset(); ?>
						</div>
						<div id="produit_tarif<?= $idproduit_tarif ?>"  data-data_model="defaultModel" class="relative">
							<i class="fa fa-spinner fa-spin"></i>
						</div>
					</div>
					<script>
				 load_table_in_zone('table=produit_tarif_gamme&vars[idproduit_tarif]=<?=$idproduit_tarif?>', 'produit_tarif<?=$idproduit_tarif?>');
					</script>
				<?php endwhile;?>
			</div>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .up, .next, .select, .first, .show, .hide, .readAttribute) to
	 * native DOM, with file-local `ptg_` helpers.
	 *
	 * Form.serializeElements is deliberately kept — shim-form.js is the shim
	 * that stays — but note it did not exist as a static until 2026-08-11, so
	 * the change handler below was throwing "Form.serializeElements is not a
	 * function" on every edit. See form-serialize.spec.ts.
	 */
	function ptg_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function ptg_show(node) { if (node) node.style.display = ''; return node; }
	function ptg_hide(node) { if (node) node.style.display = 'none'; return node; }

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function ptg_up(node, selector) {
		var parent = node ? node.parentNode : null;
		while (parent && parent.nodeType === 1) {
			if (parent.matches(selector)) return parent;
			parent = parent.parentNode;
		}
		return null;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function ptg_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (!root) return;
		if (maybeHandler === undefined) {
			root.addEventListener(eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	ptg_on(ptg_el('<?=$body?>'), 'click', 'input[type="checkbox"].avoid', function (event, node) {
		var value = node.checked;
		// `.up('td').next()` — next() with no argument is the next sibling
		// *element*, which is nextElementSibling.
		var cell = ptg_up(node, 'td');
		var monitor = cell && cell.nextElementSibling
			? cell.nextElementSibling.querySelector('input[name="prixPromoProduit_tarif_gamme"]')
			: null;
		if (value == true) {
			ptg_show(monitor)
		} else {
			ptg_hide(monitor);
		}
	})
	ptg_on(ptg_el('<?=$body?>'), 'click', 'input[type="checkbox"]:not(.avoid)', function (event, node) {
		var value = node.checked;
		// The else branch read `$(men<?=$uniquid?>)` — a bare identifier, not a
		// quoted id. It only ever resolved because browsers expose an element's
		// id as a window property; any minifier or a `use strict` module scope
		// would have turned it into a ReferenceError. Both branches now go
		// through the id string, as the show branch already did.
		var menu = ptg_el('men<?=$uniquid?>');
		if (value == true) {
			ptg_show(menu)
		} else {
			ptg_hide(menu);
		}
	})
	ptg_on(ptg_el('<?=$body?>'), 'change', 'input[type="text"]', function (event, node) {
		var value = node.value;
		var row = ptg_up(node, 'tr');
		var vars = Form.serializeElements(row.querySelectorAll('.' + node.getAttribute('grp')));
		ajaxValidation('updateProduitTarifGamme', 'mdl/production/produittarifgamme/', vars + '&scope=idproduit&idproduit=<?=$idproduit?>');
	})
</script>
<script>
	// new myddeview('<?=$body?>', {only: 'input[type=checkbox].selectable'});
</script>
