<?
	include_once($_SERVER['CONF_INC']);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	//
	$APP = new App($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$GRILLE_FK       = $APP->get_grille_fk();
	$HTTP_VARS       = $APP->translate_vars($vars);
	$BASE_APP        = $APP_TABLE['base'];
	//
	$arrFields = $APP->get_basic_fields_nude($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$top   = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom   = 'nom' . ucfirst($table);

	$arrFieldsBool = $APP->get_array_field_bool();
?>
<div class="relative ededed">
	<form action="<?= ACTIONMDL ?>app/actions.php" onsubmit="return false;">
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<? foreach ($vars as $key => $input): ?>
			<input type="hidden" name="vars[<?= $key ?>]" value="<?= $input ?>">
		<? endforeach; ?>
		<div class="  relative">
			<? foreach ($ARR_GROUP_FIELD as $key => $val) {
				$arrg = $val['group'];
				$arrf = $val['field'];
				?><br>
				<div class="relative blanc  boxshadow">
					<? foreach ($arrf as $keyf => $valf) {
						if ($valf['nomAppscheme_field'] == 'description') continue;
						?>
						<div class="borderb  padding">
							<div class="flex_h flex_align_middle relative" style="max-width: 100%;overflow: hidden;">
								<div class=" "><i class="fa fa-<?= $valf['iconAppscheme_field'] ?> textgris"></i></div>
								<div class="flex_main relative" style="overflow: hidden">
										<?= $APP->draw_field_input(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => null, 'className' => 'inputFull'], 'vars_search') ?>

								</div>
							</div>
						</div>
					<? } ?>
				</div>
			<? } ?>
		</div>
		<div class="buttonZone">
			<button type="submit" value="Ok"><i class="fa fa-search"></i></button>
		</div>
	</form>
</div>