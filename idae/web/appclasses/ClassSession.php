<?php

/**
 * Session class for MongoDB storage
 * Compatibility wrapper for legacy session handler with modern MongoDB driver
 * 
 * @package AppClasses
 * @version 2.0 (PHP 8.2 + MongoDB)
 */
class Session {
	protected $dbSession;
	protected $maxTime;
	protected $conn;  // Declare property to avoid PHP 8 deprecation

	public function __construct() {
		// Détection automatique de l'hôte MongoDB
		$mongo_host = getenv('MONGO_HOST') ?: (getenv('DOCKER_ENV') ? 'host.docker.internal' : '127.0.0.1');
		
		// Priorité: 1. Variables d'env, 2. Constantes, 3. Par défaut (empty = no auth)
		$mongo_user = getenv('MDB_USER') ?: (defined('MDB_USER') ? MDB_USER : '');
		$mongo_pass = getenv('MDB_PASSWORD') ?: (defined('MDB_PASSWORD') ? MDB_PASSWORD : '');
		
		// Build connection string (no auth if credentials empty)
		if (!empty($mongo_user) && !empty($mongo_pass)) {
			$mongo_url = 'mongodb://' . $mongo_user . ':' . $mongo_pass . '@' . $mongo_host . ':27017';
		} else {
			$mongo_url = 'mongodb://' . $mongo_host . ':27017';
		}
		
		// MongoClient is available via MongoCompat loaded in conf.lan.inc.php
		$this->conn = new MongoClient($mongo_url, ['db' => 'admin']);
		
		$sitebase_app = defined('MDB_PREFIX') && MDB_PREFIX ? MDB_PREFIX . 'sitebase_session' : 'sitebase_session';
		if (ENVIRONEMENT == 'PREPROD') $sitebase_app .= '_preprod';

		$this->dbSession = $this->conn->selectDB($sitebase_app)->selectCollection('session');
		$this->maxTime   = 3600;

		// Create indexes for session cleanup performance
		try {
			$this->dbSession->createIndex(['timeStamp' => 1]);
		} catch (Exception $e) {
			// Index might already exist - ignore
		}
	}

	public function open() { 
		return true; 
	}

	public function close() { 
		return true; 
	}

	public function read($id) {
		$this->gc();
		$doc = $this->dbSession->findOne(["_id" => $id], ["sessionData" => 1]);
		
		// Handle null document
		if ($doc === null) {
			return '';
		}
		
		return $doc['sessionData'] ?? '';
	}

	public function gc() {
		$lastAccessed = time() - $this->maxTime;
		$this->dbSession->remove(["timeStamp" => ['$lt' => $lastAccessed]]);
	}

	public function write($id, $data) {
		if (empty($id)) return false;
		
		$set = [
			"sessionData" => $data, 
			"timeStamp" => time(),
			'date_heure' => date('d-m-Y H:i:s'),
		];
		
		$this->dbSession->update(["_id" => $id], ['$set' => $set], ['upsert' => true]);
		return true;
	}

	public function destroy($id) {
		$this->dbSession->remove(["_id" => $id]);
		return true;
	}
}

// Register session handler (PHP 8 compatible way)
$session = new Session();
session_set_save_handler(
	[$session, 'open'],
	[$session, 'close'],
	[$session, 'read'],
	[$session, 'write'],
	[$session, 'destroy'],
	[$session, 'gc']
);

// Start session
session_start();