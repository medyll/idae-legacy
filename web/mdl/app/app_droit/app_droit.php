<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 14/09/2015
	 * Time: 11:41
	 */

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);

	$APP = new App();

	$APP->init_scheme('sitebase_base', 'agent_groupe');
	$APP->init_scheme('sitebase_base', 'agent_groupe_droit');

	$APPG = new App('agent_groupe');

	$APPGD = new App('agent_groupe_droit');

	// droit par groupe
	$RSG = $APPG->find()->sort(['ordreGroupe'=>1]);
	$RSGD = $APPGD->find();

?>
<div class="flex_v blanc">
	<?
		$APPGD->consolidate_scheme();
		$APPG->consolidate_scheme();
	?>
	<div class="titre_entete">
		<?=idioma('Gestion des droits')?>
		<a onclick="<?=fonctionsJs::app_create('agent_groupe')?>">agent_groupe</a>
	</div>
	<div class="flex_h flex_main">
		<div class="frmCol1 ededed">
			<div class="titre_entete">Groupes</div>
			<div class="applink applinkblock toggler">
			<? while($ARRG = $RSG->getNext()): ?>
			<a class="autoToggle" onclick="$('liste_droit').loadModule('app/app_droit/app_droit_liste','vars[idagent_groupe]=<?=$ARRG['idagent_groupe']?>');$('liste_droit_agent').loadModule('app/app_droit/app_droit_liste_agent','vars[idagent_groupe]=<?=$ARRG['idagent_groupe']?>')"><?=$ARRG['nomAgent_groupe']?></a>
			<? endwhile; ?>
			</div>
		</div>
		<div class="flex_main flex_h">
			<div class="borderr flex_main flex_v">
				<div class="titre_entete">Droits</div>
				<div id="liste_droit" class="flex_main" style="overflow:auto;" ></div>
			</div>
			<div  class="flex_main flex_v">
				<div id="liste_droit_agent" class="flex_main" ></div>
			</div>
		</div>
	</div>
</div>
