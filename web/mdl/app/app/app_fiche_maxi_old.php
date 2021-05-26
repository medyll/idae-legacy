<?php
	include_once($_SERVER['CONF_INC']);

	//  vardump($_POST);
	//
	// ESPACE
	//
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	/*if (file_exists(APPMDL . '/app/app_custom/' . $table . '/' . $table . '_espace.php')) {
		echo skelMdl::cf_module('/app/app_custom/' . $table . '/' . $table . '_espace', $_POST);
		return;
	}*/
	$APP = new App($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	$R_FK            = $APP->get_reverse_grille_fk($table, $table_value);
	//
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE_MAXI');
	//
	$zouzou = uniqid($table);
	$zou    = uniqid($table);

	$setting_home_name = 'home_fiche_maxi';
	$setting_home_save = $setting_home_name . '_' . $table;
	$mdl1              = "app/app/app_fiche_maxi_liste";
	$mdl2              = "app/app/app_fiche_maxi_liste_rfk";
	$mdl3              = "app/app_liste/app_liste";
	$mdl_load          = !empty($APP->get_settings($_SESSION['idagent'], $setting_home_name, $table)) ? $APP->get_settings($_SESSION['idagent'], $setting_home_name, $table) : $mdl1;
	$mdl_load          = !empty($_POST['mdl_load']) ? $mdl3 : $mdl_load;
	$mdl1_css          = ($mdl1 == $mdl_load) ? 'active' : '';
	$mdl2_css          = ($mdl2 == $mdl_load) ? 'active' : '';

	if (!empty($_POST['mdl_load'])) {
		$css_entete = 'none';
		$http_post  = 'table=' . $_POST['mdl_load'] . "&vars[id$table]=$table_value";
	} else {
		$css_entete = '';
		$http_post  = http_build_query($_POST);
	}
	$rgba_table = implode(',', hex2rgb($APP->colorAppscheme, 0.5));
?>
<div class="flex_h" style="width:100%;height:100%;">
	<?= skelMdl::cf_module('app/app_component/app_component_info_bar_vert', $_POST+['moduleTag'=>'none'], $table); ?>

	<div class="flex_main flex_v blanc" style="height: 100%;overflow:hidden;">
		<div class="relative">
			<div id="fme<?= $table . $table_value ?>"
			     style="display:<?= $css_entete ?>;z-index:100;"><?= skelMdl::cf_module('app/app/app_fiche_maxi_entete', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
			<div style="display:none" class="please_hide"><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche_maxi', 'table' => $table, 'table_value' => $table_value]) ?></div>
		</div>
		<div class="flex_h">

			<div class="toggler app_onglet_push">
				<a class="autoToggle borderr toggler_visible" onclick="$('fme<?= $table . $table_value ?>').unToggleContent()">
					<i class="fa fa-window-maximize"></i>
				</a>
				<a class="autoToggle borderr toggler_visible" onclick="$('fme<?= $table . $table_value ?>').toggleContent()">
					<i class="fa fa-window-restore"></i>
				</a>
			</div>
			<div class="app_onglet toggler flex_h flex_main">
				<a class="autoToggle" act_target="onglet_more_<?= $zou ?>">
					<i class="fa fa-link"></i>
				</a>
				<a class="autoToggle <?= $mdl1_css ?>" act_target="onglet_<?= $zou ?>" mdl="<?= $mdl1 ?>" vars="<?= http_build_query($_POST) ?>" onclick="save_settings('<?= $setting_home_save ?>','<?= $mdl1 ?>');">
					<i class="fa fa-home"></i>
				</a>
				<a class="autoToggle <?= $mdl2_css ?>" act_target="onglet_<?= $zou ?>" mdl="<?= $mdl2 ?>" vars="<?= http_build_query($_POST) ?>" onclick="save_settings('<?= $setting_home_save ?>','<?= $mdl2 ?>');"><i
						class="fa fa-list-ul"></i>
				</a>
				<div style="width:50px"></div>
				<? if (sizeof($R_FK) != 0): ?>
					<? foreach ($R_FK as $arr_fk):
						$value_rfk               = $arr_fk['table_value'];
						$table_rfk               = $arr_fk['table'];
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $table_rfk;
						$vars_rfk['table_value'] = $value_rfk;
						$count                   = $arr_fk['count'];
						$css_active              = ($_POST['mdl_load'] == $table_rfk) ? 'active' : '';
						if (empty($count)) continue;
						?>
						<a act_target="onglet_<?= $zou ?>" mdl="app/app_liste/app_liste" vars="table=<?= $table_rfk ?>&vars[<?= 'id' . $table ?>]=<?= $table_value ?>" class="autoToggle <?= $css_active ?>"><?= $arr_fk['nomAppscheme'] ?></a>
					<? endforeach; ?>
				<? endif; ?>
				<? if (!empty($APP_TABLE['hasImageScheme'])) { ?>
					<a act_target="onglet_<?= $zou ?>" mdl="app/app_img/image_app_liste_img" vars="table=<?= $table ?>&table_value=<?= $table_value ?>" class="autoToggle">Images</a>
				<? } ?>
			</div>
		</div>
		<div id="<?= $zouzou ?>" class="flex_main flex_h" style="width: 100%;overflow-y:hidden;overflow-x: auto;">
			<div class="frmCol1">
				hh
			</div>
			<div style="position:sticky;left:0;z-index:1;max-width:200px;" class="ededed borderr avoid">
				<? foreach ($GRILLE_FK as $field):
					$id       = 'id' . $field;
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];

					if (!empty($ARR[$field['idtable_fk']])): ?>
						<div class="margin padding " act_defer mdl="app/app/app_fiche_thumb" vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"> &nbsp;</div>                <? endif;
				endforeach; ?>
			</div>
			<div id="onglet_<?= $zou ?>" data-act_target_toggle="true" vars="<?= $http_post ?>" act_defer mdl="<?= $mdl_load ?>" style="width:100%;">
			</div>
			<div id="onglet_more_<?= $zou ?>" data-act_target_toggle="true" class="flex_h" style="display:none;">
				<? foreach ($GRILLE_FK as $field):
					// continue;
					$id       = 'id' . $field;
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
					?><? if (!empty($ARR[$field['idtable_fk']])): ?>
					<div style="height:100%;" act_defer mdl="app/app/app_fiche_forward" vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"></div>        <? endif; ?><? endforeach; ?>
			</div>
		</div>
	</div>
</div>
<div id="<?= $zou ?>" class="flex_h blanc border4 boxshadow absolute" style="max-width:50%;height:100%;right:0;top:0;overflow:hidden;display:none;z-index:100;">
	<div class="applink applinkblock padding ededed">
		<a onclick="$('<?= $zou ?>').hide();" class="textrouge"><i class="fa fa-close"></i></a>
	</div>
	<div id="for_<?= $zou ?>" class="flex_main borderl" style="overflow:hidden;">
	</div>
</div>
<script>
	$ ('<?=$zouzou?>').on ('click', '[data-table][data-table_value][data-link]', function (event, node) {
		var table       = node.readAttribute ('data-table');
		var table_value = node.readAttribute ('data-table_value');
		$ ('<?=$zou?>').show ();
		$ ('for_<?=$zou?>').loadModule ('app/app/app_fiche_preview', 'table=' + table + '&table_value=' + table_value);
	});
	//
	$ ('<?=$zouzou?>').on ('click', '[data-link][data-table][data-vars]', function (event, node) {
		if ( node.readAttribute ('data-table_value') ) return;
		var table = node.readAttribute ('data-table');
		var vars  = node.readAttribute ('data-vars');
		$ ('<?=$zou?>').show ();
		$ ('for_<?=$zou?>').loadModule ('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// act_chrome_gui('app/app_liste/app_liste', 'table=' + table + '&' + vars);
		// alert('dre')
	})
</script>