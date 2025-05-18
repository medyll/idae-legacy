<?
	include_once($_SERVER['CONF_INC']);


	$table_value = (int)$_POST['table_value'];
	$table = $_POST['table'];
	$act_from = empty($_POST['act_from']) ? '' : $_POST['act_from'];
	$id = 'id' . $table;
	$top = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom = 'nom' . ucfirst($table);
	//
	//
	$APP = new App($table);
	$TABLE_ONE = $APP->app_table_one;
	$id = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);

	$arr_ico = ['fiche' => 'file-text-o', 'update' => 'pencil', 'map' => 'map-marker'];
?>
<div class="fond_noir color_fond_noir applinkblock applink toggler relative aligncenter" style="height: 100%;">
	<i class="fa fa-<?= $arr_ico[$act_from] ?>"></i>

	<? if ($act_from != 'fiche'): ?>
		<a class="" act_chrome_gui="app/app/app_fiche" vars="table=<?= $table ?>&table_value=<?= $table_value ?>"
		   options="{ident:'fiche_<?= $table . $table_value ?>'}">
			<i class="fa fa-file-text-o"></i>
		</a>
	<? endif; ?>
	<? if ($act_from != 'preview'): ?>
		<a act_chrome_gui="app/app_liste/app_liste_preview_gui" vars="table=<?= $table ?>&table_value=<?= $table_value ?>"
		   options="{ident:'preview_<?= $table . $table_value ?>'}">
			<i class="fa fa-eye"></i>
		</a>
	<? endif; ?>
	<? if ($act_from != 'update'): ?>
		<a class="" act_chrome_gui="app/app/app_update" vars="table=<?= $table ?>&table_value=<?= $table_value ?>"
		   options="{ident:'update_<?= $table . $table_value ?>'}">
			<i class="fa fa-pencil"></i>
		</a>
	<? endif; ?>
	<?= skelMdl::cf_module('app/app_gui/app_gui_tile_click', $_POST + array('moduleTag' => 'span'), $table_value); ?>
</div>