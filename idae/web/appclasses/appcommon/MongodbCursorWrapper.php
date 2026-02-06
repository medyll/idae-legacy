<?php
/**
 * MongoDB Cursor Wrapper for ADODB-style iteration
 * 
 * Provides backward compatibility for legacy code using getNext() method
 * Wraps MongoDB\Driver\Cursor to provide ADODB-like iteration
 * 
 * Created: 2026-02-02 (MongoDB migration compatibility layer)
 */

namespace AppCommon;

class MongodbCursorWrapper implements \Iterator, \Countable {
    private $cursor;
    private $cursorIterator = null;
    private $currentDoc = null;
    private $position = 0;
    private $documents = [];
    private $isArray = false;
    private $hasStarted = false;
    
    /**
     * Constructor
     * @param \MongoDB\Driver\Cursor|array $cursor MongoDB cursor or array of documents
     */
    public function __construct($cursor) {
        if (is_array($cursor)) {
            $this->documents = $cursor;
            $this->isArray = true;
        } else {
            $this->cursor = $cursor;
            $this->cursorIterator = null; // Will be initialized on first use
        }
        $this->position = 0;
    }
    
    /**
     * ADODB-style getNext() method
     * Returns next document or false if no more documents
     * Uses lazy iteration - only fetches documents as needed
     * 
     * @return array|false Next document or false
     */
    public function getNext() {
        // Array mode - simple indexed access
        if ($this->isArray) {
            if (isset($this->documents[$this->position])) {
                $doc = $this->documents[$this->position];
                $this->position++;
                return $doc;
            }
            return false;
        }
        
        // Cursor mode - lazy iteration
        if ($this->cursor === null) {
            return false;
        }
        
        // Initialize iterator on first call
        if ($this->cursorIterator === null) {
            $this->cursorIterator = $this->cursor;
            $this->cursorIterator->rewind();
            $this->hasStarted = true;
        } elseif ($this->hasStarted) {
            // Move to next only if not first call
            $this->cursorIterator->next();
        }
        
        $this->hasStarted = true;
        
        // Check if valid
        if ($this->cursorIterator->valid()) {
            $this->position++;
            return $this->cursorIterator->current();
        }
        
        return false;
    }
    
    /**
     * Get all documents as array
     * WARNING: This loads all documents into memory
     * @return array
     */
    public function toArray() {
        if ($this->isArray) {
            return $this->documents;
        }
        
        if ($this->cursor !== null) {
            // Only convert if not already done
            if (empty($this->documents)) {
                $this->documents = iterator_to_array($this->cursor);
                $this->isArray = true;
            }
        }
        return $this->documents;
    }
    
    /**
     * Count documents in cursor
     * WARNING: This may load all documents into memory
     * @return int
     */
    public function count($foundOnly = false): int {
        if ($this->isArray) {
            return count($this->documents);
        }
        
        // For MongoDB cursor, we need to iterate to count
        // This is expensive - better to use countDocuments() if possible
        if ($this->cursor !== null) {
            $count = 0;
            foreach ($this->cursor as $doc) {
                $count++;
            }
            return $count;
        }
        
        return 0;
    }
    
    /**
     * Sort results (for chaining after find())
     * @param array $sort Sort specification
     * @return self
     */
    public function sort($sort) {
        // MongoDB cursor already sorted by find() call
        // This is just for API compatibility
        return $this;
    }
    
    /**
     * Limit results (for chaining after find())
     * @param int $limit Limit value
     * @return self
     */
    public function limit($limit) {
        // MongoDB cursor already limited by find() call
        // This is just for API compatibility
        return $this;
    }
    
    /**
     * Skip results (for chaining after find())
     * @param int $skip Skip value
     * @return self
     */
    public function skip($skip) {
        // MongoDB cursor already skipped by find() call
        // This is just for API compatibility
        return $this;
    }
    
    // Iterator interface implementation
    public function rewind(): void {
        $this->position = 0;
        $this->hasStarted = false;
        if (!$this->isArray && $this->cursor !== null) {
            $this->cursorIterator = $this->cursor;
            $this->cursorIterator->rewind();
        }
    }
    
    public function current(): mixed {
        if ($this->isArray) {
            return $this->documents[$this->position] ?? null;
        }
        
        if ($this->cursor !== null && $this->cursorIterator !== null) {
            return $this->cursorIterator->current();
        }
        
        return null;
    }
    
    public function key(): mixed {
        return $this->position;
    }
    
    public function next(): void {
        ++$this->position;
        if (!$this->isArray && $this->cursorIterator !== null) {
            $this->cursorIterator->next();
        }
    }
    
    public function valid(): bool {
        if ($this->isArray) {
            return isset($this->documents[$this->position]);
        }
        
        if ($this->cursor !== null) {
            if ($this->cursorIterator === null) {
                $this->cursorIterator = $this->cursor;
                $this->cursorIterator->rewind();
            }
            return $this->cursorIterator->valid();
        }
        
        return false;
    }
}
