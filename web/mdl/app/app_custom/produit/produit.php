<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 26/12/14
	 * Time: 15:13
	 */
	include_once($_SERVER['CONF_INC']);

	$APP       = new App('produit');
	$idproduit = (int)$_POST['idproduit'];
	$arr       = $APP->query_one(array('idproduit' => $idproduit));
	$uniqid    = uniqid('produit');
?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;" app_gui_explorer>
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="applink titre_entete uppercase bold ededed" style="z-index:10">
			<a expl_html_title
			   onclick="reloadModule('app/app_custom/produit/produit','*')">
				<i class="fa fa-refresh"></i> <?= $arr['nomProduit'] ?></a>
		</div>
		<div class="applink applinkbig  toggler uppercase borderb">
			<a class="autoToggle active aligncenter" onclick="$('<?= $uniqid ?>').loadModule('app/app_custom/produit/produit_main','idproduit=<?= $idproduit ?>')">Fiche croisiere</a>
			<a class="autoToggle aligncenter" onclick="$('<?= $uniqid ?>').loadModule('app/app_custom/produit/produit_tarif_gamme_update','idproduit=<?= $idproduit ?>',{value:<?= $idproduit ?>})">Tarification complete</a>
			<a class="autoToggle aligncenter" onclick="$('<?= $uniqid ?>').loadModule('app/app_custom/produit/produit_web','idproduit=<?= $idproduit ?>')">Vue web</a>
		</div>
		<div class="flex_main " style="position:relative;overflow:hidden;">
			<div act_defer mdl="app/app_custom/produit/produit_main" vars="idproduit=<?= $idproduit ?>" class="flex_h" style="height: 100%;width:100%;" id="<?= $uniqid ?>">
				<? // =skelMdl::cf_module('app/app_custom/produit/produit_main',['idproduit'=>$idproduit],$idproduit);?>
			</div>
		</div>
		<div class="padding ededed bordert" table="produit" table_value="<?= $idproduit ?>"><?= $APP->draw_field(['field_name_raw' => 'nombreVue', 'table' => 'produit', 'field_value' => $arr['nombreVueProduit']]) ?></div>
	</div>
</div>