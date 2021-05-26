<?
	include_once($_SERVER['CONF_INC']);

	$APP_SCH           = new APP('appscheme');
	$APP_SCH_TY        = new APP('appscheme_type');
	$APP_SCH_FIELD     = new APP('appscheme_field');
	$APP_SCH_FIELD_TY  = new APP('appscheme_field_type');
	$APP_SCH_HAS_FIELD = new APP('appscheme_has_field');

	$table             = $_POST['table'];
	$vars              = (empty($_POST['vars'])) ? [] : $_POST['vars'];
	$chart_count_field = (empty($_POST['chart_count_field'])) ? 'ct' : $_POST['chart_count_field'];
	$type_periodicite  = (empty($_POST['type_periodicite'])) ? 'week' : $_POST['type_periodicite'];
	$type_stat         = (empty($_POST['type_stat'])) ? 'general' : $_POST['type_stat'];
	$type_date         = (empty($_POST['type_date'])) ? '' : $_POST['type_date'];
	$Table             = ucfirst($table);
	$name_id           = 'id' . $table;

	$APP        = new App($table);
	$table_name = $APP->nomAppscheme;

	// Dates
	if (empty($type_date)) {
		$ARR_FIELD_DATE = $APP_SCH_FIELD->distinct_all('idappscheme_field', ['codeAppscheme_field_type' => 'date']);
		$ARR_HAS        = $APP_SCH_HAS_FIELD->distinct_all('idappscheme_field', ['codeAppscheme' => $table, 'idappscheme_field' => ['$in' => $ARR_FIELD_DATE]]);
		$RS_DATE_FIELD  = $APP_SCH_FIELD->find(['idappscheme_field' => ['$in' => $ARR_HAS]])->sort(['ordreAppscheme_field' => 1]);

		while ($ARR_FIELD = $RS_DATE_FIELD->getNext()) {
			$codeAppscheme_field = $ARR_FIELD['codeAppscheme_field'];
			if ($APP->has_field($codeAppscheme_field)) {
				$type_date = $codeAppscheme_field;
				break;
			}
		}
	}
	// test data
	// $APP->distinct_all('id' .$table, [$type_date . $Table => ['$gte' => $dateDebut, '$lte' => $dateFin]]);

	$chart_data = '';
	$chart_type = '';

	$chart_code = $table . $type_stat . 'stat' . $type_periodicite . uniqid();

	$dateDebut = date_mysql($_POST['dateDebut']);
	$dateFin   = date_mysql($_POST['dateFin']);

	$dateStart = new DateTime($dateDebut);
	$dateEnd   = new DateTime($dateFin);

	switch ($_POST['type_stat']) {
		case 'general':
			$chart_data = do_date($type_periodicite);
			$chart_type = 'line';
			break;
		case 'type':
			$chart_data = do_has('type');
			$chart_type = 'polarArea';
			//vardump($chart_data);exit;
			break;
		case 'statut':
			$chart_data = do_has('statut');
			$chart_type = 'pie';
			break;
		case 'categorie':
			$chart_data = do_has('categorie');
			$chart_type = 'doughnut';
			break;
		case 'summary':
			$chart_data = do_summary($_POST['chart_count_field']);
			$chart_type = 'bar';
			// vardump($chart_data);exit;
			break;
	}

	$chart_json = do_obj($chart_data['labels'], $chart_data['datasets'], $chart_type);

	function do_has($type) {
		$Type = ucfirst($type);
		global $APP, $table, $Table, $type_date, $dateDebut, $dateFin, $vars;
		$APP_TY           = new APP($table.'_'.$type);

		$vars_date = (empty($dateDebut) || empty($dateFin)) ? [] : [$type_date . $Table => ['$gte' => $dateDebut, '$lte' => $dateFin]];

		if ($APP->app_table_one['has' . $Type . 'Scheme']) {
			$statues = $statues_titre = [];
			$gr_all  = $APP->distinct_all('id' . $table . '_' . $type, $vars_date + $vars);
			foreach ($gr_all as $key) {
				$ct_all          = $APP->find(['id' . $table . '_' . $type => (int)$key] + $vars_date + $vars)->count();
				$arr_ty = $APP_TY->findOne(['id' . $table . '_' . $type=>(int)$key]);
				$statues[]       = $ct_all;
				$statues_titre[] = $arr_ty['nom'.$Table. '_' . $type];
			}
			$flat_type       = json_encode(array_values($statues));
			$flat_type_titre = json_encode(array_values($statues_titre));

			return ['labels' => $flat_type_titre, 'datasets' => $flat_type];
		}
	}

	function do_summary($field,$ppage=6) {

		global $APP, $table, $Table, $type_date, $dateDebut, $dateFin, $vars, $APP_SCH_FIELD;
		$vars_date = (empty($dateDebut) || empty($dateFin)) ? [] : [$type_date . $Table => ['$gte' => $dateDebut, '$lte' => $dateFin]];
		$arr_ty    = $APP_SCH_FIELD->findOne(['codeAppscheme_field' => $field]);
		$statues   = $statues_titre = [];
		$DIST      = $APP->distinct_all($field . $Table, $vars_date + $vars);
		$DIST      = fonctionsProduction::cleanPostMongo(array_filter($DIST, "my_array_filter_fn"), 1);
		sort($DIST);
		$GROUP = array_chunk($DIST, sizeof($DIST) / $ppage);

		foreach ($GROUP as $key => $arr_key) {
			$arr_key = fonctionsProduction::cleanPostMongo($arr_key, 1);
			$titre = calculateMoyenne($arr_key);
			$titre     = $APP->cast_field(['field_name_raw' => $field, 'field_value' => $titre, 'codeAppscheme_field_type' => $arr_ty['codeAppscheme_field_type']]);;
			$statues_titre[] =$titre;
			// $statues[] = ($key * (sizeof($DIST)/4));
			// between
			$a         = $APP->find($vars_date + $vars + [$field . $Table => ['$gte' => $arr_key[0], '$lte' => end($arr_key)]])->count();
			$statues[] = $a;

		}

		$flat_type       = json_encode(array_values($statues));
		$flat_type_titre = json_encode(array_values($statues_titre));

		return ['labels' => $flat_type_titre, 'datasets' => $flat_type];

	}

	/*echo '<h2>Quarters</h2>';
	$firstQuarterStart = new DateTime ( 'first day of January' );
	$firstQuaterEnd = clone $firstQuarterStart;
	$firstQuaterEnd->modify ( "+3 months" );

	echo 'First Quarter Start: ' . $firstQuarterStart->format ( 'Y-m-d' ) . '<br>';
	echo 'First Quarter End: ' . $firstQuaterEnd->format ( 'Y-m-d' ) . '<br><br>';

	$secondQuarterStart = new DateTime ( 'first day of April' );
	$secondQuarterEnd = clone $secondQuarterStart;
	$secondQuarterEnd->modify ( '+3 months' );

	echo 'Second Quarter Start: ' . $secondQuarterStart->format ( 'Y-m-d' ) . '<br>';
	echo 'Second Quarter End: ' . $secondQuarterEnd->format ( 'Y-m-d' ) . '<br><br>';

	$thirdQuarterStart = new DateTime ( 'first day of July' );
	$thirdQuarterEnd = clone $thirdQuarterStart;
	$thirdQuarterEnd->modify ( '+3 months' );

	echo 'Third Quarter Start: ' . $thirdQuarterStart->format ( 'Y-m-d' ) . '<br>';
	echo 'Third Quarter End: ' . $thirdQuarterEnd->format ( 'Y-m-d' ) . '<br><br>';

	$forthQuarterStart = new DateTime ( 'first day of October' );
	$forthQuarterEnd = new DateTime ( 'first day of January next year' );

	echo 'Forth Quarter Start: ' . $forthQuarterStart->format ( 'Y-m-d' ) . '<br>';
	echo 'Forth Quarter End: ' . $forthQuarterEnd->format ( 'Y-m-d' ) . '<br><br>';*/

	function do_date($type_periodicite) {
		global $APP, $Table, $type_date, $dateStart, $dateEnd, $vars, $chart_count_field;
		$labels = $datasets = $vars_date = [];
		$INDEX  = $ct = '';

		while ($dateStart->format('Y-m-d') <= $dateEnd->format('Y-m-d')) {
			$DADATE       = $dateStart->format('Y-m-d');
			$DADATE_WEEK  = clone $dateStart;
			$DADATE_WEEK  = $dateStart->modify('+1 week')->format('Y-m-d');
			$DADATE_MONTH = $dateStart->format('Y-m');
			$DADATE_YEAR  = $dateStart->format('Y');
			// pour trimestre
			// recupére 1er janvier
			$QUARTERS = [1 => []];
			//
			switch ($type_periodicite) {
				case "day":
					$vars_date  = [$type_date . $Table => new MongoRegex('/^' . $DADATE . '/')];
					$ct         = $APP->find($vars_date + $vars);
					$INDEX      = $DADATE;
					$INDEX_NAME = fonctionsProduction::jourMoisDate_fr_short($DADATE);
					break;
				case "week":
					$vars_date  = [$type_date . $Table => ['$gte' => $DADATE, '$lte' => $DADATE_WEEK]];
					$ct         = $APP->find($vars_date + $vars);
					$INDEX      = $DADATE_WEEK;
					$INDEX_NAME = fonctionsProduction::jourMoisDate_fr($DADATE);
					break;
				case "month":
					$vars_date  = [$type_date . $Table => new MongoRegex('/^' . $DADATE_MONTH . '/')];
					$ct         = $APP->find($vars_date + $vars);
					$INDEX      = $DADATE_MONTH;
					$INDEX_NAME = fonctionsProduction::mois_short_Date_fr($DADATE);
					break;
				case "quarter": // 1ere date dans quarter ceil(date('m')/3)
					$vars_date  = [$type_date . $Table => new MongoRegex('/^' . $DADATE_MONTH . '/')];
					$ct         = $APP->find($vars_date + $vars);
					$INDEX      = $DADATE_MONTH;
					$INDEX_NAME = fonctionsProduction::mois_short_Date_fr($DADATE);
					break;
				case "year":
					$vars_date  = [$type_date . $Table => new MongoRegex('/^' . $DADATE_YEAR . '/')];
					$ct         = $APP->find($vars_date + $vars);
					$INDEX      = $DADATE_YEAR;
					$INDEX_NAME = fonctionsProduction::moisDate_fr($DADATE);
					break;
			}
			switch ($chart_count_field) {
				case "ct":
					$chart_count = $ct->count();
					break;
				default:
					$DIST = $APP->distinct_all($chart_count_field . $Table, $vars_date + $vars);
					// vardump(array_sum($DIST));
					// => moyenne pour : duree
					$chart_count = array_sum($DIST);

					break;
			}
			//
			$labels[$INDEX]   = $INDEX_NAME;
			$datasets[$INDEX] = $chart_count;
			//
			$dateStart->modify('+1 ' . $type_periodicite);
		}
		$flat_labels   = json_encode(array_values($labels));
		$flat_datasets = json_encode(array_values($datasets));

		return ['labels' => $flat_labels, 'datasets' => $flat_datasets];
	}

	function do_obj($flat_labels, $flat_datasets, $chart_type = 'ligne') {
		global $table_name;
		$first_dataset = "{label : '$table_name', data : $flat_datasets}";

		return "{type:'$chart_type',data:{labels:$flat_labels,datasets:[" . $first_dataset . "]}}";
	}

?>
<div class="">
	<div class="titre_entete">
		<?= idioma('par') . ' ' . $type_stat ?> <?= $chart_type ?> <?= $type_periodicite ?> - <?= $chart_count_field ?>
	</div>
	<div class="relative" style="max-width:100%;max-height:650px;">
		<canvas id="<?= $chart_code ?>"></canvas>
	</div>
</div>
<script>
	var ctx = document.getElementById('<?=$chart_code?>').getContext('2d');
	var myChart<?= $chart_code ?> = new Chart(ctx, <?=$chart_json?>, {options: {maintainAspectRatio: true}}); //,fullWidth: true , {fullWidth: true}
</script>