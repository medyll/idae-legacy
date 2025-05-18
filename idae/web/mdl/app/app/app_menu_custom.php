<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 21/05/14
	 * Time: 18:23
	 */
	include_once($_SERVER['CONF_INC']);


	// info table
	$table = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$id = 'id' . $table;
	$APP = new App($table);

	$arr = $APP->query_one(array( $id => $table_value ));
	//
	$act_from = empty($_POST['act_from']) ? '' : $_POST['act_from'];
?>
<?
	switch ($table):
		case 'client':
			?>
			<a class="cancelClose" onclick="<?= fonctionsJs::client_big_fiche($table_value); ?>">
				<i class="textorange fa fa-expand"></i> <?=idioma('Vue complète')?>
			</a>
			<?
			break;
		case 'produit':
			?>
			<div class = "applink" >
				<a onclick="ajaxInMdl('app/app_custom/produit/produit','wh<?=$idproduit?>azou','idproduit=<?=$table_value?>',{onglet:'Produit <?=$table_value?>'})">
					<i class="fa fa-cog"></i> <?=idioma('Edition complète')?>
				</a>
			</div >
			<?
			break;
		case 'fournisseur':
			?>
			<div class = "applink" >
				<a onclick = "ajaxInMdl('production/fournisseurpresentation/fournisseur_presentation','tmp_oi<?= $time ?>','idfournisseur=<?= $table_value ?>',					{onglet:'Présentation <?= $arr['nomFournisseur'] ?>'})" >
					<i class = "fa fa-info" ></i >
					Présentations</a >
				<a onclick = "ajaxInMdl(" production/fournisseurclause/fournisseurClause",'tmp_cl<?= $time ?>','idfournisseur=<?= $table_value ?>',
				                                    {onglet:'Clauses <?= $arr['nomFournisseur'] ?>'})">Clauses</a></div >
			<?
			break;
		case 'ville':
			?>
			<? if ( $act_from != 'map' ): ?>
			<div class = "applink" >
			<a vars = "idville=<?= $table_value ?>" act_chrome_gui = "app/app_custom/app_custom_ville_map" >
				<i class = "fa fa-map-marker" ></i >
				Cartes</a ></div ><? endif; ?>
			<?
			break;
	endswitch;
?>