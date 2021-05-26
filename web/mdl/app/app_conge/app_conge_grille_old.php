<?
include_once ($_SERVER['CONF_INC']);
$time = time();
ini_set('display_errors',55);
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
<div class="flex_v applink applinkblock" style="height:100%;overflow:hidden;">	
	<div class="boxshadow shadow shadowbox" style="overflow:hidden;position:relative;width:100%;">
		<table class="table_conge">
			<tr >
				<td class="td_conge_titre"></td>
				<?  for ($i = 0; $i < $maxJours; $i++) {
				$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$month_run = date("m", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$numday_run = date("w", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$week_run = date("W", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				if($numday_run==1){
					$DSP_JOURS++;
					?>
					<td class="td_conge_sep" >&nbsp;</td>
					<?
					}
					if($last_month != $month_run){
					$DSP_JOURS++;
					?>
					 <td class="td_conge_sep fond_noir">&nbsp;</td>  
					<?
					$last_month = $month_run;
					}
					$DSP_JOURS++;
				?> 
				<td class="td_conge_day borderb" >  
					<div class="padding aligncenter"> 
					<?= date("m", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours)) ?>
					 </div 
				</td> 
				<? } ?>
				<td style="width:17px"></td>
			</tr>
			<tr class=""   >
				<td class="td_conge_titre">
					<div class="aligncenter bold">
						<?=$tabMois[(int)$moisEnCours-1].' '.$anneeEnCours?>
					</div>
					<div class="relative bordert" style="width:auto;">
						<div class="applink flex_h" style="width:100%;overflow: hidden;">
							<a class="flex_main" title="<?= date("d", $debutSemaine) . " " . strtolower($tabMois[date("n", $debutSemaine) - 1]); ?>" onclick="reloadModule('app/app_conge/app_conge_grille','*','sd=<?= $lienAvant; ?>');">
								<i class="fa fa-chevron-left"></i> &nbsp;
							</a>
							<a class="flex_main" title="<?= date("d", $debutSemaine) . " " . strtolower($tabMois[date("n", $debutSemaine) - 1]); ?>" onclick="reloadModule('app/app_conge/app_conge_grille','*','sd=<?= $lienPeuAvant; ?>');">
								<i class="fa fa-caret-left"></i> &nbsp;
							</a>
							<a class="flex_main alignright" title="<?= date("d", $finSemaine) . " " . strtolower($tabMois[date("n", $finSemaine) - 1]) . " " . date("Y", $finSemaine); ?>" onclick="reloadModule('app/app_conge/app_conge_grille','*','sd=<?= $lienPeuApres; ?>');">
								&nbsp;<i class="fa fa-caret-right"></i>
							</a>
							<a class="flex_main alignright" title="<?= date("d", $finSemaine) . " " . strtolower($tabMois[date("n", $finSemaine) - 1]) . " " . date("Y", $finSemaine); ?>" onclick="reloadModule('app/app_conge/app_conge_grille','*','sd=<?= $lienApres; ?>');">
								&nbsp;<i class="fa fa-chevron-right"></i>
							</a>
						</div>
					</div>					
				</td>
				<?  for ($i = 0; $i < $maxJours; $i++) {
				$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$month_run = date("m", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$numday_run = date("w", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				$week_run = date("W", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
				if($numday_run==1){
					?>
					<td class="td_conge_sep ededed" title="<?=$week_run ?>">&nbsp;</td>
					<?
					}
					if($last_month != $month_run){
					?>
					 <td class="td_conge_sep fond_noir">&nbsp;</td>  
					<?
					$last_month = $month_run;
					}
				?>
				<td class="td_conge_day borderb borderr" >  
					<div class="padding aligncenter"> 					
						<?= $tabJourCourt[date("w", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours))]; ?>
					<?= date("d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours)) ?>
					 </div>
				</td>
				<? } ?>
				<td style="width:17px">..</td>
			</tr>
		</table>
	</div>
	<div class="flex_main" data-table="conge" data-dsp="conge" id="conge_liste_div" style="overflow-x:hidden;overflow-y:auto;position:relative;">
		<table  class="table_conge">
		<?
		while ($arr_AG = $rs_AG->getNext()) {
			$rs_A = $APP_A -> find(array('idagent_groupe'=>$arr_AG['idagent_groupe']));
		?>
		<tr>
			<td class=" padding ms-font-l textgrisfonce alignright"><?=$arr_AG['nomAgent_groupe'] ?></td>
			<td colspan="<?=$DSP_JOURS?>" class=" " >  </td>
		</tr>	
		<?
		while ($arr_A = $rs_A->getNext()) {
			$idagent = (int)$arr_A['idagent'];
		?>
		<tr style="position:relative;" >
			<td class="td_conge_titre borderr"><a data-idagent="<?=$arr_A['idagent'] ?>" ><?=$arr_A['prenomAgent'] . ' ' . $arr_A['nomAgent'] ?></a></td>
			<?  for ($i = 0; $i < $maxJours; $i++) {
			$a = rand(0,2);
			$witDate = date("Y-m-d", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
			$witTime = strtotime($witDate);
			$month_run = date("m", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));			
			$numday_run = date("w", mktime(12, 0, 0, $moisEnCours, $premierJourSemaine + $i, $anneeEnCours));
			//  requete qui vas bien
			//  $test_cong = $APP->find(array('idagent' => $idagent,'dateDebutConge'=>$witDate));
			//	$test_cong = $APP->find(array( 'idagent' => $idagent,'timeDebutConge'=>array('$lte'=>(int)$witTime),'timeFinConge'=>array('$gte'=>(int)$witTime)));
			//  $has_conge = $test_cong->count();
			// 
			if($numday_run==1){
				?>
				<td class="td_conge_sep ccc">&nbsp;</td>
				<?
				}
				if($last_month != $month_run){
				?>
				<td class="td_conge_sep fond_noir">&nbsp;</td>
				<?
				$last_month = $month_run;
				}

				$css_d = ($numday_run==6 || $numday_run == 0)? 'ededed' : '' ;
				// $css_c = ($has_conge)? 'ccc' : '';
				$css_holiday = est_jour_ferie($witDate)? 'jaune' : '';  
			?>
			<td title="<?=$witTime ?>" class="td_conge_day  borderr borderb <?=$css_d ?> <?=$css_c ?> <?=$css_holiday?>"
				style="vertical-align:top;z-index:0;position:relative;overflow:visible;" >
				<div class="relative">
					<div class="flex_h" style="width:100%;">
						<div class="flex_main td_conge_midday"
						     heuredebut="AM"
						     datedebut="<?=date_fr($witDate) ?>"
						     data-dropzone="conge"
						     data-idagent="<?=$arr_A['idagent'] ?>"	>&nbsp;</div>
						<div class="flex_main td_conge_midday"
						     heuredebut="PM"
						     datedebut="<?=date_fr($witDate) ?>"
						     data-dropzone="conge"
						     data-idagent="<?=$arr_A['idagent'] ?>">&nbsp;</div>
					</div>
				</div>
			</td>
			<? } ?>
		</tr>
		<? } ?>
		<? } ?>
	</table>
	</div>
	<div class="fond_noir padding relative none">
		<?// = skelMdl::cf_module('app/app_conge/app_conge_reload', array('defer' => 'true', 'maxJours' => $maxJours, 'tableparent' => 'conge_liste_div', 'sd' => $_POST['sd']) + $_POST); ?>
	</div>
</div>
<script>
	load_table_in_zone('table=conge', 'conge_liste_div');
</script>