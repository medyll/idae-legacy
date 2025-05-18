<?
	include_once($_SERVER['CONF_INC']);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	if (file_exists(APPMDL . '/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_create.php')) {
		echo skelMdl::cf_module('/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_create', $_POST);

		return;
	}
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_create.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_create', $_POST);

		return;
	}
	//
	$APP  = new App($table);
	$Idae = new Idae($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$nom   = 'nom' . ucfirst($table);
?>
<div class="relative blanc flex_v">
	<div class="titreFor">
		<?= idioma('Création') ?> <?= $table ?>
	</div>
	<form class="flex_main flex_v" action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="app_create"/>
		<input type="hidden" name="reloadModule[app/app_select]" value="app_select_<?= $table ?>"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="vars[m_mode]" value="1"/>
		<? foreach ($vars as $key => $input): ?>
			<input type="hidden" name="vars[<?= $key ?>]" value="<?= $input ?>">
		<? endforeach; ?>
		<div class="barre_entete"><?= $APP->vars_to_titre($vars) ?></div>
		<div class="flex_h flex_main">
			<?

				if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_create_fragment.php')) {
					echo '<div class="">' . skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_create_fragment', $_POST) . '</div>';
				}
			?>
			<div class="flex_main padding">

				<? foreach ($ARR_GROUP_FIELD as $key => $val) {
					$arrg = $val['group'];
					$arrf = $val['field'];
					?>
					<div class="nth2 flex_h borderb">
						<div class="padding_more borderr  aligncenter" style="width:60px;">
							<div title="<?= $arrg['nomAppscheme_field_group'] ?>"><i class="fa fa-<?= $arrg['iconAppscheme_field_group'] ?> fa-2x textgris"></i></div>
						</div>
						<div class="flex_h flex_wrap" style="width:550px;">
							<? foreach ($arrf as $keyf => $valf) {
								$code = $valf['codeAppscheme_field'];?>
								<div style="min-width:50%;">
									<table class="table_form">
										<tr>
											<td>
												<label class="ms-Label"><i class="fa fa-<?= $valf['iconAppscheme_field'] ?>    "></i> <?= ucfirst($valf['nomAppscheme_field']) ?> </label>
											</td>
											<td>
												<?= $APP->draw_field_input(['field_name_raw' => $code, 'table' => $table, 'field_value' => empty($vars[$code . $Table]) ? '' : $vars[$code . $Table]]) ?>
											</td>
										</tr>
									</table>
								</div>
							<? } ?>
						</div>
					</div>
				<? } ?>
			</div>
			<div>
				<br>
				<?
					$arr_has = ['statut', 'type', 'categorie','group','groupe'];
					foreach ($arr_has as $key => $value):
						$Value = ucfirst($value);
						$_id   = 'id' . $table . '_' . $value;
						$_nom  = 'nom' . ucfirst($value);
						if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
							<div class="margin">
								<div class="padding margin ">
									<div class="ms-TextField padding">
										<label class="ms-Label"><?= idioma($Value) . ' ' . $table ?></label>
										<input datalist_input_name="vars[<?= $_id ?>]"
										       datalist_input_value=""
										       datalist="app/app_select"
										       populate
										       scope="app_select_<?= $table . '_' . $value ?>"
										       placeholder="<?= $value ?>"
										       paramName="search"
										       class="inputMedium"
										       vars="table=<?= $table . '_' . $value ?>"
										       value=""/>
									</div>
								</div>
							</div>
						<? endif; ?>
					<? endforeach; ?>
				<div class="padding margin borderl ">
					<?
						if (!empty($vars['idclient'])) unset($GRILLE_FK['prospect']);
						if (!empty($vars['idprospect'])) unset($GRILLE_FK['client']);

						if (empty($vars['idclient']) && empty($vars['idprospect']) && !empty($GRILLE_FK['client']) && !empty($GRILLE_FK['prospect'])) {
							unset($GRILLE_FK['prospect'], $GRILLE_FK['client']);
							echo skelMdl::cf_module('app/app_field_add', ['display_mode' => 'vert', 'module_value' => 1235, 'field' => ['prospect', 'client']], 1235);
						}
						// vardump($vars);
						foreach ($GRILLE_FK as $key_table => $field):
							$trvars   = null;
							$nohas  = '';
							$APP_FK = new App($field['table_fk']);
							$id     = 'id' . $field['table_fk'];
							if (!empty($vars[$id])) continue;
							$nom      = 'nom' . ucfirst($field['table_fk']);
							$code     = 'code' . ucfirst($field['table_fk']);
							$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
							$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
							foreach ($vars as $key_vars => $vars_vars) {
								//echo " $key_vars ";
								//echo substr($key_vars,2);
								//echo $APP_FK->has_field_fk(substr($key_vars,2));
								if ($APP_FK->has_field_fk(substr($key_vars, 2))) {
									$trvars[$key_vars] = $vars_vars;

								}
							}
							unset($trvars['idagent']);
							// vardump($trvars);
							if ($trvars) {
								$trvars = $APP_FK->translate_vars($trvars);
							}
							?>
							<div class="padding">
								<div class="label" title="<?= $field['codeAppscheme'] ?>"><i class="fa fa-<?= $field['iconAppscheme'] ?>" style="color:<?= $field['colorAppscheme'] ?>"></i><?= ucfirst($field['nomAppscheme']) ?></div>
								<div class="padding">
									<input datalist_input_name="vars[<?= $id ?>]" datalist_input_value="<?= $ARR[$id] ?>" datalist="app/app_select" populate name="vars[<?= $nom ?>]" paramName="search" vars="table=<?= $field['table_fk'] ?>&<?= $trvars ?>"
									       scope="app_select_<?= $field['table_fk'] ?>"
									       value="<?= $dsp_name ?>" type="text" class="inputMedium"/>
								</div>
							</div>
						<? endforeach; ?></div>
			</div>
		</div>
		<div class="buttonZone">
			<input type="submit" value="<?= idioma('Valider') ?>">
			<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
		</div>
	</form>
	<div class="enteteFor" style="display:none;">
		<div class="">
			<div class="titre_entete">
				<i class="fa fa-<?= $APP_TABLE['iconAppscheme'] ?>" style="color:<?= $APP->colorAppscheme ?>"></i>
				&nbsp;&nbsp;<?= idioma('Création') ?>&nbsp;<?= $table ?> <?= $arr[$nom] ?> <?= $APP->vars_to_titre($vars) ?></div>
		</div>
	</div>
</div>