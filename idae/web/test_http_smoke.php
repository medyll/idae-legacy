<?php
/**
 * HTTP Smoke Test — hits PHP endpoints via curl and reports 500 errors.
 * Catches PHP 8.2 compatibility issues (null string args, type errors, etc.)
 *
 * Usage:
 *   php test_http_smoke.php [base_url] [loginAgent] [passwordAgent]
 *
 * Example:
 *   php test_http_smoke.php http://localhost:8080 admin admin
 *
 * Can also run from browser (inside Docker):
 *   http://localhost:8080/test_http_smoke.php?base_url=http://localhost&login=admin&password=admin
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Load .env.testing if available ---
$envFile = __DIR__ . '/../../.env.testing';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            [$key, $val] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($val);
        }
    }
}

// --- Configuration (priority: CLI args > .env.testing > defaults) ---
$BASE_URL = $argv[1] ?? ($_GET['base_url'] ?? ($_ENV['TEST_BASE_URL'] ?? 'http://localhost:8080'));
$LOGIN    = $argv[2] ?? ($_GET['login'] ?? ($_ENV['TEST_LOGIN'] ?? 'admin'));
$PASSWORD = $argv[3] ?? ($_GET['password'] ?? ($_ENV['TEST_PASSWORD'] ?? 'admin'));

// Test table/value for endpoints that need them
$TEST_TABLE       = $_ENV['TEST_TABLE'] ?? 'client';
$TEST_TABLE_VALUE = $_ENV['TEST_TABLE_VALUE'] ?? '63376';

$isCli = (php_sapi_name() === 'cli');

// --- Output helpers ---
function out($msg, $isCli = true) {
    echo $isCli ? $msg : nl2br(htmlspecialchars($msg));
}

function colorStatus($status, $isCli) {
    if ($status >= 500) return $isCli ? "\033[31m$status\033[0m" : "<span style='color:red;font-weight:bold'>$status</span>";
    if ($status >= 400) return $isCli ? "\033[33m$status\033[0m" : "<span style='color:orange'>$status</span>";
    if ($status >= 200 && $status < 300) return $isCli ? "\033[32m$status\033[0m" : "<span style='color:green'>$status</span>";
    return (string)$status;
}

// --- HTTP client ---
function http_request($url, $method = 'GET', $postFields = '', $cookie = '') {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HEADER         => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=$cookie");
    }
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    }
    $response   = curl_exec($ch);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $error      = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['status' => 0, 'headers' => '', 'body' => '', 'error' => $error];
    }
    $headers = substr($response, 0, $headerSize);
    $body    = substr($response, $headerSize);

    // Extract PHPSESSID from Set-Cookie
    $sessid = '';
    if (preg_match('/Set-Cookie:\s*PHPSESSID=([^;]+)/i', $headers, $m)) {
        $sessid = $m[1];
    }

    return ['status' => $httpCode, 'headers' => $headers, 'body' => $body, 'error' => '', 'phpsessid' => $sessid];
}

// --- Step 1: Login to get a valid session ---
out("\n=== IDAE HTTP Smoke Tests ===\n", $isCli);
out("Base URL: $BASE_URL\n", $isCli);
out("Login: $LOGIN\n\n", $isCli);

// First, hit any page to get a PHPSESSID
$initResp = http_request("$BASE_URL/services/json_ssid.php");
$phpsessid = $initResp['phpsessid'] ?: '';

if (!$phpsessid) {
    // Try to extract from cookie header differently
    out("[WARN] Could not get initial PHPSESSID, trying login directly...\n", $isCli);
}

// Login
$loginUrl  = "$BASE_URL/mdl/app/app_login/actions.php";
$loginBody = http_build_query([
    'F_action'      => 'app_log',
    'type'          => 'agent',
    'loginAgent'    => $LOGIN,
    'passwordAgent' => $PASSWORD,
]);
$loginResp = http_request($loginUrl, 'POST', $loginBody, $phpsessid);

// Get the session from login response if available
if (!empty($loginResp['phpsessid'])) {
    $phpsessid = $loginResp['phpsessid'];
}

if ($loginResp['status'] >= 500) {
    out("[FATAL] Login endpoint returned {$loginResp['status']}. Cannot proceed.\n", $isCli);
    out("Body: " . substr($loginResp['body'], 0, 500) . "\n", $isCli);
    exit(1);
}

// Verify session
$ssidResp = http_request("$BASE_URL/services/json_ssid.php", 'GET', '', $phpsessid);
$ssidData = json_decode($ssidResp['body'], true);

if (empty($ssidData['idagent'])) {
    out("[FATAL] Login failed — no idagent in session. Response: {$ssidResp['body']}\n", $isCli);
    out("Trying to proceed anyway (some tests may fail due to auth)...\n\n", $isCli);
} else {
    out("[OK] Logged in as agent #{$ssidData['idagent']} (PHPSESSID: $phpsessid)\n\n", $isCli);
}

// --- Step 2: Define endpoints to test ---
$tests = [
    // --- Core modules (mdl/app/app/) — loaded via socketModule ---
    ['POST', '/mdl/app/app/app_fiche.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche (record detail)'],
    ['POST', '/mdl/app/app/app_fiche_mini.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_mini'],
    ['POST', '/mdl/app/app/app_fiche_entete.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_entete'],
    ['POST', '/mdl/app/app/app_fiche_fields.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_fields'],
    ['POST', '/mdl/app/app/app_fiche_maxi.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_maxi'],
    ['POST', '/mdl/app/app/app_fiche_fk.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_fk'],
    ['POST', '/mdl/app/app/app_fiche_rfk.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_rfk'],
    ['POST', '/mdl/app/app/app_fiche_has.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_has'],
    ['POST', '/mdl/app/app/app_fiche_history.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_history'],
    ['POST', '/mdl/app/app/app_fiche_document.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_fiche_document'],
    ['POST', '/mdl/app/app/app_explorer.php', "table=$TEST_TABLE", 'Module: app_explorer (list view)'],
    ['POST', '/mdl/app/app/app_explorer_home.php', "table=$TEST_TABLE", 'Module: app_explorer_home'],
    ['POST', '/mdl/app/app/app_explorer_search.php', "table=$TEST_TABLE", 'Module: app_explorer_search'],
    ['POST', '/mdl/app/app/app_console.php', "table=$TEST_TABLE", 'Module: app_console'],
    ['POST', '/mdl/app/app/app_create.php', "table=$TEST_TABLE", 'Module: app_create'],
    ['POST', '/mdl/app/app/app_update.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Module: app_update'],
    ['POST', '/mdl/app/app/app_gui.php', '', 'Module: app_gui (main UI)'],
    ['POST', '/mdl/app/app/app_menu.php', '', 'Module: app_menu'],
    ['POST', '/mdl/app/app/app_side_menu.php', '', 'Module: app_side_menu'],
    ['POST', '/mdl/app/app/app_dispatch.php', "table=$TEST_TABLE", 'Module: app_dispatch'],

    // --- JSON services ---
    ['GET',  '/services/json_ssid.php', '', 'Service: json_ssid (session info)'],
    ['POST', '/services/json_scheme.php', "table=$TEST_TABLE", 'Service: json_scheme (schema)'],
    ['POST', '/services/json_data.php', "table=$TEST_TABLE&table_value=$TEST_TABLE_VALUE", 'Service: json_data (record data)'],
    ['POST', '/services/json_data_table.php', "table=$TEST_TABLE&start=0&limit=10", 'Service: json_data_table (list data)'],
    ['POST', '/services/json_data_search.php', "table=$TEST_TABLE&search=test", 'Service: json_data_search'],

    // --- Actions entry point ---
    ['POST', '/actions.php', "F_action=test_noop", 'Actions: entry point (noop)'],
];

// --- Step 3: Run tests ---
$results  = ['pass' => 0, 'fail' => 0, 'warn' => 0, 'error' => 0];
$failures = [];

out(str_pad("Endpoint", 50) . " Method  Status  Size    Result\n", $isCli);
out(str_repeat('-', 95) . "\n", $isCli);

foreach ($tests as [$method, $path, $body, $label]) {
    $url  = $BASE_URL . $path;
    $resp = http_request($url, $method, $body, $phpsessid);

    $status   = $resp['status'];
    $bodySize = strlen($resp['body']);
    $colStat  = colorStatus($status, $isCli);

    $resultLabel = '';
    if ($resp['error']) {
        $resultLabel = 'CURL ERROR: ' . $resp['error'];
        $results['error']++;
        $failures[] = ['label' => $label, 'path' => $path, 'status' => $status, 'detail' => $resp['error']];
    } elseif ($status >= 500) {
        $resultLabel = 'FAIL (server error)';
        $results['fail']++;
        // Extract PHP error from body if display_errors is on
        $errorSnippet = '';
        if (preg_match('/Fatal error:.*$/m', $resp['body'], $m)) {
            $errorSnippet = substr($m[0], 0, 120);
        } elseif (preg_match('/Warning:.*$/m', $resp['body'], $m)) {
            $errorSnippet = substr($m[0], 0, 120);
        }
        $failures[] = ['label' => $label, 'path' => $path, 'status' => $status, 'detail' => $errorSnippet];
    } elseif ($status >= 400) {
        $resultLabel = 'WARN (client error)';
        $results['warn']++;
    } else {
        $resultLabel = 'PASS';
        $results['pass']++;
    }

    $sizeStr = str_pad(number_format($bodySize), 7, ' ', STR_PAD_LEFT);
    out(str_pad($label, 50) . " $method    $colStat   $sizeStr  $resultLabel\n", $isCli);
}

// --- Step 4: Summary ---
out("\n" . str_repeat('=', 95) . "\n", $isCli);
out("Results: {$results['pass']} passed, {$results['fail']} failed, {$results['warn']} warnings, {$results['error']} errors\n", $isCli);

if (!empty($failures)) {
    out("\n--- FAILURES ---\n", $isCli);
    foreach ($failures as $f) {
        out("  [{$f['status']}] {$f['label']} ({$f['path']})\n", $isCli);
        if ($f['detail']) {
            out("       {$f['detail']}\n", $isCli);
        }
    }
}

out("\n", $isCli);

// Also check the PHP error log for recent fatal errors
$errorLogPaths = [
    '/var/log/apache2/error.log',
    '/var/log/apache2/php-error.log',
];
foreach ($errorLogPaths as $logPath) {
    if (file_exists($logPath)) {
        $lines = array_slice(file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -30);
        $fatals = array_filter($lines, function($l) { return stripos($l, 'Fatal error') !== false || stripos($l, 'Uncaught') !== false; });
        if ($fatals) {
            out("--- Recent Fatal Errors in $logPath ---\n", $isCli);
            foreach (array_slice($fatals, -10) as $line) {
                out("  $line\n", $isCli);
            }
        }
    }
}

exit($results['fail'] > 0 || $results['error'] > 0 ? 1 : 0);
