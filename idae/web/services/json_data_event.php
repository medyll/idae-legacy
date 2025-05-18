<?

	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);
die();
	$APP = new App();

	$APPCACHE = $APP->plug('sitebase_sockets','data_activity');
	$APPCACHE->ensureIndex(['timeData_activity'=>1]);
	$APPCACHE->ensureIndex(['timeData_activity'=>-1]);
	$APPCACHE->ensureIndex(['codeData_activity'=>1]);
	$APPCACHE->ensureIndex(['codeData_activity'=>-1]);

	$APPCACHE->remove(['timeData_activity'=>['$lte'=>(time()-30)]]);


	ini_set('output_buffering', 'off');
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	header('Content-Encoding: none;');
	header('Connection: Keep-alive;');
	ini_set('zlib.output_compression', false);
	set_time_limit(0);

	$session_id = session_id();
	// session_write_close();
	//
	$time = time();


	ob_flush();
	flush();
	/**
	 * Constructs the SSE data format and flushes that data to the client.
	 *
	 * @param string $id Timestamp/id of this connection.
	 * @param string $msg Line of text that should be transmitted.
	 */
	function sendMsg($id , $msg) {
		ini_set('implicit_flush', true);
		ob_implicit_flush(true);
		echo "id: ".uniqid() . PHP_EOL;
		echo "event: $id" . PHP_EOL;
		echo "data: {\n";
		echo "data: \"msg\": $msg  \n";
		echo "data: }\n";
		echo PHP_EOL;
		ob_flush();
		flush();
	}

//	skelMdl::send_cmd('act_notify', array('msg'=>'Hello '),$session_id);

	$notify = 1;

	if(empty($_SESSION['idagent'])){
		$notify = 0;
		sendMsg('message','waiting ...');
		skelMdl::send_cmd('act_notify', array('msg'=>'En attente d\'identification '),$session_id);
		 sleep(5);
		//die();
	}else{

		skelMdl::send_cmd('act_notify',array('msg'=>'Agent connecté '),$session_id);

	}

	$startedAt = time();
	do {

		$TEST = $APPCACHE->find(['timeData_activity'=>['$gte'=>$startedAt]]);
		if($TEST->count()!=0){
			// event : act_upd_data
			// data : json
			$startedAt = time();
			// skelMdl::send_cmd('act_notify',  array('msg'=>'nouvelle update '.$session_id),$session_id);
			while($ARR = $TEST->getNext()){
				unset($ARR['_id']);
				$json = json_encode($ARR,JSON_FORCE_OBJECT);
				sendMsg($ARR['eventData_activity'] ,$json);
			}
			sleep(1);
		}
		if ((time() - $startedAt) > 3600) {
			// skelMdl::send_cmd('act_notify',['options'=>['sticky'=>'true']]+ array('msg'=>'DIE FOR RELOAD'.$session_id),$session_id);
			// die();
		}

		//usleep(250000); // 1/4 seconde
        sleep(1);
		if(!empty($_SESSION['idagent']) && $notify==0){
			$notify=1;
			skelMdl::send_cmd('act_notify',['options'=>['sticky'=>'true']]+ array('msg'=>'Session agent Acquise '.$session_id),$session_id);
		}
		if(empty($_SESSION['idagent']) && $notify==1){
			$notify=0;
			skelMdl::send_cmd('act_notify',['options'=>['sticky'=>'true']]+ array('msg'=>'perte de session agent '.$session_id),$session_id);
		}
		if(!empty($_SESSION['idagent']) ){
			$notify=1;
			// skelMdl::send_cmd('act_notify', array('msg'=>'perte de session agent '.$session_id),$session_id);
		}
		if(empty($_SESSION['idagent'])){
			$notify = 0;
			skelMdl::send_cmd('act_notify', array('msg'=>' Session agent vide '.$session_id),$session_id);
			sleep(5);
		}
		sendMsg('message','idle ...');
	} while(true);