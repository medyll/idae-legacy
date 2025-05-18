<?
	include_once($_SERVER['CONF_INC']);
	$time = time();
	ini_set('display_errors', 55);
	if (empty($_POST['typeCalendar'])) {
		$_POST['typeCalendar'] = 'quotidien';
	}

	// CONFIG
	$maxJours = 40;
	$hours    = ['00', '15', '30', '45'];

	$tabMois      = ["Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre"];
	$tabJour      = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
	$tabJourCourt = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

	if (!empty($_POST['date'])) {
		$tmpdate     = explode("/", $_POST['date']);
		$jour        = $tmpdate[0];
		$mois        = !empty($tmpdate[1]) ? $tmpdate[1] : date("m");
		$annee       = !empty($tmpdate[2]) ? $tmpdate[2] : date("Y");
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

	$jourEnCours  = date("d", $sd);
	$moisEnCours  = date("m", $sd);
	$anneeEnCours = date("Y", $sd);

	$indexJourCrt = date("w", $sd);
	if ($indexJourCrt == 0)
		$indexJourCrt = 7;
	// deduction du premier jour de cette semaine
	$premierJourSemaine = $jourEnCours - $indexJourCrt + 1;

	$debutSemaine = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine, $anneeEnCours);
	$finSemaine   = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 6, $anneeEnCours);

	$lienAvant    = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 30, $anneeEnCours);
	$lienPeuAvant = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine - 15, $anneeEnCours);
	$lienApres    = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 30, $anneeEnCours);
	$lienPeuApres = gmmktime(12, 0, 0, $moisEnCours, $premierJourSemaine + 15, $anneeEnCours);

	// liste des agents sur la droite => defiler jours semaines / mois /trimestres /
	$APP   = new App('conge');
	$APP_A = new App('agent');

	$APP_AG = new App('agent_groupe');
	$rs_AG  = $APP_AG->find();
	$css    = ['blanc', 'ccc', 'blanc'];

	$DSP_JOURS = 0;
	//

	$date_begin = new DateTime(date($annee.'-'.$mois.'-') . '01', new DateTimeZone('Europe/Paris'));
	$date_end   = clone $date_begin;
	$date_end->modify('last day of +6 month');
	$date_begin_str = $date_begin->format('Y-m-d');
	$date_end_str   = $date_end->format('Y-m-d');

?>
	<style>
		.td_conge_groupe {
			line-height      : 3;
			overflow         : hidden;
			border-top       : 1px solid #ccc;
			border-bottom    : 1px solid #fff;
			background-color : #ededed;
		}
		.td_conge_groupe_titre {
			padding       : 0 0.25em;
			text-overflow : ellipsis;
			font-weight   : bold;;
		}
		.td_conge_agent {
			line-height  : 2;
			overflow     : hidden;
			border-top   : 1px solid #ededed;
			border-right : 1px solid #fff;
		}
		.td_conge_agent.css_table {
			width : 100%;
		}
		.td_conge_agent_titre {
			padding       : 0 0.25em;
			text-overflow : ellipsis;
		}
		.td_conge_day {
			width      : 35px;
			min-width  : 35px;
			max-width  : 35px;
			text-align : center;
			padding    : 0.5em;
		}
		.td_conge_day_titre {
			background-color : white; border-right : 1px solid #ccc;
		}
	</style>
