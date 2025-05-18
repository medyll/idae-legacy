<?
	include_once($_SERVER['CONF_INC']);
	$vars          = empty($_POST['vars']) ? [] : $_POST['vars'];
	$table         = $_POST['table'];
	$APP           = new App($table);
	$hasTypeScheme = $APP->app_table_one['hasTypeScheme'];
	if (empty($hasTypeScheme))
		return;
	//
	//
	$ARR_APP  = $APP->plug('sitebase_app', 'appscheme')->findOne(['collection' => $table]);
	$BASE_APP = $ARR_APP['base'];
	$GRILL_FK = $ARR_APP['grilleFK'];

	$table_type      = $table . '_type';
	$nom_type        = 'nom' . ucfirst($table_type);
	$id_type         = 'id' . $table_type;
	$rsType          = $APP->plug($BASE_APP, $table_type)->find()->sort([$nom_type => 1]);
	$auto_tree_count = skelMongo::connect($BASE_APP, $table)->find()->count();

	//   $APP->;

	if ($rsType->count() != 0):

		?>
		<div class="autoBlock">
			<?
				foreach ($rsType as $row_type):
					$add_vars             = 'vars[' . $id_type . ']=' . $row_type[$id_type];
					$auto_tree_count_type = skelMongo::connect($table, $BASE_APP)->find([$id_type => (int)$row_type[$id_type]])->count();
					?>
					<div class="blanc">
						<div auto_tree
						     auto_tree_count="<?= $auto_tree_count_type ?>">
							<a app_button="<?= $table ?>"
							         vars="table=<?= $table ?>&<?= $add_vars ?>">.. <?= $row_type[$nom_type] ?> </a>
						</div>
						<div act_defer
						     mdl="app/app_nav_value"
						     vars="table=<?= $table ?>&<?= $add_vars ?>"
						     class="autoBlock toggler"
						     style="display: none;"></div>
					</div>
					<?
				endforeach; ?>
		</div>
		<?
	endif;
?>