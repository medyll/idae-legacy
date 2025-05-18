<?php
	include_once($_SERVER['CONF_INC']);

	include_once($_SERVER['CONF_INC']);
	$table       = empty($_POST['table']) ? 'client' : $_POST['table'];
	$table_value = empty($_POST['table_value']) ? null : $_POST['table_value'];
	$APP         = new App($table);
	$APPOBJ      = $APP->appobj($table_value);

	$final_rfk = [];

	$rgba_table = implode(',', hex2rgb($APP->colorAppscheme, 0.5));
	// echo implode(',', hex2rgb('#333333', 0.5));
?>
<div class="flex_v borderr app_component_info_bar_vert dark_1 boxshadowr" style="min-width:200px;border-right:5px solid <?= $APP->colorAppscheme ?>;height:100%;">
	<div class=" dark_3 square padding_more alignright  "><?= skelMdl::cf_module('app/app_gui/app_gui_tile_click', ['table' => $table, 'table_value' => $table_value], $table_value); ?></div>
	<div class=" titre square padding_more alignright  ">
		<?= $APP->nomAppscheme ?> <?= $table_value ?>
	</div>
	<div class=" dark_2 square padding_more alignright  ">
		<a onclick="<?= fonctionsJs::app_create('agent_note', ['add_table' => $table, 'add_table_value' => $table_value, 'vars' => ['id' . $table => $table_value, 'idagent' => $_SESSION['idagent']]]) ?>">
			note <i class="fa fa-sticky-note-o"></i>
		</a>
	</div>
	<div class="flex_main">
		<div class="    aligncenter" style=" overflow:hidden;">
			<? if (!empty($APPOBJ->APP_TABLE['hasImageScheme'])):
				$size = empty($APPOBJ->APP_TABLE['hasImagesquareScheme']) ? empty($APPOBJ->APP_TABLE['hasImagetinyScheme']) ? 'small' : 'tiny' : 'square';
				?>
				<div class="aligncenter  ">
					<div class="inline  boxshadow   aligncenter" act_defer mdl="app/app_img/image_dyn"
					     vars="table=<?= $table ?>&table_value=<?= $table_value ?>&codeTailleImage=small&show=small" scope="app_img"
					     value="<?= $table ?>-small-<?= $table_value ?>"></div>
				</div>
			<? endif; ?>
		</div>
	</div>
	<div>
		<? if ($table_value) {
			$R_FK = $APP->get_reverse_grille_fk($table, $table_value);
			?>
			<? foreach ($R_FK as $arr_fk):
				$final_rfk[$arr_fk['scope']][] = $arr_fk;
			endforeach; ?>
		<? } ?>
		<? if (!$table_value) { ?>
			<div class=" "><?= skelMdl::cf_module('app/app_gui/app_gui_tile_table_click', ['table' => $table], $table); ?></div>
		<? } ?>
		<div class="applink applinkblock  aignright" style="">
			<? foreach ($final_rfk as $key => $arr_final):
				?>
				<div class="padding alignright">
					<br>
				</div>
				<?
				foreach ($arr_final as $arr_fk):
					$tmp_table = $arr_fk['codeAppscheme'];
					$APP_TMP          = new App($tmp_table);
					$vars_rfk['vars'] = ['id' . $table => $table_value];
					if (!droit_table($_SESSION['idagent'], 'C', $arr_fk['table'])) continue;
					$ct        = $APP_TMP->has_field('dateDebut') . '  ' . $APP_TMP->has_field_fk('agent');
					?>
					<a style="vertical-align: middle" title="<?= $arr_fk['nomAppscheme'] ?>" onclick="<?= fonctionsJs::app_create($arr_fk['table'], ['vars' => ['id' . $table => $table_value, 'idagent' => $_SESSION['idagent']]]) ?>">
						&nbsp;<i style="vertical-align: middle" class="fa fa-<?=$APP_TMP->iconAppscheme ?>    "></i> créer <?= $arr_fk['nomAppscheme'] ?>
					</a>
				<? endforeach; ?>
			<? endforeach; ?>
			<br>
		</div>
		<div class="alignright" style=""><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', ['table' => $table]) ?></div>
	</div>
</div>
