<?
	include_once($_SERVER['CONF_INC']);
	const JOBS = 8;
	ini_set("soap.wsdl_cache_enabled", 0);
	ini_set("soap.wsdl_cache_enabled", WSDL_CACHE_NONE);
	if (empty($_POST['run'])) {
		return;
	}
	//
	$PATH      = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
	$local_rep = XMLDIR . 'costa/';

	ini_set('display_errors', 55);

	$APP_F           = new App('fournisseur');
	$ARR_FOURNISSEUR = $APP_F->findOne(['codeFournisseur' => 'COSTA']);

	$idfournisseur = (int)$ARR_FOURNISSEUR['idfournisseur'];
	//
	$arr_progress = ['progress_parent'  => 'run_costa_catalog',
	                 'progress_name'    => 'xml_job_ftp_costea',
	                 'progress_text'    => " XML ",
	                 'progress_message' => "0 / " . JOBS,
	                 'progress_max'     => JOBS,
	                 'progress_value'   => 0];
	 skelMdl::send_cmd('act_progress', $arr_progress);

	flush();
	ob_flush();

	$ns = 'http://schemas.costacrociere.com/WebAffiliation';

	/*$WSDL     = 'http://training.costaclick.net/WAWS_1_9/Export.asmx?WSDL'; // training
	$Name     = 'IST_MaW';
	$Password = 'MaW';
	$Code     = '24514717';*/

	/*	$cddata = <<<XML
		<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soapenv:Header>
				<Partner xmlns="http://schemas.costacrociere.com/WebAffiliation"><Name>$Name</Name><Password>$Password</Password></Partner>
				<Agency xmlns="http://schemas.costacrociere.com/WebAffiliation"><Code>$Code</Code></Agency>
			</soapenv:Header>
			<soapenv:Body>__body</soapenv:Body>
		</soapenv:Envelope>
	XML;*/

	$WSDL     = 'http://www.costaclick.net/WAWS_1_9/Export.asmx?WSDL';
	$Name     = 'CroisieresMAW';
	$Password = 'Cr01s13r3sMAW';
	$Code     = '24514717';

	$cddata = <<<XML
	<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" >
		<soap:Header>
			<Partner xmlns="http://schemas.costacrociere.com/WebAffiliation"><Name>$Name</Name><Password>$Password</Password></Partner>
			<Agency xmlns="http://schemas.costacrociere.com/WebAffiliation"><Code>$Code</Code><Culture /></Agency>
		</soap:Header>
		<soap:Body>__body</soap:Body>
	</soap:Envelope>
