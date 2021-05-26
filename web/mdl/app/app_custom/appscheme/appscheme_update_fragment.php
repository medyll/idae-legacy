<?
	include_once($_SERVER['CONF_INC']);
	global $buildArr;
	global $IMG_SIZE_ARR;

	$table      = $_POST['table'];
	$APP         = new App($table);
	$idappscheme = (int)$_POST['table_value'];
	$arr         = $APP->findOne(['idappscheme' => $idappscheme]);
	$table       = $arr['codeAppscheme'];
	$Table       = ucfirst($table);
	//
	$types = $APP->app_default_fields;

	$rsG = $APP->plug('sitebase_app', 'appscheme_field_group')->find()->sort(['group_ordre' => 1]);

?>
<div class="" style="width:250px;">
	<input type="hidden" value="*" name="reloadModule[app/app_skel/skelbuilder_liste_inner]">
	<div class="flex_h">
		<div class="flex_main     borderr  ">
			<div class="padding borderb"><i class="fa fa-wrench"></i><?=idioma('configuration')?></div>
			<div class="padding ededed">
				<div class="blanc">
					<table class="table_form">
						<tr>
							<td>table de type</td>
							<td><?= chkSch('isTypeScheme', $arr['isTypeScheme']); ?></td>
						</tr>
						<tr>
							<td>table de statut</td>
							<td><?= chkSch('isStatutScheme', $arr['isStatutScheme']); ?></td>
						</tr>
						<tr>
							<td>table de lignes</td>
							<td><?= chkSch('isLigneScheme', $arr['isLigneScheme']); ?></td>
						</tr>
					</table>
				</div>
			</div>
			<table class="table_form">
				<tr>
					<td>Type</td>
					<td><?= chkSch('hasTypeScheme', $arr['hasTypeScheme']); ?></td>
				</tr>
				<tr>
					<td>Statut</td>
					<td><?= chkSch('hasStatutScheme', $arr['hasStatutScheme']); ?></td>
				</tr>
				<tr class="bordert">
					<td>Catégorie</td>
					<td><?= chkSch('hasCategorieScheme', $arr['hasCategorieScheme']); ?></td>
				</tr>
				<tr class="borderb">
					<td>Groupe</td>
					<td><?= chkSch('hasGroupScheme', $arr['hasGroupScheme']); ?></td>
				</tr>
				<tr>
					<td>Lignes</td>
					<td><?= chkSch('hasLigneScheme', $arr['hasLigneScheme']); ?></td>
				</tr>
			</table>
			<br>
			<table class="table_form">
				<tr>
					<td>Image</td>
					<td><?= chkSch('hasImageScheme', $arr['hasImageScheme']); ?></td>
				</tr>
				<? foreach ($IMG_SIZE_ARR as $key => $value) { ?>
					<tr>
						<td><?=$key?> <?=$value[0].' '.$value[1]?></td>
						<td><?= chkSch('hasImage'.$key.'Scheme', $arr['hasImage'.$key.'Scheme']); ?></td>
					</tr>
				<?  } ?>
			</table>
		</div>
	</div>
</div>

