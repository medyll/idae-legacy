<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 03:24
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];

?>
<div class="relative toggler applink applinkblock aligncenter paddng marging   flex flex_margin">
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','table');" class="autoToggle" data-button-dsp="table">
		<i class="fa fa-table"></i>

		<br><?= idioma('Table') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','table');" class="autoToggle" data-button-dsp="table_line">
		<i class="fa fa-list "></i>

		<br> <?= idioma('Ligne detail') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','line_fk');" class="autoToggle" data-button-dsp="line_fk">
		<i class="fa fa-list "></i>

		<br><?= idioma('Ligne propriétés') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_forward');" class="autoToggle" data-button-dsp="mdl" data-dsp-mdl="app/app/app_fiche_forward">
		<i class="fa fa-columns"></i>

		<br> <?= idioma('fiche verticale') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_icone');" class="autoToggle" data-button-dsp="mdl" data-dsp-mdl="app/app/app_fiche_icone">
		<i class="fa fa-desktop"></i>

		<br> <?= idioma('icone') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_entete');" class="autoToggle" data-button-dsp="mdl" data-dsp-mdl="app/app/app_fiche_entete">
		<i class="fa fa-list-alt "></i>

		<br> <?= idioma('fiche') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','line');" class="autoToggle" data-button-dsp="line">
		<i class="fa fa-list "></i>

		<br><?= idioma('Simple') ?>
	</a>
	<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','fields');" class="autoToggle" data-button-dsp="fields">
		<i class="fa fa-align-left "></i>

		<br> <?= idioma('Fiche') ?>
	</a>
</div>
<style>
	.nav.nav_menu > li > a {
		display : inline-block;
	}
</style>