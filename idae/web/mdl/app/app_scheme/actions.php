<?
	include_once($_SERVER['CONF_INC']);

	array_walk_recursive($_POST, 'CleanStr', $_POST);

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	switch ($F_action) {
		default;
			$ACT = new ActionScheme();
			if (method_exists($ACT, $F_action)) {
				$ARGS = $ACT->$F_action($_POST);
				$_POST = $ARGS;
			}
			break;
	}

	skelMdl::send_cmd('act_script', ['script' => 'schemeLoad()']);
	include_once(__DIR__ . '/actions_post.php');
	include_once(DOCUMENTROOT . '/postAction.php');

