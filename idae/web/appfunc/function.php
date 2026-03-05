<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 11/12/14
	 * Time: 00:32
	 */

	function str_find($haystack, $needle, $ignoreCase = false) {
		$haystack = (string)$haystack;
		if ($ignoreCase) {
			$haystack = strtolower($haystack);
			$needle   = strtolower($needle);
		}
		$needlePos = strpos($haystack, $needle);

		return ($needlePos === false ? false : ($needlePos + 1));
	}

	function chkSch($name, $value = '', $name_vars = 'vars') {
		$ret = '<label><input' . checked(!empty($value)) . ' name="' . $name_vars . '[' . $name . ']" type="radio" value="1" class="ms-ChoiceField-input"> Oui</label>';
		$ret .= '   ';
		$ret .= '<label><input ' . checked(empty($value)) . '  name="' . $name_vars . '[' . $name . ']" type="radio" value="0" class="ms-ChoiceField-input"> Non</label>';

		return $ret;
	}

	function my_array_filter_fn($val) {
		if (is_array($val)) return array_filter($val, "my_array_filter_fn");
		$val          = trim($val);
		$allowed_vals = ["0"]; // Add here your valid values
		return in_array($val, $allowed_vals, true) ? true : ($val ? true : false);
	}

	function my_array_filter_to_time($val) {
		return strtotime($val);;
	}

	function convert_datetime($str) {

		list($date, $time) = explode(' ', $str);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);

		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

		return $timestamp;
	}


	function color_inverse($color){
		$color = str_replace('#', '', $color);
		if (strlen($color) != 6){ return '000000'; }
		$rgb = '';
		for ($x=0;$x<3;$x++){
			$c = 255 - hexdec(substr($color,(2*$x),2));
			$c = ($c < 0) ? 0 : dechex($c);
			$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		}
		return '#'.$rgb;
	}
	function color_contrast($hexcolor){
		$hexcolor = str_replace('#', '', $hexcolor);
		$r = hexdec(substr($hexcolor,0,2));
		$g = hexdec(substr($hexcolor,2,2));
		$b = hexdec(substr($hexcolor,4,2));
		$yiq = (($r*299)+($g*587)+($b*114))/1000;
		return ($yiq >= 128) ? '#000000' : '#ffffff';
	}

	function calculateMoyenne($Values) {
		$type = '';
		if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $Values[0])) {
			$type   = 'heure';
			$Values = array_map(function ($index) { return strtotime(date('Y-m-d') . ' ' . $index) . ' => '; }, $Values);

			return date('H:i:s', array_sum($Values) / sizeof($Values));
		}

		return array_sum($Values) / sizeof($Values);
	}

	function calculateMedian($Values) {
		$type = '';
		//Remove array items less than 1
		$Values = array_filter($Values, "my_array_filter_fn");

		if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $Values[0])) {
			$type   = 'heure';
			$Values = array_map(function ($index) { return strtotime(date('Y-m-d') . ' ' . $index) . ' => '; }, $Values);
		}
		//Sort the array into descending order 1 - ?
		sort($Values, SORT_NATURAL);

		//Find out the total amount of elements in the array
		$Count = count($Values);

		if ($type == '') {
			if ($Count % 2 == 0) {
				return $Values[$Count / 2];
			}

			return (($Values[($Count / 2)] + $Values[($Count / 2) - 1]) / 2);
		}
		if ($type == 'heure') {
			if ($Count % 2 == 0) {
				return date('H:i:s', $Values[$Count / 2]);
			}

			return date('H:i:s', (($Values[($Count / 2)] + $Values[($Count / 2) - 1]) / 2));
		}
	}

	function mdl_link($mdl) {
		if (file_exists(APPMDL . '/customer/' . CUSTOMERNAME . '/' . $mdl . '.php')) {
			$mdl = 'customer/' . CUSTOMERNAME . '/' . $mdl;
		} elseif (file_exists(APPMDL . '/business/' . BUSINESS . '/' . $mdl . '.php')) {
			$mdl = 'business/' . BUSINESS . '/' . $mdl;
		}

		return $mdl;
	}

	function array_key_diff($aArray1, $aArray2) {
		$aReturn = [];

		foreach ($aArray1 as $mKey => $mValue) {
			if (array_key_exists($mKey, $aArray2)) {
				if (is_array($mValue)) {
					$aRecursiveDiff = array_key_diff($mValue, $aArray2[$mKey]);
					if (count($aRecursiveDiff)) {
						$aReturn[$mKey] = $aRecursiveDiff;
					}
				} else {
					if ($mValue != $aArray2[$mKey]) {
						$aReturn[$mKey] = $mValue;
					}
				}
			} else {
				$aReturn[$mKey] = $mValue;
			}
		}

		return $aReturn;
	}

	function xml2php($xml) {
		$fils  = 0;
		$tab   = false;
		$array = [];
		foreach ($xml->children() as $key => $value) {

			$child = xml2php($value);

			//  To deal with the attributes
			foreach ($value->attributes() as $ak => $av) {
				$array[$ak] = (string)$av;
			}

			//Let see if the new child is not in the array
			if ($tab == false && in_array($key, array_keys($array))) {
				//If this element is already in the array we will create an indexed array
				$tmp           = $array[$key];
				$array[$key]   = NULL;
				$array[$key][] = $tmp;
				$array[$key][] = $child;
				$tab           = true;
			} elseif ($tab == true) {
				//Add an element in an existing array
				$array[$key][] = $child;
			} else {
				//Add a simple element
				$array[$key] = $child;
			}

			$fils++;
		}

		if ($fils == 0) {
			return (string)$xml;
		}

		return (array)$array;
	}

	function xml2array($xmlObject, $out = []) {
		foreach ((array)$xmlObject as $index => $node) $out[$index] = (is_object($node) || is_array($node)) ? xml2array($node) : $node;

		return $out;
	}

	function soapDebug($client) {

		$requestHeaders  = $client->__getLastRequestHeaders();
		$request         = $client->__getLastRequest();
		$responseHeaders = $client->__getLastResponseHeaders();
		$response        = $client->__getLastResponse(); // prettyXml();

		return ['requestHeaders'  => html_entity_decode($requestHeaders),
		        'request'         => html_entity_decode($request),
		        'responseHeaders' => html_entity_decode($responseHeaders),
		        'response'        => html_entity_decode($response)];
	}

	function scan_dir($directory) {
		$i          = 0;
		$rootDir    = [];
		$tmprootDir = scandir(trim($directory));
		if (!empty($tmprootDir)) {
			foreach ($tmprootDir as $index => $dir) {
				if (is_writable($directory . '/' . $dir) && is_dir($directory . '/' . $dir) && $dir != '.' && $dir != '..' && $dir != '_notes') {
					$rootDir[$i]['name'] = $dir;
					$i++;
					//$rootDir[]['size'] = 30;//disk_total_space($directory.'/'.$dir);
				}
			}
		}

		return (array)$rootDir;
	}

	function scan_files($directory) {
		$i          = 0;
		$rootDir    = [];
		$tmprootDir = scandir($directory);
		foreach ($tmprootDir as $index => $dir) {
			if (is_writable($directory . '/' . $dir) && !is_dir($directory . '/' . $dir) && $dir != '.' && $dir != '..' && $dir != '_notes') {
				$rootDir[$i]['name'] = $dir;
				$i++;
			}
		}

		return (array)$rootDir;
	}

	function idioma($text) {
		return $text;
		if (trim($text) == '') {
			return '';
		}

		return ($final->fields['fr'] != '') ? $final->fields['fr'] : $text;
	}

	function date_mysql($date_origine) {

		$tmp_final_date = "";
		$tmpdate        = explode("/", $date_origine);
		for ($i = (count($tmpdate) - 1); $i >= 0; $i--) {
			if (strlen($tmpdate[$i]) < 2) {
				$tmpdate[$i] = "0" . $tmpdate[$i];
			}
			$tmp_final_date .= $tmpdate[$i];
			if ($i > 0) {
				$tmp_final_date .= "-";
			}
		}

		return $tmp_final_date;

	}

	function date_fr($date_origine) {
		if ($date_origine != "") {
			$tmp_final_date = "";
			$tmpdate        = explode("-", $date_origine);
			for ($i = (count($tmpdate) - 1); $i >= 0; $i--) {
				$tmp_final_date .= $tmpdate[$i];
				if ($i > 0) {
					$tmp_final_date .= "/";
				}
			}
			if ($tmp_final_date == '00/00/0000') {
				return '';
			}

			return $tmp_final_date;
		}
	}

	function sendPost($params = '', $arr1 = '') {
		// on pase le post si instabilité du code
		if ($arr1 == '') {
			$arr1 = array_unique($_POST);
		}
		$arr2 = [];
		parse_str($params, $arr2);
		$tempArray = array_merge((array)$arr1, (array)$arr2);

		return http_build_query($tempArray);
	}

	function arrayPost($params = '') {
		$arr1 = array_unique($_POST);
		$arr2 = [];
		parse_str($params, $arr2);
		$tempArray = array_merge_recursive((array)$arr1, (array)$arr2);

		//var_dump($tempArray );
		return $tempArray;
	}

	function maskNbre($number, $idx = 4) {
		$number = str_replace(',','.',$number);
		if (is_string($number)) {
			$number = (float)$number;
		}
		return number_format($number, $idx, '.', ' ');
	}

	function cleanTel($tel) {
		$tel = str_replace(' ', '', $tel);
		$tel = str_replace('.', '', $tel);
		$tel = str_replace('-', '', $tel);
		$tel = str_replace('/', '', $tel);
		$tel = str_replace(':', '', $tel);

		return $tel;
	}

	function data_uri($filename) {
		$parsed_URL = parse_url($filename);
		$exploded   = explode('.', $parsed_URL['path']);

		$mime = end($exploded);//mime_content_type($filename);
		$data = base64_encode(file_get_contents($filename));

		return "data:$mime;base64,$data";
	}

	function cleanPostMongo($arr, $keepnumerickey = false) {
		unset($arr['F_action']);
		unset($arr['mdl']);
		unset($arr['module']);
		unset($arr['reloadModule']);
		unset($arr['afterAction']);
		unset($arr['_id']);
		if (empty($arr)) {
			return $arr;
		}
		foreach ($arr as $key => $column) {
			$pos = strpos($key, 'fake_');
			if ($pos === false) {
			} else {
				unset($arr[$key]);
			}
		}
		$arrClean = [];
		foreach ($arr as $key => $column) {
			if ((!is_int($key) || $keepnumerickey == true)) {
				$arrClean[$key] = $arr[$key];
				if ($arr[$key] == 'true') {
					$arrClean[$key] = (bool)true;
				}
				if ($arr[$key] == 'false') {
					$arrClean[$key] = (bool)false;
				}
				//if(!is_array($arrClean[$key])){if(fonctionsProduction::isTrueFloat($arrClean[$key])) {$arrClean[$key]=(float)$arrClean[$key];} }
				if (isTrueFloat($arrClean[$key])) {
					$arrClean[$key] = (float)$arrClean[$key];
				} elseif (is_numeric($arr[$key])) {
					$arrClean[$key] = (int)$arrClean[$key];
				} elseif (is_numeric(str_replace(' ', '', $arr[$key]))) {
					$arrClean[$key] = (int)str_replace(' ', '', $arr[$key]);
				}
				if (is_array($arr[$key])) {
					$arrClean[$key] = cleanPostMongo($arrClean[$key], $keepnumerickey);
				}
			}
		}

		return $arrClean;
	}

	function mysqlToMongo($arr, $keepnumerickey = false) {
		unset($arr['F_action']);
		unset($arr['mdl']);
		unset($arr['module']);
		unset($arr['reloadModule']);
		unset($arr['afterAction']);
		// $arr = cleanPostMongo($arr , $keepnumerickey);
		foreach ($arr as $key => $column) {
			$pos = strpos($key, 'fake_');
			if ($pos === false) {
			} else {
				unset($arr[$key]);
			}
		}
		$arrClean = [];
		foreach ($arr as $key => $column) {
			if ((!is_int($key) || $keepnumerickey == true)) {
				$arrClean[$key] = $arr[$key];
				if (is_array($arr[$key])) {
					$arrClean[$key] = mysqlToMongo($arrClean[$key], $keepnumerickey);
				}
				if (!is_array($arrClean[$key])) {
					$arrID = explode("_id", $key);
					if (sizeof($arrID) == 2) {
						$arrClean['id' . $arrID[1]] = $arrClean[$key];
						unset($arrClean[$key]);
					}
				}
			}
		}

		return $arrClean;
	}

	function isTrueFloat($val) {
		/*if(is_array($val)) return false;
		$pattern = '/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/';
		return (!is_bool($val) && (is_float($val) || preg_match($pattern, trim($val))));*/
		//
		if (is_string($val)) {
			$val = trim($val);
		}
		if (is_numeric($val) && (is_float($val) || ((float)$val > (int)$val || strlen($val) != strlen((int)$val)) && (ceil($val)) != 0)) {
			return true;
		} else {
			return false;
		}
	}

	function safeArrayCombine($keys, $values) {
		$combinedArray = [];

		for ($i = 0, $keyCount = count($keys); $i < $keyCount; $i++) {
			$combinedArray[strtoupper($keys[$i])] = utf8_encode($values[$i]);
		}

		return $combinedArray;
	}

	function ShortUrl($matches) {

		$link_displayed = (strlen($matches[0]) > 35) ? substr($matches[0], 0, 30) . '...' . substr($matches[0], -30) : $matches[0];

		return '<a href="' . $matches[0] . '" title="Se rendre à « ' . $matches[0] . ' »" target="_blank">' . $link_displayed . '</a>';

	}

	function maskTime($secondes) {
		$lHeure     = floor($secondes / 60);
		$lesMinutes = $secondes % 60;

		return ($lHeure . " : " . $lesMinutes);
	}

	function remainTime($sec) {
		if ($sec > time()):
			$remain     = $sec - time();
			$lHeure     = floor($remain / 3600);
			$lesMinutes = floor(($remain - $lHeure) / 60);
			//$lesSecondes = floor(($remain - $lHeure)/60);
			$add = '';
		else:
			$remain     = time() - $sec;
			$lHeure     = floor($remain / 3600);
			$lesMinutes = floor(($remain - $lHeure) / 60);
			$add        = ' retard';
		endif;

		if (empty($lHeure)) {
			return $lesMinutes . ' mn ' . $add;
		}

		return ($lHeure . " et " . $lesMinutes . ' mn ' . $add);
	}

	function calculate_time_span($date) {
		$seconds = strtotime(date('Y-m-d H:i:s')) - $date;

		$months = floor($seconds / (3600 * 24 * 30));
		$day    = floor($seconds / (3600 * 24));
		$hours  = floor($seconds / 3600);
		$mins   = floor(($seconds - ($hours * 3600)) / 60);
		$secs   = floor($seconds % 60);

		if ($seconds < 60) {
			$time = $secs . " seconds ago";
		} else {
			if ($seconds < 60 * 60) {
				$time = $mins . " min ago";
			} else {
				if ($seconds < 24 * 60 * 60) {
					$time = $hours . " hours ago";
				} else {
					if ($seconds < 24 * 60 * 60) {
						$time = $day . " day ago";
					} else {
						$time = $months . " month ago";
					}
				}
			}
		}

		return $time;
	}

	function custom_sort_newsblock($a, $b) {
		return (int)$a['sort'] > (int)$b['sort'];
	}

	function custom_sort_acompte($a, $b) {
		return strtotime($a['dateDevisAcompte']) > strtotime($b['dateDevisAcompte']);
	}

	function custom_sort_etape($a, $b) {
		return (int)$a['ordreProduit_etape'] > (int)$b['ordreProduit_etape'];
	}

	function custom_sort_date($a, $b) {
		return strtotime($a['dateDebutProduit_tarif']) > strtotime($b['dateDebutProduit_tarif']);
	}

	function sys_log($text) {
		define_syslog_variables();
		openlog(basename(__FILE__), LOG_PID | LOG_PERROR, LOG_LOCAL0);
		syslog(LOG_DEBUG, $text);
		closelog();
	}

	function vardump($value, $return = false) {
		if (!empty($return)) return '<pre class="margin borderb blanc">' . json_encode($value, JSON_PRETTY_PRINT) . '</pre>';
		?>
		<pre class="margin borderb blanc"><br/> <?php echo json_encode($value, JSON_PRETTY_PRINT);?> </pre><?php
	}

	function vardump_async($value, $sticky = false) {
		$debug = ['msg' => vardump($value, 1)];
		if ($sticky) $debug['options'] = ['sticky' => true, 'id' => 'cardump'];
		skelMdl::send_cmd('act_notify', $debug, $_COOKIE['PHPSESSID']);
	}

	function printr($value) {

		echo json_encode($value, JSON_PRETTY_PRINT);

	}

	function buffer_flush() {
		echo str_pad(" ", 1024);
		echo '<!-- -->';

		if (ob_get_length()) {
			@ob_flush();
			@flush();
			@ob_end_flush();
		}
		@ob_start();

	}

	function UrlToShortLink($text) {

		//Pattern to retrieve the url in the comment

		$pattern = '`((?:https?|ftp)://\S+?)(?=[[:punct:]]?(?:\s|\Z)|\Z)`';

		//Replacement of the pattern

		$text = preg_replace_callback($pattern, 'ShortUrl', $text);

		return $text;

	}

	function  padIt($input, $offset, $padChar, $padConstant = STR_PAD_RIGHT) {
		define('PAD_CONSTANT', $padConstant);
		if ((int)$offset === 0 || strlen($input) == 0 || !isset($padChar) || strlen($padChar) < 1) {
			return $input;
		}            // NOTHING TO PAD
		switch (PAD_CONSTANT) {
			case STR_PAD_LEFT:
				for ($i = 1; $i <= $offset; $i++) $input = "$padChar$input";
				break;
			case STR_PAD_RIGHT:
				for ($i = 1; $i <= $offset; $i++) $input = "$input$padChar";
				break;
			case STR_PAD_BOTH:
				for ($i = 1; $i <= $offset; $i++) $input = "$padChar$input$padChar";
				break;
			default: // DO NOTHING
				break;
		}

		return $input;
	}

	function pourcentage($Nombre, $Total) {
		return round(($Total * 100) / $Nombre, 2);
	}

	function calcul_joursferies($month, $day, $year) {
		$resultat = false;

		$jf1 = $year - 1900;
		$jf2 = $jf1 % 19;
		$jf3 = intval((7 * $jf2 + 1) / 19);
		$jf4 = (11 * $jf2 + 4 - $jf3) % 29;
		$jf5 = intval($jf1 / 4);
		$jf6 = ($jf1 + $jf5 + 31 - $jf4) % 7;
		$jfj = 25 - $jf4 - $jf6;
		$jfm = 4;
		if ($jfj <= 0) {
			$jfm = 3;
			$jfj = $jfj + 31;
		}
		$paques    = (($jfm < 10) ? "0" . $jfm : $jfm) . "/" . (($jfj < 10) ? "0" . $jfj : $jfj);
		$lunpaq    = date("m/d", mktime(12, 0, 0, $jfm, $jfj + 1, $year));
		$ascension = date("m/d", mktime(12, 0, 0, $jfm, $jfj + 39, $year));
		$lunpent   = date("m/d", mktime(12, 0, 0, $jfm, $jfj + 50, $year));

		$JourFerie = ["01/01", "05/01", "05/08", "07/14", "08/15", "11/01", "11/11", "12/25", "$paques", "$lunpaq", "$ascension", "$lunpent"];

		$nbj = 0;
		$val = date("m/d", mktime(0, 0, 0, $month, $day, $year));
		while ($nbj < count($JourFerie)) {
			if ($JourFerie[$nbj] == $val) {
				$resultat = true;
				$nbj      = 15;
			}
			$nbj++;
		}

		return ($resultat);
	}

	function est_jour_ferie($date) {
		if ($date === null) {
			$date = date('Y-m-d');
		}

		$date = strtotime($date);

		$year = date('Y', $date);

		$easterDate  = easter_date($year);
		$easterDay   = date('j', $easterDate);
		$easterMonth = date('n', $easterDate);
		$easterYear  = date('Y', $easterDate);

		$holidays = [ // Dates fixes
			mktime(0, 0, 0, 1, 1, $year),  // 1er janvier
			mktime(0, 0, 0, 5, 1, $year),  // Fête du travail
			mktime(0, 0, 0, 5, 8, $year),  // Victoire des alliés
			mktime(0, 0, 0, 7, 14, $year),  // Fête nationale
			mktime(0, 0, 0, 8, 15, $year),  // Assomption
			mktime(0, 0, 0, 11, 1, $year),  // Toussaint
			mktime(0, 0, 0, 11, 11, $year),  // Armistice
			mktime(0, 0, 0, 12, 25, $year),  // Noel

			// Dates variables
			mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear),
			mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
			mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),];

		return in_array($date, $holidays);
	}
	function modulo_progress($value, $modulo, $max) {
		if ($value % $modulo == 0) return true;
		if ($value == $max) return true;
	}
	function auto_code($string) {
		$red     = format_uri($string);
		$red_arr = explode('-', $red);
		$red_arr = array_map(function ($node) {
			return strtoupper(trim(substr($node, 0, 3)));
		}, $red_arr);


		return implode('',$red_arr);
	}

	function format_uri($string, $separator = '-') { // from tac-tac
		$charmap       = ['À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
		                  'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		                  'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
		                  'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
		                  'ß' => 'ss',
		                  'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
		                  'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		                  'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
		                  'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
		                  'ÿ' => 'y', '©' => '(c)'];
		$accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
		$special_cases = ['&' => 'et', "'" => ''];
		$string        = mb_strtolower(trim($string), 'UTF-8');
		$string        = str_replace(array_keys($charmap), $charmap, $string);
		$string        = str_replace(array_keys($special_cases), array_values($special_cases), $string);
		$string        = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
		$string        = preg_replace("/[^a-z0-9\\/]/u", "$separator", $string);
		$string        = preg_replace("/[$separator]+/u", "$separator", $string);

		return $string;
	}

	function niceUrl($text = '') {
		$text = str_replace('--', "-", $text);
		$text = str_replace('- -', "-", $text);
		$text = str_replace('*', "-", $text);
		$text = str_replace('(', "-", $text);
		$text = str_replace(')', "-", $text);
		$text = str_replace(' ', '-', trim(removeaccents($text)));
		$text = str_replace('\\', '-', trim($text));
		$text = str_replace('--', "-", $text);
		$text = str_replace('---', "-", $text);
		$text = str_replace("'", "-", $text);
		$text = str_replace(",", "-", $text);
		$text = str_replace("'", "-", $text);

//	$text = preg_replace("/(\r\n|\n|\r)/", " ", $text);
		return addslashes(strtolower(str_replace('/', '-', str_replace("'", '', trim($text)))));
	}

	function niceUrlSpace($text = '') {
		$text = niceUrl($text);

		return str_replace("-", " ", $text);
	}

	function noSpace($text = '') {
		$text = niceUrl($text);

		return str_replace("-", "", $text);
	}

	function multidimensional_search($parents, $searched) {
		if (empty($searched) || empty($parents)) {
			return false;
		}

		foreach ($parents as $key => $value) {
			$exists = true;
			foreach ($searched as $skey => $svalue) {
				$exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
			}
			if ($exists) {
				return $key;
			}
		}

		return false;
	}

	function makePassword($list = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", $chrs = 10) {
		$list = str_replace(CHR(32), "", trim($list));
		$list = preg_replace('/\s\s+/', ' ', $list);
		mt_srand((double)microtime() * 1000000);
		$newstring = "";
		while (strlen($newstring) < $chrs) {
			$newstring .= $list[mt_rand(0, strlen($list) - 1)];
		}

		return $newstring;
	}

	/**
	 *
	 */
	function nomAgent($id, $type = 'full') {
		$APP = new App('agent'); // verification des droits utilisateur
		$ARR = $APP->findOne(['idagent' => (int)$id]);
		switch ($type) :
			case'full':
				return $ARR['prenomAgent'] . ' ' . $ARR['nomAgent'];
				break;
			case'nom':
				return $ARR['nomAgent'];
				break;
			case'prenom':
				return $ARR['prenomAgent'];
				break;
			case'code':
				return $ARR['codeAgent'];
				break;
			case'login':
				return $ARR['loginAgent'];
				break;
			default:
				return $ARR['nomAgent'];
				break;
		endswitch;

	}

	function droit_table($idagent, $code, $table) // code = CRUD // rapport au groupe_agent
	{
		$APP    = new App('agent'); // verification des droits utilisateur // code =  $code.'_'.$table
		$APP_GD = new App('agent_groupe_droit'); // verification des droits utilisateur // code =  $code.'_'.$table
		$arr_ag = $APP->findOne(['idagent' => (int)$idagent]);

		$count = $APP_GD->find(['idagent_groupe' => (int)$arr_ag['idagent_groupe'], 'codeAppscheme' => $table, $code => true])->count();
		if ($count == 0) {
			return false;
		}

		return true;
	}

	function droit_table_multi($idagent, $code, $table = null) // code = CRUD // rapport au groupe_agent
	{
		$APP    = new App('agent'); // verification des droits utilisateur // code =  $code.'_'.$table
		$APP_GD = new App('agent_groupe_droit'); // verification des droits utilisateur // code =  $code.'_'.$table
		$arr_ag = $APP->findOne(['idagent' => (int)$idagent]);

		if (!empty($table)) {
			$count = $APP_GD->find(['idagent_groupe' => (int)$arr_ag['idagent_groupe'], 'codeAppscheme' => $table, $code => true])->count();
			if ($count == 0) {
				return false;
			}

			return $table;
		} else {
			$dist = $APP_GD->distinct_all('codeAppscheme', ['idagent_groupe' => (int)$arr_ag['idagent_groupe'], $code => true]);

			if (sizeof($dist) == 0) {
				return false;
			}

			return $dist;
		}
	}

	function droit($code) {
		//if ($code == 'ADMIN') return true;
		$APP = new App('agent'); // verification des droits utilisateur
		$arr = $APP->findOne(['idagent' => (int)$_SESSION['idagent'], 'droit_app.' . $code => 1]);
		if (empty($arr['idagent'])) {
			return false;
		}

		return true;
	}

	function removeaccents($texte) {
		//$texte = utf8_decode($texte);
		$texte = str_replace(['à',
			'â',
			'ä',
			'á',
			'ã',
			'å',
			'î',
			'ï',
			'ì',
			'í',
			'ô',
			'ö',
			'ò',
			'ó',
			'õ',
			'ø',
			'ù',
			'û',
			'ü',
			'ú',
			'é',
			'è',
			'ê',
			'ë',
			'ê',
			'&',
			strtoupper('&'),
			'ç',
			'ÿ',
			'ñ',
			'\'',
			'"',
			'_',
			'!',
			'?',
			'\\'], ['a',
			'a',
			'a',
			'a',
			'a',
			'a',
			'i',
			'i',
			'i',
			'i',
			'o',
			'o',
			'o',
			'o',
			'o',
			'o',
			'u',
			'u',
			'u',
			'u',
			'e',
			'e',
			'e',
			'e',
			'e',
			'e',
			'e',
			'c',
			'y',
			'n',
			'-',
			'-',
			'-',
			'-',
			'-',
			'-'], $texte);
		$texte = str_replace("--", "-", $texte);
		$texte = str_replace("\\", "-", $texte);

		//$texte = utf8_encode($texte);
		return stripslashes($texte);
	}

	function coupeChaine($chaine, $max = 20, $bl = ' ...') {
		if (strlen($chaine) >= $max) {
			// Met la portion de chaine dans $chaine
			$chaine = substr($chaine, 0, $max);
			// position du dernier espace
			$espace = strrpos($chaine, " ");
			// test si il ya un espace
			if ($espace) // si ya 1 espace, coupe de nouveau la chaine
			{
				$chaine = substr($chaine, 0, $espace);
			}
			// Ajoute ... à la chaine
			$chaine .= $bl;

			return $chaine;
		}

		return $chaine;
	}

	function coupeChaineMilieu($chaine, $max = 20) {
		if (strlen($chaine) >= $max) {
			$len = mb_strlen($chaine);
			$deb = mb_substr($chaine, 0, ($max / 2), 'UTF-8');
			$fin = mb_substr($chaine, -($max / 2), $len, 'UTF-8');

			return $deb . '...' . $fin;
		}

		return $chaine;
	}

	function NbTrimestres($debut, $fin) {
		if (empty($fin)) {
			$fin = $debut;
		}
		$tDeb = explode("-", $debut);
		$tFin = explode("-", $fin);
		$diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) - mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);

		return (int)((($diff / 86400) + 1) / 90);
	}

	function quoteStrSearch(&$value, $key) {
		$value = trim($value);
		$value = "'" . $value . "'";
	}

	function quoteStr(&$value, $key) {
		$value = trim($value);
		$value = "'" . $value . "'";
	}

	function cleanStr(&$value, $key = '', $userdata = null) {
		if (is_array($value) || is_object($value)) {
			array_walk_recursive($value, 'CleanStr', $value);

			return;
		}
		$value = trim($value);
		if (stristr($value, '/')) {
			$arrTest = explode('/', $value);
			if (is_numeric($arrTest[0]) && is_numeric($arrTest[1])) {
				$value = date_mysql($value);
			}
		}
	}

	function BoolStr(&$value, $key = '') {
		$value = trim($value);
		if ($value == "true") {
			$value = 1;
		}
		if ($value == "false") {
			$value = 0;
		}
	}

	function cleanPostaction(&$value) {
		$value = trim($value);
	}

	function sqlSearchDebug($param, $param2 = '') {
		// echo $param,$param2;
		//$param = trim($param);
		if (is_array($param)) {
			// tablo de int ou pas ?
			if (!is_numeric($param[0])) {
				array_walk_recursive($param, 'quoteStrSearch', $param);
				$test = implode(",", $param);

				return "  MATCH (title,body) AGAINST ('$param2') ";

				return " IN (" . $test . ")";
			} else {

				return "  MATCH (title,body) AGAINST ('$param2') ";
				$test = implode(",", $param);

				return " IN (" . $test . ")";
			}
		}
		if (!empty($param2)) {
			$pos  = strpos($param2, "id");
			$pos2 = strpos($param2, "num");
			$pos3 = strpos($param2, "nom");
			$pos4 = strpos($param2, "code");
			if ($pos2 !== false || $pos3 !== false || $pos4 !== false) {
				return "LIKE '%" . trim($param) . "%'";
			}
			if ($pos !== false) {
				return "= '" . trim($param) . "'";
			}
		}
		$param = str_replace('-', ',', $param);

		return "  MATCH ($param2) AGAINST ('$param') "; // IN BOOLEAN MODE
		if (!is_numeric($param)) {
			return "LIKE '%" . trim($param) . "%'";
		}

		return "='" . trim($param) . "'";
	}

	function sqlSearch($param, $param2 = '') {
		//$param = trim($param);
		if (is_array($param)) {
			// tablo de int ou pas ?
			if (!is_numeric($param[0])) {
				array_walk_recursive($param, 'quoteStrSearch', $param);
				$test = implode(",", $param);

				return " IN (" . $test . ")";
			} else {

				$test = implode(",", $param);

				return " IN (" . $test . ")";
			}
		}
		if (!empty($param2)) {
			$pos  = strpos($param2, "id");
			$pos2 = strpos($param2, "num");
			$pos3 = strpos($param2, "nom");
			$pos4 = strpos($param2, "code");
			if ($pos2 !== false || $pos3 !== false || $pos4 !== false) {
				return "LIKE '%" . trim($param) . "%'";
			}
			if ($pos !== false) {
				return "= '" . trim($param) . "'";
			}
		}
		if (!is_numeric($param)) {
			return "LIKE '%" . trim($param) . "%'";
		}

		return "='" . trim($param) . "'";
	}

	function sqlIn($param) {
		$testArray = (array)$param;
		if (empty($testArray)) {
			//echo '.';
		};
		if (is_array($param)) {
			array_walk_recursive($param, 'quoteStr', $param);
			$test = implode(",", $param);

			return " IN (" . $test . ")";
		}
		if (!is_numeric($param)) {
			return "LIKE '" . $param . "'";
		}

		return "='" . $param . "'";
	}

	function sqlNotIn($param) {
		if (is_array($param)) {
			if (sizeof($param) != 0) {
				array_walk_recursive($param, 'quoteStr', $param);
				$test = implode(",", $param);

				return "NOT IN (" . $test . ")";
			} else {
				$param = '';
			}
		}

		//if(!is_numeric($param)){ return "LIKE '".$param."'"; }
		return "<>'" . $param . "'";
	}

	function niceHome($home) {
		return str_replace($_SERVER["DOCUMENT_ROOT"] . '/' . APPDIR, '', $home);
	}

	function moduleAction() {
		return 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
	}

