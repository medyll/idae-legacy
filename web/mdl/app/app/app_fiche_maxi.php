<?php
	include_once($_SERVER['CONF_INC']);

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
	$APP             = new App($table);
	$Idae            = new Idae($table);
	$APPSC_HAS_FIELD = new App('appscheme_has_field');
	$APPSC_FIELD     = new App('appscheme_field');
	//
	$idappscheme = (int)$APP->idappscheme;
	$APP_TABLE   = $APP->app_table_one;
	$R_FK        = $APP->get_reverse_grille_fk($table, $table_value);
	$ARR_DIST    = $APPSC_HAS_FIELD->distinct('appscheme_field', ['idappscheme' => $idappscheme], 346, 'no_full', 'idappscheme_field');
	$HAS_FIELD   = $APPSC_FIELD->distinct('appscheme_field', ['idappscheme_field' => ['$in' => $ARR_DIST]], 346, 'no_full', 'codeAppscheme_field');
	//

	$id = 'id' . $table;
	// LOG_CODE
	$APP->set_log($_SESSION['idagent'], $table, $table_value, 'FICHE_MAXI');
	//
	$zouzou = uniqid($table);

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
	<div><?= $Idae->module('app/app_component/app_component_fiche_info', $_POST + ['moduleTag' => 'none']); ?></div>
	<div class="flex_main flex_v blanc" style="height: 100%;overflow:hidden;">
		<div style="z-index:200;"><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche_maxi', 'table' => $table, 'table_value' => $table_value]) ?></div>
		<div class="flex_h" style="width:100%;">
			<div class="toolButton applink applinkbig  toggler flex_h flex_main borderb">
				<a class="autoToggle <?= $mdl1_css ?> boxshadowr" act_target="<?= $zouzou ?>" mdl="app/app/app_fiche_maxi_home" vars="<?= http_build_query($_POST) ?>" onclick="save_settings('<?= $setting_home_save ?>','<?= $mdl1 ?>');">
					<i class="fa fa-home"></i>
				</a><? if (sizeof($R_FK) != 0):
					foreach ($R_FK as $arr_fk):
						$value_rfk               = $arr_fk['table_value'];
						$table_rfk               = $arr_fk['table'];
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $table_rfk;
						$vars_rfk['table_value'] = $value_rfk;
						$count                   = $arr_fk['count'];
						$css_active              = ($_POST['mdl_load'] == $table_rfk) ? 'active' : '';
						if (empty($count)) {
							continue;
						}
						?>
					<a act_target="<?= $zouzou ?>" mdl="app/app_liste/app_liste" vars="table=<?= $table_rfk ?>&vars[<?= 'id' . $table ?>]=<?= $table_value ?>&show_search=true" class="autoToggle <?= $css_active ?>"><i class="fa fa-<?= $arr_fk['iconAppscheme'] ?>" style="color:<?= $arr_fk['colorAppscheme'] ?>"></i><?= $arr_fk['nomAppscheme'] ?>
						</a><? endforeach; ?>
				<a act_target="<?= $zouzou ?>" mdl="app/app/app_fiche_maxi_liste" vars="<?= http_build_query($_POST) ?>&model=defaultModel" class="autoToggle <?= $css_active ?>"> Voir
				                                                                                                                                                                   tout</a><? endif; ?>
				<? if (!empty($APP_TABLE['hasImageScheme'])) { ?>
					<a act_target="<?= $zouzou ?>" mdl="app/app_img/image_app_liste_img" vars="table=<?= $table ?>&table_value=<?= $table_value ?>" class="autoToggle">Images</a>
				<? } ?>
				<? if (in_array('gpsData', $HAS_FIELD)): ?>
					<a onclick="<?= fonctionsJs::app_mdl('app/app_custom/app_custom_map', ['table' => $table, 'table_value' => $table_value], '') ?>">
						<i class="fa fa-map-pin"></i>
						&nbsp;
						<?= idioma('Localisation') ?>
					</a>
				<? endif; ?>
				<? if ($table=='secteur'): ?>
					<a onclick="<?= fonctionsJs::app_mdl('app/app_custom/app_custom_map_zone', ['table' => $table, 'table_value' => $table_value], '') ?>">
						<i class="fa fa-map-pin"></i>
						&nbsp;
						<?= idioma('Localisation zone') ?>
					</a>
				<? endif; ?>
			</div>
		</div>
		<div id="<?= $zouzou ?>" class="flex_main" style="height:100%;width: 100%;overflow:hidden;">
			<div act_defer mdl="app/app/app_fiche_maxi_home" vars="<?= http_build_query($_POST) ?>" style="height:100%;"></div>
		</div>
	</div>
</div>