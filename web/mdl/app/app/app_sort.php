<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = empty($_POST['table_value']) ? 0 : (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//

	$APP = new App($table);
	//
	$id        = 'id' . $table;
	$APP_TABLE = $APP->app_table_one;
	$BASE_APP  = $APP_TABLE['base'];
	if (!empty($table_value)):
		$RS = $APP->find([$id => $table_value] + $vars);
	else:
		$RS = $APP->find($vars);
	endif;

	$RS->sort(['ordre' . $Table => 1]);
	//
?>
<div class="blanc  margin border4 relative" app_gui_explorer>
	<div class="relative">
		<form class="Form" action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
			<input type="hidden" name="F_action" value="app_sort"/>
			<input type="hidden" name="table" value="<?= $table ?>"/>
			<input type="hidden" name="scope" value="<?= $id ?>"/>
			<input type="hidden" name="<?= $id ?>" value="<?= $table_value ?>"/>
			<input type="hidden" name="vars[m_mode]" value="1"/>
			<div class="relative margin padding" sort_zone_drag="true">
				<? while ($ARR = $RS->getNext()): ?>
					<div draggable="true" data-sort_element="true" sort_zone="sort_zone" class="padding borderb relative flex_h" style="width:100%;">
						<div class="flex_main">
							<input type="hidden" name="ordre<?= $Table ?>[]" value="<?= $ARR[$id] ?>">
							<?= empty($ARR['nom' . $Table]) ? empty($ARR['code' . $Table]) ? $ARR['field_name'] : $ARR['code' . $Table] : $ARR['nom' . $Table] ?>
						</div>
						<div class="        flex_h flex_margin" style=" vertical-align:top;">
							<div class="padding border4 sortprevious">
								<i class="fa fa-chevron-up"></i>
							</div>
							<div class="padding  border4 sortnext">
								<i class="fa fa-chevron-down"></i>
							</div>
						</div>
					</div>
				<? endwhile; ?></div>
			<div class="buttonZone">
				<input class="valid_button" type="submit" value="<?= idioma('Valider') ?>">
				<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
			</div>
		</form>
	</div>
</div>
<div class="titreFor">
	<?= idioma('Tri  des champs') ?>
</div>
<div class="enteteFor">
	<div><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
</div>