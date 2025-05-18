<?
	include_once($_SERVER['CONF_INC']);
	 
	$APP 	= new App('conge');
	$APP_TY 	= new App('conge_type');
	
	$rand = uniqid();
	// CONFIG
	$maxJours = $_POST['maxJours'];
	//
	$tableparent = $_POST['tableparent'];
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

	for ($i = 0; $i < $maxJours; $i++) {
		$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
		// de 07 à 20h
		?>
		<?
		$_POST['heureDebutConge'] = empty($_POST['heureDebutConge']) ? '' : $_POST['heureDebutConge'];
		//
		$rs = $APP->find(array('dateDebutConge' => $witDate));
		while ($arrT = $rs->getNext()) {
			//		 
			$idconge = (int)$arrT['idconge'];
			// dates 
			$datefin = $arrT['dateFinConge'];
			//Définition des heures
			$heure1 = $arrT['heureDebutConge'];
			//
			$heure2 = empty($arrT['heureFinConge']) ? $heure1 : $arrT['heureFinConge']; 
			// 
			$heure1     = empty($heure1) ? 'AM' : $heure1;
			// type du congé pour couleur 
			$arr_tty = $APP_TY->query_one(array('idconge_type'=>(int)$arrT['idconge_type']));
			//
			?>
			<div data-table="conge" data-table_value="<?= $idconge ?>" data-idconge="<?= $idconge ?>" data-dragconge="conge" data-parent="<?= $tableparent ?>" data-idagent="<?= $arrT['idagent'] ?>" data-duree="<?= $arrT['dureeConge'] ?>" draggable="true" id="<?= $conge_id ?>"
			     datefin="<?= $datefin ?>" datedebut="<?= $arrT['dateDebutConge'] ?>" heuredebut="<?= $heure1 ?>"  heurefin="<?= $heure2 ?>"
			     style="background-color:<?=$arr_tty['bgcolorConge_type']?>;display:block;"   class="dynconge congehebdo borderb mastershow"
			     idconge="<?= $arrT['idconge'] ?>">
				<div class="flex_h flex_align_middle">
					<div  class="padding fond_noir color_fond_noir"><i class="fa fa-<?=$arrT['iconConge_statut']?>" style="color:<?=$arrT['colorConge_statut']?>"></i> </div>
					<div class="ellipsis flax_main"> <?=$arrT['nomConge']?></div>
				</div>
			     <div class="slaveshow absolute boxshadow" style="left:0%;min-width:250px;bottom:100%;z-index:20000">
					<div class="blanc" act_defer mdl="app/app/app_fiche_mini" vars="table=conge&table_value=<?=$idconge?>"></div> 
				</div>
			</div>

		<? } ?>
	<? } ?>
 