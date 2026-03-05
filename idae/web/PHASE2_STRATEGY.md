# Phase 2 - ClassApp.php Migration Strategy

**File**: `appclasses/appcommon/ClassApp.php` (2072 lines)
**Status**: In Progress  
**Date**: 2026-02-02

## Core Issue

ClassApp extends `\MongoClient` (obsolete v1.x driver) and uses:
- `MongoClient` connection pattern
- `MongoId`, `MongoRegex`, `MongoDate` types
- ADODB-style cursor iteration (`fetchRow()`)
- Legacy `insert()`, `update()`, `remove()` methods

## Migration Approach

### Strategy: Gradual Refactor with Backward Compatibility Layer

**Key Principle**: Don't break existing code during migration. Create compatibility wrapper first, then migrate method by method.

### Step 1: Connection Refactor (HIGH PRIORITY)

**Current Code** (lines 14-47):
```php
class App extends \MongoClient {
    public function __construct($table = '') {
        $opt = ['db' => 'admin', 'username' => MDB_USER, 'password' => MDB_PASSWORD];
        $mongo_url = 'mongodb://' . MDB_USER . ':' . MDB_PASSWORD . '@' . $mongo_host;
        $PERSIST_CON = $this->conn = new MongoClient($mongo_url, $opt);
        $this->app_conn = $this->conn->$sitebase_app->appscheme;
        // ... 20+ collection assignments
    }
}
```

**Target Code**:
```php
use MongoDB\Client;
use AppCommon\MongoCompat;

class App {
    private $mongoClient;    // MongoDB\Client instance
    private $database;       // MongoDB\Database instance
    public $collection;      // MongoDB\Collection instance
    
    public function __construct($table = '') {
        // Singleton connection pattern (like PERSIST_CON)
        $this->mongoClient = $this->getMongoClient();
        
        $sitebase_app = MDB_PREFIX . 'sitebase_app';
        $this->database = $this->mongoClient->selectDatabase($sitebase_app);
        
        // Collection assignments
        $this->app_conn = $this->database->selectCollection('appscheme');
        $this->appscheme = $this->database->selectCollection('appscheme');
        // ... all other collections
        
        if (!empty($table)) {
            $this->collection = $this->database->selectCollection($table);
            $this->app_table_one = $this->app_conn->findOne(['codeAppscheme' => $table]);
            // ... metadata initialization
        }
    }
    
    private function getMongoClient() {
        global $PERSIST_CON;
        
        if (!empty($PERSIST_CON)) {
            return $PERSIST_CON;
        }
        
        $mongo_host = getenv('MONGO_HOST') ?: (getenv('DOCKER_ENV') ? 'host.docker.internal' : MDB_HOST);
        $mongo_url = 'mongodb://' . MDB_USER . ':' . MDB_PASSWORD . '@' . $mongo_host;
        
        $PERSIST_CON = new Client($mongo_url, [
            'username' => MDB_USER,
            'password' => MDB_PASSWORD,
            'authSource' => 'admin'
        ], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]);
        
        return $PERSIST_CON;
    }
}
```

**Why This Order**:
1. Connection is foundation - all methods depend on it
2. Singleton pattern preserved (global $PERSIST_CON)
3. typeMap ensures arrays returned (not objects), matching v1 behavior

### Step 2: Query Methods (CRITICAL PATH)

**Priority Order** (based on usage frequency):

#### 2.1 `query($vars, $page, $rppage, $fields)` - Line 2012
Most frequently used. Returns cursor for iteration.

**Current**:
```php
function query($vars = [], $page = 0, $rppage = 40, $fields = []) {
    $table = MDB_PREFIX . 'sitebase_pref';
    $rs = $this->conn->$table->$this->table->find($vars);
    return $rs; // MongoCursor
}
```

**Target**:
```php
function query($vars = [], $page = 0, $rppage = 40, $fields = []) {
    $vars = MongoCompat::convertFilter($vars);
    
    $options = [];
    if (!empty($fields)) {
        $options['projection'] = $fields;
    }
    if ($page > 0) {
        $options['skip'] = ($page - 1) * $rppage;
    }
    if ($rppage > 0) {
        $options['limit'] = $rppage;
    }
    
    $cursor = $this->collection->find($vars, $options);
    return $cursor; // MongoDB\Driver\Cursor
}
```

#### 2.2 `findOne($vars, $out)` - Line 1011
Returns single document or null.

**Current**:
```php
function findOne($vars, $out = []) {
    $table = MDB_PREFIX . 'sitebase_pref';
    $arr = $this->conn->$table->$this->table->findOne($vars, $out);
    return $arr; // array or null
}
```

**Target**:
```php
function findOne($vars, $out = []) {
    $vars = MongoCompat::convertFilter($vars);
    
    $options = [];
    if (!empty($out)) {
        $options['projection'] = $out;
    }
    
    $doc = $this->collection->findOne($vars, $options);
    return $doc; // array or null (typeMap handles this)
}
```

#### 2.3 `create_update($vars, $fields)` - Line 1333
Upsert pattern (update if exists, insert if not).

**Current**:
```php
function create_update($vars, $fields = []) {
    $table = MDB_PREFIX . 'sitebase_pref';
    $coll = $this->conn->$table->$this->table;
    
    if ($exists = $coll->findOne($vars)) {
        $coll->update($vars, ['$set' => $fields], ['upsert' => true]);
        return $exists['id' . $this->table];
    } else {
        $coll->insert($fields);
        return $fields['id' . $this->table];
    }
}
```

