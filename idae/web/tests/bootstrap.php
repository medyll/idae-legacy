<?php
declare(strict_types=1);

/**
 * PHPUnit bootstrap — loaded once before any test runs.
 *
 * Responsibilities:
 *  1. Hard-abort if MONGO_ENV is not "test" (belt-and-suspenders guard).
 *  2. Load the Composer autoloader (covers both prod classes and Idae\Tests\*).
 *  3. Define path constants used by ClassApp and helpers so they resolve
 *     correctly when invoked from the CLI / Docker exec context.
 *
 * Date: 2026-03-02
 */

// ------------------------------------------------------------------
// 1. Safety guard — must be the very first check
// ------------------------------------------------------------------
$mongo_env = getenv('MONGO_ENV') ?: ($_ENV['MONGO_ENV'] ?? 'dev');
if ($mongo_env !== 'test') {
    fwrite(STDERR,
        "\n[PHPUnit bootstrap] FATAL: MONGO_ENV must be \"test\".\n" .
        "Current value: \"$mongo_env\".\n" .
        "Tests aborted to protect the production database.\n\n"
    );
    exit(1);
}

// ------------------------------------------------------------------
// 2. Composer autoloader
// ------------------------------------------------------------------
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR,
        "\n[PHPUnit bootstrap] FATAL: vendor/autoload.php not found.\n" .
        "Run: composer install\n\n"
    );
    exit(1);
}
require_once $autoload;

// ------------------------------------------------------------------
// 3. Path constants (mirrors conf.inc.php minimal set needed by tests)
// ------------------------------------------------------------------
if (!defined('SITEPATH')) {
    define('SITEPATH', __DIR__ . '/');
}
if (!defined('APPPATH')) {
    define('APPPATH', __DIR__ . '/appclasses/');
}
if (!defined('MDB_PREFIX')) {
    define('MDB_PREFIX', '');
}
if (!defined('MDB_USER')) {
    define('MDB_USER', '');
}
if (!defined('MDB_PASSWORD')) {
    define('MDB_PASSWORD', '');
}
if (!defined('MDB_HOST')) {
    define('MDB_HOST', 'mongo-test');
}
