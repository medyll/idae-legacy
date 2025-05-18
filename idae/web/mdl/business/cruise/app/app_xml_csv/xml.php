<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';
?>
<div class="blanc relative flex_v" style="overflow:hidden;width:100%;height:100%;">
	<div class="titre_entete">
		<a onclick="reloadModule('<?= $_POST['module'] ?>','*')"> Gestion flux XML</a>
	</div>
	<div class="flex_h flex_main" style="height: 100%;overflow:hidden;">
		<div class="frmCol1">
			<div class="applink applinkblock">
				<div class="autoNext">MSC</div>
				<div class="toggler">
					<a class="autoToggle" onClick="$('xml_csv_pad').loadModule('<?=$PATH?>gui/msc','fourn=msc')">Lancer</a>
					<!--<a class="autoToggle" onClick="ajaxMdl('<?/*=$PATH*/?>xml_launch','','fourn=msc')">Lancer</a>-->

				</div>
				<div class="autoNext">Costa</div>
				<div class="toggler">
					<a class="autoToggle" onClick="$('xml_csv_pad').loadModule('<?=$PATH?>gui/costa','fourn=costa')">Lancer</a>

				</div>
			</div>
		</div>
		<div class="frmCol2 blanc" id="xml_csv_pad" style="overflow: hidden">
		</div>
	</div>
</div>