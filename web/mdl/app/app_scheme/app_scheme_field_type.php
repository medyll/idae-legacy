<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$APP               = new App('appscheme');
	$APPHASF           = new App('appscheme_has_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');

	$idappscheme = $table_value = (int)$_POST['idappscheme'];
	$arr         = $APP->findOne(['idappscheme' => $idappscheme]);
	$table       = $arr['codeAppscheme'];

	$rsG = $APPSC_FIELD_GROUP->find()->sort(['ordreAppscheme_field_group' => 1]);

?>
<div style="overflow:auto;height:100%;width:250px;">
	<div class="padding_more aligncenter ededed borderb" style="position:sticky;top:0;z-index:10">
		<div class="padding aligcenter inline ededed">
			<input type="text" data-quickFind="" data-quickFind-where="only_name_field" data-quickFind-tag=".css_row" data-quickFind-spy="uyt">
		</div>
	</div>
	<div class="padding" id="only_name_field" expl_left_zone main_auto_tree>
		<? while ($arrg = $rsG->getNext()) {
			$rsF = $APP->plug('sitebase_app', 'appscheme_field')->find(['idappscheme_field_group' => $arrg['idappscheme_field_group']])->sort(['ordreAppscheme_field_group' => 1]);
			?>
			<div auto_tree style="position:relative;" class="borderb">
				<div class="padding_more borderb trait"><?= ucfirst($arrg['nomAppscheme_field_group']) ?></div>
			</div>
			<div class="retrait">
				<div class="" >
					<? while ($arrf = $rsF->getNext()) {

						$arrSF = $APPHASF->findOne(['idappscheme' => $idappscheme, 'idappscheme_field' => (int)$arrf['idappscheme_field']]);
						$has   = !empty($arrSF['idappscheme_has_field']);
						if (!empty($arrSF['idappscheme_has_field'])) continue;
						?>
						<div class=" " draggable="true" data-vars="table=appscheme_field&table_value=<?= $arrf['idappscheme_field'] ?>">
							<input type="hidden" name="vars_has_field[<?= $arrf['idappscheme_field'] ?>]" value="1">
							<div class="padding" style="width:150px;"><?= $arrf['nomAppscheme_field'] ?></div>
						</div>
					<? } ?>
				</div>
				<br>
			</div>
		<? } ?>
	</div>
</div>

