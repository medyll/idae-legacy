<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP = new App('appscheme');

	$vars          = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$vars_scheme          = empty($_POST['vars_scheme']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars_scheme'], 1);

	$mainscope_app = empty($_POST['mainscope_app']) ? 'prod' : $_POST['mainscope_app'];

	if (!empty($_POST['table'])) {
		$table     = $_POST['table'];
		$APP_TABLE = new App($table);
		$RS_APP    = $APP->find(['codeAppscheme' => $_POST['table']])->sort(['ordreAppscheme' => 1, 'nomAppscheme' => 1]);

		if ($APP_TABLE->has_agent()) {
			$vars['idagent'] = (int)$_SESSION['idagent'];
		}

	} else {

		$RS_APP = $APP->find($vars_scheme)->sort(['ordreAppscheme' => 1, 'nomAppscheme' => 1]);
	}

	$tr_vars = $APP->translate_vars($vars);

	$mdl          = "app/app_prod/app_prod_nav_arbo";
	$mdl_value    = $table;
	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	$HTTP_VARS_NOAGENT = $APP->translate_vars($vars_noagent);

?>
<div class="flex_v" style="width:100%;height:100%;overflow:hidden;">
	<? if (droit_table($_SESSION['idagent'], 'CONF', $table) && !empty($APP_TABLE) && $APP_TABLE->has_agent()): ?>
		<!--LIEN AGENT / ALL-->
		<div class="  toggler   blanc barre_entete" style="width:100%;">
			<div class=" applink flex_h " style="width:100%;overflow:hidden;">
				<a class="autoToggle active demi" onclick="reloadModule('<?= $mdl ?>','<?= $mdl_value ?>','table=<?= $table ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>')">
					<i class="fa fa-user-circle textvert"></i> <?= nomAgent($_SESSION['idagent'], 'prenom') ?>
				</a>
				<a class="autoToggle  demi" onclick="reloadModule('<?= $mdl ?>','<?= $mdl_value ?>','table=<?= $table ?>&<?= $HTTP_VARS_NOAGENT ?>')">
					<i class="fa fa-user-circle-o textorange"></i><?= idioma('tous') ?>
				</a>
			</div>
		</div>
	<? endif; ?>

	<div   style="position:relative;padding:0.5em; overflow-y:auto;overflow-x:hidden;">
		<?= skelMdl::cf_module('app/app_prod/app_prod_nav_fk', ['table' => $table, 'vars' => $vars], $mdl_value) ?>
	</div>
	<div class="flex_main" style="position:relative;padding:0.5em; overflow-y:auto;overflow-x:hidden;">
		<?= skelMdl::cf_module('app/app_prod/app_prod_nav_arbo', ['table' => $table, 'vars' => $vars], $mdl_value) ?>
	</div>
	<div class="bordert padding blanc" style="overflow:auto;">
		<?= skelMdl::cf_module('app/app_prod/app_prod_nav_date', ['table' => $table, 'vars' => $vars], $mdl_value) ?>
	</div>

</div>