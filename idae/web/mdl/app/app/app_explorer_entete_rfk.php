<?
	include_once($_SERVER['CONF_INC']);


	$table = $_POST['table'];
	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$Table          = ucfirst($table);
	$table_value    = (int)$_POST['table_value'] || '';
	$act_chrome_gui = empty($_POST['act_chrome_gui']) ? '' : 'act_chrome_gui=' . $_POST['act_chrome_gui'];
	//
	$APP = new App($table);
	//
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	$id = 'id' . $table;
	if (!empty($table_value)) $vars[$table_value] = (int)$table_value;
	//

	if (sizeof($GRILLE_FK) != 0):

		?>
		<div class="applink applinkblock flex_h flex_wrap">
			<?
				foreach ($GRILLE_FK as $arr_fk):
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$_table       = $arr_fk['collection_fk'];
					$vars_rfk['table_value'] = $arr_fk['table_value'];
					$count                   = $arr_fk['count'];
					$ARR = $APP->distinct($_table, $vars);
					$count = $ARR->count();
					$act_chrome_gui = 'act_chrome_gui=app/app_liste/app_liste_gui'   ;
					if (empty($count))  continue;
					?>
					<a <?= $act_chrome_gui ?>  data-table="<?= $_table ?>" data-vars="groupBy=<?=$_table?>&table=<?= $table ?>&<?= $APP->translate_vars($vars) ?>" class="autoToggle flex_grow_1" style="width: 33%;"  >
					<div class="ellipsis textgrisfonce"><i class="textbold fa fa-<?= $arr_fk['icon_fk'] ?> fa-fw"></i>
						<?=$count?> <?=$_table . (($count < 2) ? '' : 's' ) ?></div>

					</a>
				<? endforeach; ?>
		</div>
		<?
	endif; ?>


