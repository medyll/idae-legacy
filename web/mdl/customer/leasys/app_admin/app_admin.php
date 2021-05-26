<?
	include_once($_SERVER['CONF_INC']);

?>
<div class="minibox">
	<div class="applink applinkblock blanc">

		<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_first','Import Artis My2Mg')"><i class="fa fa-cloud-upload"></i>Lancer import Artis</a>
		<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_mysql','Import Artis All Mysql')"><i class="fa fa-retweet"></i>Resynchroniser bases</a>
		<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_client_prospect','Import client prospects')"><i class="fa fa-retweet"></i>Import client prospects et taches depuis Idae</a>
		<hr>

		<a data-menu="data-menu">Autres</a>
		<div class="context_app_menu" style="display:none;">
			<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_check','Recherche element Artis')">Recherche element Artis</a>
			<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_insert','Insert element Artis')">Insert element Artis</a>
			<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_csv','Import csv')"> import csv ssc Artys</a>
			<a class="hide_gui_pane" onclick="ajaxMdl('customer/<?=CUSTOMERNAME?>/app_admin/app_csv_contact','Import csv contact')"> import csv contact Artys</a>
		</div>
	</div>
</div>