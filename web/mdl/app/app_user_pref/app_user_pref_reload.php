<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();

	if (!empty($_SESSION["idagent"])) {
		$idagent = (int)$_SESSION['idagent'];

		?>

		<?

		$arr = $APP->plug('sitebase_pref', 'agent_pref')->findOne(array('idagent' => $idagent, 'codeAgent_pref' => 'appgui_windowcolor'));
		if (!empty($arr['appgui_windowcolor'])) {
			$wincolor = $arr['appgui_windowcolor'];
			?>
			<style>
				.appgui_windowcolor.active {
					background-color: <?=$wincolor?>
				}</style>		<?
		}
		$arr = $APP->plug('sitebase_pref', 'agent_pref')->findOne(array('idagent' => $idagent, 'codeAgent_pref' => 'appgui_windowcolor'));
		if (!empty($set['appgui_guicolor'])) {
			$wallp = $arr['settings']['appgui_guicolor'];
			?>
			<style>
				.appgui_guicolor {
					background-color: <?=$wallp?> !important
				}</style>		<?
		}
		$arr = $APP->plug('sitebase_pref', 'agent_pref')->findOne(array('idagent' => $idagent, 'codeAgent_pref' => 'appgui_backgroundcolor'));
		if (!empty($arr['appgui_backgroundcolor'])) {
			$bgcol = $arr['appgui_backgroundcolor'];
			?>
			<style>
				.appgui_backgroundcolor {
					background-color: <?=$bgcol?>
				}</style>		<? } ?><?
	}
?>  