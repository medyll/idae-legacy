<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$field_name_raw = $_POST['field_name_raw'];
	$table          = $_POST['table'];
	$table_value    = (int)$_POST['table_value'];
	//
	$APP = new App($table);
	//
	//
?>
<div class="relative padding">
	<div class="padding">
		<? if ($_POST['mode'] == 'fk') { ?>
			<?= skelMdl::cf_module('app/app_field_fk_update', $_POST) ?>
		<? } else { ?>
			<?= skelMdl::cf_module('app/app_field_update', $_POST) ?>
		<? } ?>
	</div>
	<div class="padding">
		<a class="cancelClose">Fermer</a>
	</div>
</div>
<div class="titreFor">
	<?= idioma('Mise à jour') ?> <?= $table ?> <?= $field_name_raw ?>
</div>
