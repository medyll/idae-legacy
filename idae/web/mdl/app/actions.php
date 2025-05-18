<?
	include_once($_SERVER['CONF_INC']);

	array_walk_recursive($_POST, 'CleanStr', $_POST);

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}

	// do the before
	include_once(__DIR__ . '/actions_pre.php');
	switch ($F_action) {
		case "hide":
			if (empty($_POST['table'])) exit;
			$table = $_POST['table'];
			$Table = ucfirst($table);
			skelMdl::send_cmd('act_notify', ['msg' => $_POST['vars']['nom' . $Table]]);
			break;
		case "app_opor":
			$pattern = "/(\\d*)(\\s*)(\\w*)(\\s*)/";
			preg_match_all($pattern, $_POST['descriptionOpportunite'], $woul, PREG_SET_ORDER);
			foreach ($woul as $key => $arr_match) {
				if (!empty($arr_match[1]) && !empty($arr_match[3])) {
					vardump($arr_match[1] . ' ' . $arr_match[3]);
					$qte      = $arr_match[1];
					$prod     = $arr_match[3];
					$reg      = new MongoRegex("/^$prod/i");
					$ARR_PROD = $APP_PROD->findOne(['codeProduit' => $reg]);

					if (!empty($ARR_PROD['idproduit'])) {
						$str .= '<div>' . $qte . ' ' . $ARR_PROD['nomProduit'] . ' ' . $ARR_PROD['nomMarque'] . '</div>';
					}
				}
			}

			break;
		default;
			$ACT = new Action();
			if (method_exists($ACT, $F_action)) {
				$ARGS = $ACT->$F_action($_POST);
				$_POST = $ARGS;
			}
			break;
	}
	include_once(__DIR__ . '/actions_post.php');
	include_once(DOCUMENTROOT . '/postAction.php');