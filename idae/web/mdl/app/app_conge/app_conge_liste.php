<?
include_once ($_SERVER['CONF_INC']);
$time = time();

if (empty($_POST['typeCalendar'])) {
	$_POST['typeCalendar'] = 'quotidien';
}
// CONFIG
$maxJours = 40;
$hours = array('00', '15', '30', '45');

$tabMois = array("Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");
$tabJour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
$tabJourCourt = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim");

if (!empty($_POST['date'])) {
	$tmpdate = explode("/", $_POST['date']);
	$jour = $tmpdate[0];
	$mois = !empty($tmpdate[1]) ? $tmpdate[1] : date("m");
	$annee = !empty($tmpdate[2]) ? $tmpdate[2] : date("Y");
	$_POST['sd'] = mktime(12, 0, 0, $mois, $jour, $annee);
}
if (empty($_POST['sd'])) {
	if (!isset($jour))
		$jour = ((!empty($mois)) ? 1 : date("j"));
	if (!isset($mois))
		$mois = date("m");
	if (!isset($annee))
		$annee = date("Y");
	$sd = mktime(12, 0, 0, $mois, $jour, $annee);
} else {
	$sd = $_POST['sd'];
}

$jourEnCours = date("d", $sd);
$moisEnCours = date("m", $sd);
$anneeEnCours = date("Y", $sd);

$indexJourCrt = date("w", $sd);
if ($indexJourCrt == 0)
	$indexJourCrt = 7;
// deduction du premier jour de cette semaine
$premierJourSemaine = $jourEnCours - $indexJourCrt + 1;

$debutSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine, $anneeEnCours);
$finSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 6, $anneeEnCours);

$lienAvant = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 30, $anneeEnCours);
$lienPeuAvant = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 15, $anneeEnCours);
$lienApres = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 30, $anneeEnCours);
$lienPeuApres = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 15, $anneeEnCours);

// liste des agents sur la droite => defiler jours semaines / mois /trimestres /
$APP = new App('conge');
$APP_A = new App('agent');

$APP_AG = new App('agent_groupe');
$rs_AG = $APP_AG -> find();

$css = array('blanc', 'ccc', 'blanc');

$DSP_JOURS = 0;


?>
<style>
	.table_conge td{
		line-height:2;
		padding:0;
	}
	.table_conge{
		empty-cells: show;
		table-layout: auto;
		width:100%;
		max-width:100%;
	}

	.td_conge_titre{
		max-width:130px;
		min-width:130px;
		width:130px;
		overflow:hidden;
	}
	.td_conge_day{
		width:30px;
		min-width:30px;
		max-width:30px;
		overflow:hidden;
	}
	.td_conge_midday{
		width:50%;
		min-width:50%;
		max-width:50%;
		overflow:hidden;
	}
	.td_conge_sep{
		max-width:10px;
		min-width:10px;
		width:10px;
		overflow:hidden;
	}
</style>

<div class="titre_entete">
	<?=$tabMois[(int)$moisEnCours-1].' '.$anneeEnCours?>
</div>

<div class="relative bordert" style="width:auto;">
	<div class="applink flex_h" style="width:100%;">
		<a class="flex_main" title="<?= date("d", $debutSemaine) . " " . strtolower($tabMois[date("n", $debutSemaine) - 1]); ?>" onclick="reloadModule('app/app_conge/app_conge_liste','*','sd=<?= $lienAvant; ?>');">
			<i class="fa fa-chevron-left"></i> &nbsp;
		</a>
		<a class="flex_main" title="<?= date("d", $debutSemaine) . " " . strtolower($tabMois[date("n", $debutSemaine) - 1]); ?>" onclick="reloadModule('app/app_conge/app_conge_liste','*','sd=<?= $lienPeuAvant; ?>');">
			<i class="fa fa-caret-left"></i> &nbsp;
		</a>
		<a class="flex_main alignright" title="<?= date("d", $finSemaine) . " " . strtolower($tabMois[date("n", $finSemaine) - 1]) . " " . date("Y", $finSemaine); ?>" onclick="reloadModule('app/app_conge/app_conge_liste','*','sd=<?= $lienPeuApres; ?>');">
			&nbsp;<i class="fa fa-caret-right"></i>
		</a>
		<a class="flex_main alignright" title="<?= date("d", $finSemaine) . " " . strtolower($tabMois[date("n", $finSemaine) - 1]) . " " . date("Y", $finSemaine); ?>" onclick="reloadModule('app/app_conge/app_conge_liste','*','sd=<?= $lienApres; ?>');">
			&nbsp;<i class="fa fa-chevron-right"></i>
		</a>
	</div>
</div>
<div id="zone_conge_liste" data-data_model ="defaultModel">

</div>

<script>
	load_table_in_zone('table=conge', 'zone_conge_liste');
</script>