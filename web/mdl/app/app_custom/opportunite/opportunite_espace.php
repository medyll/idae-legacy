<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 31/08/2015
	 * Time: 11:22
	 */

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors' , 55);
	$APP_AGENT = new App('agent');
	$APP_OPOR = new App('opportunite');
	$APP_OPOR_LGN = new App('opportunite_ligne');
	$RSA = $APP_AGENT->find()->sort([ 'nomAgent' => 1 ]);
	// listes des opportunités , // BIZBOARD
?>
<div class = "flex_v blanc" style = "height: 100%;overflow:hidden;width:100%;">
	<div class = "titre_entete aligncenter bold  relative borderb">
		<?= idioma('BIZBOARD : Tableaux des opportunités') ?>
	</div>
	<div class = "flex_main flex_v" style = "height:100%;width:100%;overflow:hidden;">
		<div class = "titre_entete flex_h">
			<div class = "borderr" style = "width:230px;">
				<?= idioma('Agent') ?>
			</div>
			<div class = " flex_main">
				<?= idioma('Opportunités') ?> <a onclick="<?=fonctionsJs::app_create('opportunite')?>"><?=idioma('Nouvelle opportunité')?></a>
			</div>
		</div>
		<div class = "flex_main applink applinkblock toggler" style = "overflow:auto;" id = "oppor_oi">
			<? while ($ARR_A = $RSA->getNext()):
				$idagent = (int)$ARR_A['idagent'];
				$rs_OPOR = $APP_OPOR->find([ 'idagent' => (int)$ARR_A['idagent'] ]);
				?>
				<div class = "flex_h borderb">
					<div class = "borderr" style = "width:230px;">
						<a class = "autoToggle"><?= $ARR_A['codeAgent'] ?> <?= $ARR_A['nomAgent'] ?></a>
					</div>
					<div class = "flex_main" style = "width:100%;" id = "zone_opor<?= $idagent ?>" data-dsp = "mdl" data-dsp-mdl = "app/app/app_fiche_mini"></div>
				</div>
				<script>
					load_table_in_zone('table=opportunite&vars[idagent]=<?=$idagent?>', 'zone_opor<?=$idagent?>');
				</script>
			<? endwhile; ?>
		</div>
	</div>
</div>
<style>
	#oppor_oi .div_tbody {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		}
</style>