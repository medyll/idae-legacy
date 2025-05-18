<?
	include_once($_SERVER['CONF_INC']);

	$PATH      = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
	$local_rep = XMLDIR . 'costa/';

	ini_set('display_errors' , 55);

	$APP             = new App();
	$APP_F           = new App('fournisseur');
	$ARR_FOURNISSEUR = $APP_F->findOne(array( 'codeFournisseur' => 'COSTA' ));

	$idfournisseur = (int)$ARR_FOURNISSEUR['idfournisseur'];

	$ns = 'http://schemas.costacrociere.com/WebAffiliation';

	skelMdl::run($PATH.'notify', ['mdl'  => $PATH.'notify' ,
	                           'method' => 'POST',
	                           'delay'  => 5000]);


	$cddata = <<<XML
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://schemas.costacrociere.com/WebAffiliation">
	   <soapenv:Header><web:Partner><web:Name>IST_MaW</web:Name><web:Password>MaW</web:Password></web:Partner>
	      <web:Agency><web:Code>24514717</web:Code></web:Agency></soapenv:Header>
	   <soapenv:Body>__body</soapenv:Body>
	</soapenv:Envelope>
XML;


	$WSDL = 'http://training.costaclick.net/WAWS_1_9/Availability.asmx?WSDL';

	//
	$arr_progr = ['progress_parent'  => 'run_ftp_costa',
	              'progress_name'    => 'xml_job_ftp_costa',
	              'progress_text'    => " XML .   ftp ",
	              'progress_message' => $i . ' / ' . $total,
	              'progress_max'     => $total,
	              'progress_value'   => $i];
	//


	// ListAvailableCruises
	$APP_TMP = $APP->plug('sitebase_csv', 'costa_xml_cruise');
	$APP_TMP->drop();
	$body   = "<web:ListAvailableCruises><web:from>2017-12-04T02:00:00+02:00</web:from><web:to>2017-12-30T02:00:00+02:00</web:to></web:ListAvailableCruises>";
	$action = 'ListAvailableCruises';
	$result = do_xml_action($WSDL , $body , $action);
	foreach ($result->ListAvailableCruisesResponse->ListAvailableCruisesResult->children() as $child) {
		// echo $child->Code ;  echo $child->Description;echo $child->AdditionalInfo ;
		// echo "<hr>";
		$arr_progr['progress_log'] = $child->Code;
		skelMdl::send_cmd('act_progress', $arr_progr, session_id());
		$APP_TMP->insert($child);

	}
	// destinations
	$APP_TMP = $APP->plug('sitebase_csv', 'costa_xml_destination');
	$APP_TMP->drop();
	$body   = '<web:ListAllDestinations/>';
	$action = 'ListAllDestinations';
	$result = do_xml_action($WSDL , $body , $action);
	foreach ($result->ListAllDestinationsResponse->ListAllDestinationsResult->children() as $child) {
		// echo $child->Code ;  echo $child->Description;echo $child->AdditionalInfo ;
		// echo "<hr>";
		$arr_progr['progress_log'] = $child->Code;
		skelMdl::send_cmd('act_progress', $arr_progr, session_id());
		$APP_TMP->insert($child);

	}

	// ports
	$APP_TMP = $APP->plug('sitebase_csv', 'costa_xml_port');
	$APP_TMP->drop();
	$body   = '<web:ListAllPorts/>';
	$action = 'ListAllPorts';
	$result = do_xml_action($WSDL , $body , $action);
	foreach ($result->ListAllPortsResponse->ListAllPortsResult->children() as $child) {
		// echo $child->Code ;  echo $child->Description;echo $child->AdditionalInfo ;
		$APP_TMP->insert($child);

		$arr_progr['progress_parent'] =  'run_ftp_costa';
		$arr_progr['progress_name'] =  'xml_job_ftp_port_costa';

		$arr_progr['progress_log'] = $child->Code;
		skelMdl::send_cmd('act_progress', $arr_progr, session_id());
	}

	// ports again
	$body   = '<web:ListPorts/>';
	$action = 'ListPorts';
	$result = do_xml_action($WSDL , $body , $action);
	foreach ($result->ListPortsResponse->ListPortsResult->children() as $child) {
		//echo $child->Code ;  echo $child->Description;echo $child->AdditionalInfo ;
		//echo "<hr>";
	}

	// ships
	$APP_TMP = $APP->plug('sitebase_csv', 'costa_xml_ship');
	$APP_TMP->drop();
	$body   = '<web:ListAllShips/>';
	$action = 'ListAllShips';
	$result = do_xml_action($WSDL , $body , $action);

	foreach ($result->ListAllShipsResponse->ListAllShipsResult->children() as $child) {
		echo($child->Code);
		$APP_TMP->insert($child);

		/*{
			Code
   Name
   URL
  AdditionalInfo
    Cabins
   Crew
   Guests
 Width
 Length
  Tonnage
 MaxSpeed
   YearOfLaunch  */
		echo "<hr>";
	}



	function do_xml_action ($WSDL , $body , $action) {
		global $cddata;
		$cddata = str_replace('__body' , $body , $cddata);

		try {
			$soapClient = new SoapClient($WSDL , array( 'trace' => 1 , 'exceptions' => 0 , 'connection_timeout' => 3600 ));
		} catch (Exception $e) {
			echo $e->getMessage(); // SoapFault
		}

		$test = $soapClient->__doRequest($cddata , $WSDL , 'http://schemas.costacrociere.com/WebAffiliation/' . $action , '1.1');

		$clean_xml = str_ireplace([ 'SOAP-ENV:' , 'SOAP:' ] , '' , $test);
		$xml       = simplexml_load_string($clean_xml);

		return $result = $xml->Body;
	}

?>
