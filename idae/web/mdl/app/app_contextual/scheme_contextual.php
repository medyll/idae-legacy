<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 02/10/2016
	 * Time: 17:16
	 */

	$table = $_POST['table'];
	$APP   = new App($table);
?>
<div class="applink applinkblock">
	<? if (droit_table($_SESSION['idagent'], 'C', $table)) : ?>
		<a class="autoToggle hide_gui_pane" onclick="<?= fonctionsJs::app_create($table, ['idagent' => $_SESSION['idagent']]); ?>"><i class="fa fa-save textbold"></i> <?= idioma('Créer ' . $table) ?></a>
	<? endif; ?>
	<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
		<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_explorer','app_explorer_<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Espace ' . $table) ?>'});"><i
				class="fa fa-<?= $APP->iconAppscheme ?>"></i>
			Espace <?= idioma($APP->nomAppscheme) ?> </a>
	<? endif; ?>
	<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
		<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod_search','tmp_exp_prod_search','table=<?= $table ?>&vars[collection]=<?= $table ?>',{onglet:'Recherche rapide <?= $table ?>'});"><i
				class="fa fa-search"></i>
			Recherche rapide
		</a>
	<? endif; ?>
</div>
