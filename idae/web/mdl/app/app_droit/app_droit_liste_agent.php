<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/09/2015
	 * Time: 10:59
	 */

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP = new App();
	$APPAG = new App('agent');
	$APPAGR = new App('agent_groupe');
	$APPGD = new App('agent_groupe_droit');
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars'], "my_array_filter_fn"), 1);
	$arr_gd = $APPAGR->findOne($vars);
	// droit par groupe
	$RSAG = $APPAG->find($vars);
?>
<div class="flex_v" >

	<div class="titre_entete blanc" >Agents <?= $arr_gd['nomAgent_groupe'] ?></div >
	<div class="flex_main" style="overflow:auto;" >

		<div class="ms-Table" >
			<div class="ms-Table-row" >
				<div class="ms-Table-cell" style="width:25px;" ></div >
				<div class="ms-Table-cell" ><?= idioma('Liste des agents') ?></div >

			</div >
			<?
				while ($arr = $RSAG->getNext()) {
					?>
					<div class="ms-Table-row" >
						<div class="ms-Table-cell ededed" ><?= $arr['codeAgent'] ?></div >
						<div class="ms-Table-cell" ><?= $arr['nomAgent'] ?> <?= $arr['prenomAgent'] ?></div >

					</div >
				<? } ?>
		</div >
	</div >
</div >