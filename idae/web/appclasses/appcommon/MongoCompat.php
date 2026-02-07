<?php
/**
 * MongoCompat.php — MongoDB Driver Compatibility Helper
 * 
 * Central utility class for MongoDB v1.x → modern driver conversions.
 * Single source of truth for MongoId, MongoRegex, MongoDate, cursor handling.
 * 
 * @package AppCommon
 * @version 1.0.0
 * @date 2026-02-02
 */

namespace AppCommon;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\GridFS\Bucket;
use MongoDB\Database as MongoDriverDatabase;
use MongoDB\Collection as MongoDriverCollection;
use DateTime;

require_once __DIR__ . '/MongodbCursorWrapper.php';

class MongoCompat {
    
    /**
     * Convert string/MongoId to ObjectId (modern driver)
     * 
     * @param string|MongoId|ObjectId|null $value ID to convert
     * @return ObjectId|null ObjectId instance if valid, null if empty/invalid
     */
    public static function toObjectId($value) {
        if (empty($value)) {
            return null;
        }
        
        try {
            // Already an ObjectId
            if ($value instanceof ObjectId) {
                return $value;
            }
            
            // Legacy MongoId (v1.x driver)
            if (class_exists('MongoId', false) && $value instanceof \MongoId) {
                return new ObjectId((string)$value);
            }
            
            // String representation
            if (is_string($value) && strlen($value) === 24) {
                return new ObjectId($value);
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("MongoCompat::toObjectId error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert MongoRegex to modern regex format
     * 
     * @param string $pattern Regex pattern (without delimiters)
     * @param string $flags Regex flags (i=case-insensitive, m=multiline, s=dotall, x=extended)
     * @return Regex|null Regex instance for MongoDB queries, null if invalid
     */
    public static function toRegex($pattern, $flags = 'i') {
        if (empty($pattern)) {
            return null;
        }
        
        try {
            // Legacy MongoRegex
            if (class_exists('MongoRegex', false) && $pattern instanceof \MongoRegex) {
                $str = (string)$pattern;
                // Extract pattern and flags from /pattern/flags format
                if (preg_match('#^/(.*)/([imsx]*)$#', $str, $matches)) {
                    $pattern = $matches[1];
                    $flags = $matches[2];
                }
            }
            
            return new Regex($pattern, $flags);
        } catch (\Exception $e) {
            error_log("MongoCompat::toRegex error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert MongoDate / string / timestamp to DateTime
     * 
     * @param MongoDate|string|int|DateTime|null $value Date value to convert
     * @return DateTime|null DateTime instance if valid, null if empty
     */
    public static function toDate($value) {
        if (empty($value)) {
            return null;
        }
        
        try {
            // Already DateTime
            if ($value instanceof DateTime) {
                return $value;
            }
            
            // Legacy MongoDate (v1.x driver)
            if (class_exists('MongoDate', false) && $value instanceof \MongoDate) {
                $dt = new DateTime();
                $dt->setTimestamp($value->sec);
                return $dt;
            }
            
            // Timestamp integer
            if (is_numeric($value)) {
                $dt = new DateTime();
                $dt->setTimestamp((int)$value);
                return $dt;
            }
            
            // String representation (ISO, Y-m-d, etc.)
            if (is_string($value)) {
                return new DateTime($value);
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("MongoCompat::toDate error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert MongoDB cursor to indexed array (backward compat with ADODB fetchAll())
     * 
     * @param Cursor|array $cursor MongoDB cursor or array
     * @return array Array of documents
     */
    public static function cursorToArray($cursor) {
        if (empty($cursor)) {
            return array();
        }
        
        // Already an array
        if (is_array($cursor)) {
            return $cursor;
        }
        
        // MongoDB cursor - convert to array
        try {
            if (method_exists($cursor, 'toArray')) {
                return $cursor->toArray();
            }
            
            // Fallback: iterate and collect
            $result = array();
            foreach ($cursor as $doc) {
                $result[] = $doc;
            }
            return $result;
        } catch (\Exception $e) {
            error_log("MongoCompat::cursorToArray error: " . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Convert string/float to integer safely
     * 
     * @param mixed $value Value to convert
     * @param int $default Default if invalid
     * @return int Integer value or default
     */
    public static function toIntSafe($value, $default = 0) {
        if (is_null($value) || $value === '') {
            return $default;
        }
        
        if (is_numeric($value)) {
            return (int)$value;
        }
        
        return $default;
    }
    
    /**
     * Convert dynamic field naming (idae pattern: codeAppscheme_field + table)
     * Pattern: $fieldCode + ucfirst($tableName) → "nomProduit"
     * 
     * @param string $codeField Field code (e.g., "nom")
     * @param string $tableName Table name (e.g., "produit")
     * @return string MongoDB field name (e.g., "nomProduit")
     */
    public static function toFieldName($codeField, $tableName) {
        if (empty($codeField) || empty($tableName)) {
            return '';
        }
        
        // Pattern: nom + Produit = nomProduit
        return $codeField . ucfirst($tableName);
    }
    
    /**
     * Escape string for safe regex usage (preg_quote equivalent for MongoDB)
     * 
     * @param string $str String to escape
     * @return string Escaped string
     */
    public static function escapeRegex($str) {
        if (empty($str)) {
            return '';
        }
        
        return preg_quote($str, '/');
    }
    
    /**
     * Convert legacy array filter to modern MongoDB filter
     * Handles MongoId, MongoRegex conversions recursively
     * 
     * @param array $filter Legacy filter array
     * @return array Modern MongoDB filter
     */
    public static function convertFilter($filter) {
        if (!is_array($filter)) {
            return $filter;
        }
        
        $result = array();
        
        foreach ($filter as $key => $value) {
            // Convert MongoId to ObjectId
            if ($value instanceof \MongoId) {
                $result[$key] = self::toObjectId($value);
            }
            // Convert MongoRegex to Regex
            else if (class_exists('MongoRegex', false) && $value instanceof \MongoRegex) {
                $result[$key] = self::toRegex($value);
            }
            // Convert MongoDate to DateTime
            else if (class_exists('MongoDate', false) && $value instanceof \MongoDate) {
                $result[$key] = self::toDate($value);
            }
            // Recursive array conversion
            else if (is_array($value)) {
                $result[$key] = self::convertFilter($value);
            }
            // Keep as is
            else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}

/**
 * MongoClient compatibility wrapper for legacy code
 * Wraps MongoDB\Client for backward compatibility with old Mongo extension
 */
class MongoClient {
    private $client;
    private $defaultDb;
    
    public function __construct($uri, $options = []) {
        // Parse database from options or URI
        $this->defaultDb = $options['db'] ?? 'admin';
        
        // Create modern MongoDB client
        $this->client = new \MongoDB\Client($uri, [], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]);
    }
    
    public function selectDB($dbName) {
        return new MongoDB($this->client, $dbName);
    }
    
    public function __get($dbName) {
        return $this->selectDB($dbName);
    }
}

/**
 * MongoDB compatibility wrapper
 */
class MongoDB {
    private $client;
    private $dbName;
    private $database;
    
    public function __construct($client, $dbName) {
        $this->client = $client;
        $this->dbName = $dbName;
        $this->database = $client->selectDatabase($dbName);
    }
    
    public function selectCollection($collectionName) {
        $collection = $this->database->selectCollection($collectionName);
        return new MongoCollection($collection);
    }
    
    public function __get($collectionName) {
        return $this->selectCollection($collectionName);
    }
    
    public function getGridFs($bucketName = null) {
        return new MongoGridFS($this->database, $bucketName);
    }
    
    public function getInnerDatabase() {
        return $this->database;
    }
    
    public function getDatabaseName() {
        return $this->dbName;
    }

    public function __call($name, $arguments) {
        if (strtolower($name) === 'getgridfs') {
            return $this->getGridFs(...$arguments);
        }
        return $this->database->$name(...$arguments);
    }
}

/**
 * MongoCollection compatibility wrapper
 */
class MongoCollection {
    private $collection;
    
    public function __construct($clientOrCollection, $dbName = null, $collectionName = null) {
        if ($clientOrCollection instanceof MongoDriverCollection) {
            $this->collection = $clientOrCollection;
        } else {
            $this->collection = $clientOrCollection->selectDatabase($dbName)->selectCollection($collectionName);
        }
    }
    
    public function find($query = [], $fields = []) {
        $options = [];
        if (!empty($fields)) {
            $options['projection'] = $fields;
        }
        $cursor = $this->collection->find($query, $options);
        return new MongoCursor($cursor);
    }
    
    public function findOne($query = [], $fields = []) {
        $options = [];
        if (!empty($fields)) {
            $options['projection'] = $fields;
        }
        
        return $this->collection->findOne($query, $options);
    }
    
    public function insertOne($document, $options = []) {
        return $this->collection->insertOne($document, $options);
    }

    public function insertMany($documents, $options = []) {
        return $this->collection->insertMany($documents, $options);
    }
    
    public function insert($document, $options = []) {
        $result = $this->collection->insertOne($document);
        return $result->getInsertedId();
    }
    
    public function updateOne($criteria, $update, $options = []) {
        return $this->collection->updateOne($criteria, $update, $options);
    }

    public function updateMany($criteria, $update, $options = []) {
        return $this->collection->updateMany($criteria, $update, $options);
    }

    public function update($criteria, $update, $options = []) {
        if (isset($options['multiple']) || isset($options['multi'])) {
            $result = $this->collection->updateMany($criteria, $update, $options);
        } else {
            $result = $this->collection->updateOne($criteria, $update, $options);
        }
        return true;
    }
    
    public function deleteOne($criteria, $options = []) {
        return $this->collection->deleteOne($criteria, $options);
    }

    public function deleteMany($criteria, $options = []) {
        return $this->collection->deleteMany($criteria, $options);
    }
    
    public function remove($criteria, $options = []) {
        $justOne = (!empty($options['justOne']) || !empty($options['single']));
        if ($justOne) {
            $this->collection->deleteOne($criteria);
        } else {
            $this->collection->deleteMany($criteria);
        }
        return true;
    }
    
    public function createIndex($keys, $options = []) {
        return $this->collection->createIndex($keys, $options);
    }
    
    public function ensureIndex($keys, $options = []) {
        return $this->createIndex($keys, $options);
    }
    
    public function distinct($field, $filter = [], array $options = []) {
        return $this->collection->distinct($field, $filter, $options);
    }
    
    public function count($filter = [], $options = []) {
        return $this->collection->countDocuments($filter, $options);
    }
    
    public function drop() {
        return $this->collection->drop();
    }
    
    public function getCollection() {
        return $this->collection;
    }

    public function __call($name, $arguments) {
        return $this->collection->$name(...$arguments);
    }
}

class MongoGridFS {
    private $bucket;
    
    public function __construct(MongoDriverDatabase $database, $bucketName = null) {
        $options = [];
        if (!empty($bucketName)) {
            $options['bucketName'] = $bucketName;
        }
        $this->bucket = $database->selectGridFSBucket($options);
    }
    
    private function normalizeFilter($filter) {
        if (is_string($filter)) {
            return ['filename' => $filter];
        }
        return $filter ?: [];
    }
    
    public function find($filter = [], $options = []) {
        $cursor = $this->bucket->find($this->normalizeFilter($filter), $options);
        $files = [];
        foreach ($cursor as $file) {
            $files[] = new MongoGridFSFile($this->bucket, $file);
        }
        return new MongoCursor($files);
    }
    
    public function findOne($filter = [], $options = []) {
        $cursor = $this->find($filter, $options);
        return $cursor->getNext();
    }
    
    public function storeBytes($bytes, array $options = []) {
        $filename = $options['filename'] ?? ('file_' . uniqid());
        $metadata = $options['metadata'] ?? [];
        if (isset($options['metatag'])) {
            $metadata['metatag'] = $options['metatag'];
        }
        $stream = fopen('php://temp', 'wb+');
        fwrite($stream, $bytes);
        rewind($stream);
        $id = $this->bucket->uploadFromStream($filename, $stream, ['metadata' => $metadata]);
        fclose($stream);
        return $id;
    }
    
    public function remove($criteria, array $options = []) {
        $filter = $this->normalizeFilter($criteria);
        if (isset($filter['_id'])) {
            $this->bucket->delete($filter['_id']);
            return true;
        }
        $cursor = $this->bucket->find($filter, $options);
        foreach ($cursor as $file) {
            $this->bucket->delete($file['_id']);
        }
        return true;
    }
    
    public function delete($id) {
        $this->bucket->delete($id);
    }
    
    public function drop() {
        $this->bucket->drop();
    }
    
    public function get($id) {
        $cursor = $this->bucket->find(['_id' => $id]) ;
        foreach ($cursor as $file) {
            return new MongoGridFSFile($this->bucket, $file);
        }
        return null;
    }
}

class MongoGridFSFile {
    private $bucket;
    public $file;
    
    public function __construct(Bucket $bucket, array $file) {
        $this->bucket = $bucket;
        $this->file = $file;
    }
    
    public function getBytes() {
        $stream = fopen('php://temp', 'wb+');
        $this->bucket->downloadToStream($this->file['_id'], $stream);
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);
        return $contents;
    }
    
    public function getFilename() {
        return $this->file['filename'] ?? '';
    }
    
    public function getId() {
        return $this->file['_id'] ?? null;
    }
    
    public function __get($name) {
        if ($name === 'file') {
            return $this->file;
        }
        return $this->file[$name] ?? null;
    }
    
    public function toArray() {
        return $this->file;
    }
}

class MongoCursor extends MongodbCursorWrapper {}

// Create global aliases for backward compatibility
if (!class_exists('MongoClient', false)) {
    class_alias('AppCommon\MongoClient', 'MongoClient');
}
if (!class_exists('MongoDB', false)) {
    class_alias('AppCommon\MongoDB', 'MongoDB');
}
if (!class_exists('MongoCollection', false)) {
    class_alias('AppCommon\MongoCollection', 'MongoCollection');
}
if (!class_exists('MongoCursor', false)) {
    class_alias('AppCommon\MongoCursor', 'MongoCursor');
}
if (!class_exists('MongoGridFS', false)) {
    class_alias('AppCommon\MongoGridFS', 'MongoGridFS');
}
if (!class_exists('MongoGridFSFile', false)) {
    class_alias('AppCommon\MongoGridFSFile', 'MongoGridFSFile');
}
