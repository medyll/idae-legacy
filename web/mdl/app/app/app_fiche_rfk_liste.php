<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];

	$act_chrome_gui = empty($_POST['act_chrome_gui']) ? '' : 'act_chrome_gui=' . $_POST['act_chrome_gui'];
	//
	$APP       = new App($table);
	$APP_TABLE = $APP->app_table_one;
	$R_FK      = $APP->get_reverse_grille_fk($table, $table_value);
	//

	if (sizeof($R_FK) != 0): foreach ($R_FK as $arr_fk):
		$final_rfk[$arr_fk['scope']][] = $arr_fk;
	endforeach;
		// if (sizeof($final_rfk) == 0) return;
		?>
		<div class="nth2 flex_v" auto_tree_main>
			<?
				foreach ($final_rfk as $key => $arr_final):
					?>
					<?
					foreach ($arr_final as $arr_fk):
						if (empty($arr_fk['count'])) {
							continue;
						}
						if (str_find($arr_fk['codeAppscheme'], '_ligne')) {
							continue;
						}
						$APPTMP                  = new App($arr_fk['codeAppscheme']);
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $arr_fk['codeAppscheme'];
						$vars_rfk['table_value'] = $arr_fk['table_value'];
						$count                   = $arr_fk['count'];
						$dsp                     = empty($APP->get_settings($_SESSION['idagent'], $vars_rfk['table'] . $table . '_preview_fk_liste')) ? 'none' : $APP->get_settings($_SESSION['idagent'], $vars_rfk['table'] . $table . '_preview_fk_liste');
						?>
						<div class="applink" style="order: <?= $count ?>">
							<div auto_tree class="no_hidden_caret padding_more" onclick="save_setting_autoNext(this,'<?= $vars_rfk['table'] . $table ?>_preview_fk_liste')">
								<a <?= $act_chrome_gui ?> data-link data-table="<?= $vars_rfk['table'] ?>" data-vars="<?= http_build_query($vars_rfk); ?>" class="autoToggle">
									<i class="fa fa-<?= $APPTMP->iconAppscheme ?> fa-fw" style="color:<?= $APPTMP->colorAppscheme ?>"></i><?= $count . ' ' . $APPTMP->nomAppscheme . '' . (($count < 2) ? '' : 's') ?>
								</a>
							</div>
							<div class="retrait" style="display:<?= $dsp ?>;">
								<?= skelMdl::cf_module('app/app_liste/app_liste_mini', $vars_rfk) ?>
							</div>
						</div>
					<? endforeach; ?>
				<? endforeach; ?>
		</div>
		<?
	endif; ?>


