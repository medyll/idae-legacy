<?
	include_once($_SERVER['CONF_INC']);

	$APP  = new App();
	$POST = empty($MDLPOST) ? $_POST : $MDLPOST;
	$time = time();
	$arr  = $APP->plug('sitebase_base', 'agent')->findOne(['idagent' => (int)$_SESSION['idagent']]);

	// skelMdl::runSocketModule('dyn/dyn_daemon', ['idagent' => $_SESSION['idagent']]);

	// skelMdl::run('mdl/dyn/dyn_email_agent', ['idagent' => $_SESSION['idagent'], 'start' => time()]);

	if (empty($_SESSION['idagent'])):
		// die('Non identifié');
	endif;

	$dsp_menu      = $APP->get_settings($_SESSION['idagent'], 'gui_menu_visible');
	$dsp_menu_show = ($dsp_menu == 'none') ? '' : 'none';
	$dsp_menu_hide = ($dsp_menu == 'none') ? 'none' : '';

	$app_cache_mode = $APP->get_settings($_SESSION['idagent'], 'cache_mode');
	if (empty($app_cache_mode)) $app_cache_mode = "off";
	$app_cache_mode_on  = ($app_cache_mode == 'on') ? 'none' : '';
	$app_cache_mode_off = ($app_cache_mode == 'on') ? '' : 'none';