XML;

	if (empty($_POST['delay'])) {
		$_SESSION['count_jobs'] = 0;
		@chmod($local_rep,777);
		$fp = fopen($local_rep.'data.txt', 'w');
		fwrite($fp, '0');
		fclose($fp);
		$count_jobs = file_get_contents($local_rep.'data.txt');

		// ExportPriceWithPaxBreakdown
		$body       = '<ExportPriceWithPaxBreakdown xmlns="http://schemas.costacrociere.com/WebAffiliation"><destinationCode /><expPriceType>Basic</expPriceType><maxOccupancy>TwoPax</maxOccupancy></ExportPriceWithPaxBreakdown>';
		$action     = 'ExportPriceWithPaxBreakdown';
		$result     = do_xml_action($WSDL, $body, $action);
		$url_prices = $result->ExportPriceWithPaxBreakdownResponse->ExportPriceWithPaxBreakdownResult;
		go_delay('prices_costa', $url_prices);
		echo "<hr>";
		// ExportCatalog
		$body        = '<ExportCatalog/>';
		$action      = 'ExportCatalog';
		$result      = do_xml_action($WSDL, $body, $action);
		$url_catalog = $result->ExportCatalogResponse->ExportCatalogResult;
		go_delay('catalog', $url_catalog);
		echo "<hr>";
		// ExportPorts
		$body     = '<ExportPorts/>';
		$action   = 'ExportPorts';
		$result   = do_xml_action($WSDL, $body, $action);
		$url_port = $result->ExportPortsResponse->ExportPortsResult;
		go_delay('ports', $url_port);
		echo "<hr>";
		// ExportAvailability
		$body             = '<ExportAvailability/>';
		$action           = 'ExportAvailability';
		$result           = do_xml_action($WSDL, $body, $action);
		$url_availability = $result->ExportAvailabilityResponse->ExportAvailabilityResult;
		go_delay('availability', $url_availability);
		echo "<hr>";
		// ExportShipsAndCategories
		$body      = '<ExportShipsAndCategories/>';
		$action    = 'ExportShipsAndCategories';
		$result    = do_xml_action($WSDL, $body, $action);
		$url_ships = $result->ExportShipsAndCategoriesResponse->ExportShipsAndCategoriesResult;
		go_delay('ships', $url_ships);
		echo "<hr>";
		// ExportItineraryAndSteps
		$body    = '<ExportItineraryAndSteps/>';
		$action  = 'ExportItineraryAndSteps';
		$result  = do_xml_action($WSDL, $body, $action);
		$url_iti = $result->ExportItineraryAndStepsResponse->ExportItineraryAndStepsResult;
		go_delay('iti', $url_iti);
		echo "<hr>";
		$body       = '<ExportPriceWithPaxBreakdown xmlns="http://schemas.costacrociere.com/WebAffiliation"><destinationCode /><expPriceType>Promo</expPriceType><maxOccupancy>TwoPax</maxOccupancy></ExportPriceWithPaxBreakdown>';
		$action     = 'ExportPriceWithPaxBreakdown';
		$result     = do_xml_action($WSDL, $body, $action);
		$url_prices = $result->ExportPriceWithPaxBreakdownResponse->ExportPriceWithPaxBreakdownResult;
		go_delay('prices_costa_promo', $url_prices);
		echo "<hr>";
		$body       = '<ExportPriceWithPaxBreakdown xmlns="http://schemas.costacrociere.com/WebAffiliation"><destinationCode /><expPriceType>Costa</expPriceType><maxOccupancy>TwoPax</maxOccupancy></ExportPriceWithPaxBreakdown>';
		$action     = 'ExportPriceWithPaxBreakdown';
		$result     = do_xml_action($WSDL, $body, $action);
		$url_prices = $result->ExportPriceWithPaxBreakdownResponse->ExportPriceWithPaxBreakdownResult;
		go_delay('prices_costa_costa', $url_prices);
		echo "<hr>";

	} else {
		clearstatcache();
		$headers  = get_headers($_POST['url'], true);
		$filesize = $headers['Content-Length'];

		vardump_async(['url' => $_POST['url'], 'file' => $_POST['file'], 'filesize' => $filesize]);

		go_delay($_POST['file'], $_POST['url']);
	}

	function go_delay($file, $url) {
		//
		clearstatcache();
		if (empty($url)) {
			vardump_async("url_vide", true);

			return;
		}
		$url = trim($url);

		global $arr_progress;
		global $PATH;

		$arr_progress['progress_log']     = "test $file";
		skelMdl::send_cmd('act_progress', $arr_progress);

		$extracted_filename = end(explode('/', $url));
		vardump_async("go_delay $extracted_filename ");

		$url      = trim($url);
		$headers  = get_headers($url, true);
		$filesize = (int)$headers['Content-Length'];
		sleep(2);
		$headers_control  = get_headers($url, true);
		$filesize_control = (int)$headers_control['Content-Length'];

		vardump_async(['url' => $url, 'file' => $file, 'filesize' => $filesize]);

		if ($filesize !== 0 && $filesize == $filesize_control) {

			$arr_progress['progress_log']     = "test ok $file";
			skelMdl::send_cmd('act_progress', $arr_progress);

			go_unzip($file, $url);
			vardump_async(["end and zip - $file - $url"], true);

			return;
		} else {
			$arr_progress['progress_log']     = "test restart 10s $file";
			skelMdl::send_cmd('act_progress', $arr_progress);

			vardump_async(['retry in 10s / ' . $filesize . '-' . $filesize_control . '/' . $file]);
			// retry in 10 secondes
			$run_var = ['file'  => $file,
			            'url'   => $url,
			            'run'   => 1,
			            'delay' => 10000];

			skelMdl::run($PATH . 'ftp/costa_catalog', $run_var);
		}

		return;

	}

	function go_unzip($file, $url) {

		clearstatcache();



		$url = trim($url);
		if (empty($url)) {
			return;
		}
		global $local_rep;
		global $PATH;
		global $arr_progress;


		$count_jobs = file_get_contents($local_rep.'data.txt');
		/*@chmod($local_rep,777);
		$fp = fopen($local_rep.'data.txt', 'w');
		fwrite($fp, '1');*/

		$arr_progress['progress_log']     = "start zip $file";
		$arr_progress['progress_message'] = $_SESSION['count_jobs'] . " / " . JOBS;
		skelMdl::send_cmd('act_progress', $arr_progress);

		$extracted_filename     = end(explode('/', $url));

		vardump_async("go_unzip <br> $extracted_filename <br> $file <br> $url <br> ");

		if ($fp = fopen($url, "r")) {
			if ($pointer = fopen($local_rep . $file . '.zip', "wb+")) {
				while ($buffer = fread($fp, 1024)) {
					if (!fwrite($pointer, $buffer)) {
						echo "bad";
						// return FALSE;
					}
				}
				fclose($pointer);
			}
		} else {
			return FALSE;
		}
		fclose($fp);
		$zip = new ZipArchive;
		$zip->open($local_rep . $file . '.zip');
		$zip_filename = $zip->getNameIndex(0);
		$zip->extractTo($local_rep, [$zip_filename]);
		$zip->close();
		//
		if (file_exists($local_rep . $file . '.xml')) unlink($local_rep . $file . '.xml');

		rename($local_rep . $zip_filename, $local_rep . $file . '.xml');
		chmod($local_rep . $file . '.xml', 0777);
		unlink($local_rep . $file . '.zip');

		++$_SESSION['count_jobs'];
		$fp = fopen($local_rep.'data.txt', 'w');
		fwrite($fp, ++$count_jobs);
		fclose($fp);

		$arr_progress['progress_value']   = $count_jobs;
		$arr_progress['progress_log']     = "end zip $file";
		$arr_progress['progress_message'] = $count_jobs . " / " . JOBS;
		skelMdl::send_cmd('act_progress', $arr_progress);

		if ($count_jobs == JOBS) {
			vardump_async(JOBS . "  END => villes ", true);
			$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
			skelMdl::run($PATH . 'read/readcosta_ville', ['file'  => 'red',
			                                              'url'   => 'la suite et fin ',
			                                              'delay' => 10,
			                                              'run'   => 1]);
		}
	}

	function do_xml_action($WSDL, $body, $action) {
		global $cddata;
		global $arr_progress;

		$arr_progress['progress_log'] = "soap : $action";
		skelMdl::send_cmd('act_progress', $arr_progress);
		clearstatcache();
		$cddata = trim(str_replace('__body', $body, $cddata));

		try {
			$soapClient = new SoapClient($WSDL, ['trace' => 1, 'exceptions' => 0, 'connection_timeout' => 3600]);
		} catch (Exception $e) {
			echo "soapFault";
			echo $e->getMessage(); // SoapFault
		}

		$test = $soapClient->__doRequest($cddata, $WSDL, 'http://schemas.costacrociere.com/WebAffiliation/' . $action, '1.1');

		$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $test);
		$xml       = simplexml_load_string($clean_xml);
		if (null !== $xml->Body->$action . 'Response') {
			// print_r($xml);
		}
		$arr_progress['progress_log'] = "soap : $action : completed";
		skelMdl::send_cmd('act_progress', $arr_progress);

		return $result = $xml->Body;
	}