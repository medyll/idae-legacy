<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 11/11/2015
	 * Time: 19:09
	 */
	include_once($_SERVER['CONF_INC']);

	$mdl = empty($_POST['mdl']) ? '' : $_POST['mdl'];
?>
<div class="flex_v" style="height:450px;width:750px;">
	<div class="titre_entete">
		<i class="fa fa-image"></i> <?= idioma('Configuration et préférences') ?>
	</div>
	<div class="flex_h flex_main">
		<div class="frmCol1 ededed flex_v toggler">
			<div class="flex_main applink applinkbig applinkblock  ">
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_scheme" vars="code=app_menu_start">Menu principal</a>
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_scheme" vars="code=app_menu">Panneau latéral gauche</a>
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_scheme" vars="code=app_panel">Panneau latéral droit</a>
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_scheme" vars="code=app_search">Recherche rapide</a>
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_style">Style</a>
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_color">couleurs</a>
			</div>
			<div class="applink applinkbig applinkblock ">
				<a class="autoToggle" act_target="loader_user_pref" mdl="app/app_user_pref/app_user_pref_init"><?=idioma('Réinitialiser')?></a>
			</div>
		</div>
		<div class="flex_main">
			<div id="loader_user_pref" act_defer="<?= $mdl ?>" mdl="<?= $mdl ?>" vars="<?= http_build_query($_POST) ?>" style="height: 100%;"></div>
		</div>
	</div>
</div>