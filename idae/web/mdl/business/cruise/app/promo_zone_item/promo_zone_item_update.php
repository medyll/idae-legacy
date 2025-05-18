<?
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];

	$APP    = new App($table);
	$APPOBJ = $APP->appobj($table_value);
	$ARR    = $APPOBJ->ARR;
	//
	$exp['table']       = $table;
	$exp['table_value'] = $table_value;
?>
<div class="flex_v">
	<div><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
	<div class="ededed" style="max-width:700px;overflow:auto;">
		<div class="inline  boxshadow margin aligncenter" act_defer mdl="app/app_img/image_dyn"
		     vars="table=<?= $table ?>&table_value=<?= $table_value ?>&codeTailleImage=large" scope="app_img"
		     value="<?= $table ?>-large-<?= $table_value ?>"></div>
		<? foreach ($APPOBJ->ARR_GROUP_FIELD as $key => $val) {
			$arrg = $val['group'];
			$arrf = $val['field'];
			?>
			<div class="margin ededed flex_h flex_wrap flex_align_stretch">
				<? foreach ($arrf as $keyf => $valf) {
					$field_name     = $exp['field_name'] = $valf['codeAppscheme_field'] . $Table;
					$field_name_raw = $exp['field_name_raw'] = $valf['codeAppscheme_field'];

					?>
					<div class="padding margin flex_main blanc border4">
						<div>
							<div class="padding bold borderb">
								<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($valf['nomAppscheme_field'])) ?>
							</div>
							<div class="padding ">
								<?= skelMdl::cf_module('app/app_field_update', $exp) ?>
							</div>
						</div>
					</div>
				<? } ?>
			</div>
		<? } ?>
	</div>
</div>