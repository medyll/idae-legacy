<?php
declare(strict_types=1);

/**
 * Script de test d'int?gration MongoDB migration
 * Phase 5 - Validation en environnement Docker
 *
 * Usage: php test_integration.php
 * Modified: 2026-11-08 ? Run the actual PHPUnit integration suite without Web configuration.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$phpunit = getenv('IDAE_PHPUNIT_BINARY') ?: __DIR__ . '/vendor/bin/phpunit';
if (!is_file($phpunit)) {
    fwrite(STDERR, "PHPUnit is missing. Install Composer dev dependencies before running integration tests.\n");
    exit(2);
}

// The test bootstrap refuses any environment other than test. Never load conf.inc.php here.
putenv('MONGO_ENV=test');
$process = proc_open(
    [PHP_BINARY, $phpunit, '--configuration', __DIR__ . '/phpunit.xml', '--testsuite', 'Integration'],
    [STDIN, STDOUT, STDERR],
    $pipes,
    __DIR__
);
if (!is_resource($process)) {
    fwrite(STDERR, "Could not start PHPUnit.\n");
    exit(2);
}

exit(proc_close($process));
