<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	//
	// ESPACE
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	/*if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_espace.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_espace', $_POST);
		return;
	}*/
	$APP = new App($table);
	//
	$R_FK = $APP->get_reverse_grille_fk($table, $table_value);

	//
	$zouzou = uniqid($table);
	$zou    = uniqid($table);
?>
<div class="flex_v blanc" style="height: 100%;overflow:hidden;">
	<div id="<?= $zouzou ?>" class="flex_main flex_h" style="width: 100%;overflow-y:hidden;overflow-x: auto;">
		<? if (sizeof($R_FK) != 0): ?>
			<div class="flex_h  ">
				<? foreach ($R_FK as $arr_fk):
					$value_rfk               = $arr_fk['table_value'];
					$table_rfk               = $arr_fk['table'];
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$vars_rfk['table']       = $table_rfk;
					$vars_rfk['table_value'] = $value_rfk;
					$count                   = $arr_fk['count'];
					?>
					<div style="order:-<?= $count ?>" act_defer mdl="app/app/app_fiche_forward_liste" vars="table=<?= $table_rfk ?>&vars[<?= 'id' . $table ?>]=<?= $table_value ?>"></div>                <? endforeach; ?>
			</div>
		<? endif; ?>
	</div>
</div>
<script>
	$ ('<?=$zouzou?>').on ('click', '[data-table][data-table_value][data-link]', function (event, node) {
		var table       = node.readAttribute ('data-table');
		var table_value = node.readAttribute ('data-table_value');
		$ ('<?=$zou?>').show ();
		$ ('for_<?=$zou?>').loadModule ('app/app/app_fiche_preview', 'table=' + table + '&table_value=' + table_value);
	});
	//
	$ ('<?=$zouzou?>').on ('click', '[data-link][data-table][data-vars]', function (event, node) {
		if ( node.readAttribute ('data-table_value') ) return;
		var table = node.readAttribute ('data-table');
		var vars  = node.readAttribute ('data-vars');
		$ ('<?=$zou?>').show ();
		$ ('for_<?=$zou?>').loadModule ('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// act_chrome_gui('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// alert('dre')
	})
</script>