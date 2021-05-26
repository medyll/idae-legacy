<?
	include_once($_SERVER['CONF_INC']);


	array_walk_recursive($_POST, 'CleanStr', $_POST);

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	// vardump($_POST);
	if (empty($_POST['table'])) {
		// unset($F_action);
	}
	switch ($F_action) {
		case "update_user_version_file":
			$APP                       = new App();
			$_SESSION['ver_filemttime'] = mostRecentModifiedFileTime(APPPATH . 'web/', true, ['images_base', 'tmp', 'app_install']);
			// write to user version file
			$col = $APP->plug('sitebase_app', 'app_version_user');
			$arr = $col->update(['idagent' => (int)$_SESSION['idagent']],['$set'=>['timeLastApp_version_user'=>$_SESSION['ver_filemttime']]],['upsert'=>true]);
			?>
			<script>bag.clear ();</script>
			<script>basket.clear ();</script>
			<script>window.app_cache.clear()</script>
			<script>window.location.reload (); </script>



			<?
			exit;
			break;

	}

	include_once(DOCUMENTROOT . '/postAction.php');
?>
