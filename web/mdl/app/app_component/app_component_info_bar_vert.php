<?php
	include_once($_SERVER['CONF_INC']);

	include_once($_SERVER['CONF_INC']);
	$table       = empty($_POST['table']) ? 'client' : $_POST['table'];
	$table_value = empty($_POST['table_value']) ? null : $_POST['table_value'];
	$APP         = new App($table);

	$final_rfk = [];

	$rgba_table = implode(',', hex2rgb($APP->colorAppscheme, 0.5));
	// echo implode(',', hex2rgb('#333333', 0.5));
?>
<div class="frmCol1 flex_v borderr app_component_info_bar_vert dark_1" style="width:40px;height:100%;background: -moz-linear-gradient(top, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 40%, rgba(<?= $rgba_table ?>) 100%);">
	<div class="flex_main">  </div>
	<div>
		<? if ($table_value) {
			$R_FK = $APP->get_reverse_grille_fk($table, $table_value);
			?>
			<? foreach ($R_FK as $arr_fk):
				$final_rfk[$arr_fk['scope']][] = $arr_fk;
			endforeach; ?>
			<div class="rgba_link square   "><?= skelMdl::cf_module('app/app_gui/app_gui_tile_click', ['table' => $table, 'table_value' => $table_value], $table_value); ?></div>
		<? } ?>
		<div class="rgba_link     "><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', ['table' => $table]) ?></div>
		<? if (!$table_value) { ?>
			<div class="rgba_link"><?= skelMdl::cf_module('app/app_gui/app_gui_tile_table_click', ['table' => $table], $table); ?></div>
		<? } ?>
		<div class="applink applinkblock  aignright" style="border-right:5px solid <?=$APP->colorAppscheme?>">
			<? foreach ($final_rfk as $key => $arr_final):
?>
				<div class="padding alignright"><br> </div>
				<?
				foreach ($arr_final as $arr_fk):
					$tmp_table = $arr_fk['codeAppscheme'];
					$APP_TMP          = new App($tmp_table);
					$vars_rfk['vars'] = ['id' . $table => $table_value];
					if (!droit_table($_SESSION['idagent'], 'C', $arr_fk['table'])) continue;
					$ct        = $APP_TMP->has_field('dateDebut') . '  ' . $APP_TMP->has_field_fk('agent');
					?>
					<a style="vertical-align: middle" title="<?= $arr_fk['nomAppscheme']?>" onclick="<?= fonctionsJs::app_create($arr_fk['table'], ['vars' => ['id' . $table => $table_value, 'idagent' => $_SESSION['idagent']]]) ?>">
						&nbsp;<i style="vertical-align: middle" class="fa fa-<?= $arr_fk['icon'] ?>    "></i> créer <?= $arr_fk['nomAppscheme']?>
					</a>
				<? endforeach; ?>

			<? endforeach; ?>
			<br>
		</div>
	</div>
</div>
