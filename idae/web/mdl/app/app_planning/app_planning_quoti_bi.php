<?
include_once ($_SERVER['CONF_INC']);
$time = time();
$_POST['idagent'] = $_SESSION['idagent'];
if (!empty($_POST['date'])) {
	$tmpdate = explode("/", $_POST['date']);
	$jour = $tmpdate[0];
	$mois = !empty($tmpdate[1]) ? $tmpdate[1] : date("m");
	$annee = !empty($tmpdate[2]) ? $tmpdate[2] : date("Y");
	$_POST['sd'] = mktime(12, 0, 0, $mois, $jour, $annee);
	$_POST['sdMore'] = mktime(12, 0, 0, $mois, $jour + 1, $annee);
}

if (empty($_POST['sd'])) {
	if (!isset($jour))
		$jour = ((!empty($mois)) ? 1 : date("j"));
	if (!isset($mois))
		$mois = date("m");
	if (!isset($annee))
		$annee = date("Y");
	$sd = $_POST['sd'] = mktime(12, 0, 0, $mois, $jour, $annee);
	$sdMore = $_POST['sdMore'] = mktime(12, 0, 0, $mois, $jour + 1, $annee);
} else {
	$sd = $_POST['sd'];
	$sdMore = $_POST['sdMore'];
}
?>
<div class="flex_h" style="width:100%;height:100%;overflow:hidden;">
	<div class="demi relative" style="height:100%;">
		<?= skelMdl::cf_module('app/app_planning/app_planning_quoti',  array('sd' => $sd, 'valueModule' => $time + 20), $time + 20) ?>
	</div>
	<? 	unset($_POST['sd']);
		unset($_POST['date']);
	?>
	<div class="demi borderr relative" style="height:100%;">
		<?= skelMdl::cf_module('app/app_planning/app_planning_quoti',  array('valueModule' => $time + 10, 'sd' => $sdMore), $time + 10) ?>
	</div>
</div>
