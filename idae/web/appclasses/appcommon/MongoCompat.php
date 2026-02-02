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
use DateTime;

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
            if (class_exists('MongoId') && $value instanceof \MongoId) {
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
            if (class_exists('MongoRegex') && $pattern instanceof \MongoRegex) {
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
            if (class_exists('MongoDate') && $value instanceof \MongoDate) {
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
            else if (class_exists('MongoRegex') && $value instanceof \MongoRegex) {
                $result[$key] = self::toRegex($value);
            }
            // Convert MongoDate to DateTime
            else if (class_exists('MongoDate') && $value instanceof \MongoDate) {
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
    
    public function __construct($client, $dbName) {
        $this->client = $client;
        $this->dbName = $dbName;
    }
    
    public function selectCollection($collectionName) {
        return new MongoCollection($this->client, $this->dbName, $collectionName);
    }
    
    public function __get($collectionName) {
        return $this->selectCollection($collectionName);
    }
}

/**
 * MongoCollection compatibility wrapper
 */
class MongoCollection {
    private $collection;
    
    public function __construct($client, $dbName, $collectionName) {
        $this->collection = $client->selectDatabase($dbName)->selectCollection($collectionName);
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
    
    public function insert($document, $options = []) {
        $result = $this->collection->insertOne($document);
        return $result->getInsertedId();
    }
    
    public function update($criteria, $update, $options = []) {
        if (isset($options['multiple']) || isset($options['multi'])) {
            $result = $this->collection->updateMany($criteria, $update, $options);
        } else {
            $result = $this->collection->updateOne($criteria, $update, $options);
        }
        return true;
    }
    
    public function remove($criteria, $options = []) {
        if (isset($options['justOne']) && $options['justOne']) {
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
}

/**
 * MongoCursor compatibility wrapper
 */
class MongoCursor implements \Iterator {
    private $cursor;
    private $current;
    private $key = 0;
    
    public function __construct($cursor) {
        $this->cursor = $cursor;
    }
    
    public function rewind(): void {
        $this->cursor->rewind();
        $this->key = 0;
        $this->fetchCurrent();
    }
    
    public function current(): mixed {
        return $this->current;
    }
    
    public function key(): mixed {
        return $this->key;
    }
    
    public function next(): void {
        $this->cursor->next();
        $this->key++;
        $this->fetchCurrent();
    }
    
    public function valid(): bool {
        return $this->cursor->valid();
    }
    
    private function fetchCurrent() {
        if ($this->cursor->valid()) {
            $this->current = $this->cursor->current();
        } else {
            $this->current = null;
        }
    }
    
    public function sort($fields) {
        // Note: This won't work on already-executed cursors
        // In real usage, sorting should be done in find() options
        return $this;
    }
    
    public function limit($num) {
        return $this;
    }
    
    public function skip($num) {
        return $this;
    }
    
    public function count($all = false) {
        return iterator_count($this->cursor);
    }
}

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