?>
<div class="flex_v" id="div_app_gui_main">
	<div class="taskBar  flex_h flex_align_middle gradb" style="position:relative;z-index:0;">
		<div class="applink toggler toggler_visible" none>
			<a style="display:<?= $dsp_menu_hide ?>" class="autoToggle" onclick="$('gui_menu').hide();save_settings('gui_menu_visible','none')"><i class="fa fa-caret-left"></i></a>
			<a style="display:<?= $dsp_menu_show ?>" class="autoToggle" onclick="$('gui_menu').show();save_settings('gui_menu_visible','')"><i class="fa fa-caret-right"></i></a>
		</div>
		<div class="applink toggler toggler_visible">
			<a data-setting="gui_menu_visible" data-setting-value="none" data-setting-mode="display" data-setting-method="click" data-setting-apply="true" class="autoToggle" onclick="$('gui_menu').hide();" style="display:none;"><i
					class="fa fa-caret-left"></i>
			</a>
			<a data-setting="gui_menu_visible" data-setting-value="auto" data-setting-mode="display" data-setting-method="click" data-setting-apply="true" class="autoToggle" onclick="$('gui_menu').show();" style="display:none;"><i
					class="fa fa-caret-right"></i>
			</a>
		</div>
		<div class="applink">
			<a onclick="$('gui_pane').toggle()"><i class="ms-Icon ms-Icon--waffle"></i></a>
		</div>
		<div id="taskBar" class="flex_main hide_gui_pane" style="width:100%;"></div>
		<div class="applink" data-count_trigger="hide" style="position:relative;">
			<a><span class="" data-count="data-count" data-count_auto="true" data-table="email" data-vars="vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[estVuEmail]=1"></span><i class="fa fa-envelope-o"></i></a>
		</div>
		<div class="applink">
			<a data-menu="data-menu" data-clone="data-clone"><?= nomAgent($_SESSION['idagent']) ?>&nbsp;&nbsp;<i class="fa fa-user"></i></a>
			<div class="contexmenu" style="display:none;z-index:8000">
				<div class="applinkblock applink">
					<a onclick="bag.clear();window.location.reload();"><i class="fa fa-refresh textvert"></i> Recharger application</a>
					<? if (droit('DEV')) { ?>
						<a onclick="schemeLoad();"><i class="fa fa-refresh textvert"></i> schemeLoad()</a>
						<a onclick="runModule('mdl/dyn/dyn_ged');"><i class="fa fa-refresh textrouge"></i> dyn_ged</a>
						<a onclick="act_chrome_gui('app/app_mobile/app_mobile' );"><i class="fa fa-refresh textrouge"></i> dyn_ged</a>
					<? } ?>
					<a onClick="ajaxValidation('quitter');return false;">
						<i class="fa fa-power-off textrouge"></i>&nbsp;<?= idioma('Quitter') ?>
					</a>
				</div>
			</div>
		</div>
		<div>
			<a data-menu="data-menu" data-clone="data-clone">&nbsp;&nbsp;</a>
			<div style="display: none;">
			</div>
		</div>
		<? if (droit('DEV')) { ?>
			<div class="toggler toggler_visible applink">
				<a class="autoToggle" style="display: <?= $app_cache_mode_on ?>" onclick="localStorage.setItem('cache_mode','on');save_settings('cache_mode','on');"><i class="fa fa-refresh textvert"></i> cache</a>
				<a class="autoToggle" style="display: <?= $app_cache_mode_off ?>" onclick="localStorage.setItem('cache_mode','off');save_settings('cache_mode','off');"><i class="fa fa-refresh textrouge"></i> cache</a>
			</div>
		<? } ?>
		<div id="socket_keep_on_status" style="display:none;"></div>
		<div id="socket_status" style="display:none;"></div>
		<? if (droit('DEV')) { ?>
			<div class="" data-count="data-count" data-table="agent" data-vars="vars[onlineAgent]=1">&nbsp;</div>
			<div mdl="app/app_test" value="*" style="width:150px;">&nbsp;</div>
			<div class="" data-count="data-count" data-table="agent_history" data-vars="vars[idagent]=<?= $_SESSION['idagent'] ?>">&nbsp;</div>
		<? } ?>
		<div class="status blanc"></div>
	</div>
	<div class="animated speed slideInDown fadeIn" id="gui_pane" act_defer data-cache="true" mdl="app/app_gui/app_gui_start" value="<?= $_SESSION['idagent'] ?>"
	     style="display:none;z-index:100;height:100%;top:0px;position:absolute;min-width:28%;width:640px;max-width:100%;"></div>
	<div class="flex_main flex_h" style="z-index:0;width:100%;">
		<!--<div id="gui_menu" data-setting="gui_menu_visible" data-setting-default-value="none" data-setting-mode="display" data-setting-apply="true" class="gradb frmCol1 flex_v" style="display:none">-->
		<div id="gui_menu" class="gradb frmCol1 flex_v" style="display:none">
			<div class="padding ededed borderb aligncenter">
				<script>
					main_item_search_gui = new BuildSearch ('patolaon_bis');
				</script>
				<form onsubmit="main_item_search_gui.load_data($(this).serialize());$('for_patolon_bis').show();return false;">
					<button type="submit" style="position:absolute;right: 0.5em; z-index: 10;border: none;background-color: transparent;">
						<i class="fa fa-search"></i></button>
					<input placeholder="Recherche" name="search" style="position: relative;margin-right:0px;z-index:1;width:100%;line-height:2" value="" type="text" class=""/>
					<br>
				</form>
			</div>
			<div class="color_fond_noir panel_entete  flex_main" style="overflow:auto;">
				<?= skelMdl::cf_module('app/app_gui/app_gui_menu', ['scope' => 'app_menu']) ?>
			</div>
		</div>
		<div id="for_patolon_bis" class="flex_v frmCol1 blanc shadowbox " style="display:none;">
			<div class="padding applink applinkblock alignright" onclick="$('for_patolon_bis').unToggleContent();">
				<a><i class="fa fa-times"></i><?= idioma('fermer') ?></a>
			</div>
			<div class="flex_main" id="patolaon_bis" style="overflow:auto;"></div>
		</div>
		<div class="flex_v flex_main" style="height:auto;width:100%;">
			<div class="relative flex_main" onclick="$('gui_pane').hide()" style="width:100%;height:100%;z-index:0;overflow:hidden">
				<div class="relative" act_defer mdl="app/app_gui/app_gui_desktop" id="desktop" style="width:100%;height:100%;z-index:0;overflow:hidden"></div>
				<div class="absolute" id="mainApp" style="top:0;height:100%;display:none;width:100%;overflow:hidden;z-index:1;"></div>
			</div>
		</div>
	</div>
