<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 04/01/2016
	 * Time: 17:43
	 */
return false;
	include_once($_SERVER['CONF_INC']);

	$APP      = new App();
	$APP_CONF = new App('app_conf');
	$APP_ICON = new App('appscheme_icon');

	// return;
	$APP->init_scheme('sitebase_app', 'appscheme_icon', ['fields' => ['icon', 'code', 'nom']]);

	$fa_package = file_get_contents('https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/package.json');
	if ($fa_package) {
		$arr_package = json_decode($fa_package);
		$version     = $arr_package->version;
		if ($version) {
			$ARR_CONF       = $APP_CONF->findOne(['codeApp_conf' => 'FONT_AWESOME_VER']);
			$version_active = $ARR_CONF['valeurApp_conf'];
			if (empty($version_active) || $version_active != $version) {
				install_fa($version);
			}else{
				// vardump_async('deja ok');
			}
		}
	}
	function install_fa($version) {
		global $APP_CONF, $APP_ICON;
		// vardump_async('Installation fa-icon');
		$test_install = $APP_ICON->find()->count();
		$fa_less      = file_get_contents('https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/less/variables.less');
		preg_match_all("/@fa-var-(.*):(.*);/", $fa_less, $output_array);
		$final_fa = $output_array[1];
		foreach ($final_fa as $key => $str_fa) {
			$APP_ICON->create_update(['codeAppscheme_icon'=> $str_fa],['nomAppscheme_icon'=> $str_fa,'iconAppscheme_icon'=> $str_fa]);
		};
		vardump_async($version);
		$APP_CONF->create_update(['codeApp_conf' => 'FONT_AWESOME_VER'], ['nomApp_conf' => 'Version font-awesome', 'valeurApp_conf' => $version]);
	}

