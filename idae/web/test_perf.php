<?php
/**
 * Performance diagnostic test
 */
echo "=== Performance Diagnostic ===\n\n";

$step = function($name) {
    static $last = null;
    $now = microtime(true);
    if ($last !== null) {
        $elapsed = round(($now - $last) * 1000, 2);
        echo " [{$elapsed}ms]\n";
    }
    echo "{$name}...";
    $last = $now;
    flush();
};

$step("Loading defines");
define('APPPATH', __DIR__ . '/../../');
define('MDB_USER', 'admin');
define('MDB_PASS', 'adminpassword');
define('MDB_HOST', 'mongodb');
define('MDB_PREFIX', 'idaenext_');

$step("Loading MongoCompat");
require_once(__DIR__ . '/appclasses/appcommon/MongoCompat.php');

$step("Loading MongodbCursorWrapper");
require_once(__DIR__ . '/appclasses/appcommon/MongodbCursorWrapper.php');

$step("Loading ClassApp");
require_once(__DIR__ . '/appclasses/appcommon/ClassApp.php');

$step("Creating App instance (empty)");
$app_empty = new App();

$step("Creating App instance (appscheme)");
$app = new App('appscheme');

$step("Calling findOne");
$result = $app->findOne(['codeAppscheme' => 'appscheme']);

$step("Calling find");
$cursor = $app->find(['codeAppscheme' => ['$exists' => true]]);

$step("Iterating cursor (first 3)");
$count = 0;
while ($doc = $cursor->getNext()) {
    $count++;
    if ($count >= 3) break;
}

$step("Complete");
echo "\nTotal documents: {$count}\n";
echo "\n✓ All steps completed\n";
