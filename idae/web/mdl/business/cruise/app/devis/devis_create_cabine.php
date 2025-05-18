<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP    = new App('produit_tarif');
	$APP_TG = new App('produit_tarif_gamme');
	$APP_GM = new App('gamme');
	$time   = time();
	if (empty($_POST['idproduit_tarif'])) {
		?>
		<div class="padding aligncenter ededed"><i class="fa fa-warning fa-2x"></i>

			<br>
			Date de départ non choisie
		</div>
		<?

	} else {
		$idproduit_tarif = (int)$_POST['idproduit_tarif'];
		$idproduit       = (int)$_POST['idproduit'];
		$arrP            = $APP->query_one(array('idproduit' => $idproduit));

		?>
		<div class="toggler applink flex_h">
			<?
				$arrG  = $APP_TG->distinct_all('idgamme', array('idproduit_tarif' => (int)$idproduit_tarif, 'idproduit' => $idproduit));
				$RS_GM = $APP_GM->find(['idgamme' => ['$in' => $arrG]])->sort(['ordreGamme' => 1]);
				while ($arrGM = $RS_GM->getNext()) {
					$idgamme = (int)$arrGM['idgamme'];
					?>
					<div class="flex_main">
					<div class="padding margin borderb"><?= $arrGM["nomGamme"] ?></div><?
					$rsT = $APP_TG->query(array('idgamme' => $idgamme, 'idproduit_tarif' => (int)$idproduit_tarif, 'idproduit' => $idproduit))->sort(['prixProduit_tarif_gamme' => 1]);
					foreach ($rsT as $key => $arr) {
						// var_dump($arr);
						if (!empty($arr['idtransport_gamme'])) {
							//$arr2 = $APP_TG->query_one(array('idtransport_gamme' => (int)$arr["idtransport_gamme"]));
						}

						?>
						<label class="autoToggle table cursor hover">
							<div class="cell" style="width:20px">
								<input type="radio" name="vars[idproduit_tarif_gamme]" value="<?= $arr["idproduit_tarif_gamme"] ?>"/>
							</div>
							<div class="cell  " style="width:170px">
								<?= stripslashes($arr["nomTransport_gamme"] ) ?>
							</div>
							<div class="cell  " style="width:50px">
								<?= $arr["prixProduit_tarif_gamme"] ?>
								€
							</div>
						</label>
					<? }
					?></div><?
				}
			?>
		</div>
	<? } ?>


