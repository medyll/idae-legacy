<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($_POST['table']);
	$table_value = (int)$_POST['table_value'];

	if (!droit_table($_SESSION['idagent'], 'U', $table)) return;
	//
	if (file_exists(APPMDL . '/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_update.php')) {
		echo skelMdl::cf_module('/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_update', $_POST);

		return;
	}
	if (file_exists(APPMDL . '/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_update.php')) {
		echo skelMdl::cf_module('/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_update', $_POST);

		return;
	}
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_update.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_update', $_POST);

		return;
	}
	//
	$APP             = new App($table);
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	//
	$APP_TABLE = $APP->app_table_one;

	$GRILLE_FK = $APP->get_grille_fk();
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$nom   = 'nom' . ucfirst($table);



?>
<div class="relative">
	<div class="enteteFor">
		<div><?= skelMdl::cf_module('app/app/app_menu', ['table' => $table, 'table_value' => $table_value, 'act_from' => 'update'], $table_value) ?></div>
	</div>
	<form class="Form" action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<!--<input type="hidden" name="scope" value="<? /*= $id */ ?>"/>-->
		<input type="hidden" name="<?= $id ?>" value="<?= $table_value ?>"/>
		<input type="hidden" name="vars[<?= $id ?>]" value="<?= $table_value ?>"/>
		<input type="hidden" name="vars[m_mode]" value="1"/>
		<div class="flex_h">
			<div class="padding flex_v" style="max-width:170px">
				<? if (!empty($APP_TABLE['hasImageScheme'])): ?>
					<div class="aligncenter">
						<div class=" ededed inline aligncenter" style="width:150px;height:150px;">
							<img style="max-width:100%;" src="<?= Act::imgApp($table, $table_value, 'square') ?>">
						</div>
					</div>
				<? endif ?>
			</div>
			<?

				if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_update_fragment.php')) {
					echo '<div class="">' . skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_update_fragment', $_POST) . '</div>';
				}
			?>
			<div class="flex_main">
				<? if (!empty($APP_TABLE['hasLigneScheme'])): ?>
					<div class="padding"><?= idioma('Composantes') ?></div>
					<a onclick="<?=fonctionsJs::app_create($table.'_ligne',['vars'=>[$id=>$table_value]])?>">aa</a>
					<div class="margin border4" id="zone_maxi_ligne<?= $table . $table_value ?>" data-data_model="defaultModel" data-dsp="line">
					</div>
					<script>
						load_table_in_zone ('table=<?=$table?>_ligne&vars[<?=$id?>]=<?=$table_value?>', 'zone_maxi_ligne<?=$table.$table_value?>');
					</script>
				<? endif; ?>
				<div>
					<? foreach ($ARR_GROUP_FIELD as $key => $val) {
						$arrg = $val['group'];
						$arrf = $val['field'];
						?>
						<div class="borderb">
							<div>
								<div class="none"><?= $arrg['iconAppscheme_field_group'] ?></div>
							</div>
							<div class="flex_h flex_wrap" style="width:550px;">
								<? foreach ($arrf as $keyf => $valf) {
									?>
									<div style="min-width:50%;">
										<table class="table_form">
											<tr>
												<td>
													<label class="ms-Label"><i class="fa fa-<?= $valf['iconAppscheme_field'] ?> textgris borderr"></i> <?= ucfirst($valf['nomAppscheme_field']) ?> </label>
												</td>
												<td>
													<?= $APP->draw_field_input(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $ARR[$valf['codeAppscheme_field'] . $Table]]) ?>
												</td>
											</tr>
										</table>
									</div>
								<? } ?>
							</div>
						</div>
					<? } ?>
				</div>
			</div>
			<div class="borderl ededed">
				<br>
				<?
					$arr_has = ['statut', 'type', 'categorie', 'group'];
					foreach ($arr_has as $key => $value):
						$Value  = ucfirst($value);
						$_table = $table . '_' . $value;
						$_Table = ucfirst($_table);
						$_id    = 'id' . $_table;
						$_nom   = 'nom' . $_Table;
						if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
							<div class="padding margin borderb ">
								<div class="label"><?= ucfirst(idioma($Value)) ?></div>
								<div class="padding">
									<input datalist_input_name="vars[<?= $_id ?>]"
									       datalist_input_value="<?= $ARR[$_id] ?>"
									       datalist="app/app_select"
									       populate
									       name="vars[<?= $_nom ?>]"
									       paramName="search"
									       vars="table=<?= $_table ?>"
									       value="<?= $ARR[$_nom] ?>"
									       class="inputMedium"/>
								</div>
							</div>
						<? endif; ?>
					<? endforeach; ?>

				<? if (sizeof($GRILLE_FK != 0)): ?>
					<div class="padding margin borderb">
					<?
					if (!empty($ARR['idclient'])) unset($GRILLE_FK['prospect']);
					if (!empty($ARR['idprospect'])) unset($GRILLE_FK['client']);

					foreach ($GRILLE_FK as $key_table => $field):
						$APP_TMP       = new App($field['table_fk']);
						$GRILLE_FK_TMP = $APP_TMP->get_grille_fk($field['table_fk']);
						$id            = 'id' . $field['table_fk'];
						$nom           = 'nom' . ucfirst($field['table_fk']);
						$arr           = $APP_TMP->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name      = $arr['nom' . ucfirst($field['table_fk'])];
						//
						if ($field['table_fk'] == 'prospect' && !empty($ARR['idclient'])) $nohas = 'client';
						if ($field['table_fk'] == 'client' && !empty($ARR['idprospect'])) $nohas = 'prospect';

						// if (!empty($nohas)) continue;
						$arr_extends   = array_intersect(array_keys($GRILLE_FK), array_keys($GRILLE_FK_TMP));
						$http_add_vars = '';
						if (sizeof($arr_extends) != 0) {
							$add_vars = [];
							foreach ($arr_extends as $key_ext => $table_ext) {
								$idtable_ext = 'id' . $table_ext;
								if (!empty($ARR[$idtable_ext])) {
									$add_vars[$idtable_ext] = (int)$ARR[$idtable_ext];
								}
							}
							if (!empty($add_vars)) {
								$http_add_vars = $APP->translate_vars($add_vars);
							}
							// vardump($arr_extends );
						}
						if ($table == 'produit_tarif_gamme' && $field['table_fk'] == 'transport_gamme') {
							$APP_TMP = new App('produit');
							$arr     = $APP_TMP->findOne(['idproduit' => $ARR['idproduit']]);
							$http_add_vars .= "&vars[idtransport]=" . $arr['idtransport'];
						}
						?>
						<div class="ms-Label" title="<?= $field['codeAppscheme'] ?>">
							<i class="fa fa-<?= $field['icon_fk'] ?> textgris"></i> <?= ucfirst($field['nomAppscheme']) ?></div>
						<div class="padding  ">
							<input datalist_input_name="vars[<?= $id ?>]" datalist_input_value="<?= $ARR[$id] ?>" datalist="app/app_select" populate name="vars[<?= $nom ?>]" paramName="search"
							       scope="app_select_<?=$field['table_fk']?>"
							       vars="table=<?= $field['table_fk'] ?>&<?= $http_add_vars ?>"
							       value="<?= $dsp_name ?>" type="text" class="inputMedium"/>
						</div>
					<? endforeach; ?></div><? endif; ?>
			</div>
		</div>
		<br>
		<div class="buttonZone flex_h flex_align_middle">
			<div class="flex_main">
				<button type="button" class="trash_button left" onclick="<?= fonctionsJs::app_delete($table, $table_value) ?>">
					<?= idioma('Supprimer') ?>
				</button>
			</div>
			<div>
				<input class="valid_button" type="submit" value="<?= idioma('Valider') ?>">
				<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
			</div>
		</div>
	</form>
</div>
<div class="titreFor">
	<?= idioma('Mise à jour ') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div>
<style>
	.valid_button {
		border : 1px solid #0000FF;
	}
	.trash_button {
		border : 1px solid #B81900;
	}
	.trash_button:before {
		content     : '\f014';
		font-family : FontAwesome;
		color       : #B81900;
		margin      : 0 5px;
	}
</style>
