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
		
		$debug = !empty(getenv('DEBUG_SESSION'));
		
		// MongoClient connection with timeout and error handling
		try {
			// Add timeout to prevent hanging (5s)
			$options = ['db' => 'admin', 'connectTimeoutMS' => 5000, 'socketTimeoutMS' => 5000];
			$this->conn = new MongoClient($mongo_url, $options);
			
			// Test connection by pinging
			$this->conn->selectDB('admin')->command(['ping' => 1]);
			
			if ($debug) {
				error_log('[Session::__construct] MongoDB connected successfully to ' . $mongo_host);
			}
		} catch (Exception $e) {
			error_log('[Session::__construct] MongoDB connection FAILED: ' . $e->getMessage());
			error_log('[Session::__construct] host=' . $mongo_host . ' user=' . $mongo_user);
			// Let session_start() fail gracefully instead of crashing
			throw new Exception('Session storage unavailable: ' . $e->getMessage());
		}
		
		$sitebase_app = defined('MDB_PREFIX') && MDB_PREFIX ? MDB_PREFIX . 'sitebase_session' : 'sitebase_session';
		if (ENVIRONEMENT == 'PREPROD') $sitebase_app .= '_preprod';

		if ($debug) {
			error_log('[Session::__construct] session_db=' . $sitebase_app);
		}

		$this->dbSession = $this->conn->selectDB($sitebase_app)->selectCollection('session');
		$this->maxTime   = 3600;

		// Create indexes for session cleanup performance
		try {
			$this->dbSession->createIndex(['timeStamp' => 1]);
		} catch (Exception $e) {
			// Index might already exist - ignore
			if ($debug) {
				error_log('[Session::__construct] Index creation skipped: ' . $e->getMessage());
			}
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
		$debug = !empty(getenv('DEBUG_SESSION'));
		if ($debug) {
			$mongoHost = getenv('MONGO_HOST') ?: (defined('MDB_HOST') ? MDB_HOST : '');
			$mongoUser = getenv('MDB_USER') ?: (defined('MDB_USER') ? MDB_USER : '');
			$mongoPrefix = defined('MDB_PREFIX') ? MDB_PREFIX : '';
			$sitebase = $mongoPrefix . 'sitebase_session' . (defined('ENVIRONEMENT') && ENVIRONEMENT == 'PREPROD' ? '_preprod' : '');
			error_log('[Session::gc] delete expired sessions');
			error_log('[Session::gc] host=' . $mongoHost . ' user=' . $mongoUser . ' db=' . $sitebase . ' cutoff=' . $lastAccessed);
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
			foreach ($trace as $i => $row) {
				$file = isset($row['file']) ? $row['file'] : '';
				$line = isset($row['line']) ? $row['line'] : '';
				$func = isset($row['function']) ? $row['function'] : '';
				error_log('[Session::gc] #' . $i . ' ' . $func . ' ' . $file . ':' . $line);
			}
		}
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
		$debug = !empty(getenv('DEBUG_SESSION')) || !empty($_GET['debug_session']);
		if ($debug) {
			error_log('[Session::destroy] delete session id=' . $id);
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
			foreach ($trace as $i => $row) {
				$file = isset($row['file']) ? $row['file'] : '';
				$line = isset($row['line']) ? $row['line'] : '';
				$func = isset($row['function']) ? $row['function'] : '';
				error_log('[Session::destroy] #' . $i . ' ' . $func . ' ' . $file . ':' . $line);
			}
		}
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

// Start session with error handling
try {
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
		$debug = !empty(getenv('DEBUG_SESSION'));
		if ($debug) {
			error_log('[Session] session_start() successful, ID=' . session_id());
		}
	}
} catch (Exception $e) {
	error_log('[Session] session_start() FAILED: ' . $e->getMessage());
	// Don't propagate exception - let application handle empty session gracefully
}