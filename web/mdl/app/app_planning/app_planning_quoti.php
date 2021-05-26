<?
	include_once($_SERVER['CONF_INC']);

	$_POST['defer'] = '';
	$tabMois = array(1 => "Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
	$tabJour = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");

	if (!empty($_POST['date'])) {
		$tmpdate     = explode("/", $_POST['date']);
		$jour        = $tmpdate[0];
		$mois        = !empty($tmpdate[1]) ? $tmpdate[1] : date("m");
		$annee       = !empty($tmpdate[2]) ? $tmpdate[2] : date("Y");
		$_POST['sd'] = mktime(12, 0, 0, $mois, $jour, $annee);
	}
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
	if ($indexJourCrt == 0) {
		$indexJourCrt = 7;
	}

	$premierJourSemaine = $jourEnCours - $indexJourCrt + 1;

	$debutSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine, $anneeEnCours);
	$finSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 6, $anneeEnCours);

	$lienAvant = gmmktime(12, 0, 0, $moisEnCours, $jourEnCours - 1, $anneeEnCours);
	$lienApres = gmmktime(12, 0, 0, $moisEnCours, $jourEnCours + 1, $anneeEnCours);

	$leJour = $sd;
	$witDate = date("Y-m-d", $sd);
	$rand = rand();
