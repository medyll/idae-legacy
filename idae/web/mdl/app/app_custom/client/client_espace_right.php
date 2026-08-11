<?php

	include_once($_SERVER['CONF_INC']);
	$APP      = new App('client');
	$idclient = (int)$_POST['idclient'];
	$arrCli   = $APP->findOne(['idclient' => $idclient]);

?>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS `$` shim to native
	 * DOM, with a file-local `cer_` helper. toggleContent is not a shim call:
	 * engine/methods.js installs it on HTMLElement.prototype.
	 */
	function cer_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}
</script>

<div class="flex_v" style="overflow:hidden;width:100%;height:100%;">
	<div class="app_onglet toggler relative ">
		<a class="autoToggle active" onclick="cer_el('zone_main_mat<?= $idclient ?>').toggleContent()"><?= idioma('ressources') ?></a>
		<a class="autoToggle" onclick="cer_el('zone_main_contr<?= $idclient ?>').toggleContent()"><?= idioma('Contrats') ?></a>
		<a class="autoToggle" onclick="cer_el('zone_main_fin<?= $idclient ?>').toggleContent()"><?= idioma('Financements') ?></a>
		<a class="autoToggle" onclick="cer_el('zone_tache<?= $idclient ?>').toggleContent()"><?= idioma('taches') ?></a>
		<a class="autoToggle" onclick="cer_el('zone_intervention<?= $idclient ?>').toggleContent()"><?= idioma('interventions') ?></a>
		<a class="autoToggle" onclick="cer_el('zone_commandes<?= $idclient ?>').toggleContent()"><?= idioma('commandes') ?></a>
	</div>
	<div class="flex_main  " style="overflow:hidden;">
		<div id="zone_main_mat<?= $idclient ?>" class=" " style="overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'materiel', 'vars' => ['idclient' => $idclient],'groupBy'=>'contrat','data_model'=>'defaultModel']) ?>
		</div>
		<div id="zone_main_contr<?= $idclient ?>" style="display:none;overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'contrat', 'vars' => ['idclient' => $idclient],'data_model'=>'defaultModel']) ?>
		</div>
		<div id="zone_tache<?= $idclient ?>"   style="display:none;overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'tache', 'vars' => ['idclient' => $idclient],'data_model'=>'defaultModel']) ?>
		</div>
		<div id="zone_intervention<?= $idclient ?>"  style="display:none;overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'intervention', 'vars' => ['idclient' => $idclient],'groupBy'=>'materiel']) ?>
		</div>
		<div id="zone_commandes<?= $idclient ?>" style="display:none;overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'commande', 'vars' => ['idclient' => $idclient],'data_model'=>'defaultModel']) ?>
		</div>
		<div id="zone_main_fin<?= $idclient ?>" style="display:none;overflow:hidden;max-height:100%;height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['table' => 'financement', 'vars' => ['idclient' => $idclient],'data_model'=>'defaultModel']) ?>
		</div>
	</div>
</div>