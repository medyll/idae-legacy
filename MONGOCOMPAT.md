# MONGOCOMPAT.md — MongoDB Driver Compatibility Helper

**Purpose**: Central utility class for MongoDB v1.x → modern driver conversions. Single source of truth for MongoId, MongoRegex, MongoDate, cursor handling.

**Location**: `appclasses/appcommon/MongoCompat.php`

**Usage**: Import in ClassApp.php (Phase 2), services (Phases 4-5), modules (Phase 6), Node.js utilities (Phase 7).

---

## Class Interface

### Namespace & class definition
```php
namespace AppCommon;

class MongoCompat {
  // Static utility methods, no instantiation
}
```

---

## Methods

### 1. `toObjectId($value)`

**Purpose**: Convert string/MongoId to ObjectId (modern driver).

**Signature**:
```php
public static function toObjectId($value)
  : \MongoDB\BSON\ObjectId|null
```

**Parameters**:
- `$value` (string|MongoId|ObjectId|null): ID to convert

**Returns**:
- `ObjectId` instance if valid
- `null` if empty/invalid

**Usage contexts**:
- Isolé: `$id = MongoCompat::toObjectId($_POST['id'])`
- ClassApp: `$filter = ['_id' => MongoCompat::toObjectId($id)]`
- Service: `$product = $app->findOne(['idproduit' => (int)$_POST['id']])` (usually int, not ObjectId)

**Notes**:
- Most idae tables use `idtable` (integer PK), not `_id` (ObjectId)
- Only convert actual `_id` fields, not business keys

---

### 2. `toRegex($pattern, $flags = 'i')`

**Purpose**: Convert MongoRegex to modern regex format.

**Signature**:
```php
public static function toRegex($pattern, $flags = 'i')
  : \MongoDB\BSON\Regex|null
```

**Parameters**:
- `$pattern` (string): Regex pattern (without delimiters)
- `$flags` (string): Regex flags ('i' = case-insensitive, 'm' = multiline, 's' = dotall, 'x' = extended)

**Returns**:
- `Regex` instance for MongoDB queries
- `null` if invalid pattern

**Usage contexts**:
- Isolé: `$regex = MongoCompat::toRegex('widget', 'i')`
- ClassApp: `['nomProduit' => ['$regex' => MongoCompat::toRegex('search_term', 'i')]]`
- Service (json_data_search.php): `$pattern = MongoCompat::toRegex(preg_quote($_GET['q']), 'i')`

**Notes**:
- Always quote user input: `preg_quote($_GET['search'])`
- Default flag 'i' (case-insensitive) for search fields

---

### 3. `toDate($value)`

**Purpose**: Convert MongoDate / string / timestamp to DateTime (modern driver returns DateTime).

**Signature**:
```php
public static function toDate($value)
  : \DateTime|null
```

**Parameters**:
- `$value` (MongoDate|string|int|DateTime|null): Date value to convert

**Returns**:
- `DateTime` instance if valid
- `null` if empty

**Usage contexts**:
- Isolé: `$date = MongoCompat::toDate('2026-02-02')`
- ClassApp: stats methods returning dates
- Service: export/import dates

**Notes**:
- Modern driver returns DateTime from MongoDB, not MongoDate
- Handle both ISO string and timestamp integer formats

---

### 4. `cursorToArray($cursor)`

**Purpose**: Convert MongoDB cursor to indexed array (backward compat with ADODB `fetchAll()`).

**Signature**:
```php
public static function cursorToArray($cursor)
  : array
```

**Parameters**:
- `$cursor` (Cursor|array): MongoDB cursor or array

**Returns**:
- Array of documents

**Usage contexts**:
- Isolé: `$results = MongoCompat::cursorToArray($cursor)`
- ClassApp: aggregation results
- Service: batch operations

**Notes**:
- Modern `find()` already iterable, but this normalizes for legacy code
- Converts Cursor → array (safe for nested iteration)

---

### 5. `toIntSafe($value, $default = 0)`

**Purpose**: Convert string/float to integer safely.

**Signature**:
```php
public static function toIntSafe($value, $default = 0)
  : int
```

**Parameters**:
- `$value` (mixed): Value to convert
- `$default` (int): Default if invalid

**Returns**:
- Integer value or default

**Usage contexts**:
- Service: `(int)$_POST['idproduit']` → `MongoCompat::toIntSafe($_POST['idproduit'])`
- ClassApp: ensure numeric PKs are typed correctly

**Notes**:
- Prevents type juggling bugs ("123abc" → 123, not "123abc" string)

---

### 6. `toFieldName($codeField, $tableName)`

**Purpose**: Convert dynamic field naming (idae pattern: `codeAppscheme_field` + table).

**Signature**:
```php
public static function toFieldName($codeField, $tableName)
  : string
```

**Parameters**:
- `$codeField` (string): Field code (e.g., "nom")
- `$tableName` (string): Table name (e.g., "produit")

**Returns**:
- MongoDB field name (e.g., "nomProduit")

**Usage contexts**:
- ClassApp: dynamic field naming logic
- Services: schema-driven queries
- Modules: form field mapping

**Notes**:
- Pattern: `$fieldCode + ucfirst($tableName)` → "nomProduit"
- Critical for schema-driven architecture

---

## Rollout Plan by Phase

| Phase | Usage | Details |
|-------|-------|---------|
| **Phase 2** | Create helper | Define MongoCompat class, basic methods (toObjectId, toRegex) |
| **Phase 3** | ClassApp FK | Use toObjectId for FK queries if needed |
| **Phase 4** | Services data | toRegex in json_search.php, toFieldName for dynamic fields |
| **Phase 5** | Services métier | toDate, toIntSafe for business logic |
| **Phase 6** | Modules | cursorToArray for module data fetch |
| **Phase 7** | Node.js (optional) | Helper concept ported to JavaScript if needed |
| **Phase 8** | Integration | All helpers consistently used, test coverage |

---

## Implementation Notes

### Error handling
- All methods return `null` on error (safe fail)
- Caller must check null before using (e.g., `if ($id = MongoCompat::toObjectId($val)) { ... }`)
- Log errors to PHP error_log for debugging

### Testing helper
Example test in `test_migration.php`:
```php
<?php
use AppCommon\MongoCompat;

// Test toObjectId
$id = MongoCompat::toObjectId('507f1f77bcf86cd799439011');
assert($id instanceof \MongoDB\BSON\ObjectId);

// Test toRegex
$regex = MongoCompat::toRegex('test', 'i');
assert($regex instanceof \MongoDB\BSON\Regex);

// Test toDate
$date = MongoCompat::toDate('2026-02-02');
assert($date instanceof \DateTime);

echo "All MongoCompat tests passed.\n";
?>
```

---

## Backward Compatibility Notes

**v1.x Mongo driver types** (to deprecate):
- `MongoId` → `ObjectId`
- `MongoRegex` → `Regex` (BSON type)
- `MongoDate` → `DateTime`
- `MongoBinData` → `Binary`

**Cursor handling**:
- v1: `$rs->fetchRow()` (ADODB style)
- Modern: `foreach($cursor as $doc)` (native iteration)
- MongoCompat::cursorToArray() bridges gap

---

## Questions for implementation

1. **Should null returns throw Exception instead?** → No, null-safe design preferred for resilience
2. **Version lock mongodb/mongodb to specific version?** → Yes, recommend v1.15.0 for stability
3. **Include logging (error_log) in each method?** → Yes, minimal logging for debugging
4. **Unit test coverage requirements?** → Minimum 80% (critical path: toObjectId, toRegex, toDate)

---

**Status**: Ready for implementation in Phase 2.

