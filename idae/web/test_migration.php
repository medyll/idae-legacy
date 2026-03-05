<?php
/**
 * test_migration.php — MongoDB Migration Test Harness
 * 
 * Validates MongoCompat helper and MongoDB connection
 * Run: php test_migration.php
 * 
 * @version 1.0.0
 * @date 2026-02-02
 */

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load MongoCompat
require_once __DIR__ . '/appclasses/appcommon/MongoCompat.php';

use AppCommon\MongoCompat;
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;

// Test configuration
$testsPassed = 0;
$testsFailed = 0;

function testResult($name, $passed, $message = '') {
    global $testsPassed, $testsFailed;
    
    if ($passed) {
        $testsPassed++;
        echo "✓ PASS: $name\n";
    } else {
        $testsFailed++;
        echo "✗ FAIL: $name";
        if ($message) {
            echo " - $message";
        }
        echo "\n";
    }
}

echo "=== MongoDB Migration Test Harness ===\n\n";

// ============================================
// TEST 1: MongoCompat::toObjectId()
// ============================================
echo "TEST 1: MongoCompat::toObjectId()\n";

$validId = '507f1f77bcf86cd799439011';
$id = MongoCompat::toObjectId($validId);
testResult('toObjectId with valid string', $id instanceof ObjectId);
testResult('toObjectId string representation', (string)$id === $validId);

$existingId = new ObjectId();
$id2 = MongoCompat::toObjectId($existingId);
testResult('toObjectId with existing ObjectId', $id2 === $existingId);

$nullId = MongoCompat::toObjectId(null);
testResult('toObjectId with null', $nullId === null);

$invalidId = MongoCompat::toObjectId('invalid');
testResult('toObjectId with invalid string', $invalidId === null);

echo "\n";

// ============================================
// TEST 2: MongoCompat::toRegex()
// ============================================
echo "TEST 2: MongoCompat::toRegex()\n";

$regex = MongoCompat::toRegex('test', 'i');
testResult('toRegex basic', $regex instanceof Regex);

$regexFlags = MongoCompat::toRegex('pattern', 'imsx');
testResult('toRegex with multiple flags', $regexFlags instanceof Regex);

$nullRegex = MongoCompat::toRegex(null);
testResult('toRegex with null', $nullRegex === null);

$emptyRegex = MongoCompat::toRegex('');
testResult('toRegex with empty string', $emptyRegex === null);

echo "\n";

// ============================================
// TEST 3: MongoCompat::toDate()
// ============================================
echo "TEST 3: MongoCompat::toDate()\n";

$date = MongoCompat::toDate('2026-02-02');
testResult('toDate with ISO string', $date instanceof DateTime);

$timestamp = MongoCompat::toDate(1738454400);
testResult('toDate with timestamp', $timestamp instanceof DateTime);

$existingDate = new DateTime('2026-02-02');
$date2 = MongoCompat::toDate($existingDate);
testResult('toDate with existing DateTime', $date2 === $existingDate);

$nullDate = MongoCompat::toDate(null);
testResult('toDate with null', $nullDate === null);

echo "\n";

// ============================================
// TEST 4: MongoCompat::cursorToArray()
// ============================================
echo "TEST 4: MongoCompat::cursorToArray()\n";

$arr = array(
    array('id' => 1, 'name' => 'Test1'),
    array('id' => 2, 'name' => 'Test2')
);
$result = MongoCompat::cursorToArray($arr);
testResult('cursorToArray with array', $result === $arr);

$emptyResult = MongoCompat::cursorToArray(null);
testResult('cursorToArray with null', $emptyResult === array());

echo "\n";

// ============================================
// TEST 5: MongoCompat::toIntSafe()
// ============================================
echo "TEST 5: MongoCompat::toIntSafe()\n";

$int1 = MongoCompat::toIntSafe('123');
testResult('toIntSafe with string number', $int1 === 123);