/////////////////////////////////////
// Decoupe d'un champ de recherche (espaces)
	function dekoupSearch($string) {
		preg_match_all('@["][^"]+["]|[\S^"]+@', stripslashes($string), $m);
		$m = str_replace('"', '', $m[0]);
		foreach ($m as $k => $v) {
			$m[$k] = addslashes(trim($v));
		}

		return array_unique($m);
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION RETOUR UNIQUE

	function nomPersonne($idpersonne) {
		if (empty($idpersonne)) {
			return "";
		}
		$ClassPersonne = new Personne();
		$rs            = $ClassPersonne->getOnePersonne(['idpersonne' => $idpersonne]);
		$arr           = $rs->fetchRow();

		return $arr['prenomPersonne'] . " " . $arr['nomPersonne'];
	}

	function mois_fr($date_origine) {
		$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
		if ($date_origine != "") {
			$tmp_final_date = "";
			$tmpdate        = explode("-", $date_origine);
			for ($i = (count($tmpdate) - 1); $i >= 0; $i--) {
				$tmp_final_date .= $tmpdate[$i];
				if ($i > 0) {
					$tmp_final_date .= "/";
				}
			}
			if ($tmp_final_date == '00/00/0000') {
				return '';
			}

			return $tabmonth[(int)date('m', strtotime($date_origine))];
		}
	}

	function maskTel($tel) {
		if (empty($tel)) {
			return '';
		}
		if (strlen($tel) <= 9) {
			$tel = '0' . $tel;
		}
		$tel = str_replace(' ', '', $tel);
		$tel = str_replace('.', '', $tel);
		$tel = str_replace('-', '', $tel);
		$tel = str_replace('/', '', $tel);
		$tel = strrev(chunk_split(strrev($tel), 2, ' '));
// $tel = str_replace('0 03','003',$tel);
// $tel = str_replace('003 77','00377',$tel);
		return $tel;
	}

	function maskHeure($tel) {
		if (empty($tel)) {
			return $tel;
		}
		if (!is_int($tel)) return $tel;
		$arrtel = explode(':', $tel);
		$ret    = (int)$arrtel[0] . ":" . $arrtel[1];

		return $ret;
	}

	function maskSize($bytes, $precision = 2) {
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;

		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';

		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KB';

		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MB';

		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GB';

		} elseif ($bytes >= $terabyte) {
			return round($bytes / $terabyte, $precision) . ' TB';
		} else {
			return $bytes . ' B';
		}
	}

	function array_to_string($array) {
		$retval = '';
		foreach ($array as $index => $value) {
			$retval .= urlencode(base64_encode($index)) . '|' . urlencode(base64_encode($value)) . '||';
		}

		return urlencode(substr($retval, 0, -2));
	}

	function string_to_array($string) {

		$retval    = [];
		$string    = urldecode($string);
		$tmp_array = explode('||', $string);

		if (is_array($tmp_array)) {
			foreach ($tmp_array as $tmp_val) {
				if (stristr($tmp_val, '|')) {
					list($index, $value) = explode('|', $tmp_val);
					$retval[base64_decode(urldecode($index))] = base64_decode(urldecode($value));
				}
			}
		}

		return $retval;
	}

	function createThumbnail($fileSrc, $thumbDest, $thumb_width = 160, $thumb_height = 120, $forcewidth = false) {
		$ext = strtolower(substr($fileSrc, strrpos($fileSrc, ".")));
		if ($ext == ".png") {
			$base_img = ImageCreateFromPNG($fileSrc);
		} else {
			if (($ext == ".jpeg") || ($ext == ".jpg")) {
				$base_img = ImageCreateFromJPEG($fileSrc);
			}
		}

		// If the image is broken, skip it?
		if (!$base_img) {
			return false;
		}

		// Get image sizes from the image object we just created
		$img_width  = imagesx($base_img);
		$img_height = imagesy($base_img);
		// Work out which way it needs to be resized
		$thumb_height   = empty($thumb_height) ? $thumb_width * $img_height / $img_width : $thumb_height;
		$img_width_per  = $thumb_width / $img_width;
		$img_height_per = $thumb_height / $img_height;

		if ($img_width_per <= $img_height_per || $forcewidth == true) {
			$thumb_width  = $thumb_width;
			$thumb_height = intval($img_height * $img_width_per);
		} else {
			$thumb_width  = intval($img_width * $img_height_per);
			$thumb_height = $thumb_height;
		}
		$thumb_img = ImageCreateTrueColor($thumb_width, $thumb_height);
		ImageCopyResampled($thumb_img, $base_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $img_width, $img_height);

		if ($ext == ".png") {
			ImagePNG($thumb_img, $thumbDest);
		} else {
			if (($ext == ".jpeg") || ($ext == ".jpg")) {
				ImageJPEG($thumb_img, $thumbDest);
			}
		}
		// Clean up our images
		ImageDestroy($base_img);
		ImageDestroy($thumb_img);

		return true;
	}

	function cf_output($str, $replace = '') {
		if ($replace == '') {
			$replace = idioma('...');
		}
		if (trim($str) == '') {
			return "<span class='textgris'>" . $replace . "</span>";
		}

		return $str;
	}

	function ouiNon($val = '') {
		if ($val == '' || $val == '0') {
			return 'non';
		}

		return 'oui';
	}

	function checked($val = '') {
		if ($val == '' || $val == '0' || empty($val) || $val == false || $val === 'false') { //
			return ' ';
		}

		return " checked='checked' ";
	}

	function selected($val = '') {
		if ($val == '' || $val == '0') {
			return ' ';
		}

		return " selected='selected' ";
	}

	function br2nl($string) {
		return preg_replace('#<br\s*/?-->#i', "\n", $string);
	}

	function convert_to_csv($input_array, $output_file_name, $delimiter) {
		/** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
		$f = fopen('php://memory', 'w');
		/** loop through array  */
		foreach ($input_array as $line) {
			/** default php csv handler **/
			fputcsv($f, $line, $delimiter);
		}
		/** rewrind the "file" with the csv lines **/
		fseek($f, 0);
		/** modify header to be downloadable csv file **/
		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
		/** Send file to browser for download */
		fpassthru($f);
	}

	function execInBackground($cmd) {
		if (substr(php_uname(), 0, 7) == "Windows") {
			pclose(popen("start /B " . $cmd, "r"));
		} else {
			exec($cmd . " > /dev/null &");
		}
	}

	function recursiveDirectoryIterator($directory = null, $files = []) {
		$iterator = new \DirectoryIterator ($directory);

		foreach ($iterator as $info) {
			if ($info->isFile()) {
				$files [$info->__toString()] = $info;
			} elseif (!$info->isDot()) {
				$list = [$info->__toString() => recursiveDirectoryIterator(
					$directory . DIRECTORY_SEPARATOR . $info->__toString()
				)];
				if (!empty($files))
					$files = $files;// array_merge_recursive($files, $filest);
				else {
					$files = $list;
				}
			}
		}

		return $files;
	}

	function mostRecentModifiedFileTime($dirName, $doRecursive, $exclude = []) {
		$d            = dir($dirName);
		$lastModified = 0;
		while ($entry = $d->read()) {
			if ($entry != "." && $entry != "..") {
				if (in_array($entry, $exclude)) continue;
				if (!is_dir($dirName . "/" . $entry)) {
					$currentModified = filemtime($dirName . "/" . $entry);
				} else if ($doRecursive && is_dir($dirName . "/" . $entry)) {
					$currentModified = mostRecentModifiedFileTime($dirName . "/" . $entry, true);
				}
				if ($currentModified > $lastModified) {
					$lastModified = $currentModified;
				}
			}
		}
		$d->close();

		return $lastModified;
	}

	function hex2rgb($hex, $op = 1) {
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3) {
			$r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
		} else {
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}
		$rgb = [$r, $g, $b, $op];

		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}

	function rgb2hex($rgb) {
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		return $hex; // returns the hex value including the number sign (#)
	}