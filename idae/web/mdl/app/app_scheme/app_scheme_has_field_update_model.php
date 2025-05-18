<?
	include_once($_SERVER['CONF_INC']);
	// table_has => pour affichage table // appscheme_has_table_field
	ini_set('display_errors', 55);
	$APP               = new App();
	$APP_SCH           = new App('appscheme');
	$APP_SCH_FIELD     = new App('appscheme_field');
	$APP_SCH_HAS       = new App('appscheme_has_field');
	$APP_SCH_HAS_TABLE = new App('appscheme_has_table_field');

	$idappscheme  = (int)$_POST['idappscheme'];
	$table_value  = (int)$_POST['idappscheme'];
	$arr          = $APP->plug('sitebase_app', 'appscheme')->findOne(['idappscheme' => $idappscheme]);
	$table        = $arr['codeAppscheme'];
	$Table        = ucfirst($table);
	$GRILLE_FK    = empty($arr['grilleFK']) ? [] : $arr['grilleFK'];
	$GRILLE_COUNT = empty($arr['grilleFK']) ? [] : $arr['grilleCount'];
	$R_FK         = $APP->get_reverse_grille_fk($table);

	$types = $APP->app_default_fields;
	//
	$rs_field = $APP_SCH_HAS->find(['idappscheme' => $idappscheme])->sort(['nomAppscheme_field' => 1]);
	$APP_SCH_HAS->consolidate_scheme();

	//
