<?
	include_once($_SERVER['CONF_INC']);

	$tabmonth = $tabMois = array(1 => "Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
	$tabday = Array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
	$tabJour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
	if (empty($_POST['sd'])) {
		if (!isset($jour)) $jour = ((!empty($mois)) ? 1 : date("j"));
		if (!isset($mois)) $mois = date("m");
		if (!isset($annee)) $annee = date("Y");
		$sd = mktime(12, 0, 0, $mois, $jour, $annee);
	}
	else {
		$sd = $_POST['sd'];
	}
	$jourEnCours = date("d", $sd);
	$moisEnCours = date("m", $sd);
	$anneeEnCours = date("Y", $sd);
	$indexJourCrt = date("w", $sd);
	if ($indexJourCrt == 0) $indexJourCrt = 7;
	$lienCalAvant = gmmktime(12, 0, 0, $moisEnCours - 1, $jourEnCours, $anneeEnCours);
	$lienCalApres = gmmktime(12, 0, 0, $moisEnCours + 1, $jourEnCours, $anneeEnCours);

	$anneeAvant = $moisEnCours . "','" . ($anneeEnCours - 1);
	$anneeApres = $moisEnCours . "','" . ($anneeEnCours + 1);
	$moyear = $tabMois[intval($moisEnCours)] . "&nbsp;&nbsp;" . $anneeEnCours;
	$now = date("Y-m-d", $sd);
	$rand = rand();

	//
	// $dateDebut = date("Y-m-d", $debutSemaine);
	// $dateFin = date("Y-m-d", $finSemaine);

?>

<div class="flex_v" style="width:100%;overflow:hidden;position:relative;height:100%;"  data-dsp="planning"  id="innerPlanningMens<?= $rand ?>" >
	<div class="titre_entete toggler applink relative"  >
		<? for ($i = 1; $i < 13; $i++) {
			$sd = mktime(12, 0, 0, $i, $jourEnCours, $anneeEnCours);
			($i == $moisEnCours) ? $className = 'active bold' : $className = 'lienMoisOff';
			if ($i == ($moisEnCours - 1) || $i == ($moisEnCours + 1)) {
				$className = 'lienMoisBet';
			}
			?>
			<a class="<?= $className ?> autoToggle"
			   onclick="reloadModule('app/app_planning/app_planning_mens','*','<?= sendPost("sd=$sd") ?>');return false;">
				&nbsp;
				<?= $tabMois[intval($i)] ?>
				&nbsp;</a>
		<? } ?>
	</div>
	<div style="box-shadow:0 0 2px #666;position:relative;z-index:1">
	<table style="width:100%;">
		<tr>
			<td class="borderr ededed aligncenter" style="width: 40px;"><i class="fa fa-calendar-o"></i></td>
			<? for ($i = 0; $i < 7; $i++) { ?>
				<td class="aligncenter" valign="middle" style="height:25px;">
					<div class="titre_entete">
						<span class="bold borderb"><?= $tabday[$i] ?></span> </div>
				</td>
			<? } ?>
			<td style="width: 17px;"></td>
		</tr>
	</table></div>
	<div style="position:relative;overflow:auto;height:100%;" class="flex_main">
		<?
			$num_day = date("w", mktime(0, 0, 0, $moisEnCours, 01, $anneeEnCours));
			if ($num_day == 0) {
				$num_day = 7;
			}
			$max_day = date("t", mktime(0, 0, 0, $moisEnCours, 01, $anneeEnCours));
			$cpt_day = 2;
			$cpt_day_titre = 2;
			$nombreSemainer = 0;
		?>
		<table id="tablePlanningMensuel" class="tablePlanningMensuel" style="width:100%;table-layout:fixed;height:100%"
		       cellspacing="0" cellpadding="0">
			<tbody class="tdcalday">

			<?
				while ($cpt_day <= $max_day + $num_day) {
					// calcul le numero de semaine
					$nb_day = date("z", gmmktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day + 3, $anneeEnCours));
					$val    = intval($nb_day / 7) + 1;
					$nombreSemainer++;
					// affiche les jours du mois
					?>
					<tr>
						<td class="" style="width:40px;"></td>
						<?
							for ($i = 0; $i < 7; $i++) {
								$class     = " ";
								$jourferie = true;
								$val       = date("d", gmmktime(0, 0, 0, $moisEnCours, $cpt_day_titre - $num_day, $anneeEnCours));
								$theday    = date("D", gmmktime(0, 0, 0, $moisEnCours, $cpt_day_titre - $num_day, $anneeEnCours));
								if ((($cpt_day_titre - $num_day) < 1) or (($cpt_day_titre - $num_day) > $max_day)) {
									$class = "titrenum2 bold";
								}
								if (($theday == "Sun") or ($jourferie)) {
									$class = "";
								}
								if (($theday == "Sat")) {
									$class = "";
								}
								$cpt_day_titre++; ?>
								<td class="<?= $class ?>" style="height:16px;">
									<div class=" padding relative">
							              <div class="titre2 borderb  padding inline" style="width:50%;">
							              <?= $val ?>
							              </div>
									</div>
								</td>
							<? } ?>
					</tr>
					<tr>
						<td class="blanc" style="width:40px;"></td>
						<?
							$class = "";
							for ($i = 0; $i < 7; $i++) {
								$theday = date("D", gmmktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								$sd     = gmmktime(12, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours);
								$val    = date("d", gmmktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours));
								//$jourferie=calcul_joursferies($moisEnCours,$cpt_day-$num_day,$anneeEnCours);
								$jourferie = false;
								$class     = " ";
								//if (($theday=="Sun") or ($theday=="Sat")or ($jourferie)){ $class="hMidi";}
								if ($now == date("Y/m/d", gmmktime(0, 0, 0, $moisEnCours, $cpt_day - $num_day, $anneeEnCours))) {
									$class = "titrenow";
								}
								if ((($cpt_day - $num_day) < 1) or (($cpt_day - $num_day) > $max_day)) {
									$class = "titrenum2";
								}
								if (($theday == "Sun") or ($jourferie)) {
									$class = "";
								}
								if (($theday == "Sat")) {
									$class = "";
								}
								$cpt_day++;
								$witDate = date("Y-m-d", $sd);
								?>
								<td valign="top" class="<?= $class ?> calday" title="<?= $witDate ?>"
								    style="position:relative!important;">
									<div data-droptache="dropzone" id="<?= $sd ?>"
									     style="height:100%;width:100%;position:relative;"
									     class="   caseMois" dropvalue="<?= date_fr($witDate) ?>"
									     datedebut="<?= date_fr($witDate) ?>" date="<?= date_fr($witDate) ?>"></div>
								</td>
							<? } ?>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
	<div class="padding bordert">
		<?//= skelMdl::cf_module('app/app_planning/app_planning_tache_reload', array( 'maxJours' => 30, 'tableparent' => 'innerPlanningMens' . $rand, 'sd' => $_POST['sd']) + $_POST); ?>
	</div>
</div>
<script>
	load_table_in_zone('table=tache&vars[idagent]=<?=$_SESSION['idagent']?>','innerPlanningMens<?= $rand ?>');
</script>
<script>

	new tableGui($('tablePlanningMensuel'), {numRow: <?=$nombreSemainer?>, onlyClass: 'caseMois' })
</script>
