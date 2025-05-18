<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche_micro.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche_micro', $_POST);

		return;
	}

	$APP = new App($table);
	//
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$icon            = $APP->iconAppscheme;
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$arr_dsp_fields = $APP->get_display_fields($table);
	unset($arr_dsp_fields['description'], $arr_dsp_fields['commentaire']);
?>
<div style="margin-bottom:2em;" main_auto_tree data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
	<div onclick="'.$onclick.'" class="flex_h marginb edededhover cursor">
		<div class="aligncenter padding" style="width:46px;"><i class="textbold padding border4 fa fa-<?= $icon ?> fa-2x"></i></div>
		<div>
			<div class="titre1 padding"><?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?></div>
			<div class="flex_h flex_wrap flex_align_middle padding bordert borderb">
			<?

				foreach ($GRILLE_FK as $field):
					$id_fk = $field['idtable_fk'];
					$APPFK = new APP($field['codeAppscheme']);
					//
					$arrq     = $APPFK->findOne([$field['idtable_fk'] => (int)$ARR[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
					$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
					//
					if (!empty($dsp_name)) {

						?>

						<div class='flex_main demi'>
							<div class="ellipsis"><i class='textgris   fa fa-<?= $field['iconAppscheme'] ?>'></i><?= strtolower($dsp_name) ?></div>
						</div>
					<? }
				endforeach;
			?></div>
			<div class="     ">
				<? foreach ($ARR_GROUP_FIELD as $key => $val) {
					$arrg = $val['group'];
					$arrf = $val['field'];
					?>
					<div class="flex_h flex_wrap flex_align_middle">
						<? foreach ($arrf as $keyf => $valf) {
							if($arrg['codeAppscheme_field_group']=='identification' || $arrg['codeAppscheme_field_group']=='codification'|| $arrg['codeAppscheme_field_group']=='valeur')continue;
							$value = strip_tags($ARR[$valf['codeAppscheme_field'] . $Table]);
							if (empty($value)) continue;
							?>
							<div title=" <?= ucfirst($valf['nomAppscheme_field']) ?>" class="" style="min-width:50%;">
								<div class="ellipsis"><i class="textgris padding fa fa-<?= $valf['iconAppscheme_field'] ?>"></i>
									<?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $value]) ?>
								</div>
							</div>
						<? } ?>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>