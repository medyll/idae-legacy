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
	$arrP = $APP_PROD->query_one(array('idproduit' => $idproduit));
	$GRDATE = $arrP['grilleDateProduit'];
	//
	$idproduit_type = (int)$arrP['idproduit_type'];


	$rsT = $APP_GAMME->query()->sort(array('ordreGamme' => 1))->limit(1); // array( 'idproduit_type' => (int)$idproduit_type )
	$rsPT = $APP_PT->query(array('idproduit' => (int)$idproduit))->sort(array('dateDebutProduit_tarif' => 1));

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
				<? while ($arr_pt = $rsPT->getNext()):
					$idproduit_tarif = (int)$arr_pt['idproduit_tarif'];
					?>
					<div class="titre_entete borderb  bold"><i class="fa fa-calendar"></i> <?= date_fr($arr_pt['dateDebutProduit_tarif']) ?></div>
					<div class="padding">
						<div class="  padding borderb flex_h flex_align_stretch toggler">
							<a class="bold active">
								<i class="fa fa-euro"></i> <?= idioma('Ajouter un prix') ?>
							</a>
							<? while ($arrT = $rsT->getNext()) {
								$nomGamme          = !empty($arrP['idtransport']) ? $arrT["nomTransport_gamme"] : $arrT["nomGamme"];
								$idgamme           = $arrT["idgamme"];
								$idtransport_gamme = $arrT["idtransport_gamme"];
								?>
								<a class="autoToggle" title=" <?= $arrT['codeTransport_gamme'] ?>"
								   act_chrome_gui="app/app/app_create"
								   vars="table=produit_tarif_gamme&vars[idgamme]=<?= $idgamme ?>&vars[idtransport_gamme]=<?= $idtransport_gamme ?>&vars[idproduit]=<?= $idproduit ?>&vars[idproduit_tarif]=<?= $idproduit_tarif ?>"
								   options="{scope:'produit_tarif_gamme'}"><?= $nomGamme ?></a>

							<?
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
				<? endwhile;?>
			</div>
		</div>
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
	// new myddeview('<?=$body?>', {only: 'input[type=checkbox].selectable'});
</script>
