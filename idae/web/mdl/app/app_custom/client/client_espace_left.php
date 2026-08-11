<?php

	include_once($_SERVER['CONF_INC']);
	$APP      = new App('client');
	$idclient = (int)$_POST['idclient'];
	$arrCli   = $APP->findOne(['idclient' => $idclient]);
	//
	// LOG_CODE
	// $APP->set_log($_SESSION['idagent'], 'client', $idclient, 'DETAIL');
?>
<div class="blanc flex_v" style="overflow:hidden;height:100%;width:100%;">
	<div><?= skelMdl::cf_module('app/app/app_fiche_entete', ['table' => 'client', 'table_value' => $idclient, 'vars' => ['vars' => ['idclient' => $idclient]]]) ?></div>
	<div main_auto_tree class=" " style="height:100%;overflow:auto;">
		<div auto_tree class="ms-font-m padding boxshadow padding blanc" style="">
			<div class="uppercase padding">info</div>
		</div>
		<div class="flex_h" style="margin-bottom:2em;" id="espace_rfk_pour_<?=$table?>">
			<div style="width:100px;"></div>
			<div><?= skelMdl::cf_module('app/app/app_fiche_rfk', ['table' => 'client', 'table_value' => $idclient, 'vars' => ['vars' => ['idclient' => $idclient]]]) ?></div>
		</div>
		<div auto_tree class="ms-font-m barre_open" style="">
			<div class="uppercase padding">Contacts</div>
		</div>
		<div class="flex_h" style="margin-bottom:2em;">
			<div style="width:100px;"></div>
			<div class="flex_main"  id="zone_cli<?= $idclient ?>" style="margin-top:1em;width:100%;height:100%;">
				<?= skelMdl::cf_module('app/app_liste/app_liste_small', ['mode'=>'integrated','table' => 'contact', 'vars' => ['idclient' => $idclient], 'data-dsp-mdl' => 'app/app/app_fiche_entete_group']) ?>
			</div>
		</div>
		<div auto_tree class="ms-font-m barre_open" style="">
			<div class="uppercase padding">Sites et adresses</div>
		</div>
		<div class="flex_h">
			<div style="width:100px;"></div>
			<div>
				<div id="zone_site<?= $idclient ?>" data-dsp="mdl"
				     data-dsp-mdl="app/app/app_fiche_search" data-data_model="defaultModel">
				</div>
			</div>
		</div>
		<div auto_tree class="ms-font-m barre_open" style="">
			<div class="uppercase padding">Opportunités</div>
		</div>
		<div id="zone_opor<?= $idclient ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini"></div>

	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .readAttribute) to native DOM.
	 */
	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function cel_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (!root) return;
		if (maybeHandler === undefined) {
			root.addEventListener(eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	cel_on(document.getElementById('espace_rfk_pour_<?=$table?>'),'click','[data-link]',function(event,node){



		var table =node.getAttribute('data-table');
		var table_value =node.getAttribute('data-table_value');
		var vars =node.getAttribute('data-vars');

		act_chrome_gui('app/app_liste/app_liste_gui','table='+table+'&table_value='+table_value+'&'+vars)


	})

	// load_table_in_zone('table=commande&vars[idclient]=<?=$idclient?>', 'zone_commande<?=$idclient?>');
	load_table_in_zone('table=opportunite&vars[idclient]=<?=$idclient?>', 'zone_opor<?=$idclient?>');
	load_table_in_zone('table=site&vars[idclient]=<?=$idclient?>', 'zone_site<?=$idclient?>');
</script>
<style>
	.barre_open {
		padding: 0.5em;
		box-shadow: 0 0 3px #ccc;
		text-transform: uppercase;
		background-color: #fff;
		z-index: 10;
		position: sticky;
		top: 10px;
	}

	#zone_ocli<?= $idclient ?> .div_tbody {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
	}

	#zone_cli<?= $idclient ?> .div_tbody > .autoToggle {
		margin-bottom: 1em;
		border-bottom: 1px solid #ccc
	}
</style>