<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 04/01/2016
	 * Time: 23:57
	 */

	$table = $_POST['table'];
	$Table = ucfirst($table);;
	$APP_TMP     = new App($table);
	$idappscheme = (int)$APP_TMP->idappscheme;
	$GRILLE_FK   = $APP_TMP->get_grille_fk();

?>
<? if (!empty($table)): ?>
	<? if (droit('ADMIN')): ?>
		<div class="padding">
			<a class="aligncenter" data-menu="" data-clone="data-clone">
				<i class="fa fa-cogs"></i><i class="fa fa-caret-right textgris "></i>
			</a>
			<div class="context_app_menu  " style="display:none;">
				<div class="applink  applinkblock  ">
					<a onclick="<?= fonctionsJs::app_update('appscheme', $idappscheme) ?>">
						<i class="fa fa-database fa-fw"></i> <?= idioma('Modele general') ?>
					</a>
					<a onclick="ajaxInMdl('app/app_scheme/app_scheme_has_field_update','div_redf','idappscheme=<?= $idappscheme ?>',{onglet:'Choix des champs de table'})">
						<i class="fa fa-code fa-fw"></i> <?= idioma('Détails du modele') ?>
					</a>
					<a onclick="act_chrome_gui('app/app_scheme/app_scheme_has_field_update_model','idappscheme=<?= $idappscheme ?>')">
						<i class="fa fa-table fa-fw"></i> <?= idioma('choisir les champs de table') ?>
					</a>
					<a onclick="<?= fonctionsJs::app_sort('appscheme_has_table_field', '', ['vars' => ['idappscheme' => $idappscheme]]); ?>">
						<i class="fa fa-sort fa-fw"></i> <?= idioma('ordonner les champs de table') ?>
					</a>
					<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','idappscheme=<?= $idappscheme ?>',{value:'<?= $idappscheme ?>'})">
						<i class="fa fa-sitemap fa-fw"></i><?=idioma('gérer dépendances')?>   <?= sizeof($GRILLE_FK) ?>
					</a>
				</div>
			</div>
		</div>
	<? endif; ?>
<? endif; ?>
