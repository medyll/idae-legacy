<?
	include_once($_SERVER['CONF_INC']);

	$vars     = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$table    = $_POST['table'];
	$APP      = new App($table);
	$grilleFK = $APP->get_grille_fk();
	$id       = 'id' . $table;
	$nom      = 'nom' . ucfirst($table);

	$tr_vars = $APP->translate_vars($vars);
	//

	foreach ($grilleFK as $fk):
	$table_fk = $fk['codeAppscheme'];
	$rs_dist = $APP->distinct($table_fk, $vars, 100);

	$APP_TMP  = new App($fk['table_fk']);
	$scope = $table_fk.$tr_vars;
	?>
	<div auto_tree>
		<a app_button="<?= $table ?>" vars="table=<?= $table ?>&groupBy=<?= $fk['table_fk'] ?>&<?= $tr_vars ?>" class="autoToggle">
			<i class="fa fa-<?= $fk['iconAppscheme'] ?>"></i> par <?= $fk['nomAppscheme'] ?> <i class="fa fa-caret-right"></i></a>
	</div>
	<div style="display:none;width:100%;overflow:hidden;position:relative">
		<? if ($rs_dist->count() != $rs_dist->count(true)) { ?>
			<div class="retrait flex_h flex_align_middle">
				<div><i class="fa fa-search"></i></div>
				<div class="relative">
					<input type="text" style="max-width:100%;" onkeyup="reloadModule('app/app_prod/app_prod_nav_group_inner','<?=$scope?>','search='+this.value+'&table=<?= $table ?>&table_fk=<?= $table_fk ?>&<?= $tr_vars ?>')">
				</div>
			</div>
		<? } ?>
		<div style="overflow:hidden;" class="retrait" act_defer mdl="app/app_prod/app_prod_nav_group_inner" vars="table=<?= $table ?>&table_fk=<?= $table_fk ?>&<?= $tr_vars ?>" value="<?=$scope?>">
		</div>
	</div>
<? endforeach; ?>
