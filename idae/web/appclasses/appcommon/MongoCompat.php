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