**Target**:
```php
function create_update($vars, $fields = []) {
    $vars = MongoCompat::convertFilter($vars);
    $fields = MongoCompat::convertFilter($fields);
    
    $result = $this->collection->updateOne(
        $vars, 
        ['$set' => $fields], 
        ['upsert' => true]
    );
    
    if ($result->getUpsertedCount() > 0) {
        // New document inserted
        return $fields['id' . $this->table];
    } else {
        // Document updated - fetch ID
        $doc = $this->collection->findOne($vars);
        return $doc['id' . $this->table];
    }
}
```

#### 2.4 `insert($vars)` - Line 1370

**Target**:
```php
function insert($vars = []) {
    $vars = MongoCompat::convertFilter($vars);
    
    $result = $this->collection->insertOne($vars);
    return $vars['id' . $this->table]; // Return PK
}
```

#### 2.5 `update($vars, $fields, $upsert)` - Line 1648

**Target**:
```php
function update($vars, $fields = [], $upsert = true) {
    $vars = MongoCompat::convertFilter($vars);
    $fields = MongoCompat::convertFilter($fields);
    
    $result = $this->collection->updateMany(
        $vars, 
        $fields,  // Assuming $fields already has $set/$unset operators
        ['upsert' => $upsert]
    );
    
    return $result->getModifiedCount();
}
```

#### 2.6 `remove($vars)` - Line 1786

**Target**:
```php
function remove($vars = []) {
    $vars = MongoCompat::convertFilter($vars);
    
    $result = $this->collection->deleteMany($vars);
    return $result->getDeletedCount();
}
```

#### 2.7 `distinct($groupBy, $vars, $limit, $mode, $field, $sort_field)` - Line 886
Complex aggregation - needs pipeline conversion.

**Target**:
```php
function distinct($groupBy, $vars = [], $limit = 200, $mode = 'full', $field = '', $sort_field = ['nom', 1]) {
    $vars = MongoCompat::convertFilter($vars);
    
    // Build aggregation pipeline
    $pipeline = [];
    
    if (!empty($vars)) {
        $pipeline[] = ['$match' => $vars];
    }
    
    $pipeline[] = [
        '$group' => [
            '_id' => '$' . $groupBy,
            'count' => ['$sum' => 1]
        ]
    ];
    
    if (!empty($sort_field)) {
        $pipeline[] = ['$sort' => [$sort_field[0] => $sort_field[1]]];
    }
    
    if ($limit > 0) {
        $pipeline[] = ['$limit' => $limit];
    }
    
    $cursor = $this->collection->aggregate($pipeline);
    return MongoCompat::cursorToArray($cursor);
}
```

### Step 3: Helper Methods

#### Cursor Iteration Wrapper
Many places use `while ($row = $rs->fetchRow())` pattern. Create compatibility method:

```php
public function iterateCursor($cursor) {
    // Wrapper for legacy fetchRow() pattern
    foreach ($cursor as $doc) {
        yield $doc; // Generator pattern
    }
}
```

Usage:
```php
$rs = $app->query(['estActif' => 1]);
foreach ($app->iterateCursor($rs) as $row) {
    // ... process row
}
```

### Step 4: Testing Strategy

#### Unit Test Each Method
Add to test_migration.php:

```php
// Test query
$app = new App('agent');
$cursor = $app->query(['estActifAgent' => 1], 1, 10);
$docs = MongoCompat::cursorToArray($cursor);
assert(count($docs) <= 10);

// Test findOne
$doc = $app->findOne(['idagent' => 1]);
assert(is_array($doc) || is_null($doc));

// Test create_update
$id = $app->create_update(
    ['test_key' => 'test_value'], 
    ['test_field' => 'test_data']
);
assert(is_numeric($id));
```

## Implementation Order

1. ✅ MongoCompat helper created (Phase 1)
2. ⏳ Connection refactor (__construct)
3. ⏳ Query method (query, findOne)
4. ⏳ Write methods (create_update, insert, update, remove)
5. ⏳ Aggregation (distinct, count)
6. ⏳ FK methods (get_grille_fk, get_reverse_grille_fk)
7. ⏳ Test each method individually
8. ⏳ Integration test with json_data.php

## Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Breaking existing code | HIGH | Keep method signatures identical, test each change |
| Cursor iteration breaks | HIGH | Use typeMap for arrays, add iterateCursor() wrapper |
| FK queries fail | MEDIUM | Test reverse relationships extensively |
| Performance degradation | MEDIUM | Add indexes, profile queries |
| MongoId conversion errors | HIGH | Use MongoCompat::convertFilter() everywhere |

## Rollback Plan

If migration fails, revert to:
```php
class App extends \MongoClient {
    // Original v1 driver code
}
```

Keep original ClassApp.php as `ClassApp.php.backup`.

## Completion Criteria

- [ ] All 7 core methods migrated
- [ ] Unit tests pass for each method
- [ ] json_data.php returns identical JSON structure
- [ ] No MongoDB errors in PHP error log
- [ ] Performance within 10% of original

## Next Steps

Proceed with Step 1 (Connection refactor) when ready.
