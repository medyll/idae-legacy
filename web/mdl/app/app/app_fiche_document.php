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
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP    = new App($table);
	$APPOBJ = $APP->appobj($table_value, $vars);
	$ARR    = $APPOBJ->ARR;
	//
	// vardump($APPOBJ);
	/** @var  $EXTRACTS_VARS */
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);
	//
	//
	$arrFields = $APP->get_basic_fields();
	//
	$name_id = 'id' . $table;
	//
	$nom = 'nom' . ucfirst($table);

	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE');

?>
<div class="flex_h" style="height:100%;">
	<div><?= skelMdl::cf_module('app/app_component/app_component_fiche_info', $_POST + ['moduleTag' => 'none'], $table); ?></div>
	<div class=" relative">
		<div class="flex_v    " style="height:100%;" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
			<div><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
			<div id="fiche_table_<?= $table ?>_<?= $table_value ?>" class="flex_h relative ">
				<div class="flex_main" >
					<div class="flex_v" >
						<div class="margin flex_v" style="min-width:350px;max-width:650px; ">
							<div class="borderb">
								<? foreach ($APPOBJ->ARR_GROUP_FIELD as $key => $val) {
									$arrg = $val['group'];
									$arrf = $val['field'];
									?>
									<div class="margin   flex_h flex_wrap flex_align_stretch">
										<? foreach ($arrf as $keyf => $valf) {
											if (empty($ARR[$valf['codeAppscheme_field'] . $Table])) continue;
											?>
											<table class="table_info flex_grow_1">
												<tr>
													<td>
														<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($valf['nomAppscheme_field'])) ?>
														:
													</td>
													<td>
														<?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $ARR[$valf['codeAppscheme_field'] . $Table]]) ?>
													</td>
												</tr>
											</table>
										<? } ?>
									</div>
								<? } ?>
							</div>
							<div class="padding">
								<div><?= skelMdl::cf_module('app/app/app_fiche_fk', ['mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?></div>
							</div>
							<? if (!empty($APPOBJ->APP_TABLE['hasLigneScheme'])): ?>
								<br>
								<div class="border4" style="max-height:300px;overflow:auto;" id="zone_ligne<?= $table_value ?>" data-classname="table_vertical" data-data_model="defaultModel">
									<script>
										load_table_in_zone ('table=<?=$table?>_ligne&vars[<?=$name_id?>]=<?=$table_value?>', 'zone_ligne<?=$table_value?>');
									</script>
								</div>
								<br>
							<? endif; ?>
							<div class="flex_main"></div>
							<? if (sizeof($R_FK) != 0) : ?>
								<div class="padding_more ededed bordert">
									<?= skelMdl::cf_module('app/app/app_fiche_rfk', ['act_chrome_gui' => 'app/app_liste/app_liste_gui', 'table' => $table, 'table_value' => $table_value], $table_value) ?>
								</div>
							<? endif; ?>
						</div>
						<div class="flex_main dark_3" >main</div>
					</div>
				</div>
				<div class="" style="max-width:210px;">
					<div class="">
						<div class="padding_more relative">
							<?
								$arr_has = ['statut', 'type', 'categorie', 'group'];
								foreach ($arr_has as $key => $value):
									$APPTMP = new App($value);
									$Value  = ucfirst($value);
									$_table = $table . '_' . $value;
									$_Table = ucfirst($_table);
									$_id    = 'id' . $_table;
									$_nom   = 'nom' . $_Table;
									if (!empty($ARR[$_nom])): ?>
										<div class="padding_more">
											<div class="textgris"><?= ucfirst(idioma($Value)) ?> <i class="fa fa-<?= $APPTMP->iconAppscheme ?>"></i></div>
											<div><?= $ARR[$_nom] ?></div
										</div>
									<? endif; ?>
								<? endforeach; ?>
						</div>
						<div>
							<div><?= skelMdl::cf_module('app/app/app_fiche_history', ['mode' => 'fiche', 'table' => $table, 'table_value' => $table_value], $table_value) ?></div>
						</div>
						<? if ($APPOBJ->R_FK['document']): ?>
							<div>
								<div class=" inline padding border4 margin ededed">
									<div style="position:relative;width:100px;" id="drag_perso">
										<a class="cursor inline relative" style="overflow:hidden;width:140px">
											<i class="fa fa-upload"></i> <?= idioma('document') ?>
											<input name="file" id="file" class="cursor inline" type="file" style="opacity:0;position:absolute;left:0;top:0;z-index:0;"/>
										</a>
									</div>
								</div>
								<form novalidate id="form_upload_<?= $table ?>" action="mdl/app/app_document/actions.php" onsubmit="ajaxFormValidation(this);return false">
									<input type="hidden" name="F_action" value="addDoc"/>
									<input type="hidden" name="base" value="sitebase_ged"/>
									<input type="hidden" name="collection" value="ged_bin"/>
									<input type="hidden" name="multiple" value="1"/>
									<input type="hidden" name="vars[idagent_owner]" value="<?= $_SESSION['idagent'] ?>"/>
									<input type="hidden" name="vars[table]" value="<?= $table ?>"/>
									<input type="hidden" name="vars[table_value]" value="<?= $table_value ?>"/>
									<input type="hidden" name="vars[<?= $name_id ?>]" value="<?= $table_value ?>"/>
									<input type="hidden" name="table" value="<?= $table ?>"/>
									<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
									<input type="hidden" name="reloadModule[app/app/app_fiche_rfk]" value="<?= $table_value ?>"/>
								</form>
								<div id="pref_preview_<?= $table ?>" class="aligncenter ededed" style="overflow:auto"></div>
								<style>
									#pref_preview img {
										max-width : 150px;
									}
								</style>
								<script>
									new myddeAttach ($ ('fiche_table_<?=$table?>_<?=$table_value?>'), { form : 'form_upload_<?= $table ?>', autoSubmit : true });
								</script>
							</div>
						<? endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="buttonZone ">
	<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
</div>
<? if (droit('DEV')) { ?>
	<div class="footerFor">
		<div class="padding bordert  ededed">     <?= $_POST['module'] ?><? printr($_POST) ?></div>
	</div>
<? } ?>
<div class="titreFor">
	<i class="fa fa-<?= $APPOBJ->ICON ?>" boxshadow style="color:<?= $ICON_COLOR ?>"></i> <?= ucfirst($APPOBJ->NAME_APP) ?> <?= $APPOBJ->ARR[$nom] ?>
</div>