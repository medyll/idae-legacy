<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 09:36
	 */
	include_once($_SERVER['CONF_INC']);
	$table = empty($_POST['table']) ? 'client' : $_POST['table'];
	$APP   = new App($table);
?>
<div app_gui_explorer class="flex_v blanc" style="overflow:hidden;width:100%;z-index:0;height:100%;">
	<div class="titre_entete relative flex_h flex_align_middle">
		<div class="flex_main ms-font-m"><i class="fa fa-<?= $APP->iconAppscheme ?> textbleu"></i> <?= idioma('Console des ') ?><?= $table ?>s</div>
		<div expl_file_reload mdl="app/app_liste/app_liste_pager">
			<?= skelMdl::cf_module("app/app_liste/app_liste_pager", $_POST) ?>
		</div>
		<div><span expl_count_report="true" class="padding fond_noir color_fond_noir"></span>&nbsp;</div>
		<div>
			<a class="ellipsis padding " expl_view_button="expl_view_button">s</a>
		</div>
	</div>
	<div>
		<div act_defer mdl="app/app/app_console_entete" vars="table=<?= $table ?>">
		</div>
	</div>
	<div class=" " expl_file_list expl_file_zone id="contenu_console_<?= $table ?>" data-data_model="defaultModel" vars="<?= http_build_query($_POST) ?>" value="<?= $uniqid ?>"
	     style="overflow-x:auto;overflow-y:hidden;">
	</div>
	<div style="top:0;height:100%;right:0;" expl_preview_zone class="absolute">

	</div>
</div>
<script>
	load_table_in_zone('console_mode=1&table=<?= $table ?>&nbRows=350', 'contenu_console_<?= $table ?>');
</script>