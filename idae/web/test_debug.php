<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/conf.inc.php';

try {
    $app = new App('appscheme');
    echo "App instantiated\n";
    $count = $app->app_conn ? $app->app_conn->countDocuments() : -1;
    echo "appscheme documents: " . $count . "\n";
    echo "app_table_one: " . (empty($app->app_table_one) ? 'empty' : 'loaded') . "\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
