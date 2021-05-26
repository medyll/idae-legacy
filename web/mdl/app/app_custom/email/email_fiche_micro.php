<?
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//

	$APP = new App($table);
	//
	$ARR_GROUP_FIELD = $APP->get_field_group_list('bool');
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	$R_FK            = $APP->get_reverse_grille_fk($table, $table_value);
	$HTTP_VARS       = $APP->translate_vars($vars);
	$BASE_APP        = $APP_TABLE['base'];
	//
	$arrFields = $APP->get_basic_fields();
	//
	$id  = 'id' . $table;
	$ARR = $APP->findOne([$id => $table_value]);

	//
	// vardump($ARR);
?>
	<div style="width:100%;overflow: hidden;" class="borderb margin_more ">
		<div class="padding ms-font-l">
			<?= $APP->draw_field(['field_name_raw' => 'from', 'table' => $table, 'field_value' => $ARR['from' . $Table]]) ?>
		</div>
		<div class="padding ms-font-m">
			<?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?>
		</div>
		<div class="flex_h" style="width:100%;overflow:hidden;">
			<div class="padding" style="overflow:hidden;">
				<div class="ellipsis"><?= coupeChaine(strip_tags($ARR['description' . $Table]), 80) ?></div>
			</div>
			<div class="flex_main alignright padding bold"><?= $ARR['dateCreation' . $Table] ?></div>
		</div>
		<? foreach ($ARR_GROUP_FIELD as $key => $val) {

			$arrg = $val['group'];
			$arrf = $val['field'];
			?>
			<div class="retrait flex_h flex_wrap flex_align_stretch">
				<? foreach ($arrf as $keyf => $valf) {
					if ($valf['codeAppscheme_field'] == 'nom') continue;
					if ($valf['codeAppscheme_field'] == 'login') continue;
					if ($valf['codeAppscheme_field'] == 'password') continue;
					if ($valf['codeAppscheme_field'] == 'description') continue;
					if ($valf['codeAppscheme_field'] == 'descriptionHTML') continue;
					$value = $ARR[$valf['codeAppscheme_field'] . $Table];
					if (empty($value)) continue;
					?>
					<div table="<?= $table ?>" table_value="<?= $table_value ?>" class="ok_hide flex_grow_1 borderb">
						<table class="table_info">
							<tr>
								<td class=" ">
									<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($valf['nomAppscheme_field'])) ?>
									:
								</td>
								<td>
									<?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $ARR[$valf['codeAppscheme_field'] . $Table]]) ?>
								</td>
							</tr>
						</table>
					</div>
				<? } ?>
			</div>
		<? } ?>
	</div>