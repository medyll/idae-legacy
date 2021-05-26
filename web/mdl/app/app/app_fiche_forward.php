<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	// CONVERSION
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history' || $table == 'agent_table'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;
	//
	$APP = new App($table);
	$Idae = new Idae($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE');

	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
?>
<div style="height:100%;" class="flex_v relative borderr blanc forwarder frmCol1">
	<div class="  borderb"><?= skelMdl::cf_module('app/app/app_fiche_entete_arbo', ['act_from'    => 'fiche',
	                                                                         'table'       => $table,
	                                                                         'table_value' => $table_value]) ?></div>
	<div class="flex_main toggler" style="overflow-x:hidden;overflow-y:auto">
		<? if (!empty($APP_TABLE['hasLigneScheme'])): ?>
			<div class="padding"><?= idioma('Composantes') ?></div>
			<div class="margin border4" id="zone_maxi_ligne<?= $table . $table_value ?>" data-data_model="defaultModel" data-dsp="line">
			</div>
			<script>
				load_table_in_zone ('table=<?=$table?>_ligne&vars[<?=$id?>]=<?=$table_value?>', 'zone_maxi_ligne<?=$table.$table_value?>');
			</script>
		<? endif; ?>
		<? if (sizeof($GRILLE_FK) != 0) { ?>
			<div class="">
				<table class="applink applinkblock" style="width:100%;">
					<? foreach ($GRILLE_FK as $field):
						// query for name
						$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
						if (empty($dsp_name)) {
							continue;
						}
						?>
						<tr data-link data-table="<?= $field['table_fk'] ?>" data-table_value="<?= $ARR[$field['idtable_fk']] ?>">
							<td class="">
								<a class="autoToggle"><i class="fa fa-folder textbleu ededed border4 padding"></i> <?= ucfirst($field['table_fk']) ?> <?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?>
								</a>
							</td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
		<? } ?>
		<div class="bordertb applinkblock"><?= skelMdl::cf_module('app/app/app_fiche_rfk', ['table'       => $table,
		                                                                                    'table_value' => $table_value]) ?></div>
	</div>
	<div class="flex_main ededed  bordert forwarder_zone_fiche" style="position:relative;overflow:hidden;display:none;height:50%;"></div>
	<div class="padding_more ededed alignright bordert">
		<a class="cancelRemove alignright"> fermer <i class="fa fa-times-circle-o textrouge"></i></a>
		<? if (isset($TEST_AGENT)): ?>
			<i class="fa fa-user-secret"></i>
		<? endif; ?>
	</div>
</div>
<div class="forwarder_zone flex_h" style="position:relative;overflow:hidden;height:100%;"></div>
