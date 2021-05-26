<?
	include_once($_SERVER['CONF_INC']);
?>
<style>
	.cf_module:hover {
		/*box-shadow : 0 0 2px 2px darkolivegreen;*/
	}
</style>
<div style="height:100%;" class="flex_v relative blanc">
	<div class=" titre_entete applink   flex_h none ">
		<div class=" hide_gui_pane textrouge">
			<a><i class="fa fa-times"></i> <?= idioma('fermer') ?></a>
		</div>
		<div class="flex_main" style="width: 100%;"></div>
	</div>
	<div class="flex_h flex_main blanc" style="height:100%;overflow:hidden;box-shadow:1px 1px 2px #666;">
		<div class="  " style="width:50%;height:100%;">
			<div class="flex_v blanc" style="height:100%;">
				<div class="relative">
					<script>
						main_item_search = new BuildSearch('patolaon');
					</script>
					<div class="flex_h flex_align_middle">
						<div class=" hide_gui_pane applink">
							<a><i class="fa fa-times textrouge"></i></a>
						</div>
						<div class="flex_main blanc padding">
							<form onsubmit="main_item_search.load_data($(this).serialize());$('main_item_search_zone').toggleContent();return false;">
								<button type="submit"
								        style="position:absolute;top:0;height:100%;right: 0.5em; z-index: 10;border: none;background-color: transparent;">
									<span class="borderl"><i class="fa fa-search"></i>
								</button>
								<input data-menu="data-menu" placeholder="Recherche" name="search"
								       style="position: relative;margin-right:0px;z-index:1;width:100%;line-height:2"
								       type="text" class=""/>
								<div class="blanc" style="display:none">
									<div class="" act_defer mdl="app/app_gui/app_gui_start_search_last" data-cache="true" vars="" value="<?= $_SESSION['idagent'] ?>" style="z-index:100;overflow:visible;position:relative">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="flex_main flex_v" style="overflow:hidden;width:100%;">
					<div id="main_item_search_zone" class="flex_main fond_noir" style="overflow:hidden;width:100%;display:none;">
						<div class="flex_v padding_more" style="height:100%;">
							<div class="padding color_fond_noir  alignright  flex_h  ">
								<?= skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['code' => 'app_search', 'moduleTag' => 'div', 'css' => 'color_fond_noir padding', 'text' => 'étendre']) ?>
								<div></div>
								<a class="flex_main padding color_fond_noir" onclick="$('main_item_search_zone').unToggleContent();">
									<i class="fa fa-times textrouge"></i><?= idioma('fermer') ?></a>
							</div>
							<div class="blanc flex_main flex_h flex_wrap flex_align_top" style="overflow:hidden;">
								<div class="none fond_noir padding color_fond_noir" style="height:100%"><i class="fa fa-search"></i>
									<a><i class="fa fa-times textrouge"></i></a>
								</div>
								<div class="flex_main boxshadow relative" id="patolaon" style="overflow:auto;height:100%;">
								</div>
							</div>
						</div>
					</div>
					<div class="flex_main" style="width:100%;overflow:auto;">
						<div class="" style="height:100%;"> <?= skelMdl::cf_module('app/app_gui/app_gui_start_menu', ['scope' => 'app_menu_start']) ?> </div>
					</div>
				</div>
			</div>
		</div>
		<div id="search_admin" class="" style="width:50%;display: none;overflow:auto;"></div>

		<div style="height:100%;width:50%;" class="flex_v">
			<div class="titre_entete  applink flex_h relative">
				<div class="flex_main">
					<a class="autoToggle" act_target="loader_gui_pane" mdl="app/app_gui/app_gui_today">
						<i class="fa fa-refresh textgris"></i><i class="fa fa-home"></i><?= idioma('Aujourd\'hui') ?></a>
				</div>
				<div>
					<a class="ellipsis" onClick="act_chrome_gui('app/app_user_pref/app_user_pref','mdl=app/app_user_pref/app_user_pref_scheme&code=app_menu_create');return false;">
						<i class="fa fa-cogs"></i>
					</a>
				</div>
			</div>
			<div id="loader_gui_pane" class="flex_main" data-cache="true" style="overflow:hidden;">
				<?= skelMdl::cf_module('app/app_gui/app_gui_today') ?>
			</div>
		</div>
	</div>
	<div class="titre_entete applink transpblanc flex_h" style="widh:100%;">
		<div>
			<a class="ellipsis" onClick="reloadModule('app/app_gui/app_gui_main','*','','',{seeLoading:'false'});">
				<i class="fa fa-refresh textvert bold"></i>
				Recharger
			</a>
		</div>
		<div>
			<a class="ellipsis" onClick="act_chrome_gui('app/app_user_pref/app_user_pref','mdl=app/app_user_pref/app_user_pref_style');return false;">
				<i class="fa fa-image"></i>
				<?= idioma('Personnaliser') ?>
			</a>
		</div>
		<div class="flex_main alignright ellipsis ededed" style="width:100%;">
			<a onClick="ajaxValidation('quitter');return false;">
				<i class="fa fa-power-off textrouge"></i>&nbsp;<?= idioma('Quitter') ?>
			</a>
		</div>
	</div>
	<div id="loader_progress_pane" style="overflow:auto;" class="applinkblock blanc">
	</div>
</div>