<?
	include_once($_SERVER['CONF_INC']);

	// la valeur et combnien y'en a

	ini_set('display_errors', 0);
	$table = $_POST['table'];
	// nbre par date
	$APP               = new APP($table);
	$APP_SCH           = new APP('appscheme');
	$APP_SCH_TY        = new APP('appscheme_type');
	$APP_SCH_FIELD     = new APP('appscheme_field');
	$APP_SCH_FIELD_TY  = new APP('appscheme_field_type');
	$APP_SCH_HAS_FIELD = new APP('appscheme_has_field');

	$type_date      = (empty($_POST['type_date'])) ? 'dateCreation' : $_POST['type_date'];
	$Table          = ucfirst($table);
	$name_id        = 'id' . $table;
	$vars           = (empty($_POST['vars'])) ? [] : $_POST['vars'];
	$app_stat_scope = empty($_POST['app_stat_scope']) ? 'app_stat_scope' : $_POST['app_stat_scope'];
	$scope_str      = "scope='$app_stat_scope'";

	$dateDebut = date_mysql($_POST['dateDebut']);
	$dateFin   = date_mysql($_POST['dateFin']);

	$dateStart = new DateTime($dateDebut);
	$dateEnd   = new DateTime($dateFin);

	$vars_date = (empty($dateDebut) || empty($dateFin)) ? [] : [$type_date . $Table => ['$gte' => $dateDebut, '$lte' => $dateFin]];

	$arr_type = ['valeur','prix_precis','prix','pourcentage','heure'];
?>
	<div class="applinkblock  applink toggler padding_more   flex_h flex_wrap flex_margin  ">
		<?

			// tous les field type
			$RS_SCH_FIELD_TY = $APP_SCH_FIELD_TY->find(['codeAppscheme_field_type'=>['$in'=>$arr_type]]);
			while ($ARR_SCH_FIELD_TY = $RS_SCH_FIELD_TY->getNext()) {
				$idappscheme_field_type = (int)$ARR_SCH_FIELD_TY['idappscheme_field_type'];
				// Dates
				$ARR_FIELD_DATE = $APP_SCH_FIELD->distinct_all('idappscheme_field', ['idappscheme_field_type' => $idappscheme_field_type]);
				$ARR_HAS        = $APP_SCH_HAS_FIELD->distinct_all('idappscheme_field', ['codeAppscheme' => $table, 'idappscheme_field' => ['$in' => $ARR_FIELD_DATE]]);
				$RS_FIELD       = $APP_SCH_FIELD->find(['idappscheme_field' => ['$in' => $ARR_HAS]])->sort(['ordreAppscheme_field' => 1]);
				if ($RS_FIELD->count() == 0) continue;
				?>
			 		<?// =$ARR_SCH_FIELD_TY['codeAppscheme_field_type']?>
					<? while ($ARR_FIELD = $RS_FIELD->getNext()) {
						$css                 = (empty($i)) ? '' : '';
						$i                   = true;
						$stop                = false;
						$idappscheme_field   = $ARR_FIELD['idappscheme_field'];
						$iconAppscheme_field = $ARR_FIELD['iconAppscheme_field'];
						$codeAppscheme_field = $ARR_FIELD['codeAppscheme_field'];
						$nomAppscheme_field  = $ARR_FIELD['nomAppscheme_field'];

						$HIGH = $APP->find($vars + $vars_date, [$codeAppscheme_field . $Table => 1])->sort([$codeAppscheme_field . $Table => 1])->getNext();
						$LOW  = $APP->find($vars + $vars_date, [$codeAppscheme_field . $Table => 1])->sort([$codeAppscheme_field . $Table => -1])->getNext();
						$DIST = $APP->distinct_all($codeAppscheme_field . $Table, $vars + $vars_date);
						$DIST = fonctionsProduction::cleanPostMongo(array_filter($DIST, "my_array_filter_fn"), 1);
						sort($DIST);

						//	vardump();
						$LOW  = $DIST[0];
						$HIGH = end($DIST);
						if (empty($HIGH)) continue;
						$moyenne = array_sum($DIST) / sizeof($DIST);
						$mediane = calculateMedian($DIST);
						$moyenne = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $moyenne, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
						$mediane = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $mediane, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
						$low     = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $LOW, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
						$high    = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $HIGH, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
						?>
						<div class="<?= $css ?> flex_main flex_grow_1  ededed ">
							<div class="borderb">
								<a app_button data-vars="chart_count_field=<?= $codeAppscheme_field ?>"><i class="fa fa-<?= $iconAppscheme_field ?>"></i> <?= $nomAppscheme_field ?></a>
								<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_draw" vars="chart_count_field=<?= $codeAppscheme_field ?>&<?= http_build_query($_POST) ?>&type_stat=summary"></div>
							</div>
						</div>
					<? } ?>

				<?
			}

		?>
	</div>

