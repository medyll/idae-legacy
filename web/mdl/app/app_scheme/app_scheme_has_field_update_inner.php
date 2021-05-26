<?
	include_once($_SERVER['CONF_INC']);
	$APP               = new App('appscheme');
	$APPHASF           = new App('appscheme_has_field');

	$idappscheme = $table_value = (int)$_POST['idappscheme'];
	$arr         = $APP->findOne(['idappscheme' => $idappscheme]);

?>
<div  >
	<form class="" action="<?= ACTIONMDL ?>app/app_scheme/actions.php" onSubmit="ajaxFormValidation(this);return false" style="position:relative;display:block;height:100%;">
		<input type="hidden" value="updateForm" name="F_action">
		<input type="hidden" value="*" name="reloadModule[app/app_scheme/app_scheme_has_field_update]">
		<input type="hidden" name="idappscheme" value="<?= $idappscheme ?>"/>
		<div class="flex_h flex_align_top" >
			<div style="position:sticky;top:2em; width:150px;">
				<div class="aligncenter borderr padding">
					<?= idioma('Enregistrer les changements') ?>
					<br>
					<br>
					<input class="ms-Button" type="submit" value="Valider"/>
				</div>
			</div>
			<div class="flex_main borderr" style="width:150px;height:100%;overflow:auto;">
				<div style="overflow:hidden;" expl_left_zone data-append="true" dropzone F_action="form_up" main_auto_tree>
					<table class="table_grille">
						<thead class="padding_more">
							<tr class=" padding_more   bold">
								<td class="borderr padding_more" style="width:150px;"><?= idioma('champ'); ?> </td>
								<td class="borderr padding_more" style="width:150px;"><?= idioma('inclus'); ?></td>
								<td class="borderr padding_more" style="width:150px;"><?= idioma('obligatoire') ?></td>
								<td class="padding_more" style="width:100px;"><?= idioma('Trier par defaut') ?></td>
								<td class="borderr padding_more" style="width:150px;"> <?= idioma('Sens du tri') ?></td>
								<td class="padding_more" style="width:100px;"><?= idioma('Tri  secondaire') ?></td>
								<td class=" padding_more" style="width:150px;"> <?= idioma('Sens secondaire') ?></td>
								<td class="padding_more"><i class="fa fa-vcard"></i> <?= idioma('Mini fiche') ?></td>
							</tr>
						</thead>
						<tbody>
							<?
								$rsF = $APPHASF->find(['idappscheme' => $idappscheme])->sort(['ordreAppscheme_has_field' => 1]);
								while ($arrf = $rsF->getNext()) {
									$Key = ucfirst($arrf['codeAppscheme_field']);
									// dans appscheme_has_field ?
									$arrSF = $APPHASF->findOne(['idappscheme' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
									//	vardump($arrSF);
									$has         = !empty($arrSF['idappscheme_has_field']);
									$required    = !empty($arrSF['required']);
									$sort        = ($arr['sortFieldId'] == $arrf['idappscheme_field']);
									$sort_second = ($arr['sortFieldSecondId'] == $arrf['idappscheme_field']);
									if (empty($arrSF['idappscheme_has_field'])) continue;
									?>
									<tr class="" draggable="true" data-vars="table=appscheme_field&table_value=<?= $arrf['idappscheme_field'] ?>">
										<td class="name_field borderr padding_more" style="width:150px;"><?= $arrf['nomAppscheme_field'] ?></td>
										<td class="borderr" style="width:150px;"><?= chkSch($arrf['idappscheme_field'], $has, 'vars_has_field'); ?></td>
										<td class="borderr"><?= chkSch($arrf['idappscheme_field'], $required, 'vars_has_required_field'); ?></td>
										<td class="aligncenter">
											<input <?= checked($sort) ?> type="radio" name="vars_sort" value="<?= $arrf['idappscheme_field'] ?>">
										</td>
										<td class="borderb borderr">
											<label class="padding alignmiddle"><i class="fa fa-caret-down"></i>
												<input <?= checked($sort && ($arr['sortFieldOrder'] == 1)) ?> type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="1">
											</label>
											<label class="padding alignmiddle"><i class="fa fa-caret-up"></i>
												<input <?= checked($sort && ($arr['sortFieldOrder'] == -1)) ?> type="radio" name="vars_sort_order[<?= $arrf['idappscheme_field'] ?>]" value="-1">
											</label>
										</td>
										<td class="aligncenter">
											<input <?= checked($sort_second) ?> type="radio" name="vars_sort_second" value="<?= $arrf['idappscheme_field'] ?>">
										</td>
										<td class="borderb">
											<label class="padding alignmiddle"><i class="fa fa-caret-down"></i>
												<input <?= checked($sort_second && ($arr['sortFieldSecondOrder'] == -1)) ?> type="radio" name="vars_sort_second_order[<?= $arrf['idappscheme_field'] ?>]" value="-1">
											</label>
											<label class="padding alignmiddle"><i class="fa fa-caret-up"></i>
												<input <?= checked($sort_second && ($arr['sortFieldSecondOrder'] == 1)) ?> type="radio" name="vars_sort_second_order[<?= $arrf['idappscheme_field'] ?>]" value="1">
											</label>
										</td>
										<td class="borderb">
											<label class="padding alignmiddle">
												Oui
												<input <?= checked(!empty($arrf['in_mini_fiche']) && ($arrf['in_mini_fiche'] == 1)) ?> type="radio" name="vars_in_mini_fiche[<?= $arrf['idappscheme_field'] ?>]" value="1">
											</label>
											<label class="padding alignmiddle">
												Non
												<input <?= checked(!empty($arrf['in_mini_fiche']) && ($arrf['in_mini_fiche'] == -1)) ?> type="radio" name="vars_in_mini_fiche[<?= $arrf['idappscheme_field'] ?>]" value="-1">
											</label>
										</td>
									</tr>
								<? } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>