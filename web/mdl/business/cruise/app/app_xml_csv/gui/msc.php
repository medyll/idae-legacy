<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

?>
<div class="flex_h" style="overflow-x:auto;width:100%;height:100%;">
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Ftp</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>ftp/msc')">lancer recupération ftp</a>
				</div>
			</div>
			<div id="run_ftp_msc" class="flex_main">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Copy to database</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc','run=1')">lancer</a>
				</div>
			</div>
			<div id="read_msc" act_defer mdl="<?= $PATH ?>/read/readmsc" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération villes</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc_ville','run=1')">lancer</a>
				</div>
			</div>
			<div id="ville_msc" act_defer mdl="<?= $PATH ?>/read/readmsc_ville" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération destinations</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc_destination','run=1')">lancer</a>
				</div>
			</div>
			<div class="flex_main" id="destination_msc" act_defer mdl="<?= $PATH ?>/read/readmsc_destination" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération navires</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc_ship','run=1')">lancer</a>
				</div>
			</div>
			<div id="ship_msc" act_defer mdl="<?= $PATH ?>/read/readmsc_ship" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération croisieres</div>
				<div>
					<!--<a onclick="$('debug_cruise_msc').loadModule('<?/*= $PATH */?>read/readmsc_cruise','run=1')">debug</a>-->
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc_cruise','run=1')">lancer</a>
				</div>
			</div>
			<div id="debug_cruise_msc" ></div>
			<div id="cruise_msc" act_defer mdl="<?= $PATH ?>/read/readmsc_cruise" class="flex_main">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Finalisation croisieres</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readmsc_build','run=1')">lancer</a>
				</div>
			</div>
			<div id="build_msc" act_defer mdl="<?= $PATH ?>/read/readmsc_build" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
</div>
