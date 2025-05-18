<?php

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/08/2017
	 * Time: 20:43
	 */
	class Router extends AltoRouter {

		function __construct() {
			parent::__construct();
			$this->do_match();
		}

		function do_match() {

			$this->addRoutes($this->routes());

			//

			//
			$match = $this->match();

			//   Helper::dump($match);

			if ($match) {
				if (is_string($match['target']) && strpos($match['target'], '#') !== false) {
					$is_cl = explode('#', $match['target']);

					if (sizeof($is_cl) == 2) {
						$cl   = new $is_cl[0]();
						$meth = $is_cl[1];
						$cl->$meth($match['params']);
					}
				} else if (is_callable($match['target'])) {

					call_user_func_array($match['target'], $match['params']);
				} else {
					// no route was matched
					echo "404";
					header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
				}
			}
		}

		public function routes() {
			return [
				['POST', '/demo/[*:action]/[*:value]', 'Demo#do_action', 'demo_value_exec'],
				['POST', '/demo/[*:action]', 'Demo#do_action', 'demo_exec'],

				['GET', '/json_services/[*:file]', function ($file) {
					include_once('bin/services/' . $file . '.php');
				}],
				['GET', '[*:action]grttr', function ($file) {
					echo "bla $file";
				}],
				['GET|POST', '/idae/module/[*:module]/[*:http_vars]', function ($module, $http_vars) {
					$idae = new Idae();
					if (is_string($http_vars)) {

						$tmp = explode('/', $http_vars);
						if (sizeof($tmp) > 1) {
							$new_http_vars = end($tmp);
							array_pop($tmp);
							$module    = $module . '/' . implode('/', $tmp);
							$http_vars = $new_http_vars;
						}
						parse_str($http_vars, $arr_http_vars);
					}
					if (!empty($http_vars['table'])) {
						$idae->set_table($http_vars['table']);
						$name_id = "id" . $http_vars['table'];
					}
					if (!empty($name_id) && !empty($http_vars[$name_id]) && !is_int($http_vars[$name_id])) {
						$arr_idae            = $idae->findOne(['_id' => new MongoId($http_vars[$name_id])]);
						$http_vars[$name_id] = (int)$arr_idae[$name_id];
					}
					echo $idae->module($module, $http_vars);
				}],
				['GET', '/idae/[*:module]/[*:table]/[*:id]', function ($module, $table, $id) {
					$idae = new Idae($table);
					$id   = trim($id);
					if ((int)$id != $id) {
						$arr_idae = $idae->findOne(['_id' => new MongoId($id)]);
						$id       = (int)$arr_idae["id$table"];
					}
					if (method_exists($idae, $module)) {
						$idae->{$module}($id);
					} else {
						echo json_encode(['responseCode' => '404',
						                  'errorMessage' => 'Not found',
						                  'result'       => '']);
					}
				}],

				['POST', '/idae_action/[*:action]/[*:table]/[*:id]', function ($action, $table, $id) {

					$idae = new IdaeAction($table);
					if (method_exists($idae, $action)) {
						$vars = (empty($_POST['vars'])) ? [] : $_POST['vars'];
						$idae->{$action}($id, $vars);
					} else {
						echo json_encode(['responseCode' => '404',
						                  'errorMessage' => 'Action Not found ' . $action,
						                  'result'       => '']);
					}
				}],
				['POST', '/idae_action/[*:action]/[*:table]', function ($action, $table) {

					$idae = new IdaeAction($table);
					if (method_exists($idae, $action)) {
						$vars = (empty($_POST['vars'])) ? [] : $_POST['vars'];
						$idae->{$action}($vars);
					} else {
						echo json_encode(['responseCode' => '404',
						                  'errorMessage' => 'Action Not found ' . $action,
						                  'result'       => '']);
					}
				}]
			];
		}

		static function build_route($type = 'index', $params = [], $query_vars = []) {

			$link = '';
			switch ($type) {
				case 'restaurant':
					$APP        = new App('shop');
					$query_vars = $APP->findOne($params, ['_id' => 0]);

					$link = 'restaurant/' . $query_vars['villeShop'] . '/' . $query_vars['nomSecteur'] . '/' . $query_vars['nomShop'];
					break;
				case 'restaurants':
					/*$APP        = new App('shop');
					$query_vars = $APP->findOne($params, ['_id' => 0]);

					$link = 'restaurant/' . $query_vars['villeShop'] . '/' . $query_vars['nomSecteur'] . '/' . $query_vars['nomShop'];*/

					break;
			}

			return Router::format_uri($link);
		}

		static function format_uri($string, $separator = '-') {
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
	}