<?
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	//
	$table_value = (int)$_POST['table_value'];
	//
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche_micro.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche_micro', $_POST);

		return;
	}

	$APP  = new App($table);
	$Idae = new Idae($table);
?>
<div class="flex_h flex_align_top  " style="height:100%;"   data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
	<div class="padding aligncenter">
		<i class="fa fa-<?=$APP->iconAppscheme?>" style="color:<?=$APP->colorAppscheme?>"></i>
	</div>
	<div class="relative boxshadowl ">
		<?= $Idae->module('app/app/fiche_fields', ['table'         => $table,
		                                           'in_mini_fiche' => 1,
		                                           'hide_empty' => 1,
		                                           'hide_field_title' => 1,
		                                           'table_value'   => $table_value]) ?>
	</div>
</div>