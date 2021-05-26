<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$time = time();
	$i    = 0;
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$table = $_POST['table'];
	$Table = ucfirst($table);
	$APP   = new App($table);

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
		$periode['semaine_prochaine'] = ['titre' => 'Sem. prochaine', 'dates' => [$prochain_lundi, $fin_semaine_prochaine]];

		// $periode['mois_fin']      = ['titre' => 'Fin du mois', 'dates' => [$prochain_lundi, $dernier_jour_mois]];
		$periode['mois_prochain'] = ['titre' => 'Mois prochain', 'dates' => [$premier_jour_prochain_mois, $dernier_jour_prochain_mois]];
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

	$ARR_TYPE_DATE       = ['date' => '', 'installation' => '_ins', 'debut' => '_deb', 'fin' => '_fin'];
	$ARR_TYPE_DATE_FIELD = ['date' => 'date', 'installation' => 'dateInstallation', 'debut' => 'dateDebut', 'fin' => 'dateFin'];
	$ARR_TYPE_DATE_ICON  = ['date' => 'fa-plus', 'installation' => 'plus-circle', 'debut' => 'plus-circle textvert', 'fin' => 'minus-circle textrouge'];

	//echo $lundi_semaine_derniere->format('d-m-Y');echo $fin_derniere_semaine->format('d-m-Y');
?>
<div class="    ">
	<div class="padding relative boxshadowb bordert">
		<div class="   flex_align_middle  padding   flex_h">
			<div class="padding aligncenter " style="width:40px"><i class="fa fa-calendar fa-2x textgrisfonce"></i></div>
			<div class="flex_main bold">
				<?= strtoupper(idioma($main_titre)) ?>
			</div>
		</div>
	</div>
	<div class="flex_v padding">
		<div class="  ">
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
			// vardump(['$gte' => $date_begin_str, '$lte' => $date_begin_str]);
			if (!empty($_POST['table'])) {
				$APP_tmp    = new App($_POST['table']);
				$TEST_AGENT = $APP_tmp->has_agent();
				$vars_tmp   = $vars;
				// if (!empty($TEST_AGENT)):$vars_tmp['idagent'] = (int)$_SESSION['idagent']; endif;
				$Tmp_table = ucfirst($_POST['table']);

				$rs_tot     = $APP_tmp->find($vars_tmp + ['date' . $Tmp_table => ['$gte' => $date_begin_str, '$lte' => $date_end_str]]);
				$rs_tot_deb = $APP_tmp->find($vars_tmp + ['dateDebut' . $Tmp_table => ['$gte' => $date_begin_str, '$lte' => $date_end_str]]);
				$rs_tot_fin = $APP_tmp->find($vars_tmp + ['dateFin' . $Tmp_table => ['$gte' => $date_end_str, '$lte' => $date_end_str]]);

			}

			?>
			<div class="padding" title="<?= $titre_period ?>">
				<div class="flex_h flex_align_middle" style="width:100%;">
					<div class="" style="width:80px;overflow:hidden">
					 &nbsp;<?= $titre ?>
					</div>
					<div class="flex_h flex_margin flex_main  ">
					<? if (!empty($_POST['table'])) { ?>
						<div class="flex_grow_0 padding border4"><span class="bold titre1"> <?= $rs_tot->count() ?></span><i class="fa fa-plus textbleu"></i></div>
						<div class="flex_grow_0 padding border4"><span class="bold titre1"> <?= $rs_tot_deb->count() ?></span><i class="fa fa-plus-circle textvert"></i></div>
						<div class="flex_grow_0 padding border4"><span class="bold titre1"> <?= $rs_tot_fin->count() ?> </span><i class="fa fa-minus-square textorange"></i></div>
					<? } ?>
					</div>
				</div>
			</div>
		<? } ?></div>
	</div>
</div>