<?php
// Simple test without full config
echo "Test started\n";
$start = microtime(true);

// Only essential includes
define('APPPATH', __DIR__ . '/../../');
define('MDB_USER', 'admin');
define('MDB_PASS', 'adminpassword');
define('MDB_HOST', 'mongodb');

echo "Loading ClassApp...\n";
require_once(__DIR__ . '/appclasses/appcommon/MongoCompat.php');
require_once(__DIR__ . '/appclasses/appcommon/MongodbCursorWrapper.php');
require_once(__DIR__ . '/appclasses/appcommon/ClassApp.php');

echo "Creating App instance...\n";
$app = new App('appscheme');

echo "Testing findOne...\n";
$result = $app->findOne(['codeAppscheme' => 'appscheme']);
echo "Result: " . ($result ? $result['nomAppscheme'] : 'NULL') . "\n";

echo "Testing find with getNext...\n";
$cursor = $app->find(['codeAppscheme' => ['$exists' => true]]);
$count = 0;
while ($doc = $cursor->getNext()) {
    $count++;
    if ($count >= 3) break;
}
echo "Found {$count} documents\n";

$elapsed = round(microtime(true) - $start, 3);
echo "\nTotal time: {$elapsed}s\n";
echo "✓ Test completed successfully!\n";
