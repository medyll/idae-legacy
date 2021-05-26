<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/09/2015
	 * Time: 19:43
	 */



	class UserSession {
		protected $dbSession;
		protected $maxTime;
		public function __construct($dbSession) {

			$this->maxTime = get_cfg_var("session.gc_maxlifetime");
			$this->dbSession = $dbSession;

			register_shutdown_function('session_write_close');
			session_set_save_handler(
				array($this, 'open'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc')
			);
			session_start();
		}
		public function open() { return true; }
		public function close() { return true; }
		public function read($id) {
			$doc = $this->dbSession->findOne(array("_id" => $id), array("sessionData" => 1));
			return $doc['sessionData'];
		}
		public function write($id,$data) {
			$this->dbSession->save(array("_id" => $id, "sessionData" => $data, "timeStamp" => time()));
			return true;
		}
		public function destroy($id) {
			$this->dbSession->remove(array("_id" => $id));
			return true;
		}
		public function gc() {
			$agedTime = time() - $this->maxTime;
			$this->dbSession->remove(array("timeStamp" => array('$lt' => $agedTime)));
		}
	}




	ini_set('session.save_handler' , 'user');//on définit l'utilisation des sessions en personnel
	$session = new Session();//on déclare la classe
	session_set_save_handler(array( $session , 'open' ) , array( $session , 'close' ) , array( $session , 'read' ) , array( $session , 'write' ) , array( $session , 'destroy' ) , array( $session ,'gc' ));//on précise les méthodes à employer pour les sessions
	session_start();//on démarre la session

	$_SESSION['tamere']="nue";