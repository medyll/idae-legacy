<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']);
	$table = 'tache';
	$_POST['vars']['idagent'] = $_SESSION['idagent'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$id = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	//
	$zone = uniqid($table);


?>
<div class="flex_v relative" style="height:100%;" app_gui_explorer>
	<div class="ededed flex_h relative borderb" style="width:100%;">
		<div class="flex_main alignright padding relative" style="width:100%;">
			<div><input placeholder="Rechercher" expl_search_button="expl_search_button" style="width:200px;"
			            type="text"></div>
		</div>

	</div>
	<div class="relative borderb">
		<?= skelMdl::cf_module('app/app_prod/app_prod_liste_menu', $_POST); ?>
	</div>
	<div class="flex_main relative" style="height:100%;overflow: auto;position:relative;" expl_file_zone>
		<div style="height:100%;position:relative;" expl_file_list id="<?= $zone ?>"></div>
	</div>
	<div class="padding">
		<progress id="auto_progress_<?= $table ?>" style="display: none;"></progress>
	</div>
</div>
<script>
	load_table_in_zone('table=tache&<?=http_build_query($_POST)?>&<?=$APP->translate_vars($vars)?>','<?=$zone?>');
	/*new BuildTbl($('<?=$zone?>'), {
	table_name: '<?=$table?>',
	groupBy: '<?=$groupBy?>',
	url_data: '<?=http_build_query($_POST)?>&<?=$APP->translate_vars($vars)?>',
	nbRows: 2000
	});*/
</script>