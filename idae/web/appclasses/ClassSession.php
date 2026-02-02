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
	// Détection automatique de l'hôte MongoDB
	$mongo_host = getenv('MONGO_HOST') ?: (getenv('DOCKER_ENV') ? 'host.docker.internal' : '127.0.0.1');
	
	// Debug: voir d'où viennent les credentials
	error_log("DEBUG: getenv(MDB_USER)=" . (getenv('MDB_USER') ?: 'empty'));
	error_log("DEBUG: defined(MONGO_USER)=" . (defined('MONGO_USER') ? MONGO_USER : 'not defined'));
	error_log("DEBUG: defined(MDB_USER)=" . (defined('MDB_USER') ? MDB_USER : 'not defined'));
	
	// Priorité: 1. Variables d'env, 2. Constantes, 3. Par défaut
	$mongo_user = getenv('MDB_USER') ?: (defined('MONGO_USER') ? MONGO_USER : (defined('MDB_USER') ? MDB_USER : ''));
	$mongo_pass = getenv('MDB_PASSWORD') ?: (defined('MONGO_PASS') ? MONGO_PASS : (defined('MDB_PASSWORD') ? MDB_PASSWORD : ''));
	
	// Debug: afficher les credentials utilisés
	if (getenv('DOCKER_ENV')) {
		error_log("MongoDB credentials: user=$mongo_user, host=$mongo_host");
	}
	
	// Build connection string (no auth if credentials empty)
	if (!empty($mongo_user) && !empty($mongo_pass)) {
		$mongo_url = 'mongodb://' . $mongo_user . ':' . $mongo_pass . '@' . $mongo_host . ':27017';
	} else {
		$mongo_url = 'mongodb://' . $mongo_host . ':27017';
	}
	
	// MongoClient is now available via MongoCompat loaded in conf.lan.inc.php
	$this->conn = new MongoClient($mongo_url, ['db' => 'admin']);
	
		$sitebase_app = defined('MDB_PREFIX') && MDB_PREFIX ? MDB_PREFIX . 'sitebase_session' : 'sitebase_session';
		if(ENVIRONEMENT=='PREPROD') $sitebase_app .='_preprod';

		$this->dbSession = $this->conn->selectDB($sitebase_app)->selectCollection('session');
		$this->maxTime   = 3600;//get_cfg_var("session.gc_maxlifetime");

		// Migration: ensureIndex → createIndex
		try {
			$this->dbSession->createIndex(['timeStamp' => 1]);
			$this->dbSession->createIndex(['timeStamp' => -1]);
		} catch (Exception $e) {
			// Index might already exist
		}
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