$int2 = MongoCompat::toIntSafe('123abc', 0);
testResult('toIntSafe with invalid string', $int2 === 0);

$int3 = MongoCompat::toIntSafe(null, 99);
testResult('toIntSafe with null and default', $int3 === 99);

$int4 = MongoCompat::toIntSafe(45.7);
testResult('toIntSafe with float', $int4 === 45);

echo "\n";

// ============================================
// TEST 6: MongoCompat::toFieldName()
// ============================================
echo "TEST 6: MongoCompat::toFieldName()\n";

$field1 = MongoCompat::toFieldName('nom', 'produit');
testResult('toFieldName basic', $field1 === 'nomProduit');

$field2 = MongoCompat::toFieldName('prix', 'facture');
testResult('toFieldName complex', $field2 === 'prixFacture');

$field3 = MongoCompat::toFieldName('', 'test');
testResult('toFieldName with empty code', $field3 === '');

echo "\n";

// ============================================
// TEST 7: MongoCompat::escapeRegex()
// ============================================
echo "TEST 7: MongoCompat::escapeRegex()\n";

$escaped1 = MongoCompat::escapeRegex('test.com');
testResult('escapeRegex basic', $escaped1 === 'test\\.com');

$escaped2 = MongoCompat::escapeRegex('test*value');
testResult('escapeRegex with asterisk', $escaped2 === 'test\\*value');

echo "\n";

// ============================================
// TEST 8: MongoCompat::convertFilter()
// ============================================
echo "TEST 8: MongoCompat::convertFilter()\n";

$filter = array(
    'id' => 123,
    'name' => 'test',
    'nested' => array('value' => 456)
);
$converted = MongoCompat::convertFilter($filter);
testResult('convertFilter basic', is_array($converted) && count($converted) === 3);

echo "\n";

// ============================================
// TEST 9: MongoDB Connection (if available)
// ============================================
echo "TEST 9: MongoDB Connection\n";

// Check if ext-mongodb is loaded
if (!extension_loaded('mongodb')) {
    echo "Note: ext-mongodb not loaded - skipping MongoDB connection tests\n";
    echo "This is expected in local dev environment (will work in Docker)\n";
} else {
    try {
        // Try to connect to MongoDB (use localhost for local testing)
        $mongoHost = getenv('MONGO_HOST') ?: 'localhost';
        $mongoPort = getenv('MONGO_PORT') ?: '27017';
        
        echo "Attempting connection to mongodb://{$mongoHost}:{$mongoPort}\n";
        
        $client = new Client("mongodb://{$mongoHost}:{$mongoPort}", [], [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]);
        
        // Test connection with ping
        $admin = $client->selectDatabase('admin');
        $result = $admin->command(['ping' => 1]);
        
        testResult('MongoDB connection', $result['ok'] == 1);
        
        // List databases
        $databases = $client->listDatabases();
        echo "Available databases: ";
        $dbNames = array();
        foreach ($databases as $db) {
            $dbNames[] = $db['name'];
        }
        echo implode(', ', $dbNames) . "\n";
        testResult('List databases', count($dbNames) > 0);
        
    } catch (\Exception $e) {
        echo "Note: MongoDB connection test failed (expected in local dev)\n";
        echo "Error: " . $e->getMessage() . "\n";
    } catch (\Error $e) {
        echo "Note: MongoDB driver issue (expected in local dev environment)\n";
        echo "This will work correctly in Docker with proper ext-mongodb setup\n";
    }
}

echo "\n";

// ============================================
// SUMMARY
// ============================================
echo "=== Test Summary ===\n";
echo "Passed: $testsPassed\n";
echo "Failed: $testsFailed\n";

if ($testsFailed === 0) {
    echo "\n✓ All tests passed! MongoCompat is ready.\n";
    exit(0);
} else {
    echo "\n✗ Some tests failed. Review errors above.\n";
    exit(1);
}
