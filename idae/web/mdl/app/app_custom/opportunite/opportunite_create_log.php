<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	ini_set('display_errors', 55);

	$APP      = new App('opportunite');
	$APP_PROD = new App('produit');
	$APPS     = new App('opportunite_statut');

	$time = time();
	$vars = empty($_POST['vars']) ? [] : $_POST['vars'];
	// vardump($_POST);
	$pattern = "/(\\d*)(\\s*)(\\w*)(\\s)/i";
	$pattern = "/(\\d*)(\\s*)([\\a-z]*[0-9]*)(\\s)/i";
	preg_match_all($pattern, $_POST['descriptionOpportunite'], $woul, PREG_SET_ORDER); // (\d*)(\s*)((\w*)|(\w*)-(\w*))(\s*)
	$str = '';
	vardump($woul);
	foreach ($woul as $key => $arr_match) {
		if (!empty($arr_match[1]) && !empty($arr_match[3])) {
			// vardump($arr_match[1].' '.$arr_match[3]);
			$qte        = $arr_match[1];
			$prod       = $arr_match[3];
			$prod_2     = str_replace('-', ' ', $prod);
			$reg        = MongoCompat::toRegex('^' . MongoCompat::escapeRegex($prod) . '$', 'i');
			$reg2       = MongoCompat::toRegex('^' . MongoCompat::escapeRegex($prod_2) . '$', 'i');
			$ARR_PROD   = $APP_PROD->findOne(['codeProduit' => $reg]);
			$ARR_PROD_2 = $APP_PROD->findOne(['codeProduit' => $reg2]);
			$ARR_PROD_3 = $APP_PROD->findOne(['nomProduit' => $reg]);
			$ARR_PROD_4 = $APP_PROD->findOne(['nomProduit' => $reg2]);

			if (!empty($ARR_PROD['idproduit'])) {
				$str .= '<div> 1 => ' . $prod . ' ' . $qte . ' ' . $ARR_PROD['nomProduit'] . ' ' . $ARR_PROD['nomMarque'] . '</div>';
			} else if (!empty($ARR_PROD_2['idproduit'])) {
				$str .= '<div> 2 => ' . $prod . ' ' . $qte . ' ' . $ARR_PROD_2['nomProduit'] . ' ' . $ARR_PROD_2['nomMarque'] . '</div>';
			}else if (!empty($ARR_PROD_3['idproduit'])) {
				$str .= '<div> 3 => ' . $prod . ' ' . $qte . ' ' . $ARR_PROD_3['nomProduit'] . ' ' . $ARR_PROD_3['nomMarque'] . '</div>';
			}else if (!empty($ARR_PROD_4['idproduit'])) {
				$str .= '<div> 4 => ' . $prod . ' ' . $qte . ' ' . $ARR_PROD_4['nomProduit'] . ' ' . $ARR_PROD_4['nomMarque'] . '</div>';
			}
		}
	}
	if (!empty($str)) {
		$out['msg']     = $str;
		$out['options'] = ['sticky' => 1, 'id' => 'opoloki'];
		skelMdl::send_cmd_eleph('act_notify', $out, session_id());
	} else {
		$out['msg']     = json_encode($woul);
		$out['options'] = ['sticky' => 1, 'id' => 'opoloki'];
		skelMdl::send_cmd_eleph('act_notify', $out, session_id());
	}
?>
<div class="blanc">
</div>
