<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App('tache');
	$rand = uniqid();
	// CONFIG
	$maxJours = $_POST['maxJours'];

	$tableparent = $_POST['tableparent'];
	//
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
 
$time = time();
	$_POST['date'] = date('Y-m-d', $sd);
	$jourEnCours = date("d", $sd);
	$moisEnCours = date("m", $sd);
	$anneeEnCours = date("Y", $sd);
	$indexJourCrt = date("w", $sd);
	// mois
	$debutMois = gmmktime(12,0,0,$moisEnCours,1,$anneeEnCours);
	$nbJourMois = date("t", $sd);

	if ($indexJourCrt == 0) $indexJourCrt = 7;
	// deduction du premier jour de cette semaine, sauf si un seul jour
	if($maxJours!=1):
		$premierJourSemaine = $jourEnCours - $indexJourCrt + 1;
	elseif($maxJours>20):
		$maxJours = $nbJourMois;
		$premierJourSemaine = $jourEnCours;
	endif;
	$arrStatut = array('END');

	for ($i = 0; $i <= $maxJours; $i++) {
		$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
		// de 07 à 20h
		?>
		<?
		$_POST['heureDebutTache'] = empty($_POST['heureDebutTache']) ? '' : $_POST['heureDebutTache'];
		//
		$rs = $APP->find(array('idagent' => (int)$_SESSION['idagent'], 'dateDebutTache' => $witDate)); // 'idagent' => (int)$_SESSION['idagent'], 
		?>
	 
		<?
		while ($rs->hasNext()) {
			$arrT = $rs->getNext();
			//Définition des heures
			$heure1 = $arrT['heureDebutTache'];
			$heure2 = empty($arrT['heureFinTache']) ? $heure1 : $arrT['heureFinTache'];
			//Extractions des différents paramètres
			list($h1, $m1, $sec1) = explode(':', $heure1);
			list($h2, $m2, $sec2) = explode(':', $heure2);
			//Calcul des timestamps
			$timestamp1 = @mktime($h1, $m1, $sec1, 7, 9, 2006);
			$timestamp2 = @mktime($h2, $m2, $sec2, 7, 9, 2006);
			$diff       = floor(abs($timestamp2 - $timestamp1) / 60);
			$diff       = (!empty($heure2) && $heure1 != $heure2) ? ($diff / 15) * 20 : "20"; //
			$idtache = (int)$arrT['idtache'] ;
			$tache_id   = "tach" . $arrT['idtache'] . $rand;
			$heure1     = empty($heure1) ? 'AM' : $heure1;
			$draggable = ($time>=strtotime($arrT['dateDebutTache']))? '' : 'draggable="true"';
			?>
			<div data-idtache="<?= $idtache ?>" data-dragtache="tache" data-parent="<?= $tableparent ?>" <?=$draggable?> id="<?= $tache_id ?>"
			     datedebut="<?= date_fr($arrT['dateDebutTache']) ?>" heuredebut="<?= $heure1 ?>"
			     style="display:block;width:100%;"   class="dyntache tachehebdo"
			     idtache="<?= $arrT['idtache'] ?>">
				<div style="height:100%;overflow:hidden;" act_defer mdl="app/app_planning/app_planning_tache" vars="idtache=<?=$idtache?>">
				</div>
			</div>

		<? } ?>
	<? } ?>