<?
	$str_link = '';
	$str_arr  = [];
	while ($arr_AG = $rs_AG->getNext()) {
		$rs_A = $APP_A->find(['idagent_groupe' => $arr_AG['idagent_groupe']],['_id'=>0]);
		$str_link .= '<div class="td_conge_groupe td_conge_groupe_titre">' . $arr_AG['nomAgent_groupe'] . '</div>';
		$str_arr[] = '<div class="td_conge_groupe">&nbsp;</div>';
		$arr_rs_A = @iterator_to_array($rs_A);
		foreach ($arr_rs_A as $arr_A) {
			$idagent = (int)$arr_A['idagent'];
			$str_link .= '<div data-idagent="' . $arr_A['idagent'] . '" class="td_conge_agent td_conge_agent_titre">' . $arr_A['prenomAgent'] . ' ' . $arr_A['nomAgent'] . ' </div>';
			$for_str   = "data-idagent='$idagent'  data-dropzone='conge' str_tmp";
			$str_arr[] = '<div class="td_conge_agent css_table"><div ' . $for_str . ' heuredebut="AM"   class="css_cell demi">&nbsp;</div><div  ' . $for_str . ' heuredebut="PM"  class="css_cell demi">&nbsp;</div></div>';

		}
	} ?>
	<div class=" " style="height:100%;;position:relative;overflow-x:auto;overflow-y:auto;">
		<div class="flex_h" style=" position:static; min-height:100%;">
			<div class="  borderr" style="width:230px;min-width:230px;position:sticky;left:0;z-index:50;">
				<div class="padding_more borderb ededed">&nbsp;</div>
				<div class="    " style="width:100%;">
					<div class=" ">
						<div class="padding_more blanc borderb alignright" style="position:sticky;top:0;z-index:50;"><i class="fa fa-calendar-o"></i></div>
						<div class="flex_v">
							<div class="td_conge_day">
								&nbsp;
								<br>
								&nbsp;
							</div>
							<div><?= $str_link ?></div>
						</div>
					</div>
				</div>
			</div>
			<div class=" " data-table="conge" data-dsp="conge" id="conge_liste_div">
				<div class="css_table">
					<?
						for ($i = $date_begin; $date_begin < $date_end; $i->modify('first day of +1 month')) {
							$period_month       = $i;
							$period_clone_month = clone $i;
							$period_end_month   = clone $i;
							$period_end_month->modify('last day of this month');
							?>
							<div class="css_cell borderr" style="overflow:visible;">
								<div class="padding_more borderb ededed"><?= fonctionsProduction::moisDate_fr($period_end_month->format("Y-m-d")) ?></div>
								<div class="  css_table ">
									<?
										for ($ai = $period_clone_month; $period_clone_month < $period_end_month; $ai->modify('+8 day')) {
											$period_week              = $ai;
											$period_clone_week        = clone $ai;
											$period_clone_week_monday = clone $ai;
											$period_end_week          = clone $ai;
											$period_end_week->modify('sunday this week');
											$period_clone_week_monday->modify('monday this week');
											// si pas le meme mois, of
											if ($period_clone_week->format('Y') != $period_clone_month->format('Y')) echo $period_week->format('Y');
											?>
											<div class="css_cell  borderl" style="margin-right:0em;">
												<div class="">
													<div class="padding_more borderb"  style="position:sticky;top:0;">Semaine&nbsp;<?= $period_clone_week->format('W') ?></div>
													<div class="  flex_h">
														<?
															for ($aai = $period_clone_week_monday; $period_clone_week_monday <= $period_end_week; $aai->modify('+1 day')) {
																$period_day = clone $aai;
																$datedebut  = $period_day->format('d/m/Y');
																$new_str    = " datedebut='$datedebut' ";
																$expl       = array_map(function ($n) {
																	global $new_str;

																	return str_replace('str_tmp', $new_str, $n);
																}, $str_arr);

																// si pas le meme mois, of
																if ($period_day->format('Y') != $period_clone_week->format('Y')) continue;
																if ($period_day->format('m') != $period_clone_week->format('m')) continue;
																// $css_holiday = est_jour_ferie($period_day->format('Y-m-d'))? 'jaune' : '';
																?>
																<div class="<?= $css_holiday ?>">
																	<div class="td_conge_day td_conge_day_titre" style="position:sticky;top:0;">
																		<?= $tabJourCourt[$period_day->format('w')]; ?>
																		<br>
																		<?= $period_day->format('d') ?>
																	</div>
																	<div>
																		<?= implode('', $expl) ?>
																	</div>
																</div>
															<? } ?>
													</div>
												</div>
											</div>
										<? } ?>
								</div>
							</div>
						<? } ?>
				</div>
			</div>
		</div>
	</div>
	<script>
		// alert('goo')
		setTimeout (function () {load_table_in_zone ('table=conge', 'conge_liste_div')}, 1);
	</script>
<? // vardump_async('rd'); ?>