<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	//

	$APP = new App($table);
	//
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
?>
<div style="margin-bottom:2em;" main_auto_tree data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
	<div class="relative">
		<div class=" applink   ">
			<div class="alignright borderb">
				<a onclick="<?=fonctionsJs::app_update($table,$table_value)?>">Modifier</a>
			</div>
			<? foreach ($ARR_GROUP_FIELD as $key => $val) {
				$arrg = $val['group'];
				$arrf = $val['field'];
				?>
				<div class="flex_h flex_wrap flex_align_middle">
						<? foreach ($arrf as $keyf => $valf) {
							//	if($arrg['codeAppscheme_field_group']!='identification' && $arrg['codeAppscheme_field_group']!='codification'&& $arrg['codeAppscheme_field_group']!='valeur')continue;
							$value = $ARR[$valf['codeAppscheme_field'] . $Table];
							// if (empty($value)) continue;
							?>
							<div class="relative" style="min-width:50%;">
							<table class="tabletop">
							<tr onclick="act_chrome_gui('app/app_update_field','table=newsletter_item&table_value=<?=$table_value?>&field_name_raw=<?=$valf['codeAppscheme_field']?>')" title=" <?= ucfirst($valf['nomAppscheme_field']) ?>" class="cursor" style="min-width:50%;">
								<td>
									<i class="textbold padding fa fa-<?= $valf['iconAppscheme_field'] ?>"></i>
								</td>
								<td>
									<div class="flex_h flex_wrap flex_align_top">
										<div  ><div class="ellipsis"> <?= $valf['nomAppscheme_field'] ?> :&nbsp;&nbsp;</div></div>
										<div class="flex_main">
											<div class="ellipsis">
												<?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $value]) ?>
											</div>
										</div>
									</div>


								</td>
							</tr>
					</table></div>
						<? } ?>
				</div>
			<? } ?>
		</div>
	</div>
</div>