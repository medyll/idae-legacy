<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	//

	$APP = new App($table);
	//
	$arr_dsp_fields = $APP->get_display_fields($table);
	unset($arr_dsp_fields['description'], $arr_dsp_fields['commentaire']);
?>
<div class="flex_h" data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" table="<?= $table ?>" table_value="<?= $table_value ?>">
	<div style="width:46px;">
		<div class="padding aligncenter">
			<i class="ms-Icon ms-Icon--person"></i>
		</div>
	</div>
	<div class="borderl padding">
		<div><?= skelMdl::cf_module('app/app/app_fiche_entete_group', ['table' => $table, 'table_value' => $table_value]) ?></div>
	</div>
</div>
