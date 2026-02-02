<?php
// Absolute minimal test - just connection
echo "Starting test at " . date('H:i:s.u') . "\n";
echo "Memory start: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB\n\n";

$t0 = microtime(true);

echo "[1/5] Loading defines... ";
define('APPPATH', __DIR__ . '/../../');
define('MDB_USER', 'admin');
define('MDB_PASS', 'adminpassword');
define('MDB_HOST', 'mongodb');
define('MDB_PREFIX', 'idaenext_');
echo "✓\n";

$t1 = microtime(true);
echo "[2/5] Importing MongoDB driver... ";
$mongoUrl = 'mongodb://admin:adminpassword@mongodb';
$mongoClient = new MongoDB\Client($mongoUrl, [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);
echo "✓\n";

$t2 = microtime(true);
echo "[3/5] Selecting database... ";
$db = $mongoClient->selectDatabase('idaenext_sitebase_app');
echo "✓\n";

$t3 = microtime(true);
echo "[4/5] Getting collection... ";
$col = $db->selectCollection('appscheme');
echo "✓\n";

$t4 = microtime(true);
echo "[5/5] Querying findOne... ";
$result = $col->findOne(['codeAppscheme' => 'appscheme']);
echo "✓\n";

$t5 = microtime(true);

echo "\n--- TIMINGS ---\n";
echo "Defines: " . round(($t1 - $t0) * 1000, 2) . " ms\n";
echo "MongoDB connection: " . round(($t2 - $t1) * 1000, 2) . " ms\n";
echo "Select database: " . round(($t3 - $t2) * 1000, 2) . " ms\n";
echo "Get collection: " . round(($t4 - $t3) * 1000, 2) . " ms\n";
echo "findOne query: " . round(($t5 - $t4) * 1000, 2) . " ms\n";
echo "Total: " . round(($t5 - $t0) * 1000, 2) . " ms\n";

echo "\nResult: " . ($result ? $result['nomAppscheme'] : 'NULL') . "\n";
