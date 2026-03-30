<?php
declare(strict_types=1);

/**
 * Session class for MongoDB storage
 * Compatibility wrapper for legacy session handler with modern MongoDB driver
 *
 * @package AppClasses
 * @version 2.1 (PHP 8.2 + MongoDB modern driver)
 * Modified: 2026-03-29 — Fixed: use MongoDB\Client instead of legacy MongoClient
 */

use MongoDB\Client;

class Session {
	protected $dbSession;
	protected $maxTime;
	protected Client $conn;

	public function __construct() {
		// Détection automatique de l'hôte MongoDB
		$mongo_host = getenv('MONGO_HOST') ?: (getenv('DOCKER_ENV') ? 'host.docker.internal' : '127.0.0.1');

		// Priorité: 1. Variables d'env, 2. Constantes, 3. Par défaut (empty = no auth)
		$mongo_user = getenv('MDB_USER') ?: (defined('MDB_USER') ? MDB_USER : '');
		$mongo_pass = getenv('MDB_PASSWORD') ?: (defined('MDB_PASSWORD') ? MDB_PASSWORD : '');

		// Build connection string with credentials (only if both present)
		if (!empty($mongo_user) && !empty($mongo_pass)) {
			$mongo_url = 'mongodb://' . urlencode($mongo_user) . ':' . urlencode($mongo_pass) . '@' . $mongo_host . ':27017';
		} else {
			$mongo_url = 'mongodb://' . $mongo_host . ':27017';
		}

		// MongoDB\Client connection with timeout and error handling
		try {
			$options = [
				'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
				'connectTimeoutMS' => 5000,
				'serverSelectionTimeoutMS' => 5000,
				'socketTimeoutMS' => 30000,
			];
			$authOptions = [];
			if (!empty($mongo_user) && !empty($mongo_pass)) {
				$authOptions['username'] = $mongo_user;
				$authOptions['password'] = $mongo_pass;
				$authOptions['authSource'] = 'admin';
			}
			$this->conn = new Client($mongo_url, $authOptions, $options);
			// Ping to verify connection
			$this->conn->getDatabase('admin')->command(['ping' => 1]);
		} catch (Exception $e) {
			error_log('[Session::__construct] MongoDB connection FAILED: ' . $e->getMessage());
			throw new Exception('Session storage unavailable: ' . $e->getMessage());
		}

		$sitebase_app = defined('MDB_PREFIX') && MDB_PREFIX ? MDB_PREFIX . 'sitebase_session' : 'sitebase_session';
		if (defined('ENVIRONEMENT') && ENVIRONEMENT == 'PREPROD') $sitebase_app .= '_preprod';

		$this->dbSession = $this->conn->selectDatabase($sitebase_app)->selectCollection('session');
		$this->maxTime   = 3600;

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
		$doc = $this->dbSession->findOne(["_id" => $id], ['projection' => ['sessionData' => 1]]);

		// Handle null document
		if ($doc === null) {
			return '';
		}

		return $doc['sessionData'] ?? '';
	}

	public function gc() {
		$lastAccessed = time() - $this->maxTime;
		$this->dbSession->deleteMany(["timeStamp" => ['$lt' => $lastAccessed]]);
	}

	public function write($id, $data) {
		if (empty($id)) return false;

		$this->dbSession->updateOne(
			["_id" => $id],
			['$set' => [
				"sessionData" => $data,
				"timeStamp" => time(),
				'date_heure' => date('d-m-Y H:i:s'),
			]],
			['upsert' => true]
		);
		return true;
	}

	public function destroy($id) {
		$this->dbSession->deleteOne(["_id" => $id]);
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
		// If we have a session cookie, tell session_start() to use it
		$incoming_session_id = $_COOKIE[session_name()] ?? null;
		if ($incoming_session_id && ctype_alnum($incoming_session_id)) {
			session_id($incoming_session_id);
		}
		session_start();
	}
} catch (Exception $e) {
	error_log('[Session] session_start() FAILED: ' . $e->getMessage());
	// Don't propagate exception - let application handle empty session gracefully
}

// Initialize CSRF token in session (generated once, persists across requests)
require_once(__DIR__ . '/appcommon/CsrfGuard.php');
\AppCommon\CsrfGuard::init();