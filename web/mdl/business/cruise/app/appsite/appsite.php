<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 23/01/2016
	 * Time: 02:14
	 */

	include_once($_SERVER['CONF_INC']);

	if (!droit('DEV')) return;
	if (!defined('APPSITE')) {
		echo "pas de APPSITE";

		return;
	}
	$APP      = new App();
	$APP_SITE = new AppSite();
	$APP_SITE->install();

	$filter = ['estSiteAppscheme' => 1];

?>
	<div class="flex_v flex_align_stretch blanc" style="height:100%;overflow:hidden;">
		<div class="applink titre_entete">
			<a data-reloader></a>
			<a onclick="<?= fonctionsJs::app_mdl('app/appsite/appsite_map') ?>"><i class="fa fa-trash"></i> voir map</a>
			<a onclick="<?= fonctionsJs::app_mdl('app/appsite/appsite') ?>"><i class="fa fa-trash"></i> Construire site</a>
		</div>
		<div class="flex_main flex_h" style="height: 100%;width:100%;overflow:hidden;">
			<div class="frmCol1" data-linker="col2" data-linker_item=".app_site_scheme_link" data-linker_mdl="<?= mdl_link('app/appsite/appsite_scheme_values') ?>">
				<?= skelMdl::cf_module(mdl_link('app/appsite/appsite_scheme')) ?>
			</div>
			<div class="frmCol1" id="col2" data-linker="col3" data-linker_item=".app_site_scheme_link" data-linker_mdl="<?= mdl_link('app/appsite/appsite_page') ?>">
				<?= skelMdl::cf_module(mdl_link('app/appsite/appsite_scheme_values')) ?>
			</div>
			<div class="frmCol2"  style="height:100%;overflow-x:auto;overflow-y:hidden;">
				 <div  id="col3"  style="width:5000px;height:100%;">sssss</div>
			</div>
		</div>
	</div>
<?

	$dakeys = ['idproduit'              => 'iprod',
	           'idproduit_type'         => 'prt',
	           'dureeJourProduit'       => 'nbj',
	           'dateDebutProduit_tarif' => 'date',
	           'idvilleDepartProduit'   => 'vd',
	           'idville'                => 'vi',
	           'idpays'                 => 'py',
	           'idpaysDepartProduit'    => 'pyd',
	           'idtransport'            => 'idt',
	           'idtransport_cabine'     => 'idtc',
	           'idfournisseur'          => 'fo',
	           'iddestination'          => 'des',
	           'idcontinent'            => 'cont',
	           'idmer'                  => 'mer',
	           'idvacance'              => 'vac',
	           'homePageProduit'        => 'star',
	           'coeurProduit'           => 'coupdecoeur',
	           'promoProduit'           => 'promo',
	           'volProduit'             => 'volinclu',
	           'toutIncluProduit'       => 'toutinclu',
	           'idtheme'                => 'th',
	           'idproduit_selection'    => 'ps',
	           'idhotel'                => 'idh',
	           'idfleuve'               => 'flv'];

/*foreach ($arr_page_type as $code_page_type => $nom_page_type) {
			$idappsite_page_type = $APP_SITE_PAGE_TYPE->create_update(['idappsite' => (int)$idappsite, 'codeAppsite_page_type' => 'page_' . $code_page_type], ['nomAppsite_page_type' => $nom_page_type]);
			echo "<br>page_type $idappsite_page_type $code_page_type<br>";
			if ($code_page_type == 'page_home') continue;
			while ($ARR_SCH = $RS_SCH->getNext()) {
				$table = $ARR_SCH['codeAppscheme'];
				if ($table == 'produit') continue;
				$idsite_page = $APP_SITE_PAGE->create_update(['idappsite_page_type' => $idappsite_page_type, 'idsite' => (int)$idappsite, 'codeAppsite_page' => 'page_' . $code_page_type . '_' . $table], ['nomAppsite_page' => $nom_page_type . ' ' . $table]);
				echo "- page  $idsite_page $code_page_type $table <br>";
				$APP        = new App($table);
				$main_table = $APP->app_table_one;
				$GRILLE_FK  = $APP->get_grille_fk($table,['estSiteAppscheme'=>1]);
				$GRILLE_RFK = $APP->get_reverse_grille_fk($table);

				if ($code_page_type == 'intermediaire' ){
					$arr_mdl    = ['destination', 'ville', 'pays', 'mer', 'fournisseur'];
					foreach ($arr_mdl as $index => $table_mdl) {
						echo "-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Module&nbsp;$table _ $table_mdl<br>";
					}

				}
				if ($code_page_type == 'page_fiche' || $code_page_type == 'page_liste_detail'){
					foreach ($GRILLE_FK as $table_fk => $arr_fk) {
						echo "-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Module&nbsp;$table _ $table_fk<br>";
					}
				}
			}
			$RS_SCH->reset();
		}*/