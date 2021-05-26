<?
	include_once($_SERVER['CONF_INC']);
	$sd         = $_POST['sd'];
	$calendarId = $_POST['calendarId'];
	$jour       = date("d", $sd);
	$mois       = date("m", $sd);
	$annee      = date("Y", $sd);
	$tabMois2   = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
	$lienP      = gmmktime(12, 0, 0, $mois, $jour, $annee - 1);
	$lienS      = gmmktime(12, 0, 0, $mois, $jour, $annee + 1);
	// vardump($_POST);
?>
<div class="padding_more ededed bordert" style="text-align:left;height: 100%;overflow:hidden;" id="dynlistMois">
	<div class="    blanc  relative applink applinkblock toggler flex_h flex_wrap  flex_align_stretch " style="text-align:left;padding:0px;height: 100%;overflow:hidden;" >
		<?
			for ($m = 1; $m <= 12; $m++) {
				$lien = gmmktime(12, 0, 0, $m, $jour, $annee);
				($mois == $m) ? $class = 'active' : $class = '';
				?>
				<div class="aligncenter select_month   edededhover flex_h flex_align_middle" style="width:33%;" vars="<?= http_build_query(['sd' => $lien]) ?>">
					<div class="aligncenter applink applinkblock flex_main">
						<a class="ellipsis <?= $class ?> autoToggle"> <?= $tabMois2[$m] ?> </a>
					</div>
				</div>
			<? } ?>
	</div>
</div>