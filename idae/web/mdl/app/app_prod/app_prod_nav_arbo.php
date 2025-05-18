<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP = new App('appscheme');

	$vars          = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$mainscope_app = empty($_POST['mainscope_app']) ? 'prod' : $_POST['mainscope_app'];

	if (!empty($_POST['table'])) {
		$table     = $_POST['table'];
		$APP_TABLE = new App($table);
		$RS_APP    = $APP->find(['codeAppscheme' => $_POST['table']])->sort(['ordreAppscheme' => 1, 'nomAppscheme' => 1]);

		if ($APP_TABLE->has_agent()) {
			//	$vars['idagent']=(int)$_SESSION['idagent'];
		}
	} else {

		$RS_APP = $APP->find($vars)->sort(['ordreAppscheme' => 1, 'nomAppscheme' => 1]);
	}


	$tr_vars = $APP->translate_vars($vars);

	$mdl = "app/app_prod/app_prod_nav";
?>
<div main_auto_tree class="applink applinkblock toggler relative flex_main" style="position:relative;padding:0.5em; overflow-y:auto;overflow-x:hidden;">
	<?
		foreach ($RS_APP as $ARR_APP):
			//
			$table           = $ARR_APP['codeAppscheme'];
			$Table         = ucfirst($table);
			$APP_TMP       = new App($table);
			$ARR_TMP_TABLE = $APP_TMP->app_table_one;

			$auto_tree_count = $APP_TMP->find($vars)->count();

			//
			?>
			<div auto_tree auto_tree_count="<?= $auto_tree_count ?>">
				<a class="autoToggle" app_button="<?= $table ?>" vars="table=<?= $table ?>&<?= $tr_vars ?>"><i class="fa fa-<?= $APP_TMP->iconAppscheme ?>"></i> <?= $table ?></a>
			</div>
			<div style="position:relative;">
				<?
					$arr_has = ['categorie', 'statut', 'type'];
					foreach ($arr_has as $key => $value):
						$Value        = ucfirst($value);
						$table_type = $table . '_' . $value;
						$Table_type = ucfirst($table_type);
						//
						$id_type  = 'id' . $table_type;
						$nom_type = 'nom' . ucfirst($table_type);
						//
						if (empty($ARR_TMP_TABLE['has' . $Value . 'Scheme'])) continue;
						$APP_TMP_type = new App($table_type);
						$rsType       = $APP_TMP_type->find()->sort([$nom_type => 1]);
						?>
						<div class="autoBlock">
							<div auto_tree>
								<a class="autoToggle">Par <?= ucfirst(idioma($Value)) ?></a>
							</div>
							<div class="autoBlock toggler" style="display:none;overflow:hidden;">
								<?
									foreach ($rsType as $row_type):
										$add_vars             = 'vars[' . $id_type . ']=' . $row_type[$id_type];
										$auto_tree_count_type = $APP_TMP->find($vars + [$id_type => (int)$row_type[$id_type]])->count();
										?>
										<div auto_tree auto_tree_count="<?= $auto_tree_count_type ?>">
											<a app_button="<?= $table ?>" vars="table=<?= $table ?>&<?= $tr_vars ?>&<?= $add_vars ?>" class="ellipsis"><?= $row_type[$nom_type] ?> </a>
										</div>
										<div act_defer
										     mdl="app/app_prod/app_prod_nav_group"
										     vars="table=<?= $table ?>&<?= $tr_vars ?>&<?= $add_vars ?>"
										     class="autoBlock toggler"
										     style="display: none;"></div>
										<?
									endforeach; ?>
							</div>
						</div>
					<? endforeach; ?>
				<? if (sizeof($ARR_APP['grilleFK']) != 0) : ?>
					<div class="autoBlock">
						<div auto_tree>
							<a class="autoToggle">Grouper</a>
						</div>
						<div class="autoBlock toggler"><?= skelMdl::cf_module('app/app_prod/app_prod_nav_group', ['table' => $table, 'vars' => $vars]) ?></div>
					</div>
				<? endif; ?>
			</div>
		<? endforeach; ?>
</div>