<?
	include_once($_SERVER['CONF_INC']);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_preview.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_preview', $_POST);

		return;
	}

	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	if ($table == 'agent_tuile'): // || $table=='agent_activite'
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
	endif;
	//
	$APP = new App($table);
	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP->extract_vars($table_value, $vars);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$name_id = 'id' . $table;

	$Idae        = new Idae($table);
	$ARR_COLLECT = $Idae->get_table_fields($table_value);

?>
<div class="flex_v" style="height:100%;width:100%;max-width:532px;position:relative;overflow:auto;">
	<div><?= skelMdl::cf_module('app/app/app_menu', ['table' => $table, 'table_value' => $table_value]) ?></div>
	<div class="flex_main dark_1" style="overflow-y:auto;overflow-x:hidden;z-index:0;">
		<div main_auto_tree>
			<div class="dark_2 flex_h">
				<?
					$arr_has = ['categorie', 'statut', 'type'];
					foreach ($arr_has as $key => $value):
						$Value  = ucfirst($value);
						$_table = $table . '_' . $value;
						$_Table = ucfirst($_table);
						$_id    = 'id' . $_table;
						$_nom   = 'nom' . $_Table;
						if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
							<div class="padding_more flex_main">
								<div class="label"><?= ucfirst(idioma($Value)) ?> : <?= $ARR[$_nom] ?></div>
							</div>
						<? endif; ?>
					<? endforeach; ?>
			</div>
			<div class="dark_1">
				<div auto_tree right="right" class="dark_3 padding_more" onclick="save_setting_autoNext(this,'<?= $table ?>_preview_grillefk')">
					<div class="padding_more titre" auto_tree_click="true"> <?= idioma('Détails') ?></div>
				</div>
				<tr class="dark_1 relative">
					<table class="table_info">
						<? foreach ($ARR_COLLECT as $key => $ARR_FIELD_GROUP) {
							?>
							<tr class="none">
								<td><i class="fa fa-<?= $ARR_FIELD_GROUP['appscheme_field_group']['iconAppscheme_field_group'] ?>"></i>
									<?= $ARR_FIELD_GROUP['appscheme_field_group']['nomAppscheme_field_group'] ?>
								</td>
							</tr>
							<?
							foreach ($ARR_FIELD_GROUP['appscheme_fields'] as $CODE_ARR_FIELD => $ARR_FIELD) { ?>
								<tr>
									<td class=" dark_2" style="padding-left:1rem;">
										<div class="no_wrap alignright">
											<i class="fa fa-<?= $ARR_FIELD['icon'] ?> item_icon"></i> <?= ucfirst(idioma($ARR_FIELD['nom'])) ?>
										</div>
									</td>
									<td class="<?= $ARR_FIELD['css_bol'] ?>">
										<?= $ARR_FIELD['value_html'] ?>
									</td>
								</tr>
							<? } ?>
						<? } ?>
					</table>
			</div>
		</div>
		<? if (!empty($APP_TABLE['hasLigneScheme'])): ?>
			<br>
			<div class="margin padding">
				<div class="border4 boxshadow" id="ligne_preview_<?= $table . $table_value ?>" data-classname="table_vertical" data-data_model="defaultModel">
					<script>
						load_table_in_zone ('table=<?=$table?>_ligne&vars[<?=$name_id?>]=<?=$table_value?>', 'ligne_preview_<?=$table.$table_value ?>');
					</script>
				</div>
			</div>
		<? endif; ?>
		<? if (sizeof($GRILLE_FK) != 0): ?>
			<div auto_tree right="right" class="dark_3 padding_more" onclick="save_setting_autoNext(this,'<?= $table ?>_preview_grillefk')">
				<div class="padding_more titre" auto_tree_click="true"> <?= idioma('Propriétés') ?></div>
			</div>
			<div class="dark_1">
				<div class="retrait" style="display:">
					<?= skelMdl::cf_module('app/app/app_fiche_fk', ['table' => $table, 'table_value' => $table_value, 'mode' => 'fiche'], $table_value) ?>
				</div>
			</div>
		<? endif; ?>
		<div auto_tree right="right" class="dark_3 padding_more" onclick="save_setting_autoNext(this,'<?= $table ?>_preview_list')">
			<div class="padding_more titre" auto_tree_click="true"> <?= idioma('Listes') ?></div>
		</div>
		<div class="dark_2">
			<div class="retrait" style="display:<?= $APP->get_settings($_SESSION['idagent'], $table . '_preview_list') ?>;">
				<div class="padding applink flex_main"><?= skelMdl::cf_module('app/app/app_fiche_rfk_liste', ['table' => $table, 'table_value' => $table_value, 'nbRows' => 10]) ?></div>
			</div>
		</div>
	</div>
</div>