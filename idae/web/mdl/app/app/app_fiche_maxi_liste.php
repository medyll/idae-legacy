<?
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];

	$APP            = new App($table);
	$GRILLE_RFK_BIS = $APP->get_grille_rfk($table);

	$zouzou           = uniqid($table);
	$idappscheme_type = (empty($_POST['idappscheme_type'])) ? (int)$GRILLE_RFK_BIS[0]['idappscheme_type'] : (int)$_POST['idappscheme_type'];
	$R_FK_FIRST       = $APP->get_reverse_grille_fk($table, $table_value, ['idappscheme_type' => $idappscheme_type]);
?>
<div class="blanc" style="height: 100%;overflow-x:hidden;overflow-y:auto;">
	<div class="flex_v" main_auto_tree>
		<div style="z-index:100;" class="flex_h flex_align_middle borderb  blanc ">
			<div class="padding_more  flex_main alignright   borderr " style="width:90px;">
				<?= idioma('Rechercher') ?> <?= $APP->nomAppscheme ?>
			</div>
			<div class="padding_more   ">
				<div class="relative">
					<input data-quickFind="" data-quickFind-where="where_all_<?= $zouzou ?>"
					       data-quickFind-tag=".autoToggle" data-quickFind-parent=".squickfind"
					       data-quickFind-post="[expl_file_list]" type="text">
				</div>
			</div>
		</div>
		<div id="where_all_<?= $zouzou ?>" class="flex_main flex_v" style="overflow-x:hidden;overflow-y: auto;">
			<? if (sizeof($R_FK_FIRST) != 0):
				foreach ($R_FK_FIRST as $arr_fk):
					$vars_rfk              = [];
					$value_rfk               = $arr_fk['table_value'];
					$table_rfk               = $arr_fk['table'];
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$vars_rfk['table']       = $table_rfk;
					$vars_rfk['table_value'] = $value_rfk;
					//if($table_rfk=='materiel')  $vars_rfk['groupBy'] = "site";

					$count = $arr_fk['count'];
					// if ($count == 0) continue;
					$settings_button_model = $APP->get_settings($_SESSION['idagent'], 'list_data_button_model', $table_rfk);
					$settings_button_model = empty($settings_button_model) ? 'miniModel' : $settings_button_model;
					$css_eye               = ($settings_button_model == 'defaultModel') ? 'active' : '';
					$css_info              = ($settings_button_model == 'miniModel') ? 'active' : '';

					$settings_button_model = empty($_POST['model']) ? 'miniModel' : $_POST['model'];

					$html_vars = "table=$table_rfk&vars[id$table]=$table_value&nbRows=15";
				 	if($table_rfk=='materiel') $html_vars.="&groupBy=site";
					?>
					<div class="flex_h    squickfind  " style="order:-<?= (int)$count ?>">
						<div class="padding_more aligncenter textgrisfonce  none    bordert"
						     style="width:90px;position:sticky;top:0;">
							<i class="fa fa-<?= $arr_fk['iconAppscheme'] ?> fa-2x "
							   style="color:<?= $arr_fk['colorAppscheme'] ?>"></i>
						</div>
						<div class="flex_main" app_gui_explorer>
							<div style="position:sticky;top:0;z-index:20;" auto_tree right="right"
							     class="no_hidden_caret padding_more blanc boxshadowb   bordert        ">
								<div>
									<div class="flex_h flex_align_middle uppercase bold">
										<div class="padding flex_main"><i
												class="fa fa-<?= $arr_fk['iconAppscheme'] ?>  "
												style="color:<?= $arr_fk['colorAppscheme'] ?>"></i><?= $count ?> <?= $arr_fk['nomAppscheme'] ?>
											<br>
										</div>
									</div>
								</div>
							</div>
							<div class="flex_main flex_h" style=";z-index:2">
								<div class="toggler textgrisfonce borderr   " style="overflow:hidden;width:80px;">
									<div class="padding applink applinkblock aligncenter">
										<a onclick="<?= fonctionsJs::app_create($arr_fk['table'], ['vars' => ['id' . $table => $table_value, 'idagent' => $_SESSION['idagent']]]) ?>">
											<i class="fa fa-plus-circle textbleu"></i>
										</a>
									</div>
								</div>
								<div class="flex_main">
									<div id="<?= $table_rfk . $zouzou ?>" data-dsp_liste=""
									     data-vars="<?=$html_vars?>"
									     data-dsp-pager="pager_<?= $table_rfk . $zouzou ?>"
									     data-dsp-sum="sum_<?= $table_rfk . $zouzou ?>" data-dsp="table_div"
									     data-data_model="<?= $settings_button_model ?>" expl_file_list>
									</div>
								</div>
								<div id="pager_<?= $table_rfk . $zouzou ?>" class="alignright flex_h"></div>
								<div id="sum_<?= $table_rfk . $zouzou ?>" class="flex_h"></div>
							</div>
						</div>
					</div>
				<? endforeach; ?>
			<? endif; ?>
		</div>
	</div>
</div>