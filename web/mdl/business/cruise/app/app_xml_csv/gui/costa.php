<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

?>
<div class="flex_h" style="overflow-x:auto;width:100%;height:100%;">
	<div class="frmCol1 none">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Ftp</div>
				<div>
					<a onclick="$('run_ftp_costa').loadModule('<?= $PATH ?>ftp/costa')">lancer recupération ftp</a>
				</div>
			</div>
			<div id="run_ftp_costa" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Copy to database</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>ftp/costa_catalog','run=1')">Export</a>
				</div>
			</div>
			<div id="run_costa_catalog" act_defer mdl="<?= $PATH ?>/ftp/costa_catalog" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération villes</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_ville','run=1')">lancer</a>
				</div>
			</div>
			<div id="ville_costa" act_defer mdl="<?= $PATH ?>/read/readcosta_ville" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération destinations</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_destination','run=1')">lancer</a>
					<!--<a onclick="$('debug_destinations').loadModule('<?/*= $PATH */?>read/readcosta_destination','run=1')">lancer</a>-->
				</div>
			</div>
			<div id="debug_destinations" ></div>

			<div class="flex_main" id="destination_costa" act_defer mdl="<?= $PATH ?>/read/readcosta_destination" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération navires</div>
				<div>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_ship','run=1')">lancer</a>
				</div>
			</div>
			<div id="ship_costa" act_defer mdl="<?= $PATH ?>/read/readcosta_ship" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération dates de depart</div>
				<div>
					<!--<a onclick="$('debug_iti_costa').loadModule('<?/*= $PATH */?>read/readcosta_datedepart','run=1')">debug</a>-->
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_datedepart','run=1')">lancer</a>
				</div>
			</div>
			<div id="debug_iti_costa" ></div>
			<div id="datedepart_iti" act_defer mdl="<?= $PATH ?>/read/readcosta_datedepart" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Récupération croisieres</div>
				<div>
					<a onclick="$('debug_cruise_costa').loadModule('<?= $PATH ?>read/readcosta_cruise','run=1')">debug</a>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_cruise','run=1')">lancer</a>
				</div>
			</div>
			<div id="debug_cruise_costa" ></div>
			<div id="xml_cruise_costa" act_defer mdl="<?= $PATH ?>/read/readcosta_cruise" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
	<div class="frmCol1">
		<div class="flex_v">
			<div class="titre_entete borderb flex_h">
				<div class="flex_main">Itinéraires</div>
				<div>
					<a onclick="$('debug_itilast_costa').loadModule('<?= $PATH ?>read/readcosta_iti','run=1')">debug</a>
					<a onclick="runModule('mdl/<?= $PATH ?>read/readcosta_iti','run=1')">lancer</a>
				</div>
			</div>
			<div id="debug_itilast_costa" >go</div>
			<div id="build_itis_costa" act_defer mdl="<?= $PATH ?>/read/readcosta_iti" class="flex_main" style="overflow:auto;">
			</div>
		</div>
	</div>
</div>
