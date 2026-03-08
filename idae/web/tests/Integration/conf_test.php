<?php
// Minimal configuration stub used by integration tests that include services directly.
// Keep this file intentionally small to avoid pulling the full application bootstrap.

if (!defined('SITEPATH')) {
    define('SITEPATH', realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
    define('APPPATH', SITEPATH);
}
if (!defined('DOCUMENTROOT')) {
    define('DOCUMENTROOT', SITEPATH);
}
if (!defined('BASE_APP')) {
    define('BASE_APP', '');
}
// Ensure include path contains the web root so require_once calls using __DIR__ work
set_include_path(get_include_path() . PATH_SEPARATOR . SITEPATH);
