<?
	include_once($_SERVER['CONF_INC']);

	$APP  = new App();
	$POST = empty($MDLPOST) ? $_POST : $MDLPOST;
	$time = time();
	$arr  = $APP->plug('sitebase_base', 'agent')->findOne(['idagent' => (int)$_SESSION['idagent']]);

	if (empty($_SESSION['idagent'])):
		die('Non identifié');
	endif;

?>
<div class="relative flex_h" style="width:100%;height:100%;z-index:0;overflow:hidden">
	<div class="relative flex_h flex_main" style="height:100%;z-index:0;overflow:hidden">
		<div class="flex_main  flex_v " style="width: 100%;">
			<div class="flex_h">
				<div class="relative" style="z-index: 0;width:50%;">
					<div id="zone_agent_table" data-dsp_liste="dsp_lists" data-vars="table=agent_table&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_icone"
					     style=" resize: horizontal;"></div>
				</div>
			</div>
			<div class="flex_main"></div>
			<div class="relative flex_h" style="z-index: 0;">
				<div id="zone_agent_tuile" data-dsp_liste="dsp_lists" data-vars="table=agent_tuile&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini"
				     style=" resize: horizontal;">

				</div>
			</div>
		</div>
		<div class="relative">
			<div id="note_panel" data-dsp_liste="dsp_lists" data-vars="table=agent_note&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche"
			     style="max-width:700px;;max-height:100%;resize: horizontal;"></div>
		</div>
	</div>
	<div class="flex_v">
		<div class="padding flex_main" act_defer mdl="app/app_gui/app_gui_calendar"></div>
		<div class="   " style="z-index:1;bottom:0;right:0;" act_defer mdl="app/app_img/image_dyn"
		     vars="noEdit=true&table=agent&table_value=<?= $_SESSION['idagent'] ?>&codeTailleImage=square" scope="app_img"
		     value="agent-square-<?= $_SESSION['idagent'] ?>"
		     onclick="<?= fonctionsJs::app_fiche('agent', $_SESSION['idagent']) ?>"></div>
	</div>
	<div class="gradb flex_v  animated   slideInRight speed" style="width:210px;height:100%;overflow:auto;top:0;right:0;">
		<div class="flex_main" style="overflow: hidden;" main_auto_tree>
			<?= skelMdl::cf_module('app/app_gui/app_gui_panel_list', ['scope' => 'app_panel']) ?>
		</div>
		<div class="alignright applink boxshadow">
			<?= skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['code' => 'app_panel', 'css' => 'color_fond_noir']) ?>
		</div>
	</div>
</div>
<div class="alignright padding margin none">
	<?= skelMdl::cf_module('app/app_user_pref/app_user_pref_css', [], $_SESSION['idagent']) ?>
</div>
<style>
	.guinext, guiprev, guiclose {
		position         : relative;
		background-color : #000;
		height           : 20px;
		width            : 20px;
		margin-top       : 4em;
		left             : 0
	}
	.guiprev {
		position         : relative;
		background-color : #333;
		height           : 20px;
		width            : 20px;
		margin-top       : 2em;
		right            : 0;
	}
	.guiclose {
		position         : relative;
		background-color : #900;
		height           : 20px;
		width            : 20px;
		margin-top       : 2em;
		right            : 0;
	}
	.inArea {
		width    : 100%;
		height   : 100%;
		position : relative;
		overflow : hidden;
	}
	.appDelim {
		position         : absolute;
		background-color : rgba(255, 255, 255, 0.2);
		width            : 25px;
		height           : 100%;
		overflow         : hidden;
		top              : 0;
		left             : 0;
	}
	.forNav:hover #navbar {
		opacity : 0.8
	}
	.linker {
		position         : absolute;
		background-color : #FFF;
		border           : 1px solid #ccc;
		width            : 200px;
		background-color : #999
	}
	#startpanel {
		position         : absolute;
		min-width        : 100%;
		height           : 100%;
		top              : 0;
		left             : 0;
		background-color : rgba(0, 0, 0, 0.1);
		z-index          : 100;
		overflow         : visible;
	}
	.animated.speed {
		-webkit-animation-duration : 0.2s;
		animation-duration         : 0.2s;
	}
</style>
