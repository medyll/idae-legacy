<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 26/12/14
	 * Time: 15:13
	 */
	include_once($_SERVER['CONF_INC']);

	$APP = new App('produit');
	$idproduit = (int)$_POST['idproduit'];
	$arr = $APP->query_one(array('idproduit' => $idproduit));
?>
<script>
	load_table_in_zone('table=produit_tarif&vars[idproduit]=<?=$idproduit?>', 'produit_tarif<?=$idproduit?>');
	load_table_in_zone('table=produit_etape&vars[idproduit]=<?=$idproduit?>', 'produit_etape<?=$idproduit?>');
</script>
<div class="flex_v" style="width:33%;height:100%;position:relative;overflow:auto;">
	<div><?= skelMdl::cf_module('app/app/app_fiche_mini', array('table' => 'produit', 'table_value' => $idproduit, 'scope' => 'idproduit'), $idproduit) ?></div>
	<div class="relative titre_entete ededed applink" style="z-index:10;">
		<a class = "cell" act_chrome_gui = "app/app/app_update" vars = "table=produit&table_value=<?= $idproduit ?>" options = "{ident:'update_produit<?=  $idproduit ?>'}" >
			<i class = "fa fa-pencil" ></i >
			&nbsp;
			<?= idioma('Modifier') ?>
		</a >
		<a target="_blank" href="<?=Act::lienProduit($arr["idproduit"])?>"><i class="fa fa-globe"></i>
			<?=idioma('Vue web')?>
		</a>
		<a onclick="loadModule('app/app_admin/app_build_prod','run=1&vars[idproduit]=<?=$idproduit?>')"><i class="fa fa-rebel"></i> Valider</a>
	</div>
	<div class="flex_main"  style="height:100%;z-index: 100;overflow:auto;"><?= skelMdl::cf_module('app/app/app_fiche_preview', array('table' => 'produit', 'table_value' => $idproduit, 'scope' => 'idproduit'), $idproduit) ?></div>

</div>
<div class="flex_v borderl" style="width:33%;height: 100%;overflow:hidden;">

	<div class="flex_v relative" style="height:50%;overflow:hidden;">
		<div class="padding applink ">
			<div class="inline padding ededed">
				<a act_chrome_gui="app/app/app_create"
				   vars="table=produit_tarif&vars[idproduit]=<?= $idproduit ?>"
				   options="{scope:'produit_tarif'}">
					<i class="fa fa-plus-circle"></i> <?= idioma('Ajouter une date de départ') ?>
				</a></div>
		</div>
		<div class="flex_main relative" style="z-index: 100;overflow:auto;"
		     id="produit_tarif<?= $idproduit ?>">

		     </div>
	</div>
	<div class="flex_v bordert" style="height:50%;overflow:hidden;">
		<div class="padding applink borderb">
			<div class="inline padding ededed">
				<a act_chrome_gui="app/app_custom/produit/produit_clause_create"
				   vars="table=produit&vars[idproduit]=<?= $idproduit ?>&clause=clauseInclus">
					<i class="fa fa-plus-circle"></i> <?= idioma('Inclus') ?></a>
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
					<a act_chrome_gui="app/app_custom/produit/produit_clause_create"
				   vars="table=produit&vars[idproduit]=<?= $idproduit ?>&clause=clauseNonInclus">
					<i class="fa fa-minus-circle"></i> <?= idioma('Non inclus') ?>
				</a></div>
		</div>
		<div class="flex_main relative" style="z-index: 100;overflow:auto;" table="produit" table_value="<?=$idproduit?>">
			<span class="inline padding borderb">Inclus</span>
			<div class="retrait"><?= $APP->draw_field([ 'field_name_raw' => 'clauseInclus' , 'table' => 'produit' , 'field_value' => $arr['clauseInclusProduit'] ]) ?></div>
			<span class="inline padding borderb">Non inclus</span>
			<div class="retrait"><?= $APP->draw_field([ 'field_name_raw' => 'clauseNonInclus' , 'table' => 'produit' , 'field_value' => $arr['clauseNonInclusProduit'] ]) ?></div>
		</div>
	</div>
</div>
<div class="flex_v borderl" style="width:33%;height: 100%;overflow:hidden;">
	<div class="padding applink ">
		<div class="inline padding ededed">
			<a act_chrome_gui="app/app/app_create"
			   vars="table=produit_etape&vars[idproduit]=<?= $idproduit ?>"
			   options="{scope:'produit_etape'}">
				<i class="fa fa-plus-circle"></i> <?= idioma('Ajouter une étape') ?>
			</a></div>
	</div>
	<div class="flex_main" style="height:100%;z-index: 100;overflow:auto;"
	     id="produit_etape<?= $idproduit ?>"></div>
</div>