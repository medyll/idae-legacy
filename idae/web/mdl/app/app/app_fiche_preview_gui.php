<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']);
	$table = $_POST['table'];
	$table_value = $_POST['table_value'];
	$APP = new App();
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'PREVIEW');
	// skelMdl::reloadModule('gui/gui_activity', $_SESSION['idagent']);
?>
<div style="min-width:450px;min-height:750px;;" class="flex_v" >
	<div class="flex_main" style="min-height:150px;overflow: auto;position:relative;" >
		<?= skelMdl::cf_module('app/app/app_fiche_preview', $_POST + ['scope' => 'id' . $table], $table_value) ?>
	</div >
	<div class="buttonZone" >
		<button type="button" class="cancelClose" ><?= idioma('Fermer') ?></button >
	</div >
</div >
<div class="titreFor" >
	<?= idioma('Détails') . ' ' . ucfirst($table) ?></div >