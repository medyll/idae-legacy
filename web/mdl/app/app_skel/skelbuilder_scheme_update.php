<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP = new App();
	$idappscheme = (int)$_POST['idappscheme'];
	$arr = $APP->plug('sitebase_app', 'appscheme')->findOne(['idappscheme' => $idappscheme]);
	$table = $arr['collection'];
	$Table = ucfirst($table);

	$types = $APP->app_default_fields;

	$rsG = $APP->plug('sitebase_app', 'appscheme_field_group')->find()->sort(['group_ordre' => 1]);

?>

<div style="width:750px;">
	<div class="titre_entete">
		<?= $arr['base'] . ' ' . $arr['collection'] ?>
	</div>
	<form class="Form" action="mdl/app/app_skel/actions.php" onSubmit="ajaxFormValidation(this);return false" auto_close="auto_close">
		<div class="flex_main padding" style="overflow:auto;">
			<div style="overflow:hidden;" id=" ">
				<input type="hidden" value="appscheme_update" name="F_action">
				<input type="hidden" value="*" name="reloadModule[app/app_skel/skelbuilder_liste_inner]">
				<input type="hidden" name="idappscheme" value="<?= $idappscheme ?>"/>

				<div class="flex_h">
					<div class="flex_main padding">
						<div class="ms-TextField">
							<label class="ms-Label">db:</label>
							<input class="ms-TextField-field" required type="text" name="vars[base]" value="<?= $arr['base'] ?>">
						</div>
						<div class="ms-TextField">
							<label class="ms-Label">Collection données:</label>
							<input class="ms-TextField-field" required type="text" name="vars[collection]" value="<?= $arr['collection'] ?>">
						</div>
						<div class="ms-TextField">
							<label class="ms-Label">mainscope_app:</label>
							<input class="ms-TextField-field" required type="text" name="vars[mainscope_app]" value="<?= $arr['mainscope_app'] ?>">
						</div>
						<div class="ms-TextField">
							<label class="ms-Label">appscheme_scope:</label>
							<input class="ms-TextField-field" required type="text" name="vars[appscheme_scope]" value="<?= $arr['appscheme_scope'] ?>">
						</div>
						<div class="ms-TextField">
							<label class="ms-Label">Icone:</label>
							<input class="ms-TextField-field" required type="text" name="vars[icon]" value="<?= $arr['icon'] ?>">
						</div>
					</div>
					<div class="flex_main padding ededed borderl retrait">
						<div  >
							<label>Type</label><br>
							<?= chkSch('hasTypeScheme', $arr['hasTypeScheme']); ?>
						</div>
						<br>

						<div >
							<label>Statut</label><br>
							<?= chkSch('hasStatutScheme', $arr['hasStatutScheme']); ?></div>
						<br>

						<div  >
							<label>Lignes</label><br>
							<?= chkSch('hasLigneScheme', $arr['hasLigneScheme']); ?></div>
						<br>

						<div >
							<label><?= $arr['collection'] ?>_booleens</label><br>
							<?= chkSch('hasBoolScheme', $arr['hasBoolScheme']); ?></div>
						<br>
						<div >
							<label>Image</label><br>
							<?= chkSch('hasImageScheme', $arr['hasImageScheme']); ?>
						</div>
						<div >
							<label>Grande image</label><br>
							<?= chkSch('hasImageBigScheme', $arr['hasImageBigScheme']); ?>
						</div>
						<br>
					</div>
				</div>

			</div>
		</div>

		<div class="buttonZone">
			<input type="button" value="Annuler" class="cancelClose"/>
			<input type="submit" value="Valider"/>
		</div>
	</form>
</div>

