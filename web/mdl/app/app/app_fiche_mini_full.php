<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	//
	if ($table == 'agent_tuile' || $table == 'agent_activite'|| $table == 'agent_history'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;

	$APP = new App($table);
	//
	$ARR_GROUP_FIELD = $APP->get_field_group_list(['$nin' => ['', '']]);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$css = empty($APP->get_settings($_SESSION['idagent'], $table . '_preview_fk')) ? 'none' : $APP->get_settings($_SESSION['idagent'], $table . '_preview_fk');

?>
<div class="">
	<div class="borderb relative ">
		<div class="padding flex_h flex_align_middle ">
			<div class=""><i class="fa fa-<?= $APP->iconAppscheme ?> fa-fw" style="color:<?= $APP->colorAppscheme ?>"></i></div>
			<div class="flex_main"> <?= $APP->nomAppscheme ?></div>
			<div class="flex_h flex_align_middle" >
				<?
					$arr_has = ['statut', 'type', 'categorie', 'group'];
					foreach ($arr_has as $key => $value):
						$APPTMP = new App($value);
						$Value  = ucfirst($value);
						$_table = $table . '_' . $value;
						$_Table = ucfirst($_table);
						$_id    = 'id' . $_table;
						$_nom   = 'nom' . $_Table;
						if (!empty($ARR[$_nom])): ?>
							<div class="borderl">
								<span style="color:<?= $ARR['color' . $_Table] ?>">
									<?= $APPTMP->draw_field(['field_name_raw' => 'icon', 'table' => $_table, 'field_value' => $ARR['icon' . $_Table]]) ?>
								</span>
								<?= $APPTMP->draw_field(['field_name_raw' => 'nom','field_name' => $_nom, 'table' => $table, 'field_value' => $ARR['nom' . $_Table]]) ?>
							</div>
						<? endif; ?>
					<? endforeach; ?>
			</div>
		</div>
		<div class=" padding_more">
			<div class="retrait"><?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?></div>
		</div>
	</div>
	<div class="">
		<? foreach ($ARR_GROUP_FIELD as $key => $val) {
			$arrg = $val['group'];
			$arrf = $val['field'];
			?>
			<div class="retrait flex_h flex_wrap flex_align_stretch">
				<? foreach ($arrf as $keyf => $valf) {
					if ($valf['codeAppscheme_field'] == 'nom') continue;
					if ($valf['codeAppscheme_field'] == 'login') continue;
					if ($valf['codeAppscheme_field'] == 'password') continue;
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
	<div class="  retrait">
		<?= skelMdl::cf_module('app/app/app_fiche_fk', ['act_chrome_gui' => 'app/app_liste/app_liste_gui', 'mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?>
	</div>
</div>