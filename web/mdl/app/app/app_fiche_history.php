<?
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$Table = ucfirst($table);

	if (!droit_table($_SESSION['idagent'], 'R', $table)) return;

	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP    = new App($table);
	$APPOBJ = $APP->appobj($table_value, $vars);
	$ARR    = $APPOBJ->ARR;
	//
	/** @var  $EXTRACTS_VARS */
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);
	//
	$name_id = 'id' . $table;
	//
	$nom = 'nom' . ucfirst($table);

?>
<?
	$arr_same = [];
	foreach ($APPOBJ->GRILLE_FK as $field):
		//	print_r($field);
		$scheme_fk  = $field['codeAppscheme'];
		$id_fk      = 'id' . $scheme_fk;
		$idfk_value = (int)$ARR[$id_fk];
		if (empty($idfk_value)) continue;
		$arr_same[$id_fk] = $idfk_value;
		// if (str_find($scheme_fk,'_ligne')) continue;
	endforeach;
	if (!empty($arr_same)) {
		$RS_SAME = $APP->find($arr_same + [$name_id => ['$nin' => [$table_value]]])->sort(['dateCreation' . $Table => -1])->limit(5);

		if ($RS_SAME->count() != 0) {
			$list_vars = $APP->translate_vars($arr_same);
			?>
			<div  class="  padding_more" style="overflow:hidden; ">
				<div class="padding alignright ellipsis  "><?= idioma('Derniers') . ' ' . $table . ' crée(e)s' ?> <i class="fa fa-history    "></i></div>
				<div class="padding retrait">
					<?
						while ($ARR_SAME = $RS_SAME->getNext()):

							?>
							<div class="alignright textgris none"><?= date_fr($ARR_SAME['dateCreation' . $Table]); ?></div>
							<div class="padding ">
								<div class="ellipsis">
									<a onclick="<?= fonctionsJs::app_fiche($table, $ARR_SAME[$name_id]) ?>">- <?= strtolower($ARR_SAME['nom' . $Table]); ?></a>
								</div>
							</div>
							<?
						endwhile;
						if ($RS_SAME->count() != $RS_SAME->count(true)) {
							?>
							<div class="padding alignright ededed">
								<a onclick="<?= fonctionsJs::app_liste($table, '5', $list_vars . '&sortBy=dateCreation' . $Table . '&sortDir=-1') ?>"><?= idioma('Voir tout') . ' (' . $RS_SAME->count() . ')' ?></a>
								<div class="textbold none"> <?= $APP->vars_to_titre($arr_same) ?></div>
							</div>
						<? } ?>
				</div>
			</div>
		<? } ?>
	<? } ?>