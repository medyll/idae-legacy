<?
	include_once($_SERVER['CONF_INC']);

	$_POST['defer'] = '';
	ini_set('display_errors', 55);
	// CONFIG
	$maxJours = 6;
	$hours = array('00', '15', '30', '45');

	$tabMois = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
	$tabJour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");

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


	$dateEnCours = date("Y-m-d", $sd);
	$jourEnCours = date("d", $sd);
	$moisEnCours = date("m", $sd);
	$anneeEnCours = date("Y", $sd);

	$indexJourCrt = date("w", $sd);
	if ($indexJourCrt == 0) $indexJourCrt = 7;
	// deduction du premier jour de cette semaine
	$premierJourSemaine = $jourEnCours - $indexJourCrt + 1;

	$debutSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine, $anneeEnCours);
	$finSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 6, $anneeEnCours);
	$dateDebut = date("Y-m-d", $debutSemaine);
	$dateFin = date("Y-m-d", $finSemaine);

	$lienAvant = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 7, $anneeEnCours);
	$lienPeuAvant = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 3, $anneeEnCours);
	$lienApres = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 7, $anneeEnCours);
	$lienPeuApres = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 3, $anneeEnCours);


?> 
	<div class="flex_v flex_main relative" style="overflow:hidden;height:100%;width:100%;" id="DIVPlanningHebdo"  data-dsp="planning" >
		<div class="titre_entete" style="box-shadow:0 0 2px #666;position:relative;z-index:1">
			<table border="0" class="" width="100%" style="width:100%;table-layout:fixed;" cellspacing="0"
			       cellpadding="0">
				<tbody style="z-index:0;">
				<tr class="">
					<td class="borderr ededed aligncenter" style="width: 40px;"><i class="fa fa-calendar-o"></i></td>
					<?  for ($i = 0; $i < $maxJours; $i++) {
						$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
						?>
						<td valign="top" class="">
							<div class=" aligncenter">
				              <span class=" borderb bold cursor">
					              <?= $tabJour[date("w", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours))]; ?>
					              &nbsp;
					              <?= date("d/m", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours)) ?>
				              </span>
							</div>
						</td>
					<? } ?>
					<td class="cccccc" style="width: 17px;"></td>
				</tr>
				</tbody>
			</table>
		</div>

		<div id="innerPlanningHebdo" class="flex_main relative" style="height:100%;width:100%;position:relative;overflow:auto;">
			<br>
			<table border="0" class="tablePlanning" width="100%" style="width:100%;table-layout:fixed;" cellspacing="0"
			       cellpadding="0">
				<tbody style="z-index:0;">
				<tr class="evenement borderb">
					<td class="borderr aligncenter" style="width: 40px;">AM</td>
					<?  for ($i = 0; $i < $maxJours; $i++) {
						$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
						?>
						<td data-droptache="dropzone" valign="top" class="evenement  avoid" heuredebut="AM"
						    dropvalue="<?= date_fr($witDate) ?>"></td>
					<? } ?>
				</tr>
				<tr class="evenement">
					<td class="aligncenter" style="width: 40px;">PM</td>
					<?  for ($i = 0; $i < $maxJours; $i++) {
						$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
						?>
						<td data-droptache="dropzone" valign="top" class="evenement  avoid " heuredebut="PM"
						    dropvalue="<?= date_fr($witDate) ?>"></td>
					<? } ?>
				</tr>
				</tbody>
			</table>
			<div class="relative blanc padding" style="position:sticky;top:0;width:auto;z-index:10">
				<div class="applink padding blanc flex_h">
					<a onclick="$('innerPlanningHebdo').fade();reloadModule('app/app_planning/app_planning_hebdo','*','sd=<?= $lienAvant; ?>');">
						<i class="fa fa-chevron-left"></i> &nbsp;
						<?= date("d", $debutSemaine) . " " . strtolower($tabMois[date("n", $debutSemaine) - 1]); ?>
					</a>
					<a onclick="$('innerPlanningHebdo').fade();reloadModule('app/app_planning/app_planning_hebdo','*','sd=<?= $lienApres; ?>');">
						<?= date("d", $finSemaine) . " " . strtolower($tabMois[date("n", $finSemaine) - 1]) . " " . date("Y", $finSemaine); ?>
						&nbsp;&nbsp;<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			</div>
			<table id="tablePlanninHebdo" border="0" class="tablePlanning" width="100%"
			       style="width:100%;table-layout:fixed;" cellspacing="0" cellpadding="0">
				<tbody style="z-index:10;">
				<tr class="">
					<td class="borderr" style="width: 40px;"><?
							// de 07 à 20h
							for ($hr = 7; $hr < 23; $hr++) {
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
								<div class="hPile<?= $clf ?> calday aligncenter titre2">
									<?= $hr . ':00' ?>
								</div>
								<div class="hPile<?= $clf ?> calday aligncenter">
									<?= '15' ?>
								</div>
								<div class="hPile<?= $clf ?> calday aligncenter">
									<?= '30' ?>
								</div>
								<div class="hPile<?= $clf ?> calday aligncenter">
									<?= '45' ?>
								</div>
							<? } ?></td>
					<?
						/*skelMongo::connect('activity','sitebase_base')->ensureIndex(array('dateActivite'=>1));
						  skelMongo::connect('activity','sitebase_base')->ensureIndex(array('heureActivite'=>1));
						  skelMongo::connect('activity','sitebase_base')->ensureIndex(array('idagent'=>1));*/
						for ($i = 0; $i < $maxJours; $i++) {
							$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
							?>
							<td class="tdcalday"><?
									// de 07 à 20h
									for ($hr = 7; $hr < 23; $hr++) {
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

										foreach ($hours as $hour):
											// $c1 = skelMongo::connect('activity','sitebase_base')->find(array('idagent'=>11,'dateActivite'=>$witDate,'heureActivite'=>$hr.':'.$hour.':00'));
											?>
											<div data-droptache="dropzone" style="position:relative;"
											     class="hPile<?= $clf ?> calday "
											     heuredebut="<?= $hr . ':' . $hour . ':00' ?>"
											     dropvalue="<?= date_fr($witDate) ?>"> 
											</div>
										<? endforeach;
									} ?></td>
						<? } ?>
				</tr>
				</tbody>
			</table>
			<div class="padding">
				<?//= skelMdl::cf_module('app/app_planning/app_planning_tache_reload', array('defer' => 'true', 'maxJours' => $maxJours, 'tableparent' => 'DIVPlanningHebdo', 'sd' => $_POST['sd']) ); ?>
			</div>
		</div>
	</div>
<script>
	console.log('table=tache&vars[idagent]=<?=$_SESSION['idagent']?>&vars[gte][dateDebutTache]=<?=$dateDebut?>&vars[lte][dateDebutTache]=<?=$dateFin?>','DIVPlanningHebdo');
	load_table_in_zone('table=tache&vars[idagent]=<?=$_SESSION['idagent']?>&vars[gte][dateDebutTache]=<?=$dateDebut?>&vars[lte][dateDebutTache]=<?=$dateFin?>','DIVPlanningHebdo');
</script>
