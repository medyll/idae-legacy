<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 08/11/2016
	 * Time: 16:15
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	$table       = empty($_POST['table']) ? 'destination' : $_POST['table'];
	$table_value = empty($_POST['table_value']) ? '0' : $_POST['table_value'];
	$page        = empty($_POST['page']) ? 'fiche' : $_POST['page'];
	$vars        = empty($_POST['vars']) ? [] : $_POST['vars'];

	if (!defined('APPSITE')) {
		echo "pas de APPSITE";

		return;
	}
	$APP      = new App();
	$APP_SITE = new AppSite();

	$filter = ['estSiteAppscheme' => 1];

	// $page = $APP_SITE->get_page('fiche',[ 'table'=>'ville','table_value'=>51,'vars'=>['idtest'=>'test']]);//
	$fiche_detail = $APP_SITE->get_page('fiche_detail', ['table' => $table, 'table_value' => (int)$table_value]);//, 'vars' => ['idtest' => 'test']
	$fiche        = $APP_SITE->get_page($page, ['table' => $table, 'table_value' => (int)$table_value, 'vars' => ['idtest' => 'test']]);//
	$liste        = $APP_SITE->get_page('liste', ['table' => $table, 'table_value' => (int)$table_value, 'vars' => ['iddestination' => 2]]);//

?>
	<div class="flex_h flex_align_top flex_margin fond_noir flex_border" style="height:100%;overflow:hidden;">
		<div class="border4 blanc" style="min-width:960px;width:960px;max-width:960px;height:100%;overflow:auto;">
			<?= $fiche_detail ?>
		</div>
		<div class="border4 blanc" style="min-width:960px;width:960px;max-width:960px;height:100%;overflow:auto;"><?= $fiche ?>
		</div>
		<div class="border4 blanc" style="min-width:960px;width:960px;max-width:960px;height:100%;overflow:auto;"><?= $liste ?>
		</div>
	</div>
<?

