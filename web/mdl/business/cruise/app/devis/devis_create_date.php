<?
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/'.BUSINESS.'/app/devis/' ;

	$APP    = new App('produit');
	$APP_TF = new App('produit_tarif');

	$time = time();
	//
	$idproduit = (int)$_POST['idproduit'];
	$arrP = $APP->query_one(array( 'idproduit' => $idproduit ));
	$arr_TF = $APP_TF->query(array( 'idproduit' => $idproduit ))->sort(array('dateDebutProduit_tarif'=>1));
?>

<div class = " toggler" >
	<?
		$year = $month = $day;
		foreach ($arr_TF as $key => $arrDate) {
			$date    = date_create($arrDate['dateDebutProduit_tarif']);
			$numjour = (date_format($date , 'w') == 0) ? 0 : date_format($date , 'w') - 1;
			$annee   = date_format($date , 'Y');
			$mois    = date_format($date , 'm');
			$jour    = date_format($date , 'd');
			?>
			<? if ( $year != $annee ) {
				$year  = $annee;
				$month = '';
				?><br />
				<div class = "padding inline" >
					<strong >
						<?= $annee ?>
					</strong >
				</div >
			<? } ?>
			<?
			if ( $month != $mois && $year == $annee ) {
				echo '<br>';
			}
			if ( $month != $mois ) {
				$month = $mois;

				?>
				<div class = "inline applink retrait" style = "width:80px;vertical-align:middle;" >
					<label class = "nolabel " ><span class = "borderb" ><?= fonctionsSite::mois_fr($mois) ?></span ></label >
				</div >
			<? } ?>
			<div class = "inline applink aligncenter borderr" style = "width:30px;overflow:hidden;vertical-align:middle;" onclick = "reloadModule('<?=$path_to_devis?>devis_create_cabine','*','idproduit=<?= $idproduit ?>&idproduit_tarif=<?= $arrDate['idproduit_tarif'] ?>')" >
				<label class = "autoToggle aligncenter nolabel" >
					<input style = "height:0;width:0;position:absolute;visibility:hidden" type = "radio" name = "vars[idproduit_tarif]" value = "<?= $arrDate['idproduit_tarif'] ?>" /><?= $tabjour[$numjour] . ' ' . $jour ?>
				</label >
			</div >
		<? } ?>
</div >
<div class = "spacer" ></div >
<? if ( ! empty($arrP['grilleDateProduit'][0]) ) { ?>
	<script >
		reloadModule('<?=$path_to_devis?>devis_create_cabine', '*', 'idproduit_tarif=<?=$arrP['grilleDateProduit'][0]['idproduit_tarif']?>&idproduit=<?=$idproduit?>')
	</script >
<? } ?>