?>
<div class="flex_v relative" style="height: 100%;overflow:hidden;">
	<div class="borderb titre_entete applink">
		<a class="borderr ededed aligncenter" style="width: 40px;"><i class="fa fa-calendar-o"></i></a>
		<a onclick="reloadModule('app/app_planning/app_planning_quoti','<?= $_POST['valueModule'] ?>','valueModule=<?= $_POST['valueModule'] ?>&sd=<?= $lienAvant; ?>');">
			<i class="fa fa-chevron-left"></i></a>
		<a>&nbsp;&nbsp;
			<?= $tabJour[intval($indexJourCrt - 1)]; ?>
			<?= $jourEnCours ?>
			<?= $tabMois[intval($moisEnCours)] ?>
			<?= $anneeEnCours ?>
			&nbsp;&nbsp;</a>
		<a onclick="reloadModule('app/app_planning/app_planning_quoti','<?= $_POST['valueModule'] ?>','valueModule=<?= $_POST['valueModule'] ?>&sd=<?= $lienApres; ?>');">
			<i class="fa fa-chevron-right"></i></a><a>debug : <?=$witDate?></a>
	</div>
	<div id="innerPlanningQuotidien<?= $rand ?>" data-dsp="planning"  class="flex_main flex_v relative" style="overflow:hidden;height:100%;">

		<div class="flex_main relative" style="height:100%;overflow:auto">
			<br>
			<table border="0" class="tablePlanning" width="100%" style="width:100%;table-layout:fixed;" cellspacing="0"
			       cellpadding="0">
				<tbody style="z-index:0;">
				<tr class="evenement borderb">
					<td class="borderr aligncenter" style="width: 40px;">AM</td>
					<td data-droptache="dropzone" valign="top" class="evenement  avoid" heuredebut="AM"
					    dropvalue="<?= date_fr($witDate) ?>"></td>
				</tr>
				<tr class="evenement">
					<td class="aligncenter" style="width: 40px;">PM</td>
					<td data-droptache="dropzone" valign="top" class="evenement  avoid " heuredebut="PM"
					    dropvalue="<?= date_fr($witDate) ?>"></td>
				</tr>
				</tbody>
			</table>
			<br>
			<table border="0" id="tablePlanningQuoti" class="tablePlanning" width="100%"
			       style="width:100%;table-layout:fixed;" cellspacing="0" cellpadding="0">
				<tbody class="tdcalday">
				<?
					for ($hr = 7; $hr < 25; $hr++) {
						$idPile   = $witDate . $hr . '0000';
						$idQuart  = $witDate . $hr . '1500';
						$idDemi   = $witDate . $hr . '3000';
						$idTQuart = $witDate . $hr . '4500';
						if ($hr == 12) {
							$clf = ' hMidi';
						}
						else {
							$clf = '';
						}
						if ($hr < 8) {
							$clf = ' hMidi';
						}
						if ($hr > 18) {
							$clf = ' hMidi';
						}
						if ($hr < 10) $hr = '0' . $hr;
						?>
						<tr >
							<td valign="middle" class="hPile<?= $clf ?> titre2" style="width: 40px;"
							    heuredebut="<?= $hr . ':00:00' ?>"><?= $hr . ':00' ?></td>
							<td>
								<div data-droptache="dropzone" style="height:100%" class=" hPile<?= $clf ?>"
								     heuredebut="<?= $hr . ':00:00' ?>" heuredebut="<?= $hr . ':00:00' ?>"
								     dropvalue="<?= date_fr($witDate) ?>"> 
								     </div>
							</td>
							<td valign="middle" class="hPile<?= $clf ?> titre2" style="width: 40px;"
							    heuredebut="<?= $hr . ':00:00' ?>" heuredebut="<?= $hr . ':00:00' ?>">
								<?= $hr . ':00' ?>
							</td>
						</tr>

						<tr>
							<td valign="middle" class="hQuart<?= $clf ?>" heuredebut="<?= $hr . ':15:00' ?>"
							    style="text-align:right;width: 40px;">15&nbsp;</td>
							<td>
								<div data-droptache="dropzone" style="height:100%" class=" hQuart<?= $clf ?>"
								     heuredebut="<?= $hr . ':15:00' ?>" heuredebut="<?= $hr . ':15:00' ?>"
								     dropvalue="<?= date_fr($witDate) ?>">
								</div>
							</td>
							<td valign="middle" class="hQuart<?= $clf ?>" heuredebut="<?= $hr . ':15:00' ?>"
							    style="width: 40px;">15&nbsp;</td>
						</tr>
						<tr>
							<td valign="middle" class="hDemi<?= $clf ?>" heuredebut="<?= $hr . ':30:00' ?>"
							    style="text-align:right;width: 40px;">30&nbsp;</td>
							<td>
								<div data-droptache="dropzone" style="height:100%" class=" hDemi<?= $clf ?>"
								     heuredebut="<?= $hr . ':30:00' ?>" heuredebut="<?= $hr . ':30:00' ?>"
								     dropvalue="<?= date_fr($witDate) ?>">
								</div>
							</td>
							<td valign="middle" class="hDemi<?= $clf ?>" heuredebut="<?= $hr . ':30:00' ?>"
							    style="width: 40px;">30&nbsp;</td>
						</tr>
						<tr>
							<td valign="middle" class="hQuart<?= $clf ?>" heuredebut="<?= $hr . ':45:00' ?>"
							    style="text-align:right;width: 40px;">45&nbsp;</td>
							<td>
								<div data-droptache="dropzone" style="height:100%" class=" hQuart<?= $clf ?>"
								     heuredebut="<?= $hr . ':45:00' ?>"
								     dropvalue="<?= date_fr($witDate) ?>">
								</div>
							</td>
							<td valign="middle" class="hQuart<?= $clf ?>" heuredebut="<?= $hr . ':45:00' ?>"
							    style="width: 40px;">45&nbsp;</td>
						</tr>
					<? } ?>
				</tbody>

			</table>
			<div>
				<?// = skelMdl::cf_module('app/app_planning/app_planning_tache_reload', array('defer' => 'true', 'maxJours' => 4, 'tableparent' => 'innerPlanningQuotidien' . $rand, 'sd' => $sd)); ?>
			</div>
		</div>
	</div>
</div>
<script>
	load_table_in_zone('table=tache&vars[idagent]=<?=$_SESSION['idagent']?>','innerPlanningQuotidien<?=$rand?>');
</script>
