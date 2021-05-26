<?
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/' . BUSINESS . '/app/devis/';

	$APP_DV = new App('devis');
	$APP_PR = new App('produit');
	$APP    = new App('devis');

	$time = time();

	$iddevis = (int)$_POST['iddevis'];
	$arr     = $APP_DV->query_one(array('iddevis' => (int)$iddevis));
	//
	$table       = 'devis';
	$Table       = ucfirst($table);
	$table_value = $iddevis;
	//
	$idproduit             = $arr['idproduit'];
	$idproduit_tarif       = (int)$arr['idproduit_tarif'];
	$idproduit_tarif_gamme = (int)$arr['idproduit_tarif_gamme'];
	$idtransport_gamme     = (int)$arr['idtransport_gamme'];
	$idclient              = $arr['idclient'];
	$idagent               = (int)$arr['idagent'];

	$arrProduit = $APP_PR->query_one(array('idproduit' => (int)$idproduit));
?>
<div style="width:550px;">
	<div class="titre_entete">
		<?= $idproduit ?>
		&nbsp;|&nbsp;
		<?= $arrProduit['nomProduit'] ?>
		<?= $arrProduit['nomFournisseur'] ?>
	</div>
	<form action="<?=ACTIONMDL. $path_to_devis ?>actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<input type="hidden" name="vars[iddevis]" id="iddevis" value="<?= $iddevis ?>">
		<input type="hidden" name="vars[idtransport_gamme]" id="idtransport_gamme" value="<?= $idtransport_gamme ?>">
		<input type="hidden" name="nomTransport_gamme" id="nomTransport_gamme" value="">
		<div class="margin padding" style="">
			<div class="autoNext avoid">
				<?= idioma('Passagers') ?>
			</div>
			<div class="retrait">
				<label>
					<?= idioma('adultes') ?>
				</label>
				<select name="vars[nbreAdulteDevis]" style="width:50%">
					<? for($i=1;$i<=6;$i++){
						?>
						<option <?=selected($i==$arr['nbreAdulteDevis']);?> value="<?=$i?>"><?=$i?> adulte</option>
					<? } ?>
				</select>
				<br/>
				<br/>
				<label>
					<?= idioma('Enfants') ?>
				</label>
				<select name="vars[nbreEnfantDevis]" id="nbreEnfantDevis" style="width:50%" >
					<? for($i=0;$i<=6;$i++){ ?>
						<option <?=selected($i==$arr['nbreEnfantDevis']);?> value="<?=$i?>"><?=$i?> enfants</option>
					<? } ?>
				</select>
			</div>
			<div class="flex_h flex_align_top">
				<div class="flex_main">
					<div class="autoNext avoid">
						<?= idioma('Date de départ') ?>
					</div>
					<div class="retrait">
						<?= skelMdl::cf_module($path_to_devis . 'devis_create_date', array('idproduit' => (int)$idproduit), $_SESSION['idagent']) ?>
					</div>
				</div>
				<div class="flex_main">
					<div class="autoNext avoid">
						<?= idioma('Cabine') ?>
					</div>
					<div>
						<div class="retrait">
							<?= skelMdl::cf_module($path_to_devis . 'devis_create_cabine', array('idproduit' => $idproduit, 'idproduit_tarif' => $idproduit_tarif)) ?>
						</div>
						<div id="zone_tarif" style="max-width:550px;position:relative" class="applink"></div>
					</div>
				</div>
			</div>
			<div class="spacer"></div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="Mettre à jour ce devis"/>
		</div>
	</form>
</div>
<script>
	monitor_enf = function (val) {

	}
	reloadTarif = function (id) {
		reloadModule('app/app_custom/devis/devis_create_cabine', '*', 'idproduit=<?=$idproduit?>&idproduit_tarif=' + id)
	}
	//reloadTarif($('idproduit_tarif').value);
	monitor_enf($('nbreEnfantDevis').value);
</script>
