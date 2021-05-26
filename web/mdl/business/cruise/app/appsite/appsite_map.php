<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 23/01/2016
	 * Time: 02:14
	 */

	include_once($_SERVER['CONF_INC']);

	if (!defined('APPSITE')) {
		echo "pas de APPSITE";

		return;
	}
	$APP      = new App();
	$APP_SITE = new AppSite();
	$APP_SITE->install();

	$filter = ['estSiteAppscheme' => 1];

	$APP_SCH    = new App('appscheme');
	$RS_SCH     = $APP_SITE->get_site_scheme();
	$RS_PRODUIT = $APP_SITE->get_produit();

?>
	<div>
		<? if (droit('DEV')) { ?>
		<a onclick="<?= fonctionsJs::app_mdl('app/appsite/appsite_map') ?>"><i class="fa fa-trash"></i> voir map</a><? } ?>
		<? if (droit('DEV')) { ?>
		<a onclick="<?= fonctionsJs::app_mdl('app/appsite/appsite') ?>"><i class="fa fa-trash"></i> Construire site</a><? } ?>
	</div>
	<div class="blanc padding flex_h flex_wrap" style="height: 100%;overflow:auto;">
		<?

			while ($ARR_SCH = $RS_SCH->getNext()) {

				$table     = $ARR_SCH['codeAppscheme'];
				$table_nom = $ARR_SCH['nomAppscheme'];

				$GRILLE_FK  = $APP_SITE->get_grille_fk($table, $filter);
				$GRILLE_RFK = $APP_SITE->get_reverse_grille_fk($table, '', $filter);
				?>
				<div class="flex_main borderr">
					<div class="titre_entete"><?= $table ?></div>
					<div>
						Liens vers fiche fk
						<? vardump(array_keys($GRILLE_FK)); ?>
					</div>
					<hr>
					<div>
						Lister : rfk
						<? vardump(array_keys($GRILLE_RFK)); ?>
					</div>
				</div>
				<?

			} ?>
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

