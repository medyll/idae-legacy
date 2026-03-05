<?
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
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Supprimer prix') ?>',Form.serializeElements($('<?= $body ?>').select('.selectable') )+'&F_action=suppr&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>trash16.png"/>
				<?= idioma('Supprimer prix') ?>
			</a>
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Supprimer dates') ?>',Form.serializeElements($('<?= $body ?>').select('.selectable') )+'&F_action=supprdates&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>trash16.png"/>
				<?= idioma('Supprimer dates') ?>
			</a>
			<a onclick = "ajaxMdl('production/produittarifgamme/produit_tarif_gamme_update_multi','<?= idioma('Actions multiples') . ' ' . idioma('Modifier prix') ?>',Form.serializeElements($('<?= $body ?>').select('.selectable') )+'&F_action=edit&idproduit=<?= $idproduit ?>');"><img src = "<?= ICONPATH ?>edit16.png"/>
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
					<? while ($arrT = $rsT->getNext()) {
						$nomGamme = ! empty($arrP['idtransport']) ? $arrT["nomTransport_gamme"] : $arrT["nomGamme"];
						$nomGamme = (empty($arrP['idtransport']) && ! empty($idhotel)) ? $arrT["nomHotel_gamme"] : $nomGamme;
						?>
						<td style = "width:25px;" class = "aligncenter" title = "<?= idioma("Promotion") ?>"><img src = "<?= ICONPATH ?>tarif16.png"/></td>
						<td style = "width:100px" class = "bold alignright" title = "<?= $nomGamme ?> <?= $arrT["xmlListeTransportGamme"] ?>"><?= $nomGamme ?> <?= $arrT["xmlListeTransportGamme"] ?></td>
						<td style = "width:40px;"><?= idioma("3°") ?></td>
						<td style = "width:40px;"><?= idioma("singl") ?></td>
						<td style = "width:40px;"><?= idioma("enf") ?></td>
					<? }
						$rsT->reset(); ?>
					<td></td>
				</tr>
			</thead>
			<tbody id = "<?= $body ?>">
				<? foreach ($rsPT as $key => $arr) {
					$idproduit_tairf = $arr["idproduit_tarif"];
					$idproduit       = $arr["idproduit"];
					$mois            = date('m' , strtotime($arr["dateDebutProduit_tarif"]));
					$annee           = date('Y' , strtotime($arr["dateDebutProduit_tarif"]));
					?>
					<?
					if ( $mois != $oldmois ): ?>
						<tr class = "">
							<td colspan = "3" class = "uppercase bold ">
								<div class = "padding"><img src = "<?= ICONPATH ?>arrowClose.png"/>&nbsp;
									<?= mois_fr($arr["dateDebutProduit_tarif"]); ?>
									<?= $mois . ' ' . $annee ?></div>
							</td>
							<? while ($arrT = $rsT->getNext()) { ?>
								<td class = "cursor" colspan = "5">&nbsp;</td>
							<? }
								$rsT->reset(); ?>
							<td></td>
						</tr>
						<?
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
						<? while ($arrT = $rsT->getNext()) {
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
						<? }
							$rsT->reset(); ?>
						<td></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$('<?=$body?>').on('click', 'input[type="checkbox"].avoid', function (event, node) {
		value = node.checked;
		monitor = $(node).up('td').next().select('input[name="prixPromoProduit_tarif_gamme"]').first();
		if (value == true) {
			$(monitor).show()
		} else {
			$(monitor).hide();
		}
	})
	$('<?=$body?>').on('click', 'input[type="checkbox"]:not(.avoid)', function (event, node) {
		value = node.checked;
		if (value == true) {
			$('men<?=$uniquid?>').show()
		} else {
			$(men<?=$uniquid?>).hide();
		}
	})
	$('<?=$body?>').on('change', 'input[type="text"]', function (event, node) {
		value = node.value;
		vars = Form.serializeElements($(node).up('tr').select('.' + node.readAttribute('grp')));
		ajaxValidation('updateProduitTarifGamme', 'mdl/production/produittarifgamme/', vars + '&scope=idproduit&idproduit=<?=$idproduit?>');
	})
</script>
<script>
	new myddeview('<?=$body?>', {only: 'input[type=checkbox].selectable'});
</script>
