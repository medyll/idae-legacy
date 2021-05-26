<?
	include_once($_SERVER['CONF_INC']);

?>
<div class="applink applinkblock">
	<a onclick="ajaxInMdl('app/app_droit/app_droit','tmp_exp_app_app_droit','vars[mainscope_app]=prod',{onglet:'app_droit'});">
		<i class="fa fa-key"></i> <?= idioma('Gestion des droits') ?>
	</a>
	<hr>
	<a class="hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_skel','vars[codeAppscheme_base]=sitebase_app',{onglet:'<?= idioma('Tables de données') ?>'});">
		<i class="fa fa-warning"></i> <?= idioma('Tables de données') ?>
	</a>
	<a class="hide_gui_pane" onclick="ajaxInMdl('app/app_scheme/app_scheme','tmp_app_scheme','',{onglet:'Modèle de données'})"><i class="fa fa-warning"></i> <?= idioma('Modèle de données') ?></a>
	<hr>
	<a class="hide_gui_pane" act_chrome_gui="app/app_admin/app_admin_reindex"><i class="fa fa-external-link"></i>Réindexer la base</a>
	<a class="hide_gui_pane" act_chrome_gui="app/app_admin/app_admin_consolidate"><i class="fa fa-text-height"></i>Consolider les données</a>
	<hr>
	<a class="hide_gui_pane" act_chrome_gui="app/app_admin/app_admin_revision"><i class="fa fa-external-link"></i>Construire revision</a>

	<a class="" onclick="<?= fonctionsJs::app_mdl('app/app_scheme/app_scheme_export_model',[],['title'=>'app_scheme_export_model']) ?>"> Exporter modele</a>
	<? if (droit('DEV')) { ?>
		<a class="hide_gui_pane" onclick="<?= fonctionsJs::app_mdl('app/app_sitebuilder/app_sitebuilder',[],'app_sitebuilder') ?>"><i class="fa fa-wordpress"></i></a><? } ?>
</div>
<? if (droit('DEV')) { ?>
	<div class="margin_more">
		<a onclick="loadModule('dyn/dyn_deploy')"><i class="fa fa-cloud-upload"></i></a>
	</div>
<? } ?>
<div><?= skelMdl::cf_module('business/' . BUSINESS . '/app/app_admin/app_admin'); ?></div>
<div><?= skelMdl::cf_module('customer/' . CUSTOMERNAME . '/app_admin/app_admin'); ?></div>

