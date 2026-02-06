<?php
	error_log("DEBUG: actions.php START - POST: " . print_r($_POST, true));
	include_once($_SERVER['CONF_INC']);

	array_walk_recursive($_POST, 'CleanStr');

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}

	switch ($F_action) {
		case "app_log":

			$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
			$type  = isset($_POST['type']) ? $_POST['type'] : 'agent';
			$Type  = ucfirst($type);
			$APP   = new App($type);
			
			$arrAgent = $APP->findOne(["login$Type" => $_POST["login$Type"], "password$Type" => $_POST["password$Type"]]);

			$phpsessid = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '';

			if (!empty($arrAgent)) {
				error_log("DEBUG: actions.php agent found: " . $arrAgent["id$type"]);

				$idagent             = (int)$arrAgent["id$type"];
				$_SESSION['type']    = $type;
				$_SESSION["id$type"] = $_POST["id$type"] = $arrAgent["id$type"];
				if ($type == 'agent') {
					$_SESSION['idagent_groupe'] = $_POST['idagent_groupe'] = $arrAgent['idagent_groupe'];
				}

				$base = $APP->plug_base('sitebase_sockets');
				$base->onLine->update(['idagent' => (int)$arrAgent['idagent']], ['$set' => ['online' => 1, 'firstConnect' => time()]], ['upsert' => true]);

				setcookie("login", $_POST['loginAgent'], time() + 3600 * 24 * 30, '/');
				setcookie("id$type", $idagent, 0, '/');
				setcookie("SESSID", $idagent, 0, '/');
				setcookie("APPID", $phpsessid, 0, '/');

				skelMdl::send_cmd('act_notify', ['msg' => 'En ligne ' . $arrAgent["prenom$Type"], "id$type" => $arrAgent["id$type"], 'PHPSESSID' => $phpsessid, 'SESSID' => $arrAgent["idagent"]]);
				$APP->update(['idagent' => $idagent], ['PHPSESSID' => $phpsessid]);

				error_log("DEBUG: actions.php calling app_login_success");
				$mdl = skelMdl::cf_module('app/app_login/app_login_success');//;
				error_log("DEBUG: actions.php app_login_success done");
				
				skelMdl::send_cmd('act_notify', ['msg' => $mdl]);
				?>
				<script>
					localStorage.setItem ('SESSID', '<?=$arrAgent["id$type"]?>');
					localStorage.setItem ('IDAGENT', '<?=$arrAgent["id$type"]?>');
					localStorage.setItem ('APPID', '<?=$phpsessid?>');
					localStorage.setItem ('PHPSESSID', '<?=$phpsessid?>');

					socket.emit ('grantIn', { DOCUMENTDOMAIN : '<?=DOCUMENTDOMAIN?>', IDAGENT :<?=$arrAgent["id$type"]?>, SESSID :<?=$arrAgent["id$type"]?>, PHPSESSID : '<?=$phpsessid?>' }, function (data) {
						$ ('inBody').loadModule ('app/app_gui/app_gui_main', { onComplete : hide_login () });
					})
				</script>
				<?php
			} else {
				$_SESSION["id$type"] = '';
				unset($_SESSION["id$type"]);

				skelMdl::send_cmd('act_notify', ['msg' => 'Accés refusé ' . $_POST["login$Type"]], $phpsessid);
			}
			break;
	}

	include_once(DOCUMENTROOT . '/postAction.php');