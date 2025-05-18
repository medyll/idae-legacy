<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$time = time();
	$i    = 0;
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$table = $_POST['table'];
	$Table = ucfirst($table);
	$APP  = new App($table);

	$dateStart = new DateTime();

	$WAY     = empty($_POST['way']) ? 'forward' : $_POST['way'];
	$default = empty($_POST['periode_default']) ? 'semaine' : $_POST['periode_default'];

	$DADATE               = $dateStart->format('Y-m-d');
	$jourEnCours          = $dateStart->format('d');
	$moisEnCours          = $dateStart->format('m');
	$anneeEnCours         = $dateStart->format('Y');
	$numeroSemaineEnCours = $dateStart->format('W');

	$hier = clone $dateStart;
	$hier->modify('yesterday');
	$ce_lundi = clone $dateStart;
	$ce_lundi->modify('monday this week');
	$lundi_semaine_derniere = clone $dateStart;
	$lundi_semaine_derniere->modify('monday last week');
	$fin_derniere_semaine = clone $dateStart;
	$fin_derniere_semaine->modify('sunday last week');
	$fin_semaine = clone $dateStart;
	$fin_semaine->modify('last day next week');
	$fin_semaine_prochaine = new DateTime();
	$fin_semaine_prochaine->modify('sunday next week');
	$prochain_lundi = clone $dateStart;
	$prochain_lundi->modify('monday next week');
	$premier_jour_du_mois = clone $dateStart;
	$premier_jour_du_mois->modify('first day of this month');
	$dernier_jour_mois = clone $dateStart;
	$dernier_jour_mois->modify('last day of this month');
	$premier_jour_prochain_mois = clone $dateStart;
	$premier_jour_prochain_mois->modify('first day of next month');
	$dernier_jour_prochain_mois = clone $dateStart;
	$dernier_jour_prochain_mois->modify('last day of next month');

	// 	cette semaine
	// 	le reste du mois
	//	le mois prochain
	//  on prend les timestamp. puis boucle :
	if ($WAY == 'forward') {
		$main_titre                   = 'echeancier';
		$periode['semaine']           = ['titre' => 'Cette semaine', 'dates' => [$ce_lundi, $fin_semaine]];
		$periode['semaine_prochaine'] = ['titre' => 'Semaine prochaine', 'dates' => [$prochain_lundi, $fin_semaine_prochaine]];

		$periode['mois_fin']      = ['titre' => 'Fin du mois', 'dates' => [$prochain_lundi, $dernier_jour_mois]];
		$periode['mois_prochain'] = ['titre' => 'Le mois prochain', 'dates' => [$premier_jour_prochain_mois, $dernier_jour_prochain_mois]];
	}
	if ($WAY == 'back') {
		$main_titre = 'historique';
		// WAYBACKMACHINE
		$periode['cette_semaine']    = ['titre' => 'Cette semaine', 'dates' => [$ce_lundi, $hier]];
		$periode['semaine_derniere'] = ['titre' => 'Semaine derniere', 'dates' => [$lundi_semaine_derniere, $fin_derniere_semaine]];
		$periode['ce_mois']          = ['titre' => 'Plus tot ce mois ci', 'dates' => [$premier_jour_du_mois, $lundi_semaine_derniere]];
	}

	// Les schemes avec dates :
	$rs_sc = $APP->get_schemes(['codeAppscheme' => $table]);//['hasDateScheme' => 1]
	// $rs_sc = $APP->get_schemes();
	$arr_sc = iterator_to_array($rs_sc);
	//
	$BIG_ARR = [];
	//$arrdate = $periode[$default]['dates'];
	//$titre   = $periode[$default]['titre'] . " du " . $arrdate[0]->format('d/m/Y') . ' au ' . $arrdate[1]->format('d/m/Y');

	$ARR_PERIOD = $periode;

	$ARR_TYPE_DATE       = ['date' => '', 'installation' => '_ins','debut' => '_deb', 'fin' => '_fin'];
	$ARR_TYPE_DATE_FIELD = ['date' => 'date', 'installation' => 'dateInstallation','debut' => 'dateDebut', 'fin' => 'dateFin'];
	$ARR_TYPE_DATE_ICON  = ['date' => 'fa-plus', 'installation' => 'plus-circle','debut' => 'plus-circle textvert', 'fin' => 'minus-circle textrouge'];

	//echo $lundi_semaine_derniere->format('d-m-Y');echo $fin_derniere_semaine->format('d-m-Y');
