<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	// CONVERSION
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history' || $table == 'agent_table'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
		$Table       = ucfirst($table);
	endif;

	$APP = new App($table);
	$Idae = new Idae($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$css = empty($APP->get_settings($_SESSION['idagent'], $table . '_preview_fk')) ? '' : $APP->get_settings($_SESSION['idagent'], $table . '_preview_fk');


?>
<div main_auto_tree class="">
	<div auto_tree  onclick="save_setting_autoNext(this,'<?= $table ?>_preview_fk')" style="border-bottom:1px solid <?= $APP->colorAppscheme ?>">
		<div class="relative ">
			<div class="flex_h flex_align_middle">
				<div class="padding"><i class="fa fa-<?= $APP->iconAppscheme ?>"></i></div>
				<div class="padding ">
					<div class="ellipsis"><?= $APP->nomAppscheme ?></div>
				</div>
				<div class="padding flex_main  ">
					<a onclick="<?= fonctionsJs::app_fiche($table, $table_value); ?>"><?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="relative" style="margin-left:15px; ">
		<?= $Idae->module('app/app/fiche_fields', ['table'         => $table,
		                                           'in_mini_fiche' => 1,
		                                           'hide_empty' => 1,
		                                           'table_value'   => $table_value]) ?>
	</div>
</div>