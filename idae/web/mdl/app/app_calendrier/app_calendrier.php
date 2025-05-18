<?
	include_once($_SERVER['CONF_INC']);
	// generalement , le nom du container
	$daCal = 'idcal' . $uniqid;

	if (!empty($_POST['date'])) {
		$sd = $_POST['sd'] = strtotime(date_mysql($_POST['date']));
	}
	unset($_POST['date']);
	if (empty($_POST['sd'])) {
		$sd = $_POST['sd'] = time();
	} else {
		$sd = $_POST['sd'];
	};

	$attr = empty($_POST['calendar_target']) ? '' : "data-calendar_target=" . $_POST['calendar_target'];

?>
<div data-app_calendrier <?= $attr ?> class="flex_v padding" style="margin:0 auto;overflow:hidden;height:220px;padding:0;width:100%;max-width:250px;">
	<div data-nav_cal><?= skelMdl::cf_module('app/app_calendrier/calendrier_nav', ['sd' => $sd] + $_POST); ?></div>
	<div data-nav_zone class="flex_main"><?= skelMdl::cf_module('app/app_calendrier/calendrier_day', ['sd' => $sd] + $_POST); ?></div>
</div>