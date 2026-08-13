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
	$arrP = $APP_PROD->query_one(array( 'idproduit' => $idproduit ));
	$GRDATE = $arrP['grilleDateProduit'];
	//
	$idproduit_type = (int)$arrP['idproduit_type'];


	$rsT  = $APP_GAMME->query([], 0, 1); // array( 'idproduit_type' => (int)$idproduit_type )
	$rsPT = $APP_PT->query(array( 'idproduit' => (int)$idproduit ), 0, 250);

	if ( ! empty($arrP['idtransport']) ) {
		$rsT = $APP_T_G->query(array( 'idtransport' => (int)$arrP['idtransport'] ))->sort(array( 'ordreTransport_gamme' => 1 ,
		                                                                                         'ordreGamme'           => 1 ));
	}

	//
	$colspan = $rsT->count();
?>

<div class = "flowDown blanc">
	<div class = "applink padding">
		<a onClick = "ajaxMdl('production/produittarif/produit_tarif_create','','idproduit=<?= $idproduit ?>',{value:'<?= $idproduit ?>'})"><img src = "<?= ICONPATH ?>date16.png">Ajouter une date de
		                                                                                                                                                                           départ / tarif</a>


		<div id = "men<?= $uniquid ?>" class = "inline">
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Supprimer prix') ?>',serializeFields(ptga_el('<?= $body ?>').querySelectorAll('.selectable'))+'&F_action=suppr&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>trash16.png"/>
				<?= idioma('Supprimer prix') ?>
			</a>
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Supprimer dates') ?>',serializeFields(ptga_el('<?= $body ?>').querySelectorAll('.selectable'))+'&F_action=supprdates&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>trash16.png"/>
				<?= idioma('Supprimer dates') ?>
			</a>
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Modifier prix') ?>',serializeFields(ptga_el('<?= $body ?>').querySelectorAll('.selectable'))+'&F_action=edit&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>edit16.png"/>
				<?= idioma('Modifier prix') ?>
			</a>
			<a onclick = "ajaxValidation('repairProduitTarif','mdl/production/produittarif/','scope=idproduit&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>repair16.png"/>&nbsp;Ré-indexer</a>
		</div>
	</div>
	<div class = "flowDown" style = "overflow:auto;">
		<table style = "width:100%" class = "tableinput tabletop" cellspacing = "0" cellpadding = "0">
			<thead>
				<tr class = "entete">
					<td style = "width:25px;" class = "aligncenter"></td>
					<td style = "width:80px" class = "borderr ededed aligncenter bold"><?= idioma("date") ?></td>
					<td style = "width:25px;" class = "aligncenter" title = "<?= idioma("Supprimer") ?>"></td>
					<?php while ($arrT = $rsT->getNext()) {
						$nomGamme = ! empty($arrP['idtransport']) ? $arrT["nomTransport_gamme"] : $arrT["nomGamme"];
						$nomGamme = (empty($arrP['idtransport']) && ! empty($idhotel)) ? $arrT["nomHotel_gamme"] : $nomGamme;
						?>
						<td style = "width:25px;" class = "aligncenter" title = "<?= idioma("Promotion") ?>"><img src = "<?= ICONPATH ?>tarif16.png"/></td>
						<td style = "width:100px" class = "bold alignright" title = "<?= $nomGamme ?> <?= $arrT["xmlListeTransportGamme"] ?>"><?= $nomGamme ?> <?= $arrT["xmlListeTransportGamme"] ?></td>
						<td style = "width:40px;"><?= idioma("3°") ?></td>
						<td style = "width:40px;"><?= idioma("singl") ?></td>
						<td style = "width:40px;"><?= idioma("enf") ?></td>
					<?php }
						$rsT->reset(); ?>
					<td></td>
				</tr>
			</thead>
			<tbody id = "<?= $body ?>">
				<?php foreach ($rsPT as $key => $arr) {
					$idproduit_tairf = $arr["idproduit_tarif"];
					$idproduit       = $arr["idproduit"];
					$mois            = date('m' , strtotime($arr["dateDebutProduit_tarif"]));
					$annee           = date('Y' , strtotime($arr["dateDebutProduit_tarif"]));
					?>
					<?php
					if ( $mois != $oldmois ): ?>
						<tr class = "">
							<td colspan = "3" class = "uppercase bold ">
								<div class = "padding"><img src = "<?= ICONPATH ?>arrowClose.png"/>&nbsp;
									<?= mois_fr($arr["dateDebutProduit_tarif"]); ?>
									<?= $mois . ' ' . $annee ?></div>
							</td>
							<?php while ($arrT = $rsT->getNext()) { ?>
								<td class = "cursor" colspan = "5">&nbsp;</td>
							<?php }
								$rsT->reset(); ?>
							<td></td>
						</tr>
						<?php
						$oldmois = $mois;
					endif;

					?>
					<tr>
						<td class = "cursor aligncenter cellmiddle" style = "vertical-align:middle;">
							<input type = "checkbox" name = "produit_tarif[]" value = "<?= $arr['idproduit_tarif'] ?>" class = "selectable"/></td>
						<td class = "borderr applink   blanc">
							<a onclick = "ajaxMdl('production/produittarif/produit_tarif_update','Maj Date','idproduit=<?= $arr["idproduit"] ?>&idproduit_tarif=<?= $arr["idproduit_tarif"] ?>')">
								<?= date_fr($arr["dateDebutProduit_tarif"]) ?>
							</a></td>
						<td class = "cursor">
							<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_duplique','Dupliquer des tarifs','idproduit=<?= $arr["idproduit"] ?>&idproduit_tarif=<?= $arr["idproduit_tarif"] ?>')"><img onclick = "" src = "<?= ICONPATH ?>copy16.png" class = "cursor"/></a>
						</td>
						<?php while ($arrT = $rsT->getNext()) {
							$daGrp   = 'dagrp_' . uniqid();

							$arrMore["idproduit_tarif"] = (int)$arr["idproduit_tarif"];
							$arrMore["idtransport_gamme"] = (int)$arr["idtransport_gamme"];
							$arrMore["idproduit"]       = (int)$idproduit;
							//$arrMore["idgamme"]         = (int)$arrT["idgamme"];
							//
							$ARRPT = $APP_T_G->query_one($arrMore);
							//
							$display     = (! empty($ARRPT["prixPromoProduit_tarif_gamme"])) ? '' : 'none';
							$cssWAITLIST = ($ARRPT["codeDispoProduit_tarif"] == 'WAITLIST') ? 'alert' : '';
							//
							foreach ($arrMore as $key => $vars):
								echo "<input class='" . $daGrp . "'  type='hidden' name='vars[" . $key . "]' value='" . $vars . "' />";
							endforeach;
							?>
							<td class = "aligncenter ededed borderl">
								<input value = "1" class = "avoid" name = "promoProduit_tarif" type = "checkbox" <?= checked($ARRPT["prixPromoProduit_tarif_gamme"]) ?> /></td>
							<td class = "<?= $cssWAITLIST ?>"><?=$arr["idproduit_tarif"]?>
								<input grp = "<?= $daGrp ?>" type = "text" class = "<?= $daGrp ?> alignright inputFree" name = "prixProduit_tarif_gamme" value = "<?= empty($ARRPT["prixProduit_tarif_gamme"]) ? '' : maskNbre($ARRPT["prixProduit_tarif_gamme"]); ?>"/>
								<br/>
								<input grp = "<?= $daGrp ?>" type = "text" class = "<?= $daGrp ?> alignright inputFree" style = "display:<?= $display ?>" name = "prixPromoProduit_tarif_gamme" value = "<?= empty($ARRPT["prixPromoProduit_tarif_gamme"]) ? '' : maskNbre($ARRPT["prixPromoProduit_tarif_gamme"]); ?>"/>
							</td>
							<td class = "ededed">
								<input grp = "<?= $daGrp ?>" type = "text" class = "<?= $daGrp ?> inputFree alignright" name = "troisQuatreProduit_tarif_gamme" value = "<?= $ARRPT["troisQuatreProduit_tarif_gamme"] ?>"/>
							</td>
							<td>
								<input grp = "<?= $daGrp ?>" type = "text" class = "<?= $daGrp ?> inputFree alignright" name = "singleProduit_tarif_gamme" value = "<?= $ARRPT["singleProduit_tarif_gamme"] ?>"/>
							</td>
							<td class = "ededed">
								<input grp = "<?= $daGrp ?>" type = "text" class = "<?= $daGrp ?> inputFree alignright" name = "chProduit_tarif_gamme" value = "<?= $ARRPT["chProduit_tarif_gamme"] ?>"/>
							</td>
						<?php }
							$rsT->reset(); ?>
						<td></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .up, .next, .select, .first, .show, .hide, .readAttribute) to
	 * native DOM, with file-local `ptga_` helpers.
	 *
	 * Arbitrary field collections use the native serializeFields helper.
	 * See form-serialize.spec.ts for the regression contract.
	 */
	function ptga_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function ptga_show(node) { if (node) node.style.display = ''; return node; }
	function ptga_hide(node) { if (node) node.style.display = 'none'; return node; }

	/** Prototype's Element#up: nearest ancestor matching `selector`. */
	function ptga_up(node, selector) {
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
	function ptga_on(root, eventName, selectorOrHandler, maybeHandler) {
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

	ptga_on(ptga_el('<?=$body?>'), 'click', 'input[type="checkbox"].avoid', function (event, node) {
		var value = node.checked;
		// `.up('td').next()` — next() with no argument is the next sibling
		// *element*, which is nextElementSibling.
		var cell = ptga_up(node, 'td');
		var monitor = cell && cell.nextElementSibling
			? cell.nextElementSibling.querySelector('input[name="prixPromoProduit_tarif_gamme"]')
			: null;
		if (value == true) {
			ptga_show(monitor)
		} else {
			ptga_hide(monitor);
		}
	})
	ptga_on(ptga_el('<?=$body?>'), 'click', 'input[type="checkbox"]:not(.avoid)', function (event, node) {
		var value = node.checked;
		// The else branch read `$(men<?=$uniquid?>)` — a bare identifier, not a
		// quoted id. It only ever resolved because browsers expose an element's
		// id as a window property; any minifier or a `use strict` module scope
		// would have turned it into a ReferenceError. Both branches now go
		// through the id string, as the show branch already did.
		var menu = ptga_el('men<?=$uniquid?>');
		if (value == true) {
			ptga_show(menu)
		} else {
			ptga_hide(menu);
		}
	})
	ptga_on(ptga_el('<?=$body?>'), 'change', 'input[type="text"]', function (event, node) {
		var value = node.value;
		var row = ptga_up(node, 'tr');
		var vars = serializeFields(row.querySelectorAll('.' + node.getAttribute('grp')));
		ajaxValidation('updateProduitTarifGamme', 'mdl/production/produittarifgamme/', vars + '&scope=idproduit&idproduit=<?=$idproduit?>');
	})
</script>
<script>
	new myddeview('<?=$body?>', {only: 'input[type=checkbox].selectable'});
</script>
