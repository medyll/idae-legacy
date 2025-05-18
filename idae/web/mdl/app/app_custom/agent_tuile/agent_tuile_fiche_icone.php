<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 02/10/2015
	 * Time: 22:18
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$mdl_table       = 'agent_tuile';
	$mdl_table_value = (int)$_POST['table_value'];
	$mdl_Table       = ucfirst($mdl_table);
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$id          = 'id' . $table;
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	// CONVERSION
	$APP = new App($table);
	$ARR = $APP->query_one([$id => $table_value] + $vars);

	$table       = $ARR['code' . $mdl_Table];
	$Table       = ucfirst($table);
	$table_value = (int)$ARR['valeur' . $mdl_Table];
	$id          = 'id' . $table;
	// CONVERSION
	$APP = new App($table);
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$APPOBJ = $APP->appobj($table_value);
?>
<div class=" relative borderr borderb cursor edededhover">
	<div class="relative mastershow flex_h" ondblclick="<?= fonctionsJs::app_fiche($table, $table_value) ?>">
		<div class="applink alignright ededed borderr"><?= skelMdl::cf_module('app/app_gui/app_gui_tile_click', ['table' => $table, 'table_value' => $table_value], $table_value); ?></div>
		<div class="tile" data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
			<div class="aligncenter mastershow" style="vertical-align:top;padding:0.25em;">
				<div class="inline relative padding " style="width: 50px;overflow:hidden;">
					<i class="fa fa-file-o fa-2x textgris" style="color:<?= $APPOBJ->ICON_COLOR ?>"></i>

					<br>
					<div class="inline absolute" style="top:10px">
						<i class="fa fa-<?= $APP->iconAppscheme ?> fa-2x"></i>
					</div>
				</div>
				<div class="">
					<div class="tile_text aligncenter"><span class="borderb textgris"> <?= $table ?> </span></div>
					<div class="tile_text aligncenter inline" style="height: 5vh;overflow:hidden;">
						<?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . ucfirst($table)]]) ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
