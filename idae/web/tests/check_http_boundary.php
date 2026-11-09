<?php
declare(strict_types=1);

/**
 * Verify Apache access restrictions on both application virtual hosts.
 * @package Idae\Tests
 * @date 2026-11-09
 */

$paths = [
    '/app_node/.env' => 403,
    '/app_node/package.json' => 403,
    '/tests/' => 403,
    '/tests/bootstrap.php' => 403,
    '/vendor/' => 403,
    '/vendor/autoload.php' => 403,
    '/composer.lock' => 403,
    '/.htaccess' => 403,
    '/inf.php' => 403,
    '/inf_crfr.php' => 403,
    '/test_integration.php' => 403,
    '/javascript/main_bag.js' => 200,
];

foreach (['localhost', 'idae.preprod.lan'] as $host) {
    $context = stream_context_create(['http' => [
        'method' => 'HEAD',
        'header' => 'Host: ' . $host,
        'follow_location' => 0,
        'timeout' => 5,
    ]]);
    foreach ($paths as $path => $expected) {
        $headers = get_headers('http://127.0.0.1' . $path, false, $context);
        $status = $headers[0] ?? 'No HTTP response';
        if (!preg_match('/^HTTP\/\S+ ' . $expected . '\b/', $status)) {
            fwrite(STDERR, "$host $path expected $expected, received $status\n");
            exit(1);
        }
    }
}

echo 'Apache access checks passed for both virtual hosts.', PHP_EOL;
