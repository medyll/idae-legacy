<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']);

	unset($_POST['mdl'], $_POST['module']);
	$table       = $_POST['table'];
	$table_value = $_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$nbRows      = empty($_POST['nbRows']) ? 50 : $_POST['nbRows'];
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$datadsp     = empty($_POST['datadsp']) ? '' : $_POST['datadsp'];
	//
	$APP = new App($table);
	//
	$GRILLE_FK = $APP->get_grille_fk();
	//

	//
	$zone = 'app_liste_' . $table . implode('_', array_keys($vars)) . '_' . implode('_', array_values($vars));
	//
	//
	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
	//
	$settings_button_group = empty($groupBy) ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_group', $table) : $groupBy;

	$settings_button_dsp            = $APP->get_settings($_SESSION['idagent'], 'list_data_button_dsp', $table);
	$settings_button_dsp_mdl        = ($datadsp == '') ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_dsp_mdl', $table) : $datadsp;
	$settings_data_button_className = $APP->get_settings($_SESSION['idagent'], 'list_data_button_className', $table);

	$settings_button_dsp = empty($settings_button_dsp_mdl) ? $settings_button_dsp : '';

	$string_settings = empty($settings_button_dsp) ? '' : " data-dsp='$settings_button_dsp' data-dsp-mdl=''";
	$string_settings .= empty($settings_button_dsp_mdl) ? '' : " data-dsp='mdl' data-dsp-mdl='$settings_button_dsp_mdl'";
	$string_settings .= empty($settings_data_button_className) ? '' : " data-dsp-className='$settings_data_button_className'";

?>
<div class="flex_v blanc" style="height:100%;overflow:hidden;width:100%;z-index:0;" id="explo_<?= $table ?>" app_gui_explorer>
	<div class="boxshadow relative flex_h" style="z-index:1;height: 100%;">
		<? if (!empty($_POST['show_search'])) { ?>
			<div style="width:250px;" class="borderr" id="app_liste_search_<?= $zone ?>"><?= skelMdl::cf_module("app/app/app_explorer_search", $_POST) ?></div>
		<? } ?>
		<div class="flex_v flex_main">
			<div main_auto_tree>
				<div class="flex_h flex_align_middle ededed">
					<div class="padding aligncenter " style="width:60px;height:100%;color: <?= $APP->colorAppscheme ?>">&nbsp;<i class="fa fa-<?= $APP->iconAppscheme ?>"></i></div>
					<div class="flex_main  ">
						<div class="titre_entete" auto_tree right="right" flex_h flex_align_middle>
							<div class="flex_h flex_align_middle">
								<div expl_file_reload mdl="app/app_liste/app_liste_pager">
									<?= skelMdl::cf_module("app/app_liste/app_liste_pager", $_POST) ?>
								</div>
								<div style="line-height: 1;"><span expl_count_report="true" class="padding"></span></div>
								<div class="flex_h flex_align_middle flex_margin">
									<div><span class="bold uppercase"><?= $APP->nomAppscheme ?></span></div>
									<div><?= $APP->vars_to_titre($_POST); ?></div>
								</div>
								<? if (!empty($TEST_AGENT)): ?>
									<div style="width:30px;" class="alignright flex_main">
										<i class="fa fa-user-secret"></i>...
									</div>
								<? endif; ?>
							</div>
						</div>
						<div style="display:none;" class="blanc">
							<? if (sizeof($vars) == 1) {
								$new_key       = $vars[0];
								$new_table_arr = array_keys($vars);
								$new_table     = $new_table_arr[0];
								$new_table     = substr($new_table, 2);
								?>
								<div act_defer mdl="app/app/app_fiche_entete" vars="table=<?= $new_table ?>&table_value=<?= $vars['id' . $new_table] ?>"></div>
							<? } ?>
						</div>
					</div>
				</div>
			</div>
			<? if (empty($_POST['hide_menu'])): ?>
				<div class="" expl_file_reload mdl="app/app_liste/app_liste_menu" expl_act_target_receiver>
					<?= skelMdl::cf_module("app/app_liste/app_liste_menu", $_POST) ?>
				</div>
			<? endif; ?>
			<div id="sum_<?= $zone ?>"></div>
			<div class="flex_h flex_main" style="overflow: hidden;z-index:0;">
				<div class="flex_main flex_v  " style="overflow: hidden;z-index:0;" expl_file_zone>
					<div class="flex_main" id="<?= $zone ?>" data-dsp-sum="sum_<?= $zone ?>" data-data_model="defaultModel" expl_file_list <?= $string_settings ?> ></div>
				</div>
			</div>
		</div>
	</div>
	<!--<div expl_preview_zone class="flex_h blanc   absolute" style="max-width:50%;height:100%;right:0;top:0;overflow:hidden;display:none;z-index:100;">
	</div>-->
</div>
<script>
	load_table_in_zone ('groupBy=<?=$settings_button_group?>&<?=$APP->translate_vars($vars)?>&<?=http_build_query($_POST)?>&nbRows=<?=$nbRows?>&page=0', '<?=$zone?>');

	$ ('app_liste_search_<?=$zone?>').on ('submit', 'form', function (event, node) {
		var form_vars = $ (node).serialize ();
		//alert(form_vars);
		load_table_in_zone ('groupBy=<?=$settings_button_group?>&<?=$APP->translate_vars($vars)?>&nbRows=<?=$nbRows?>&page=0&' + form_vars, '<?=$zone?>');

		// $ ('contenu_explorer_<?= $zone ?>').loadModule ('app/app_liste/app_liste', 'table=<?= $table ?>&nbRows=750&' + form_vars);
	}.bind (this));
</script>
<style>
	<?='#'.$zone?>[data-dsp-mdl="app/app/app_fiche_forward"] > .div_tbody {
		display        : flex;
		flex-direction : row;
		height         : 100%;
		overflow       : hidden !important;
	}
	<?='#'.$zone?>[data-dsp-mdl="app/app/app_fiche_forward"] > .div_tbody div.autoToggle {
		height : 100%;
	}
	<?='#'.$zone?>[data-dsp-mdl="app/app/app_fiche_entete"] > .div_tbody {
		display        : flex;
		flex-direction : row;
		flex-wrap      : wrap;
	}
	<?='#'.$zone?>[data-dsp-mdl="app/app/app_fiche_entete"] > .div_tbody div.autoToggle {
		width      : 50%;
		box-shadow : 0 0 1px #ccc;
	}
	<?='#'.$zone?>[data-dsp-mdl="app/app_img/image_dyn"] > .div_tbody {
		display        : flex;
		flex-direction : row;
		flex-wrap      : wrap;
		align-items    : flex-start;
	}
</style>