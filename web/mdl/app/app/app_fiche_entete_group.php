<?
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? ['id' . $table => (int)$table_value] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];

	// CONVERSION
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;

	$APP  = new App($table);
	$Idae = new Idae($table);

	if (!empty($vars['vars'])) {
		$APPVARS = new App($table);
		$CT      = $APPVARS->find($vars['vars']);
		$z       = '<a act_chrome_gui = "app/app_liste/app_liste_gui" vars = "table=' . $vars['table'] . '&' . $APPVARS->translate_vars($vars['vars']) . '" >' . $vars['table'] . ' ' . $i . '</a >';
	}
?>
<div style="width: 100%;" class="boxshadowb">
	<div class="flex_h flex_align_top">
		<div style="width:32px;max-width:32px;" class="aligncenter padding_more margin boxshadowr   "><i class="fa fa-<?= $APP->iconAppscheme ?>" style="color:<?= $APP->colorAppscheme ?>"></i></div>
		<div class="flex_main">
			<div class=" padding_more  ">
				<?= $Idae->module('app/app/fiche_fields', ['table'            => $table,
				                                           'table_fields'    => 1,
				                                           'hide_empty'       => 1,
				                                           'hide_field_title' => 0,
				                                           'table_value'      => $table_value]) ?>
			</div>
			<div><?=$z?></div>
		</div>
	</div>
</div>

