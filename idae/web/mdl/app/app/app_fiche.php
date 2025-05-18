<?
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$Table = ucfirst($table);

	if (!droit_table($_SESSION['idagent'], 'R', $table)) return;
	//
	if (file_exists(APPMDL . '/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche.php')) {
		echo skelMdl::cf_module('/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche', $_POST);

		return;
	}
	if (file_exists(APPMDL . '/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche.php')) {
		echo skelMdl::cf_module('/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche', $_POST);

		return;
	}
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche', $_POST);

		return;
	}

	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	$APP  = new App($table);
	$Idae = new Idae($table);

	$APPOBJ = $APP->appobj($table_value, $vars);
	$ARR    = $APPOBJ->ARR;
	//
	$nom = 'nom' . ucfirst($table);
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE');

	$arr_has        = ['statut', 'type', 'categorie', 'group'];
	$arr_has_filter = array_map(function ($key, $value) {
		global $table, $ARR;
		$_table = $table . '_' . $value;
		if (!empty($ARR["id$_table"])):
			return $_table;
		endif;
	}, array_keys($arr_has), array_values($arr_has));
	$arr_has_filter = array_filter($arr_has_filter);

	$fiche_fields_mini = $Idae->module('app/app/app_fiche_fields', ['table'         => $table,
	                                                            'in_mini_fiche' => 1,
	                                                            'hide_empty'    => 1,
	                                                            'table_value'   => $table_value]);
	if (!empty($APPOBJ->APP_TABLE['hasLigneScheme'])):
		$fuche_ligne = $Idae->module('app/app/app_fiche_ligne', ['table' => $table . '_ligne',
		                                                         'vars'  => ["id$table" => $table_value]]);
	endif;
	$fiche_fields_nomini = $Idae->module('app/app/app_fiche_fields', ['table'         => $table,
	                                                              'in_mini_fiche' => 0,
	                                                              'hide_empty'    => 1,
	                                                              'table_value'   => $table_value]);


?>
<div class="flex_v">
	<div class=""><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
	<div class="flex_h" style="height:100%;">
		<div data-menu="data-menu" data-menu_free="data-menu_free" class="padding_more blanc borderr">
			<div class="padding_more cursor">
				<i class="fa fa-navicon fa-2x"></i>
			</div>
		</div>
		<div style="display:none;"><?= skelMdl::cf_module('app/app_component/app_component_fiche_info', $_POST + ['moduleTag' => 'none'], $table); ?></div>
		<div class="flex_main">
			<div class="flex_v " style="height:100%;" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
				<div class="flex_main borderb">
					<div class="flex_h" style="height:100%; ">
						<div class="flex_main flex_v">
							<div class="padding_more      alignright">
								<?= $APP->draw_field(['field_name_raw' => 'dateCreation', 'table' => $table, 'field_value' => $ARR['dateCreation' . $Table]]) ?>
							</div>
							<div class="flex_h flex_main">
								<div class="flex_main padding_more relative" style="max-width:650px;">
									<br>
									<div class="flex_main">
										<?= $fiche_fields_mini ?>
									</div>
									<? if (!empty($APPOBJ->APP_TABLE['hasLigneScheme'])): ?>
										<div class="padding_more  ededed   ">
											<div class="border4 blanc">
												<?= $fuche_ligne ?>
											</div>
										</div>
									<? endif; ?>
									<div class="padding"></div>
									<div class="flex_main">
										<?= $fiche_fields_nomini ?>
									</div>
								</div>
								<div>
									<? if (sizeof($arr_has_filter) != 0) { ?>
										<div class=" padding_more  " style="min-width:220px;">
											<div class="padding_more relative   ">
												<?
													foreach ($arr_has as $key => $value):
														$APPTMP = new App($value);
														$Value  = ucfirst($value);
														$_table = $table . '_' . $value;
														$_Table = ucfirst($_table);
														$_icon  = 'icon' . $_Table;
														$_color = 'color' . $_Table;
														$_nom   = 'nom' . $_Table;
														if (!empty($ARR[$_nom])): ?>
															<div class="padding_more borderb  " style="border-color:<?= $ARR[$_color] ?>">
																<div class="bold padding"><?= ucfirst($Value) ?> </div>
																<div class="padding"><i class="fa fa-<?= $ARR[$_icon] ?>"></i> <?= $ARR[$_nom] ?></div>
															</div>
														<? endif; ?>
													<? endforeach; ?>
											</div>
										</div>
									<? } ?>
									<? if ($table == 'tache') { ?>
										<div class="blanc"><?= skelMdl::cf_module('app/app/app_fiche_history', ['mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?></div>
									<? } ?>
								</div>
							</div>
							<? if (sizeof($APP->get_grille_fk()) != 0) : ?>
								<div class="relative ededed padding_more">
									<div class="padding_more border4 blanc">
										<?= skelMdl::cf_module('app/app/app_fiche_fk', ['mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?>
									</div>
								</div>
							<? endif; ?>
						</div>
					</div>
				</div>
				<? if (sizeof($APP->get_table_rfk()) != 0) : ?>
					<div class="padding_more ededed   flex_h flex_align_middle">
						<div class="flex_main ">
							<?= skelMdl::cf_module('app/app/app_fiche_rfk', ['act_chrome_gui' => 'app/app_liste/app_liste_gui', 'table' => $table, 'table_value' => $table_value], $table_value) ?>
						</div>
					</div>
				<? endif; ?>
			</div>
		</div>
	</div>
	<div class="buttonZone   ">
		<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
	</div>
</div>
<? if (droit('DEV')) { ?>
	<div class="footerFor">
		<div class="padding bordert  ededed none"><?= $_POST['module'] ?><? printr($_POST) ?></div>
	</div>
<? } ?>
<div class="titreFor">
	<i class="fa fa-<?= $APPOBJ->ICON ?>" boxshadow style="color:<?= $APPOBJ->COLOR ?>"></i> <?= ucfirst($APPOBJ->NAME_APP) ?> <?= $APPOBJ->ARR[$nom] ?>
</div>