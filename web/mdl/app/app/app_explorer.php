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

	$rgba_table = implode(',', hex2rgb($APP->colorAppscheme, 0.5));
?>
<div class="flex_v " style="overflow:hidden;height:100%;">
	<div app_gui_explorer class="flex_h flex_main" style="overflow:hidden;width:100%;z-index:0;">
		<div class="frmColSmall  flex_v dark_1 app_component_info_bar_vert gradb_<?=$table?>"  >
			<div class="flex_main toggler rgba_link">
				<a class="autoToggle aligncenter active" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app/app_explorer_home','<?= http_build_query($_POST) ?>')">
					<i class="fa fa-home"></i>
				</a>
				<a class="autoToggle aligncenter" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app_liste/app_liste','table=<?= $table ?>&nbRows=750');">
					<i class="fa fa-list-ul"></i>
				</a>
				<a class="autoToggle aligncenter" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app_prod/app_prod_search','vars[mainscope_app]=prod&vars[collection]=<?= $table ?>&table=<?= $table ?>');">
					<i class="fa fa-search-plus"></i>
				</a>
				<a class="autoToggle aligncenter" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app/app_explorer_home_entete','way=back&table=<?= $table ?>&nbRows=750');">
					<i class="fa fa-info-circle"></i>
				</a>
				<a class="autoToggle aligncenter" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app_echeance/app_echeance','table=<?= $table ?>&nbRows=750');">
					<i class="fa fa-calendar"></i>
				</a>
				<a class="autoToggle aligncenter" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app_echeance/app_echeance','way=back&table=<?= $table ?>&nbRows=750');">
					<i class="fa fa-history"></i>
				</a>
			</div>
			<div>
				<div class=" "><?= skelMdl::cf_module('app/app_component/app_component_table_pin', ['table' => $table], $table); ?></div>
				<div class="aligncenter"><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', ['table' => $table]) ?></div>
			</div>
		</div>
		<div class="frmCol1 flex_v transpblanc">
			<div class="flex_h toggler applink applinkblock borderb none">
				<div class="flex_main aligncenter borderr">
					<a class="flex_main autoToggle " onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app/app_explorer_home','<?= http_build_query($_POST) ?>')"><i class="fa fa-home"></i>

						<br>
						Home
					</a>
				</div>
				<div class="flex_main aligncenter borderr">
					<!--<a class="flex_main autoToggle" onclick=" $('menu_expl_<? /*=$table*/ ?>').show();load_table_in_zone('table=<? /*= $table */ ?>&nbRows=350', 'contenu_explorer_<? /*= $table */ ?>');"><i class="fa fa-list-ul"></i>-->
					<a class="flex_main autoToggle" onclick="$('contenu_explorer_<?= $table ?>').loadModule('app/app_liste/app_liste','table=<?= $table ?>&nbRows=750');"><i class="fa fa-list-ul"></i>

						<br>
						Voir tout
					</a>
				</div>
				<div class="flex_main aligncenter">
					<a class="flex_main" onclick="ajaxInMdl('app/app_prod/app_prod_search','tmp_exp_search_<?= $table ?>','vars[mainscope_app]=prod&vars[collection]=<?= $table ?>&table=<?= $table ?>',{onglet:'Ffwd <?= $table ?>'});">
						<i class="fa fa-search-plus"></i>

						<br><?= idioma('Recherche') ?>
					</a>
				</div>
			</div>
			<div class="app_onglet toggler">
				<a class="autoToggle active" act_target="contenu_explorer_search_<?= $table ?>" mdl="app/app/app_explorer_search" vars="table=<?= $table ?>">Recherche</a>
				<a class="autoToggle" act_target="contenu_explorer_search_<?= $table ?>" mdl="app/app_prod/app_prod_nav" vars="table=<?= $table ?>">Explorer</a>
				<a class="autoToggle" act_target="contenu_explorer_search_<?= $table ?>" mdl="app/app/app_explorer_search_field" vars="table=<?= $table ?>">Plus ..</a>
			</div>
			<div class="flex_main" id="contenu_explorer_search_<?= $table ?>" data-cache="true" act_defer mdl="app/app/app_explorer_search" vars="table=<?= $table ?>">
			</div>
		</div>
		<div class="flex_main flex_v blanc" style="overflow:hidden;">
			<div class="flex_main" id="contenu_explorer_<?= $table ?>" act_defer mdl="app/app/app_explorer_home" vars="<?= http_build_query($_POST) ?>" value="<?= $uniqid ?>"
			     style="min-height:100%;overflow-x:auto;overflow-y:hidden;">
			</div>
		</div>
		<div expl_preview_zone class="flex_h blanc   absolute" style="max-width:33%;height:100%;right:0;top:0;overflow:hidden;display:none;z-index:100;">
		</div>
	</div>
</div>
<style>
	.gradb_<?=$table?> {
		background: -moz-linear-gradient(top, rgba(237,237,237,0.1) 0%, rgba(255,255,255,0.2) 33%, rgba(<?= $rgba_table ?>) 100%);
		background : linear-gradient(to bottom, rgba(237,237,237,0.1) 0%,rgba(255,255,255,0.2) 33%,rgba(<?= $rgba_table ?>) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	}
</style>
<script>
	$ ('contenu_explorer_search_<?=$table?>').on ('submit', 'form', function (event, node) {
		var form_vars = $ (node).serialize ();

		$ ('contenu_explorer_<?= $table ?>').loadModule ('app/app_liste/app_liste', 'table=<?= $table ?>&nbRows=750&' + form_vars);
	}.bind (this));

	$ ('contenu_explorer_search_<?=$table?>').on ('click', '[app_button]', function (event, node) {
		event.preventDefault ();
		Event.stop (event)
		var form_vars = $ (node).readAttribute ('vars');

		$ ('contenu_explorer_<?= $table ?>').loadModule ('app/app_liste/app_liste', 'table=<?= $table ?>&nbRows=750&' + form_vars);
		return false;
	}.bind (this));
</script>