?>
<div style="margin:0.5em;" main_auto_tree>
	<div auto_tree class="padding ">
		<div class="trait  flex_align_middle  padding uppercase flex_h">
			<div class="flex_main">
				<?= strtoupper(idioma($main_titre)) ?>
			</div>
		</div>
	</div>
	<div class="flex_h flex_wrap flex_align_top margin retrait">
		<? foreach ($periode as $code_periode => $val_periode) {
			$ARR_DATE       = $periode[$code_periode]['dates'];
			$date_begin     = $ARR_DATE[0];
			$date_end       = $ARR_DATE[1];
			$date_begin_str = $date_begin->format('Y-m-d');
			$date_end_str   = $date_end->format('Y-m-d');

			if (strtotime($date_begin_str) > strtotime($date_end_str)) continue;
			$titre             = $ARR_PERIOD[$code_periode]['titre'];
			$titre_period      = " du " . fonctionsProduction::jourMoisDate_fr_short($date_begin->format('Y-m-d')) . ' au ' . fonctionsProduction::jourMoisDate_fr($date_end->format('Y-m-d'));
			$count_empty_month = 0;
			//
			if (!empty($_POST['table'])) {
				$APP_tmp    = new App($_POST['table']);
				$TEST_AGENT = $APP_tmp->has_agent();
				$vars_tmp   = $vars;
				// if (!empty($TEST_AGENT)):$vars_tmp['idagent'] = (int)$_SESSION['idagent']; endif;
				$Tmp_table = ucfirst($_POST['table']);

				$rs_tot     = $APP_tmp->find($vars_tmp + ['date' . $Tmp_table => ['$gte' => $date_begin_str, '$lte' => $date_begin_str]]);
				$rs_tot_deb = $APP_tmp->find($vars_tmp + ['dateDebut' . $Tmp_table => ['$gte' => $date_begin_str, '$lte' => $date_begin_str]]);
				$rs_tot_fin = $APP_tmp->find($vars_tmp + ['dateFin' . $Tmp_table => ['$gte' => $date_end_str, '$lte' => $date_end_str]]);

			}

			?>
			<div style="margin-bottom:2em;overflow:hidden;min-width:50%;" class=" " main_period>
				<div class="" style="width:100%;" auto_tree right="right">
					<div class="flex_h flex_align_middle margin">
						<div class="padding aligncenter borderr" style="width:40px"><i class="fa fa-calendar fa-2x textgrisfonce"></i></div>
						<div class="padding" style="min-width:250px">
							<div class="ms-font-m"><?= $titre ?></div>
							<div class="retrait textgrisfonce"> <?= $titre_period ?></div>
						</div>
						<div class="padding none">
							<? if (!empty($_POST['table'])) { ?>
								<?= $rs_tot->count() ?> <?= idioma('debut') ?> ,
								<?= $rs_tot_deb->count() ?> <?= idioma('debut') ?> ,
								<?= $rs_tot_fin->count() ?> <?= idioma('fin') ?>
							<? } ?>
						</div>
					</div>
				</div>
				<div style="margin-left:45px;overflow:auto;width:auto;" class="flex_h flex_wrap flex_margin">
					<?
						for ($i = $date_begin; $date_begin <= $date_end; $i->modify('+1 day')) {
							$dadate      = $i->format("Y-m-d");
							$datime      = strtotime($dadate);
							$count_empty = 0;
							$count_empty_month++;
							//
							$vars_tmp   = $vars;
							$test_count = 0;
							foreach ($ARR_TYPE_DATE as $key_date => $valkey_date) {
								$vars_to_key = $vars_tmp + [$ARR_TYPE_DATE_FIELD[$key_date] . $Table => $dadate];
								$rs_test     = $APP->find($vars_to_key)->count();
								$test_count += $rs_test;
							}
							if($test_count==0) continue;
							?>
							<div id="eche_table_empty<?= $datime ?>" class="" style="width:250px;margin-bottom:0.5em;">
								<div class=" ">
									<div class="padding ellipsis"><i class="fa fa-calendar-o"></i><?=$test_count?> <?= fonctionsProduction::jourMoisDate_fr_short($dadate) ?></div>
								</div>
								<div boucle scheme table class="">
									<? foreach ($arr_sc as $key => $value) {
										$dsp = '';

										$table      = $value['codeAppscheme'];
										$Table      = ucfirst($table);
										$APP_tmp    = new App($table);
										$GRILLE_FK  = $APP_tmp->get_grille_fk();
										$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
										$vars_tmp   = $vars;


										$BIG_ARR[$key]['table'][$key][$dadate] = $table;
										?>
										<div>
											<? if (empty($_POST['table'])) { ?>
												<div auto_tree class="margin padding "><div><i class="fa fa-<?= $value['icon'] ?> textbleu"></i> <?= ucfirst($value['nomAppscheme']) ?></div></div>
											<? } ?>
											<div class="" style="display:<?//= $dsp ?>;overflow:hidden;">
												<? $stille_here = [];

													foreach ($ARR_TYPE_DATE as $k_date => $val_date) {
														//$rset           = 'rs_tmp' . $val_date; //
														$vars_to = $vars_tmp + [$ARR_TYPE_DATE_FIELD[$k_date] . $Table => $dadate];
														if($k_date=='fin'){$vars_to['dateDebut'.$Table]=['$ne'=>$dadate];};

														$rset = $APP_tmp->find($vars_to)->limit(5);
														// echo $rset2->count();
														$count_date     = $rset->count();
														$count_date_all = $rset->count(true);
														if (!$count_date) continue;
														//
														$http_vars = ['vars' => [$ARR_TYPE_DATE_FIELD[$k_date] . $Table => $dadate]];

													//	if($k_date=='fin'){$http_vars['vars']['dateDebut'.$Table]=['$ne'=>$dadate];};
														//vardump($http_vars);
														$onclick   = fonctionsJs::app_gui('app_liste/app_liste_gui', $table, $value_id, $http_vars);
														?>
														<div class="flex flex_h flex_align_middle bordert edededHover">
															<div class="flex_h flex_align_middle borderr applink  aligncenter" style="width:30px;">
																<a onclick="<?= $onclick ?>"><i class="fa fa-eye textbold"></i>
																	<? if ($count_date != 1) { ?><? } ?>
																	<? if ($count_date != $count_date_all) { ?>
																		<br><?= $count_date ?>
																	<? } ?></a>
																&nbsp;
															</div>
															<div class="flex_main" style="overflow: hidden;">
																<?
																	while ($arr_tmp = $rset->getNext()) {
																		$value_id = (int)$arr_tmp['id' . $table];
																		if (!empty($stille_here[$value_id])) continue;
																			$stille_here[$value_id] = $value_id;
																		?>
																		<a class="hide_gui_pane ellipsis  autoToggle" data-contextual="table=<?= $table ?>&table_value=<?= $value_id ?>" act_chrome_gui="app/app/app_fiche"
																		   data-vars="table=<?= $table ?>&table_value=<?= $value_id ?>">
																			<i class="fa fa-<?= $ARR_TYPE_DATE_ICON[$k_date] ?> fa-fw"></i> <?= $arr_tmp['nom' . ucfirst($table)]; ?>
																		</a>
																		<?
																	}
																?>
															</div>
														</div>
													<? } ?>
											</div>
										</div>
										<?
										// $dsp='none';
									}

										if ($count_empty == sizeof($arr_sc)) { ?>
											<style>/*#eche_table_empty<?=$datime?> {
													display : none
												}*/</style>
										<? } ?>
								</div>
							</div>
						<? } ?>
				</div>
			</div>
			<? if ($count_empty_month == 0) { ?>
				<style>/*#month_table_empty<?=$datime?> {
						display : none
					}*/</style>
			<? } ?>
			<?
		} ?>
	</div>
</div>