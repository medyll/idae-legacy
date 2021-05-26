<?

	class AppTemplate {

		static function cf_template($tpl, $datas = [], $block = '') {
			$a = get_defined_constants(true);
			/*var_dump($a['user']);*/
			$datas += $a['user'];

			$tempPost = $_POST;
			if (trim($tpl == '')) {
				return '';
			}
			ob_start();
			//  echo '<tpl tpl="$tpl.$block"></tpl>';
			if (file_exists(APPBINTPL . '/' . $tpl . '.latte')) {
				$file = APPBINTPL . '/' . $tpl . '.latte';

				$latte = new Latte\Engine();
				$latte->setTempDirectory(APPTPL . 'cache/');
				$latte->addFilter('dump', function ($s) {
					return vardump($s, 1);
				});

				if (!empty($block)) {
					$latte->render($file, $datas, $block);
				} else {
					$latte->render($file, $datas);
				}
			} else {
				echo "missing template " . APPTPL . '/' . $tpl . '.latte';
			}
			$final = ob_get_contents();
			ob_end_clean();
			flush();
			ob_flush();

			return stripslashes($final);
		}

		function _construct() {

		}
	} ?>