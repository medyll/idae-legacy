<?
	include_once($_SERVER['CONF_INC']);
?>
<hr>
<div class="minibox">
	<div class="applink applinkblock blanc">
		<a class="hide_gui_pane" onclick="<?= fonctionsJs::app_mdl('app/app_admin/app_build_pre_prod') ?>"><i class="fa fa-upload"></i> <?= idioma('Mise en production') ?></a>
		<? if (droit('DEV')) { ?>
			<a class="hide_gui_pane" onclick="<?= fonctionsJs::app_mdl('app/appsite/appsite') ?>"><i class="fa fa-trash"></i> Construire site</a><? } ?>
		<a class="hide_gui_pane" onclick="loadModule('dyn/dyn_produit_clean','run=1')"><i class="fa fa-trash"></i> Nettoyer base-produit</a>
		<a class=" " onclick="loadModule('dyn/dyn_cache_vide')"><i class="fa fa-trash-o"></i> Vider le cache</a>
		<a class="hide_gui_pane" onclick="<?= fonctionsJs::app_mdl('app/app_xml_csv/xml') ?>"><i class="fa fa-code"></i> XML CSV</a>
		<a class="hide_gui_pane" onclick="<?= fonctionsJs::app_mdl('app/app_xml/xml') ?>"><i class="fa fa-code"></i> XML FIBOS</a>
	</div>
</div>