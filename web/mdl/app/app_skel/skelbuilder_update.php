<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP               = new App('appscheme');
	$APPHASF           = new App('appscheme_has_field');
	$APPSC_FIELD       = new App('appscheme_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');
	$APPSC_HAS_FIELD   = new App('appscheme_has_field');

	$idappscheme = $table_value = (int)$_POST['idappscheme'];
	$arr         = $APP->findOne(['idappscheme' => $idappscheme]);
	$table       = $arr['codeAppscheme'];
	$Table       = ucfirst($table);

	$types = $APP->app_default_fields;

	$rsG = $APPSC_FIELD_GROUP->find()->sort(['group_ordre' => 1]);

?>
<form action="mdl/app/app_skel/actions.php" onSubmit="ajaxFormValidation(this);return false" style="position:static;height:100%;">
	<div class="flex_v blanc">
		<div class="titre_entete">
			<?= $arr['mainscope_app'] ?> <i class="fa fa-caret-right"></i> <?= $arr['nomAppscheme_base'] ?>
			<i class="fa fa-caret-right"></i> <?= $arr['nomAppscheme'] ?>
		</div>
		<div class="applink titre_entete_menu">
			<a class="blanc border4"><i class="fa fa-navicon"></i></a>
			<a onclick="<?= fonctionsJs::app_update('appscheme', $table_value) ?>"><i class="fa fa-cog"></i>
				collection <?= $arr['collection'] ?></a>
			<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
				<i class="fa fa-sitemap"></i> dépendances (<?= sizeof($arr['grilleFK']) ?>)
			</a>
			<a onclick="ajaxMdl('app/app_skel/skelbuilder_grille','Fiche FK app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
				<i class="fa fa-laptop"></i> grilles (<?= sizeof($arr['grille']) ?>)
			</a>
			<a onclick="ajaxMdl('app/app_skel/skelbuilder_update_model','Choix des champs de table','idappscheme=<?= $arr['idappscheme'] ?>')">
				<i class="fa fa-table"></i> personnalisé
			</a>
			<a onclick="<?= fonctionsJs::app_sort('appscheme_has_field', '', ['vars' => ['idappscheme' => $idappscheme]]) ?>" class="cancelClose">
				<i class="fa fa-sort"></i>&nbsp;<?= idioma('Tri primaire') ?>
			</a>
			<a onclick="<?= fonctionsJs::app_sort('appscheme_has_field', '', ['vars' => ['idappscheme' => $idappscheme]]) ?>" class="cancelClose">
				<i class="fa fa-sort"></i>&nbsp;<?= idioma('Ordonner les champs') ?>
			</a>
		</div>
		<div class="flex_main" style="overflow:hidden;">
			<div class="flex_h flex_align_top" style="height:100%;overflow:auto;">
				<div style="position:sticky;top:2em; width:150px;">
					<div class="aligncenter borderr padding">
						<?=idioma('Enregistrer les changements')?><br><br>
						<input class="ms-Button" type="submit" value="Valider"/>
					</div>
				</div>
				<div class="flex_main padding"  app_gui_explorer>
					<div style="overflow:hidden;" id=" " expl_left_zone main_auto_tree>
						<input type="hidden" value="updateForm" name="F_action">
						<input type="hidden" value="*" name="reloadModule[app/app_scheme/app_scheme_has_field_update]">
						<input type="hidden" name="idappscheme" value="<?= $idappscheme ?>"/>
						<br>
						<? while ($arrg = $rsG->getNext()) {
							$rsF = $APP->plug('sitebase_app', 'appscheme_field')->find(['idappscheme_field_group' => $arrg['idappscheme_field_group']])->sort(['ordreAppscheme_field_group' => 1]);
							?>
							<div auto_tree style="position:relative;">
								<div class="trait"><?= ucfirst($arrg['nomAppscheme_field_group']) ?></div>
							</div>
							<div class="autoBlock">
								<div class="ms-Table" data-append="true" dropzone data-vars="vars[idappscheme_field_group]=<?= $arrg['idappscheme_field_group'] ?>">
									<div class="ms-Table-row">
										<div class="ms-Table-cell" style="width:150px;"><?= idioma('champ'); ?> </div>
										<div class="ms-Table-cell" style="width:150px;"><?= idioma('inclus'); ?></div>
										<div class="ms-Table-cell" style="width:150px;"><?= idioma('obligatoire') ?></div>
										<div class="ms-Table-cell  " style="width:100px;"><?= idioma('Trier par defaut') ?></div>
										<div class="ms-Table-cell"> <?= idioma('Sens du tri') ?></div>
									</div>
									<? while ($arrf = $rsF->getNext()) {
										$Key = ucfirst($arrf['codeAppscheme_field']);
										// dans appscheme_has_field ?
										$arrSF = $APPHASF->findOne(['idappscheme' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
									//	vardump($arrSF);
										$has      = !empty($arrSF['idappscheme_has_field']);
										$required = !empty($arrSF['required']);
										$sort     =  ($arr['sortFieldId'] == $arrf['idappscheme_field']) ;
										?>
										<div class="ms-Table-row" draggable="true" data-vars="table=appscheme_field&table_value=<?= $arrf['idappscheme_field'] ?>">
											<div class="ms-Table-cell" style="width:150px;"><?= $arrf['nomAppscheme_field'] ?></div>
											<div class="ms-Table-cell" style="width:150px;"><?= chkSch($arrf['idappscheme_field'], $has, 'vars_has_field'); ?></div>
											<div class="ms-Table-cell"><?= chkSch($arrf['idappscheme_field'], $required, 'vars_has_required_field'); ?></div>
											<div class="ms-Table-cell">
												<input <?=checked($sort)?> type="radio" name="vars_sort" value="<?= $arrf['idappscheme_field'] ?>">
											</div>
											<div class="ms-Table-cell">
												<label><i class="fa fa-caret-down"></i>
													<input <?=checked($sort && ($arr['sortFieldOrder']==1))?> type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="1">
												</label>
												<label><i class="fa fa-caret-up"></i>
													<input <?=checked($sort && ($arr['sortFieldOrder']==-1))?> type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="-1">
												</label>
											</div>
										</div>
									<? } ?>
								</div>
								<table class="table_info" data-append="true" dropzone data-vars="vars[idappscheme_field_group]=<?= $arrg['idappscheme_field_group'] ?>">
									<? while ($arrf = $rsF->getNext()) {
										$Key = ucfirst($arrf['codeAppscheme_field']);
										// dans appscheme_has_field ?
										$arrSF    = $APPHASF->findOne(['idappscheme' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
										$has      = !empty($arrSF['idappscheme_has_field']);
										$required = !empty($arrSF['required']);
										$sort     = !empty($arrSF['sort']);
										?>
										<tr draggable="true" data-vars="table=appscheme_field&table_value=<?= $arrf['idappscheme_field'] ?>">
											<td><?= $arrf['nomAppscheme_field'] ?></td>
											<td><?= chkSch($arrf['idappscheme_field'], $has, 'vars_has_field'); ?></td>
											<!--'has' . $Key . 'Scheme'-->
											<td class="borderl alignright"><?= idioma('obligatoire') ?></td>
											<td><?= chkSch($arrf['idappscheme_field'], $required, 'vars_has_required_field'); ?></td>
											<td class="borderl"> Trier par</td>
											<td>
												<input type="radio" name="vars_sort" value="<?= $arrf['idappscheme_field'] ?>">
											</td>
											<td>
												<input type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="-1">
												<input type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="1">
											</td>
										</tr>
									<? } ?>
								</table>
							</div>
						<? } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<style>
	.label {
		width: 80px;
		vertical-align: middle;
		line-height: 20px;
		text-overflow: ellipsis;
	}
</style>

