<?
	include_once($_SERVER['CONF_INC']);

	//
	echo $table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	//
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;

	$APP  = new App($table);
	$Idae = new Idae($table);
	//
	$id  = 'id' . $table;
	$ARR = $APP->findOne([$id => $table_value]);

?>
<div class="">
	<div class="relative">
		<div class="flex_h flex_align_middle" style="border-bottom:1px solid <?= $APP->colorAppscheme ?>">
			<div class="padding_more"><i class="fa fa-<?= $APP->iconAppscheme ?>"></i>
			</div>
			<div class="padding_more flex_main ">
				<div class="ellipsis">
					<?= $APP->nomAppscheme ?>
					<a onclick="<?= fonctionsJs::app_fiche($table, $table_value); ?>"><?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="">
		<?= $Idae->module('app/app/fiche_fields', ['table'         => $table,
		                                           'in_mini_fiche' => 1,
		                                           'hide_empty'    => 1,
		                                           'table_value'   => $table_value]) ?>
	</div>
	<? if($table=="service"){ ?>
		<?= skelMdl::cf_module('app/app/app_fiche_rfk', ['mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?>

	<? }?>
</div>