?>
<div style="width:1050px;" class="ededed">
	<div class="blanc">
		<div style="overflow:hidden;" id=" ">
			<form action="<?= ACTIONMDL ?>app/app_scheme/actions.php" onSubmit="ajaxFormValidation(this);return false" auto_close="auto_close">
				<input type="hidden" value="update_appscheme_has_table_field" name="F_action">
				<input type="hidden" name="idappscheme" value="<?= $idappscheme ?>"/>
				<div class="flex_h">
					<div class="flex_main  borderr ededed padding">
						<table class="table_form">
							<? while ($arrf = $rs_field->getNext()) {
								$ARR_FIELD = $APP_SCH_FIELD->findOne(['idappscheme_field' => (int)$arrf['idappscheme_field']]);
								$Key       = ucfirst($arrf['field_raw']);
								// dans appscheme_has_table_field ?
								$rsSF = $APP->plug('sitebase_app', 'appscheme_has_table_field')->find(['idappscheme' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
								$has  = $rsSF->count();
//
								$act_vars     = $idappscheme . '_' . $idappscheme . '_' . $arrf['idappscheme_field'];
								$arr_test_has = $APP_SCH_HAS_TABLE->findOne(['idappscheme' => $idappscheme, 'idappscheme_link' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);

								if (!empty($has) && empty($arr_test_has['idappscheme_has_table_field'])) {

									$ins               = ['idappscheme' => $idappscheme, 'idappscheme_link' => (int)$idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']];
									$ins['field_name'] = $ARR_FIELD['field_raw'] . ucfirst($arrf['collection']);
									$ins['collection'] = $arrf['collection'];
									$ins['field_raw']  = $ARR_FIELD['field_raw'];
									$APP_SCH_HAS_TABLE->insert($ins);

								}
								?>
								<tr>
									<td>
										<div class="ellipsis"><?= ucfirst(empty($arrf['nomAppscheme_has_field']) ? $arrf['codeAppscheme_has_field'] : $arrf['nomAppscheme_has_field']) ?></div>
									</td>
									<td style="width:110px;"><?= chkSch($act_vars, $arr_test_has['idappscheme'], 'vars_has_table_field'); ?></td>
								</tr>
							<? } ?>
						</table>
					</div>
					<? if (sizeof($GRILLE_FK) != 0): ?>
						<div class="flex_main padding">
							<div auto_tree_main>
								<? foreach ($GRILLE_FK as $fk):
									$fkTable = ucfirst($fk['table']);
									$ARR_SCH = $APP_SCH->findOne(['codeAppscheme' => $fk['table']]);
									?>
									<div auto_tree>
										<div class="flex_h flex_align_middle padding borderb">
											<div class="flex_main"><?= $fk['table'] ?></div>
										</div>
									</div>
									<?
									// =>
									$arr_field   = $APP->plug('sitebase_app', 'appscheme_has_field')->distinct('idappscheme_field', ['idappscheme' => (int)$ARR_SCH['idappscheme']]);
									$arr_field_2 = $APP->plug('sitebase_app', 'appscheme_field')->distinct('idappscheme_field_group', ['idappscheme_field' => ['$in' => $arr_field]]);
									$rsG         = $APP->plug('sitebase_app', 'appscheme_field_group')->find(['idappscheme_field_group' => ['$in' => $arr_field_2]])->sort(['ordreAppscheme_field_group' => 1]);

									while ($arrg = $rsG->getNext()) {
										$rsF = $APP->plug('sitebase_app', 'appscheme_field')->find(['idappscheme_field_group' => (int)$arrg['idappscheme_field_group'], 'idappscheme_field' => ['$in' => $arr_field]])->sort(['ordreAppscheme_field_group' => 1]);
										?>
										<div class="autoBlock">
											<table class="table_form">
												<? while ($arrf = $rsF->getNext()) {
													$field_name   = $arrf['field_raw'] . '_' . $fkTable . '_' . $arrf['field_raw'];
													$Key          = ucfirst($arrf['field_raw']);
													$act_vars     = $idappscheme . '_' . $ARR_SCH['idappscheme'] . '_' . $arrf['idappscheme_field'];
													$arr_test_has = $APP_SCH_HAS_TABLE->findOne(['idappscheme' => $idappscheme, 'idappscheme_link' => (int)$ARR_SCH['idappscheme'], 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
													?>
													<tr>
														<td><?= ucfirst(empty($arrf['nomAppscheme_field']) ? $arrf['codeAppscheme_field'] : $arrf['nomAppscheme_field']) ?></td>
														<td>
															<div>
																<?= chkSch($act_vars, $arr_test_has['idappscheme'], 'vars_has_table_field'); ?></div>
														</td>
													</tr>
												<? } ?>
											</table>
										</div>
									<? } ?>
									<?
								endforeach; ?>
							</div>
						</div>
					<? endif; ?>
					<?
						if (sizeof($R_FK) != 0): foreach ($R_FK as $arr_fk):
							$final_rfk[$arr_fk['scope']][] = $arr_fk;
						endforeach;

							?>
							<div class=" ">
								<div auto_tree>
									<div class="flex_h flex_align_middle padding borderb">
										<div class="padding"><?= idioma('Tables de décompte') ?> </div>
									</div>
								</div>
								<div class="autoBlock">
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
														$vars_rfk['vars']        = ['id' . $table => $table_value];
														$vars_rfk['table']       = $arr_fk['table'];
														$vars_rfk['table_value'] = $arr_fk['table_value'];
														$count                   = $arr_fk['count'];
														?>
														<label class="flex flex_h flex_align_middle padding  ">
															<div>
																<input <?= checked($GRILLE_COUNT[$arr_fk['table']]) ?> name="vars_has_table_count[<?= $vars_rfk['table'] ?>]" type="checkbox" value="1">
															</div>
															<div>
																<i class="fa fa-<?= $arr_fk['icon'] ?>   padding"></i> <?= $arr_fk['nomAppscheme'] ?>
															</div>
														</label>
													<? endforeach; ?>
											</div>
										<? endforeach; ?></div>
							</div>
							<?
						endif; ?>
					<div class="flex_main padding borderr">
						<div auto_tree_main>
							<?
								$ARR_HAS = ['type', 'statut','categorie','groupe'];
								foreach ($ARR_HAS as $TYPE_NAME):

									if (!empty($arr['has' . ucfirst($TYPE_NAME) . 'Scheme'])) {
										$table_name = $table . '_' . $TYPE_NAME;
										$Table_name = $Table . '_' . $TYPE_NAME;
										$ARR_SCH    = $APP_SCH->findOne(['codeAppscheme' => $table_name]);

										?>
										<div auto_tree>
											<div class="padding borderb"><?= $Table_name ?></div>
										</div>
										<?
										// =>
										$arr_field   = $APP->plug('sitebase_app', 'appscheme_has_field')->distinct('idappscheme_field', ['idappscheme' => (int)$ARR_SCH['idappscheme']]);
										$arr_field_2 = $APP->plug('sitebase_app', 'appscheme_field')->distinct('idappscheme_field_group', ['idappscheme_field' => ['$in' => $arr_field]]);
										$rsG         = $APP->plug('sitebase_app', 'appscheme_field_group')->find(['idappscheme_field_group' => ['$in' => $arr_field_2]])->sort(['group_ordre' => 1]);

										while ($arrg = $rsG->getNext()) {
											$rsF = $APP->plug('sitebase_app', 'appscheme_field')->find(['idappscheme_field_group' => (int)$arrg['idappscheme_field_group'], 'idappscheme_field' => ['$in' => $arr_field]])->sort(['group_ordre' => 1]);
											?>
											<div class="autoBlock">
												<table class="table_form">
													<? while ($arrf = $rsF->getNext()) {
														$field_name   = $arrf['field_raw'] . '_' . $Table_name . '_' . $arrf['field_raw'];
														$Key          = ucfirst($arrf['field_raw']);
														$act_vars     = $idappscheme . '_' . $ARR_SCH['idappscheme'] . '_' . $arrf['idappscheme_field'];
														$arr_test_has = $APP_SCH_HAS_TABLE->findOne(['idappscheme' => $idappscheme, 'idappscheme_link' => (int)$ARR_SCH['idappscheme'], 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
														?>
														<tr>
															<td><?= ucfirst($arrf['nomAppscheme_field']) ?></td>
															<td>
																<div>
																	<?= chkSch($act_vars, $arr_test_has['idappscheme'], 'vars_has_table_field'); ?></div>
															</td>
														</tr>
													<? } ?>
												</table>
											</div>
										<? } ?>
									<? } ?>
								<? endforeach; ?>
						</div>
					</div>
				</div>
				<div class="buttonZone">
					<input type="button" value="Annuler" class="cancelClose"/>
					<input type="submit" value="Valider"/>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="enteteFor">
	<div class="titre_entete">"<?= $Table ?>" Champs de table</div>
	<div class="applink titre_entete_menu">
		<div class="in_menu">
			<a onclick="<?= fonctionsJs::app_sort('appscheme_has_table_field', '', ['vars' => ['idappscheme' => $idappscheme]]); ?>"><i class="fa fa-sort"></i> <?= idioma('ordonner les champs') ?></a>
		</div>
	</div>
</div>