</div>
<div class="alignright padding margin">
	<?= skelMdl::cf_module('app/app_user_pref/app_user_pref_css', [], $_SESSION['idagent']) ?>
</div>
<? if (droit('DEV')) {
	?>
	<div id="upload_app_gui_main" style="position:absolute;bottom:0em;left:0em;height:100px;width:100%;display:none;" class="fond_noir bordert">
	</div>
	<div class="padding blanc">
		<? if (droit('DEV')): ?>
			<div class="padding bordert margin">
				<div style="position:relative;min-height:100px;width:100%;display:none;" id="drag_perso">
				</div>
			</div>
			<form novalidate id="form_upload_app_gui_main" action="mdl/app/app_document/actions.php" onsubmit="ajaxFormValidation(this);return false">
				<input type="hidden" name="F_action" value="addDoc"/>
				<input type="hidden" name="tag" value="1"/>
				<input type="hidden" name="multiple" value="1"/>
				<input type="hidden" name="base" value="sitebase_ged"/>
				<input type="hidden" name="collection" value="ged_bin"/>
				<input type="hidden" name="vars[idagent_owner]" value="<?= $_SESSION['idagent'] ?>"/>
			</form>
			<div id="pref_preview" class="aligncenter ededed" style="overflow:auto"></div>
			<style>
				#pref_preview img {
					max-width : 150px;
				}
			</style>
			<script>
				// new myddeAttach($('upload_app_gui_main'), {form: 'form_upload_app_gui_main',show_hide:true, priority:false, autoSubmit: true});
			</script>
		<? endif; ?>
	</div>
<? } ?>
<script>
	window.JSGUI = new appGui ($ ('mainApp'));
	localStorage.setItem ('cache_mode', 'off');
	$ ('body').on ('click', '.hide_gui_pane', function (event, node) {
		$ ('gui_pane').hide ();
	})
	/*setTimeout(function () {
	 load_table_in_zone('table=agent_tuile&sortBy=codeAgent_tuile&vars[idagent]=<?=$_SESSION['idagent']?>', 'zone_agent_tuile');
	 }, 50)
	 setTimeout(function () {
	 load_table_in_zone('table=agent_table&sortBy=codeAgent_table&vars[idagent]=<?=$_SESSION['idagent']?>', 'zone_agent_table');
	 }, 50)
	 setTimeout(function () {
	 load_table_in_zone('table=agent_note&vars[estActifAgent_note]=1&vars[idagent]=<?=$_SESSION['idagent']?>', 'note_panel');
	 }, 20)*/
	var session_verif = function () {

		get_data ('json_ssid', {}).then (function (res) {
			res_json       = JSON.parse (res);
			cookieJar      = new CookieJar ();
			var cookie_str = cookieJar.getPack ();
			var exit_app   = false;

			var arr_verif = ['PHPSESSID', 'SESSID', 'idagent'];
			arr_verif.forEach (function (item) {
				var test = cookieJar.get (item) || null;
				if ( test == null ) exit_app = true;
			}.bind (this))

			if ( exit_app == true ) ajaxValidation ('quitter')
			// if (PHPSESSID_TEST.PHPSESSID != localStorage.getItem('PHPSESSID'))
		}).then (function () {

		})
	}
	session_verif ();
	setInterval (function () {
		session_verif ();

	}, 60000);

	<? if(droit('DEV')){ ?>
	/*runModule ('mdl/dyn/dyn_email_agent','start=<?=time()?>');
	 setInterval (function () {
	 runModule ('mdl/dyn/dyn_email_agent','start=<?=time()?>');

	 }, 15000);*/
	<? }?>
</script>
<style>
	<? if(droit('DEV')){ ?>
	.cf_module:hover {
		/*box-shadow:  0 0 3px  #000  ;*/
	}
	<? }?>
</style>
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
