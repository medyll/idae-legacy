<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$table = $_POST['table'];
	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$Table          = ucfirst($table);
	$table_value    = empty($_POST['table_value']) ? '' : (int) $_POST['table_value'];
	$act_chrome_gui = empty($_POST['act_chrome_gui']) ? '' : 'act_chrome_gui=' . $_POST['act_chrome_gui'];
	//
	$APP = new App($table);
	//
	/** @var  $EXTRACTS_VARS */
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);

	$BASE_APP        = $APP_TABLE['codeAppscheme_base'];
	//
	  $id = 'id' . $table;
	if (!empty($table_value)) $vars[$id] = (int)$table_value;

	$ARR = $APP->query_one($vars);
	//
	//

	if (sizeof($R_FK) != 0): foreach ($R_FK as $arr_fk):
		$final_rfk[$arr_fk['scope']][] = $arr_fk;
	endforeach;

		//if (sizeof($final_rfk) == 0) return;
		?>
		<div class="">
			<?
				foreach ($final_rfk as $key => $arr_final):
					// vardump($arr_final);
					?>

					<div class="applink applinkblock">
						<?
							foreach ($arr_final as $arr_fk):
								if (empty($arr_fk['count'])) {
								  continue;
								}
								if (str_find($arr_fk['table'],'_ligne')) continue;
								$vars_rfk['vars']        = ['id' . $table => $table_value];
								$vars_rfk['table']       = $arr_fk['table'];
								$vars_rfk['table_value'] = $arr_fk['table_value'];
								$count                   = $arr_fk['count'];
								?>
								<a style="min-width:140px;" <?= $act_chrome_gui ?> data-link data-table="<?= $vars_rfk['table'] ?>" data-vars="<?= http_build_query($vars_rfk); ?>" class="autoToggle">
									<i class="fa fa-<?= $arr_fk['icon'] ?>   padding" style="color: <?=$APP->colorAppscheme?>;"></i>
									<span data-count="data-count" data-table="<?=$arr_fk['table']?>" data-vars="<?= http_build_query($vars_rfk); ?>"><?=$count?></span>
									<?=  $arr_fk['nomAppscheme'] . '' . (($count < 2) ? '' : 's') ?>
								</a>
							<? endforeach; ?>
					</div>
				<? endforeach; ?>
		</div>
		<?
	endif; ?>


