#!/usr/bin/env php
<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/09/2015
	 * Time: 15:40
	 */
	$_SERVER['CONF_INC'] =  "/var/www/web26/web/conf.inc.php";
	$_SERVER['HTTP_HOST'] =  'http://idaertys.mydde.fr:8000' ;
	require_once("../conf.inc.php");

	vardump($_SERVER);
	set_time_limit(0);

	$connections = [];

	$websocket = new Hoa\Websocket\Server(
		new Hoa\Socket\Server('tcp://idaertys.mydde.fr:9000')
	);

	$websocket->on('open', function (Hoa\Core\Event\Bucket $bucket) {
			$nodes = $bucket->getSource()->getConnection()->getNodes();

			echo 'new connection', "\n"; // with or without cookie
		return;
	});
	$websocket->on('error', function (Hoa\Core\Event\Bucket $bucket) {

			// var_dump($bucket);
			echo 'ERROR', "\n";
		return;
	});
	$websocket->on('message', function (Hoa\Core\Event\Bucket $bucket) {
		$data = $bucket->getData();
		$message = $data['message'];
		$response = [];
		if(is_string($message)) $message =  json_decode($message,true);
		echo 'Action '.$message['action'], "\n";
		switch ($message['action']):
			//
			case "register":
				$connections[$message['PHPSESSID']]= $bucket->getSource();
				$arr_response = ['action'=>'registred','PHPSESSID'=>$message['PHPSESSID']];
				$response           = json_encode($arr_response,JSON_FORCE_OBJECT);
				break;

			case "loadModule": // file, vars
				echo 'loadmodule  '.$message['data']['file'], "\n";
				$return = $message['data'];
 				$file = $return['file'];
				$vars = $return['vars'];
				$element = $return['element'];
				if(is_string($vars)){parse_str($vars, $vars);}
				$vars['PHPSESSID'] = $message['PHPSESSID'];
				/*session_id($message['PHPSESSID']);
				var_dump($_SESSION);
				$_POST = $vars;
				ob_start();
				include(APPMDL . $file . '.php');
				$final = ob_get_contents();*/

				//  $return['response']=skelMdl::cf_module($file,$vars);
				//  $return['response'] = $final;
				ob_start();
				// $return['response']     =  skelMdl::doCurl(HTTPMDL . $file . '.php' , $vars);//skelMdl::cf_module($file,$vars);
				$_POST = $vars;
				include_once(APPMDL. $file . '.php' );
				$return['response']     = ob_get_contents();
				ob_end_clean();

				$response               = json_encode($return,JSON_FORCE_OBJECT);
				break;
			case "send_cmd": // on renvoie la commande au(x) client(s)
				$response           = json_encode($message,JSON_FORCE_OBJECT);

				if(!empty ($message['OWN'])){
					try {
						$bucket->getSource()->broadcast($response);
					} catch (Exception $e) {
						echo 'Exception reçue : ',  $e->getMessage(), "\n";
					}

					return;
				}
				break;
			default:
				echo "no action defined : ";
				return;
				break;
			endswitch;

		try {
			$bucket->getSource()->send($response);
		} catch (Exception $e) {
			echo 'Exception reçue : ',  $e->getMessage(), "\n";
		}
		flush();
		ob_flush();
		//
		return;
	});
	$websocket->on('close', function (Hoa\Core\Event\Bucket $bucket) {
		  echo 'connection closed', "\n";

		return;
	});

	$websocket->run();

?>