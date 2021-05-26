<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	if (empty($_SESSION['idagent'])) return;
	$APP       = new App('agent');
	$APP_VER   = new App('app_version_file');
	$arrAgent  = $APP->findOne(['idagent' => (int)$_SESSION['idagent']]);
	$init_date = strtotime('2016-11-01');
	//
	$col = $APP->plug('sitebase_app', 'app_version_user');
	$arr = $col->findOne(['idagent' => (int)$_SESSION['idagent']]);
	if (!empty($_SESSION['ver_filemttime'])) $init_date = $_SESSION['ver_filemttime'];
	//
	// $_SESSION['ver_filemttime'] = mostRecentModifiedFileTime(APPPATH . 'web/', true, ['images_base', 'tmp', 'app_install']);

	// for count
	$RS_COUNT = $APP_VER->find(['ENVIRONEMENT' => ENVIRONEMENT, 'timeApp_version_file' => ['$gte' => $init_date]]);
?>
<div class="blanc animated bounceIn" style="width:340px;;text-align: left;">
	<div class="flex_h flex_align_middle boxshadow padding" style="z-index:1;">
		<div class="padding_more borderr"><i class="fa fa-cloud-download fa-4x textvert textshadow"></i></div>
		<div class="padding_more applink applinkbig applinkblock flex_main">
			<a onclick="ajaxValidation('update_user_version_file','<?=HTTPMDL.'app/app_version/'?>')"><i class="fa fa-refresh textvert"></i> <?= idioma('mettre à jour') ?></a>
			<div class="retrait bordert"><?= ($RS_COUNT->count() < 2 )? 'des' : $RS_COUNT->count() ?> <?= idioma('fichiers ont été modifiés') ?></div>
		</div>
	</div>
</div>