<?
	include_once($_SERVER['CONF_INC']);

	//
	// ESPACE
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//

	//
	if ($table == 'agent_tuile' || $table == 'agent_activite'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;

	$APP = new App($table);
	//
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$R_FK            = $APP->get_reverse_grille_fk($table, $table_value);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);


	//
	$arrFieldsBool = $APP->get_array_field_bool();
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE_MAXI');
	// skelMdl::reloadModule('gui/gui_activity', $_SESSION['idagent']);
	$id_fiche_maxi = $table . '_fiche_maxi';
?>
<div class="flex_v blanc" style="height: 100%;" id="fiche_entete_table_<?=$table?>_<?=$table_value?>">
	<div style="z-index:200;"><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
	<div class="flex_h padding borderb" style="z-index:0;">
		<div id="<?= $id_fiche_maxi ?>"  class="flex_h flex_main">
			<div class="none" style="z-index:200;"><?= skelMdl::cf_module('app/app/app_fiche_entete', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
			<div class="flex_main flex_h flex_wrap">
				<? foreach ($ARR_GROUP_FIELD as $key => $val) {
					$arrg = $val['group'];
					$arrf = $val['field'];
					?>
					<div>
						<div class="  autoBlock">
							<table class="table_info  autoBlock" style="table-layout: fixed;">
								<? foreach ($arrf as $keyf => $valf) {
									if (empty($ARR[$valf['codeAppscheme_field'] . $Table])) continue;
									?>
									<tr>
										<td>
											<label class="textgrisfonce"><i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst($valf['nomAppscheme_field']) ?>
											</label>
										</td>
										<td>
											<?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $ARR[$valf['codeAppscheme_field'] . $Table]]) ?>
										</td>
									</tr>
								<? } ?>
							</table>
						</div>
					</div>
				<? } ?>
			</div>
			<div class="flex_h flex_wrap">
				<? if (sizeof($R_FK) != 0): ?>
					<? foreach ($R_FK as $arr_fk):
						$final_rfk[$arr_fk['scope']][] = $arr_fk;
					endforeach; ?><? foreach ($final_rfk as $key => $arr_final):
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
									<div act_chrome_gui="app/app_liste/app_liste_gui" vars="<?= http_build_query($vars_rfk); ?>" data-link data-table="<?= $vars_rfk['table'] ?>" data-vars="<?= http_build_query($vars_rfk); ?>">
										<a class="ededed border4 autoToggle"><i class="fa fa-<?= $arr_fk['icon'] ?> textbleu padding"></i> <?= $count . ' ' . $arr_fk['nomAppscheme'] . '' . (($count == 0) ? '' : 's') ?>
										</a>
									</div>                        <? endforeach; ?>
						</div>            <? endforeach; ?>
					<br>        <? endif; ?>
			</div>

			<? if ($R_FK['document'] && droit('DEV')): ?>
				<div>
					<div class="padding border4 margin ededed">
						<div style="position:relative;min-height:100px;width:100px;" id="drag_perso">
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
						<input type="hidden" name="vars[idagent_owner]" value="<?= $_SESSION['idagent'] ?>"/>
						<input type="hidden" name="vars[table]" value="<?= $table ?>"/>
						<input type="hidden" name="vars[table_value]" value="<?= $table_value ?>"/>
						<input type="hidden" name="vars[<?= $name_id ?>]" value="<?= $table_value ?>"/>
						<input type="hidden" name="act_thumb" value="1"/>
						<input type="hidden" name="multiple" value="1"/>
						<input type="hidden" name="table" value="<?= $table ?>"/>
						<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
						<input type="hidden" name="reloadModule[app/app/app_fiche_rfk]" value="<?= $table_value ?>"/>
					</form>
					<div id="pref_preview_<?= $table ?>" class="aligncenter ededed" style="overflow:auto"></div>
					<style>
						#pref_preview img {
							max-width: 150px;
						}
					</style>
					<script>
						new myddeAttach($('fiche_entete_table_<?=$table?>_<?=$table_value?>'), {form: 'form_upload_<?= $table ?>', autoSubmit: true });
					</script>
				</div>
			<? endif; ?>
		</div>
	</div>
</div>