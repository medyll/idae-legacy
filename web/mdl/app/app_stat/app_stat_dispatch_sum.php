<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
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

	$arr_type = ['valeur', 'prix_precis', 'prix', 'pourcentage', 'heure']
?>
	<div class="margin_more alignright">
		Du <?= fonctionsProduction::jourMoisDate_fr($dateDebut) ?>
		au <?= fonctionsProduction::jourMoisDate_fr($dateFin) ?>
	</div>
	<div class="margin_more ededed  flex_h flex_wrap">
		<?

			// tous les field type
			$RS_SCH_FIELD_TY = $APP_SCH_FIELD_TY->find(['codeAppscheme_field_type' => ['$in' => $arr_type]]);
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

					switch ($ARR_SCH_FIELD_TY['codeAppscheme_field_type']) {
						case 'date' :
							$stop = true;

							break;
						case 'texte' :
							$stop = true;

							break;
						case 'phone' :
							$stop = true;

							break;
						case 'color' :
							$stop = true;

							break;
						case 'textelibre' :
							$stop = true;

							break;
						case 'email' :
							$stop = true;

							break;
						case 'valeur' :

							break;
						case 'heure' :

							break;
						case 'prix' :

							break;
						case 'prix_precis' :

							break;
					}
					if (!empty($stop)) continue;
					//	vardump();
					$LOW  = $DIST[0];
					$HIGH = end($DIST);
					if (empty($HIGH)) continue;
					$moyenne = calculateMoyenne($DIST);
					$mediane = calculateMedian($DIST);
					$moyenne = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $moyenne, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
					$mediane = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $mediane, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
					$low     = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $LOW, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
					$high    = $APP->cast_field(['field_name_raw' => $codeAppscheme_field, 'field_value' => $HIGH, 'codeAppscheme_field_type' => $ARR_SCH_FIELD_TY['codeAppscheme_field_type']]);
					?>
					<div class="<?= $css ?> flex_main">
						<div class="borderb bold applink">
							<a app_button data-vars="chart_count_field=<?= $codeAppscheme_field ?>"><i class="fa fa-<?= $iconAppscheme_field ?>"></i> <?= $nomAppscheme_field ?></a>
						</div>
						<div class="retrait">
							<div class="flex_h">
								<div class=" flex_h flex_main">
									<div><i class="textrouge fa fa-long-arrow-right fa-rotate-90"></i> <span><?= $low ?></span></div>

								</div>
								<div></div>
								<div class="flex_h flex_main"><span><?= $high ?></span><i class="textvert fa fa-long-arrow-right fa-rotate-270"></i></div>
							</div>
							<div class="textvert flex_h">
								<div class="padding"><i class="textorange fa fa-credit-card"></i> med</div>
								<div class="padding  flex_h flex_main"><span><?= $mediane; ?></span></div>
							</div>
							<div class="textbleu flex_h">
								<div class="padding"><i class="textgris fa fa-mobile"></i> moy</div>
								<div class="padding  flex_h flex_main"><span><?= $moyenne ?></span></div>
							</div>
						</div>
					</div>
				<? } ?>
				<?
			}

		?>
	</div>
