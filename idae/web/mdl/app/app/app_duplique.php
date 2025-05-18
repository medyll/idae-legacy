<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($_POST['table']);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_update.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_update', $_POST);

		return;
	}
	//
	$APP             = new App($table);
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	//
	$APP_TABLE = $APP->app_table_one;

	$GRILLE_FK  = $APP->get_grille_fk();
	$GRILLE_RFK = $APP->get_reverse_grille_fk($table, $table_value);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$nom   = 'nom' . ucfirst($table);

	$arrFieldsBool = $APP->get_array_field_bool();

?>
<div class="relative">
	<div class="titre_entete">
		<?= idioma('Dupliquer un élément') ?>
	</div>
	<form class="Form" action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="app_create"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value_duplique" value="<?= $table_value ?>"/>
		<input type="hidden" name="vars[m_mode]" value="1"/>
		<div class="flex_h">
			<div class="padding flex_v" style="max-width:170px">
				<? if (!empty($APP_TABLE['hasBoolScheme'])): ?>
					<div class="aligncenter  padding">
						<? foreach ($arrFieldsBool as $field => $arr_ico):
							$fa         = empty($ARR[$field . ucfirst($table)]) ? 'circle-thin' : 'check-circle';
							$css        = empty($ARR[$field . ucfirst($table)]) ? 'textgris' : 'textvert';
							$input_name = "vars[" . $field . ucfirst($table) . "]";
							?>
							<div class="inline <?= $css ?>" style="margin-right:1em;line-height:15px;">
								<?= ucfirst(idioma($field)) ?>
								&nbsp;
								<input name="<?= $input_name ?>" type="range" min="0" max="1" value="<?= $ARR[$field . ucfirst($table)] ?>" style="width:40px;height:15px;vertical-align: middle;"/>
							</div>
						<? endforeach; ?></div>
				<? endif ?>
			</div>
			<?

				if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_update_fragment.php')) {
					echo '<div class="">' . skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_update_fragment', $_POST) . '</div>';
				}
			?>
			<div class="flex_main">
				<div>
					<table class="table_form ededed">
						<tr>
							<td>
								<label class="ms-Label"><i class="fa fa-count textgris borderr"></i> <?= idioma('Occurences') ?> </label>
							</td>
							<td>
								<input class="inputSmall" type="number" min="1" name="vars_duplique_occurence" value="1" required="required">
							</td>
						</tr>
					</table>
					<hr>
					<? foreach ($ARR_GROUP_FIELD as $key => $val) {
						$arrg = $val['group'];
						$arrf = $val['field'];
						?>
						<div class="">
							<div>
								<div class="none"><?= $arrg['iconAppscheme_field_group'] ?></div>
							</div>
							<div class="flex_h flex_wrap" style="width:550px;">
								<? foreach ($arrf as $keyf => $valf) {
									if($valf['codeAppscheme_field']=='nom'){
										$ARR[$valf['codeAppscheme_field'] . $Table] = 'copie - '.$ARR[$valf['codeAppscheme_field'] . $Table];
									}
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
					<? if (sizeof($GRILLE_RFK) != 0) { ?>
						<hr>
						<div class="applink">
							<table class="table_form table_middle">
								<?
									foreach ($GRILLE_RFK as $arr_fk):
										$vars_rfk['vars']        = ['id' . $table => $table_value];
										$vars_rfk['table']       = $arr_fk['table'];
										$vars_rfk['table_value'] = $arr_fk['table_value'];
										$count                   = $arr_fk['count'];
										?>
										<tr>
											<td><div class="ellipsis"> <i class="fa fa-<?= $arr_fk['icon'] ?> borderr"></i> <?= $arr_fk['nomAppscheme'] ?></div></td>
											<td>
												<label>
													<input name="vars_duplique[<?= $arr_fk['table'] ?>]" type="checkbox"> <?= $count ?> <?= idioma('élément') ?>(s)
												</label>
											</td>
										</tr>
									<? endforeach; ?>
							</table>
						</div>
					<? } ?>
				</div>
			</div>
			<div class="ededed borderl">
				<br>
				<?
					$arr_has = ['statut', 'type','group'];
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
									       datalist_input_value=""
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
					<div class="padding margin">
					<?
					if (!empty($ARR['idclient'])) unset($GRILLE_FK['prospect']);
					if (!empty($ARR['idprospect'])) unset($GRILLE_FK['client']);

					foreach ($GRILLE_FK as $key_table => $field):
						$id       = 'id' . $field['table_fk'];
						$nom      = 'nom' . ucfirst($field['table_fk']);
						$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
						//
						if ($field['table_fk'] == 'prospect' && !empty($ARR['idclient'])) $nohas = 'client';
						if ($field['table_fk'] == 'client' && !empty($ARR['idprospect'])) $nohas = 'prospect';
						// if (!empty($nohas)) continue;
						?>
						<div class="label">
							<i class="fa fa-<?= $field['icon_fk'] ?> textgris"></i> <?= ucfirst($field['table_fk']) ?></div>
						<div class="padding borderb">
							<input datalist_input_name="vars[<?= $id ?>]" datalist_input_value="<?= $ARR[$id] ?>" datalist="app/app_select" populate name="vars[<?= $nom ?>]" paramName="search" vars="table=<?= $field['table_fk'] ?>"
							       value="<?= $dsp_name ?>" type="text" class="inputMedium"/>
						</div>
					<? endforeach; ?></div><? endif; ?>
			</div>
		</div>
		<br>
		<div class="buttonZone">
			<input class="valid_button" type="submit" value="<?= idioma('Valider') ?>">
			<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
		</div>
	</form>
</div>
<div class="titreFor">
<i class="fa fa-copy"></i>	<?= idioma('dupliquer ') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div>
<style>
	.valid_button {
		border: 1px solid #0000FF;
	}

	.trash_button {
		border: 1px solid #B81900;
	}

	.trash_button:before {
		content: '\f014';
		font-family: FontAwesome;
		color: #B81900;
		margin: 0 5px;
	}
</style>
