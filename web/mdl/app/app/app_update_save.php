<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($_POST['table']);
	$table_value = (int)$_POST['table_value'];
	//
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_update.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_update', $_POST);

		return;
	}
	//
	$APP = new App($table);

	//
	$APP_TABLE = $APP->app_table_one;

	$GRILLE_FK = $APP->get_grille_fk();
	$GRILLE    = $APP->get_grille($table);

	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//

	$nom = 'nom' . ucfirst($table);

	$arrFieldsBool = $APP->get_array_field_bool();

	$arr_dsp_fields = $APP->get_display_fields($table);
?>
<div class="relative">
	<div class="enteteFor">
		<?= skelMdl::cf_module('app/app/app_menu', ['table' => $table, 'table_value' => $table_value, 'act_from' => 'update'], $table_value) ?></div>
	<form class="Form" action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="F_action" value="app_update"/>
		<input type="hidden" name="table" value="<?= $table ?>"/>
		<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
		<input type="hidden" name="scope" value="<?= $id ?>"/>
		<input type="hidden" name="<?= $id ?>" value="<?= $table_value ?>"/>
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
			<div class="flex_main">
				<table class="table_info">
					<? if (!empty($APP_TABLE['hasNomSddcheme'])): ?>
						<tr class="aligncenter">
							<td colspan="4">
								<div class="ms-TextField ms-TextField--underlined">
									<label class="ms-Label">Nom</label>
									<input class="ms-TextField-field">
								</div>
							</td>
						</tr>
					<? endif ?>
					<? foreach ($arr_dsp_fields as $key => $value):
						//
						continue;
						$title     = $value['title'];
						$field     = $value['field_name'];
						$field_raw = $value['field_name_raw'];
						$val       = $ARR[$field];
						?>
						<tr>
							<td>
								<label class="ms-Label"><?= ucfirst(idioma($title)) ?></label>
							</td>
							<?
								switch ($field_raw):
									case "description":
										?>
										<td class="justify" colspan="3">
											<div class="ms-TextField  ms-TextField--multiline">
												<textarea name="vars[<?= $field ?>]" class="ms-TextField-field"><?= $val ?></textarea>
											</div>
										</td>
										<?
										break;
									case "lpko":
										?>
										<?
										break;
									default:
										?>
										<td class="justify" colspan="3">
											<div class="ms-TextField">
												<input name="vars[<?= $field ?>]" value="<?= $val ?>" class="ms-TextField-field" type="text">
											</div>
										</td>
										<?
										break;
								endswitch; ?>
						</tr>
					<? endforeach; ?>
				</table>
				<table class="table_form">
					<tr class=" marginb">
						<? if (!empty($APP_TABLE['hasOrdreScheme'])): ?>
							<td class="label">Ordre</td>
							<td>
								<input name="vars[ordre<?= $Table ?>]" type="text" class="inputSmall" value="<?= $ARR['ordre' . $Table] ?>">
							</td>
						<? endif ?>
						<? if (!empty($APP_TABLE['hasQuantiteScheme'])): ?>
							<td class="label">Quantite</td>
							<td>
								<input name="vars[quantite<?= $Table ?>]" type="text" class="inputSmall" value="<?= $ARR['quantite' . $Table] ?>">
							</td>
						<? endif ?>
						<? if (!empty($APP_TABLE['hasTypeScheme'])): ?>
							<td class="label">Type <?= $Table ?></td>
							<td>
								<input datalist_input_name="vars[<?= $id_type ?>]" datalist_input_value="<?= $arrType[$id_type] ?>" datalist="app/app_select" populate name="vars[<?= $nom_type ?>]" paramName="search" vars="table=<?= $table_type ?>" value="<?= $arrType[$nom_type] ?>"/>
							</td>
						<? endif ?>
						<? if (!empty($APP_TABLE['hasCodeScheme'])):
							if (is_array($ARR['code' . $Table])) $ARR['code' . $Table] = implode(';', $ARR['code' . $Table]);
							?>
							<td class="label">Code</td>
							<td>
								<input name="vars[code<?= $Table ?>]" type="text" value="<?= $ARR['code' . $Table] ?>">
							</td>
						<? endif ?>
					</tr>
				</table>
				<table class="table_form borderb marginb">
					<tr class="" style="width:100%;">
						<td class="label">Nom</td>
						<td colspan="5">
							<input name="vars[nom<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['nom' . $Table] ?>">
						</td>
					</tr>
					<? if (!empty($APP_TABLE['hasAtoutScheme'])): ?>
						<tr class="text-alert">
							<td class="label">Atout</td>
							<td colspan="5">
								<input name="vars[atout<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['atout' . $Table] ?>">
							</td>
						</tr>
					<? endif ?>
					<? if (!empty($APP_TABLE['hasPetitNomScheme'])): ?>
						<tr class="" style="width:100%;">
							<td class="label">Nom court</td>
							<td colspan="5">
								<input name="vars[petitNom<?= $Table ?>]" type="text" class="inputMedium" value="<?= $ARR['petitNom' . $Table] ?>">
							</td>
						</tr>
					<? endif ?>
					<? if (!empty($APP_TABLE['hasPrenomScheme'])): ?>
						<tr class="" style="width:100%;">
							<td class="label">Prénom</td>
							<td colspan="5">
								<input name="vars[prenom<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['prenom' . $Table] ?>">
							</td>
						</tr>
					<? endif ?>
					<? if (!empty($APP_TABLE['hasEmailScheme'])): ?>
						<tr class="" style="width:100%;">
							<td class="label">Email</td>
							<td colspan="5">
								<input name="vars[email<?= $Table ?>]" type="text" class="inputMedium" value="<?= $ARR['email' . $Table] ?>">
							</td>
						</tr>
					<? endif ?>
				</table>
				<? if (!empty($APP_TABLE['hasTelephoneScheme'])): ?>
					<table class="table_form borderb">
						<tr class="">
							<td class="label">Téléphone 1</td>
							<td>
								<input name="vars[telephone<?= $Table ?>]" type="text" class="" value="<?= $ARR['telephone' . $Table] ?>">
							</td>
							<td class="label">Téléphone 2</td>
							<td>
								<input name="vars[telephone2<?= $Table ?>]" type="text" class="" value="<?= $ARR['telephone2' . $Table] ?>">
							</td>
						</tr>
					</table>
				<? endif ?>
				<? if (!empty($APP_TABLE['hasAdresseScheme'])): ?>
					<table class="table_form borderb">
						<tr class="">
							<td class="label">Adresse</td>
							<td colspan="3">
								<input name="vars[adresse<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['adresse' . $Table] ?>">
							</td>
						</tr>
						<tr class="">
							<td class="label">&nbsp;</td>
							<td colspan="3">
								<input name="vars[adresse2<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['adresse2' . $Table] ?>">
							</td>
						</tr>
						<tr class="">
							<td class="label">Code postal</td>
							<td>
								<input name="vars[codePostal<?= $Table ?>]" type="text" value="<?= $ARR['codePostal' . $Table] ?>">
							</td>
							<td class="label">Ville</td>
							<td>
								<input name="vars[ville<?= $Table ?>]" type="text" value="<?= $ARR['ville' . $Table] ?>">
							</td>
						</tr>
					</table>
				<? endif ?>
				<? if (!empty($APP_TABLE['hasPrixScheme']) || !empty($APP_TABLE['hasRefScheme'])): ?>
					<table class="table_form borderb">
						<tr>
							<? if (!empty($APP_TABLE['hasPrixScheme'])): ?>
								<td class="label">Prix</td>
								<td>
									<input name="vars[prix<?= $Table ?>]" type="text" class="" value="<?= $ARR['prix' . $Table] ?>">
								</td>
								<td class="label">Ancien prix</td>
								<td>
									<input name="vars[oldPrix<?= $Table ?>]" type="text" class="" value="<?= $ARR['oldPrix' . $Table] ?>">
								</td>
							<? endif ?>
							<? if (!empty($APP_TABLE['hasRefScheme'])): ?>
								<td class="label">Reference</td>
								<td>
									<input name="vars[reference<?= $Table ?>]" type="text" value="<?= $ARR['reference' . $Table] ?>">
								</td>
							<? endif ?>
						</tr>
					</table>
				<? endif ?>
				<table class="table_form borderb">
					<? if (!empty($APP_TABLE['hasDateScheme']) || !empty($APP_TABLE['hasHeureScheme'])): ?>
						<tr class="" style="width:100%;">
							<? if (!empty($APP_TABLE['hasDateScheme'])): ?>
								<td class="label">Date de début</td>
								<td>
									<input class="validate-date-au" name="vars[dateDebut<?= $Table ?>]" type="text" value="<?= date_fr($ARR['dateDebut' . $Table]) ?>">
								</td>
							<? endif ?>
							<? if (!empty($APP_TABLE['hasDateScheme'])): ?>
								<td class="label">Date de fin .</td>
								<td>
									<input class="validate-date-au" name="vars[dateFin<?= $Table ?>]" type="text" value="<?= date_fr($ARR['dateFin' . $Table]); ?>">
								</td>
							<? endif ?>
						</tr>
						<tr class="" style="width:100%;">
							<? if (!empty($APP_TABLE['hasHeureScheme'])): ?>
								<td class="label">Heure de début</td>
								<td>
									<input class="heure" name="vars[heureDebut<?= $Table ?>]" type="text" value="<?= $ARR['heureDebut' . $Table] ?>">
								</td>
							<? endif ?>
							<? if (!empty($APP_TABLE['hasHeureScheme'])): ?>
								<td class="label">Heure de fin</td>
								<td>
									<input class="heure" name="vars[heureFin<?= $Table ?>]" type="text" value="<?= $ARR['heureFin' . $Table] ?>">
								</td>
							<? endif ?>
						</tr>
					<? endif ?>

					<? if (!empty($APP_TABLE['hasDureeScheme'])): ?>
						<tr class="" style="width:100%;">
							<td class="label">Durée</td>
							<td>
								<input name="vars[duree<?= $Table ?>]" type="text" class="inputTiny" value="<?= $ARR['duree' . $Table] ?>">
							</td>
						</tr>
					<? endif ?>
				</table>
				<? if (!empty($APP_TABLE['hasDescriptionScheme'])):
					$css_desc = '';
					if ($table == 'newsletter' || $table == 'newsletter_item') $css_desc = 'ext_mce_textarea';
					?>
					<table class="table_form   ededed  ">
						<tr class="" style="width:100%;">
							<td class="label">Description <?= date('i') ?></td>
							<td>
								<textarea <?= $css_desc ?> name="vars[description<?= $Table ?>]" class="inputLarge" rows="10"><?= $ARR['description' . $Table] ?></textarea>
							</td>
						</tr>
					</table>
				<? endif ?>
				<? if (!empty($APP_TABLE['hasUrlScheme'])): ?>
					<br>
					<table class="table_form border4  ededed">
						<tr class="" style="width:100%;">
							<td class="label">Url</td>
							<td>
								<input name="vars[url<?= $Table ?>]" type="text" class="inputLarge" value="<?= $ARR['url' . $Table] ?>">
							</td>
						</tr>
					</table>
				<? endif ?>

				<? if (!empty($APP_TABLE['hasColorScheme'])): ?>
					<table class="table_form    ">
						<td class="label    ">Couleur</td>
						<td class="     ">
							<input name="vars[color<?= $Table ?>]" type="color" class="inputSmall" value="<?= $ARR['color' . $Table] ?>">
						</td>
						<td class="label    ">Couleur fond</td>
						<td class="     ">
							<input name="vars[bgcolor<?= $Table ?>]" type="color" class="inputSmall" value="<?= $ARR['bgcolor' . $Table] ?>">
						</td>
					</table>
				<? endif ?>
			</div>
			<div>
				<div class="padding margin border4 ededed">
					<? foreach ($GRILLE_FK as $field):
						$id       = 'id' . $field['table_fk'];
						$nom      = 'nom' . ucfirst($field['table_fk']);
						$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
						?>
						<div class="label"><i class="fa fa-<?= $field['icon_fk'] ?>"></i> <?= ucfirst($field['table_fk']) ?></div>
						<div class="padding">
							<input datalist_input_name="vars[<?= $id ?>]" datalist_input_value="<?= $ARR[$id] ?>" datalist="app/app_select" populate name="vars[<?= $nom ?>]" paramName="search" vars="table=<?= $field['table_fk'] ?>" value="<?= $dsp_name ?>" type="text" class="inputMedium"/>
						</div>
					<? endforeach; ?></div>
			</div>
		</div>
		<div class="padding margin border4 ededed">
			<? foreach ($GRILLE as $field):
				$id       = 'id' . $field['table_grille'];
				$nom      = 'nom' . ucfirst($field['table_grille']);
				$arr      = $APP->plug($field['base_grille'], $field['table_grille'])->findOne([$field['idtable_grille'] => $ARR[$field['idtable_grille']]]);
				$dsp_name = $arr['nom' . ucfirst($field['table_grille'])];
				?>
				<div class="label"><i class="fa fa-<?= $field['icon_grille'] ?>"></i> <?= ucfirst($field['table_grille']) ?>
					<a onclick="act_chrome_gui('app/app/app_create_grille','table=<?= $table ?>&table_value=<?= $table_value ?>&table_grille=<?= $field['table_grille'] ?>')">ajouter</a>
				</div>
				<div class="padding">
				</div>
			<? endforeach; ?></div>
		<div class="buttonZone">
			<button type="button" class="trash_button left" onclick="<?= fonctionsJs::app_delete($table, $table_value) ?>">
				<?= idioma('Supprimer') ?>
			</button>
			<input class="valid_button" type="submit" value="<?= idioma('Valider') ?>">
			<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>">
		</div>
	</form>
</div>
<div class="titreFor">
	<?= idioma('Mise à jour') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div>
<style>
	.valid_button { border : 1px solid #0000FF; }
	.trash_button { border : 1px solid #B81900; }
	.trash_button:before {
		content     : '\f014';
		font-family : FontAwesome;
		color       : #B81900;
		margin      : 0 5px;
	}
</style>
