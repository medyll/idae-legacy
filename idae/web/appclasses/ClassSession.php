<?php

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/09/2015
	 * Time: 19:43
	 */
	class Session {
		protected $dbSession;
		protected $maxTime;

		public function __construct() {
			$opt = ['db' => 'admin', 'username' => MDB_USER, 'password' => MDB_PASSWORD];

			$this->conn   = new MongoClient('mongodb://admin:gwetme2011@127.0.0.1:27017',$opt);
			// $this->conn   = new MongoClient('mongodb://127.0.0.1:27017');
			$sitebase_app = DEFINED(MDB_PREFIX) ? 'sitebase_session' : MDB_PREFIX . 'sitebase_session';
			if(ENVIRONEMENT=='PREPROD') $sitebase_app .='_preprod';

			$this->dbSession = $this->conn->$sitebase_app->session;
			$this->maxTime   = 3600;//get_cfg_var("session.gc_maxlifetime");

			// $this->dbSession->ensureIndex(['id'=>1]);
			$this->dbSession->ensureIndex(['timeStamp' => 1]);
			$this->dbSession->ensureIndex(['timeStamp' => -1]);
			/*register_shutdown_function('session_write_close');
			session_set_save_handler(
				array($this, 'open'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc')
			);*/

		}

		public function open() { return true; }

		public function close() { return true; }

		public function read($id) {
			$this->gc();
			$doc = $this->dbSession->findOne(["_id" => $id], ["sessionData" => 1]);

			return $doc['sessionData'];
		}

		public function gc() {
			$lastAccessed = time() - $this->maxTime;
			// $lastAccessed = new UTCDateTime(floor((microtime(true) - $this->maxTime) * 1000));

			$this->dbSession->remove(["timeStamp" => ['$lt' => $lastAccessed]]);
		}

		public function write($id, $data) {
			//
			if(empty($id)) return false;
			// if(trim($data)=='') vardump_async(['VIDE !!! ',date('d-m-Y H:i:s', time() - $this->maxTime),$data,$_POST],true);
			$backtrace = debug_backtrace();
			// vardump_async([date('d-m-Y H:i:s', time() - $this->maxTime),$data,$_GET]);
			// $this->dbSession->save(array("_id" => $id, "sessionData" => $data, "timeStamp" => time()));,'referrer'=>$_SERVER['HTTP_REFERER']
			$set = ["sessionData" => $data, "timeStamp" => time(),'date_heure'=>date('d-m-Y H:i:s', time() - $this->maxTime),'nodebug'=>true,'MDB_PREFIX'=>MDB_PREFIX,'ENVIRONEMENT'=>ENVIRONEMENT ];
			$this->dbSession->update(["_id" => $id],['$set'=>$set],['upsert'=>true]);
			// $this->dbSession->update(["_id" => $id],['$set'=>[ "sessionData" => $data, "timeStamp" => time()]],['upsert'=>true]);
			//$this->dbSession->update(["_id" => $id],[ "sessionData" => $data, "timeStamp" => time()]);

			return true;
		}

		public function destroy($id) {
			// vardump_async(['DESTROY !!! ',date('d-m-Y H:i:s', time() - $this->maxTime),'$data',$_POST]);
			$this->dbSession->remove(["_id" => $id]);

			return true;
		}
	}



	ini_set('session.save_handler', 'user');

	// register_shutdown_function('session_write_close');

	$session = new Session();
	session_set_save_handler(
		[$session, 'open'],
		[$session, 'close'],
		[$session, 'read'],
		[$session, 'write'],
		[$session, 'destroy'],
		[$session, 'gc']
	);
	session_start();