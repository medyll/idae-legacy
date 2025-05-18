<?php
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_fiche_micro.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_fiche_micro', $_POST);

		return;
	}

	// CONVERSION
	if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history' || $table == 'agent_table'):
		$APP         = new App($table);
		$ARR         = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
		$table       = $ARR['code' . $Table];
		$table_value = (int)$ARR['valeur' . $Table];
		$Table       = ucfirst($table);
	endif;

	$APP    = new App($table);

	$APPOBJ = $APP->appobj($table_value, $vars);

	$ARR_GROUP_FIELD = $APP->get_field_group_list(['$nin'=>['','']],['in_mini_fiche'=>1]);
	$icon            = $APP->iconAppscheme;
	$GRILLE_FK       = $APP->get_grille_fk();

	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
?>
<div ondblclick="<?= fonctionsJs::app_fiche($table, $table_value) ?>" class="blanc" style="overflow:hidden;height:100%;" main_auto_tree data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" data-table="<?= $table ?>"
     data-table_value="<?= $table_value ?>"  >
	<div class="flex_h flex_main marginb edededhover cursor relative">
		<div class="aligncenter padding flex_v  " style="height:100%;width:46px;">

			<a class="padding_more" data-menu="data-menu" data-clone="true"   ><i class="fa fa-navicon"></i></a>
			<div class=" " style="display:none;bottom:-1px;" >
				<div class="blanc">
					<div   class= "boxshadow   " act_defer mdl="app/app/app_fiche_thumb_full"
					     vars="table=<?= $table ?>&table_value=<?= $table_value ?>"
					     value="<?= $table_value ?>"></div>

				</div>
			</div>
			<? if (!empty($APPOBJ->APP_TABLE['hasImageScheme'])):
				$size = empty($APPOBJ->APP_TABLE['hasImagesquareScheme']) ? empty($APPOBJ->APP_TABLE['hasImagetinyScheme']) ? 'small' : 'tiny' : 'square';
				?>
				<div class="aligncenter">
					<div class="inline  boxshadow margin aligncenter" act_defer mdl="app/app_img/image_dyn"
					     vars="table=<?= $table ?>&table_value=<?= $table_value ?>&codeTailleImage=square&show=thumb" scope="app_img"
					     value="<?= $table ?>-square-<?= $table_value ?>"></div>
				</div>
			<? else: ?>
				<div class="aligncenter">
					<i style="color:<?= $APP->colorAppscheme ?>" class="padding fa fa-<?= $icon ?> fa-2x"></i>
				</div>
			<? endif; ?>
			<div style="color:<?= $ARR['color' . $Table . '_statut'] ?>">
				<?= $APP->draw_field(['field_name_raw' => 'icon', 'field_name' => 'icon' . $Table . '_statut', 'table' => $table, 'field_value' => $ARR['icon' . $Table . '_statut']]) ?>
			</div>
		</div>
		<div class="flex_main borderl " style="overflow: hidden;">
			<div class="ms-font-m padding_more">
				<div class="  bold"><?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?></div>
			</div>
			<div class="bordert "  >
				<div class="flex_v ">
					<? foreach ($ARR_GROUP_FIELD as $key => $val) {
						$arrg = $val['group'];
						$arrf = $val['field'];
						?>
						<div style="order:<?= $arrg['ordreAppscheme_field_group'] ?>">
							<div class="flex_h flex_wrap flex_align_middle">
								<?
									$totitre = '';
									foreach ($arrf as $keyf => $valf) {

										$value = strip_tags($ARR[$valf['codeAppscheme_field'] . $Table]);
										if (empty($value)) continue;
										if (empty($totitre)) {
											$totitre = true;
											?>
											<div class="borderb none flex_main padding" style="min-width:100%;">- <?= $arrg['nomAppscheme_field_group'] ?></div>
											<?
										}
										?>
										<div title=" <?= ucfirst($valf['nomAppscheme_field']) ?>" class="flex_h flex_align_middle">
											<div class=" ">
												<i class="textgris padding fa fa-<?= $valf['iconAppscheme_field'] ?>"></i>
											</div>
											<div class="flex_main padding"><span
													class="bold"><?= $valf['nomAppscheme_field'] ?> : </span> <?= $APP->draw_field(['field_name_raw' => $valf['codeAppscheme_field'], 'table' => $table, 'field_value' => $value]) ?></div>
										</div>
									<? } ?>
							</div>
						</div>
					<? } ?>
				</div>
				<div class="padding bordert ededed none">
					<?

						foreach ($GRILLE_FK as $field):
							$id_fk    = $field['idtable_fk'];
							$APPFK = new APP($field['codeAppscheme']);
							//
							$arrq     = $APPFK->findOne([$field['idtable_fk'] => (int)$ARR[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
							$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
							//
							if (!empty($dsp_name)) {

								?>
								<div class='padding flex_h ' title="<?= strtolower($dsp_name) ?>">
									<div class="ellipsis" style="min-width:70px;overflow:hidden;">
										<i class='textgris   fa fa-<?= $field['iconAppscheme'] ?>'></i>
										<?= $field['nomAppscheme'] . ' : ' ?></div>
									<div class="flex_main alignright"><span class="bold ellipsis"><?= strtolower($dsp_name) ?></span></div>
								</div>
							<? }
						endforeach;
					?>
				</div>
			</div>

		</div>
	</div>
</div>