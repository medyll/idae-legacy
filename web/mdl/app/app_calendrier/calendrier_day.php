<?
	include_once($_SERVER['CONF_INC']);
	$time = uniqid();
	//
	$vars            = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars'], "my_array_filter_fn"), 1);
	// generalement , le nom du container
	$calendarId = $_POST['calendarId'];
	$sd         = $_POST['sd'];

	$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
	$tabjour  = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
	$tabjour  = ["L", "M", "M", "J", "V", "S", "D"];

	$jourEnCours  = date("d", $sd);
	$moisEnCours  = date("m", $sd);
	$anneeEnCours = date("Y", $sd);
	$indexJourCrt = date("w", $sd);
	if ($indexJourCrt == 0)
		$indexJourCrt = 7;

	$lienCalAvant = gmmktime(12, 0, 0, $moisEnCours - 1, $jourEnCours, $anneeEnCours);
	$lienCalApres = gmmktime(12, 0, 0, $moisEnCours + 1, $jourEnCours, $anneeEnCours);

	$anneeAvant = $moisEnCours . "','" . ($anneeEnCours - 1);
	$anneeApres = $moisEnCours . "','" . ($anneeEnCours + 1);

	$moyear = $tabmonth[intval($moisEnCours)] . "&nbsp;&nbsp;" . $anneeEnCours;
	$now    = date("Y/m/d", $sd);

	$moisPrec = mktime(12, 0, 0, $moisEnCours - 1, $jourEnCours, $anneeEnCours);
	$moisSuiv = mktime(12, 0, 0, $moisEnCours + 1, $jourEnCours, $anneeEnCours);
	$today    = date("d/m/Y", $sd);

	if(!empty($_POST['table'])){
		$table = $_POST['table'];
		$APPTMP = new App($table);
	}

?>
<div class="applink" style="overflow:hidden;margin:0 auto;padding:0;width:100%;max-width:250px;">
	<table style="width:100%" border="0" cellpadding=0 cellspacing="1">
		<thead>
			<tr>
				<? for ($i = 0; $i < 7; $i++) { ?>
					<td class="aligncenter"><?= $tabjour[$i] ?></td>
				<? } ?>
			</tr>
		</thead>
		<tbody class="toggler">
			<?
				$num_day = date("w", mktime(0, 0, 0, $moisEnCours, 01, $anneeEnCours));
				if ($num_day == 0) {
					$num_day = 7;
				}
				$max_day = date("t", mktime(0, 0, 0, $moisEnCours, 01, $anneeEnCours));
				$cpt_day = 2;
				while ($cpt_day <= $max_day + $num_day) {
					// calcul le numero de semaine
					$nb_day = date("z", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day + 3, $anneeEnCours));
					$val    = intval($nb_day / 7) + 1; ?>
					<tr>
						<?
							// affiche les jours du mois
							for ($i = 0; $i < 7; $i++) {
								$theday    = date("D", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								$date_fr   = date("d/m/Y", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								$date_us   = date("Y-m-d", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								$sd        = mktime(12, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours);
								$val       = date("d", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								$jourferie = calcul_joursferies($moisEnCours, $cpt_day - $num_day, $anneeEnCours);
								$active    = ($sd == $_POST['sd']) ? 'active' : '';
								$class     = "titrenum";
								if ((($cpt_day - $num_day) < 1) or (($cpt_day - $num_day) > $max_day)) {
									$class = "titrenum2";
									if (($theday == "Sun") or ($theday == "Sat") or ($jourferie)) {
										$class = "titrewend2";
									}
								}
								if (($theday == "Sun") or ($theday == "Sat") or ($jourferie)) {
									$class = "titrewend";
								}
								if ($now == date("Y/m/d", mktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours))) {
									$class = "titrenow";
								}
								$cpt_day++;
								if(!empty($_POST['table']) && !empty($_POST['date_field'])){
									$ct = $APPTMP->find([$_POST['date_field']=>$date_us])->count();
									$ct_css    = ($ct == 0) ? 'textgris' : ' ';
								}
								?>
								<td style="white-space:nowrap" data-droptache="rebound" dropvalue="<?= $date_fr ?>" class="<?= $class ?> aligncenter" title="<?= $theday . ' ' . $date_fr ?>" value="<?= http_build_query(['sd' => $sd]) ?>">
									<a class="autoToggle <?= $active ?> <?=$ct_css?>" onclick="$(this).fire('dom:act_click',{value:'<?= $date_fr ?>',value_us:'<?= $date_us ?>'});">
										<?= $val ?>
									</a>
								</td>
							<? } ?>
					</tr>
				<? } ?></tbody>
	</table>
</div>
