<?
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);
	$APP = new App('appscheme');

	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];

	$louid = uniqid('louid');
	unset($_POST['module']);
	unset($_POST['mdl']);

?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;" app_gui_explorer>
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div style="z-index:500;" act_defer mdl="app/app_prod/app_prod_menu" vars="<?= http_build_query($_POST) ?>"></div>
		<div class="relative">
			<progress class="auto_prog" style="border:none;display:none;position: absolute;left:0;top:0;width:100%;height:100%;z-index:0"></progress>
			<div expl_file_reload mdl="app/app_prod/app_prod_history"
			     class="hidden-xs relative">
				<div act_defer mdl="app/app_prod/app_prod_history" vars="<?= http_build_query($_POST); ?>"
				     style="height:100%;position:relative;"></div>
			</div>
		</div>
		<div class="flex_main " style="position:relative;overflow:hidden;background-color: red">
			<div style="position: absolute;height: 100%;width: 100%;">
				<div class="flex_h" style="height:100%;overflow: hidden;">
					<div class="frmCol1 borderr">
						<div act_defer mdl="app/app_prod/app_prod_nav" vars="<?= http_build_query($_POST); ?>"
						     style="height:100%;position:relative;"></div>
					</div>
					<div class="frmCol2 flex_main" style="overflow:hidden;">
						<div style="height: 100%;overflow:hidden;">
							<div class="flex_h" style="height: 100%;width:100%;">
								<div class="flex_main" expl_file_zone expl_drag_selection_zone="expl_drag_selection_zone"
								     style="height: 100%;">
									<div id="zone_<?= $uniqid ?>" data-dsp="table" expl_file_list="<?= $expl_file ?>"
									     act_defer mdl="app/app/app_explorer_home_entete" data-draggable="true"
									     vars="<?= http_build_query($_POST) ?>" value="<?= $uniqid ?>"
									     style="overflow:hidden;height: 100%;max-height: 100%;position: relative;"></div>
								</div>
								<div class="blanc borderl shadow" expl_preview_zone
								     style="display:none;width:40%;height:100%;z-index: 100"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>