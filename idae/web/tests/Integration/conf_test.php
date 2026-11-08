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

// Authorization double: service tests verify the gate, not the helper's internal policy.
if (!function_exists('droit_table_enforce')) {
    function droit_table_enforce(string $code, string $table): bool
    {
        $GLOBALS['json_data_auth_calls'][] = [$code, $table];
        return !empty($_SESSION['_test_read_allowed']);
    }
}


// Service doubles are loaded only inside isolated service calls, never during PHPUnit discovery.
// Lightweight stubs used by tests when including services/json_data.php directly.
    // Each test resets $_POST['table'] before including the service; the App stub
    // reads that value so query() targets the right collection.

    if (!class_exists('fonctionsProduction', false)) {
        class fonctionsProduction {
            public static function cleanPostMongo($vars, $a = 1) { return $vars; }
        }
    }

    if (!class_exists('Act', false)) {
        class Act {
            public static function decodeVars($arr) { return $arr; }
            public static function imageSite($table, $id, $size, ...$rest) { return ''; }
        }
    }

    if (!class_exists('App', false)) {
        class App {
            /** @var array<string,mixed> */
            public array $app_table_one;
            private string $table;

            public function __construct(string $table = '') {
                $this->table = $table ?: ($_POST['table'] ?? '');
                $this->app_table_one = [
                    'codeAppscheme'      => $this->table,
                    'codeAppscheme_base' => 'sitebase_pref',
                    'nomAppscheme'       => ucfirst($this->table),
                    'hasPrixScheme'      => 0,
                    'hasTypeScheme'      => 0,
                    'hasProdScheme'      => 0,
                ];
            }

            public function get_schemes(): array       { return []; }
            public function get_array_field_bool(): array { return []; }
            public function get_sort_fields(string $t = ''): array { return []; }
            public function get_date_fields(string $t = ''): array  { return []; }
            public function get_bool_fields(string $t = ''): array  { return []; }
            public function get_grille_fk(): array     { return []; }
            public function translate_vars(array $vars): array { return $vars; }

            public function query(array $vars = [], int $page = 0, int $nbRows = 1000): \AppCommon\MongodbCursorWrapper {
                $cursor = \Idae\Tests\TestCase::$db->selectCollection($this->table)->find($vars);
                return new \AppCommon\MongodbCursorWrapper($cursor);
            }

            public function plug(string $base, string $table): mixed {
                return \Idae\Tests\TestCase::$db->selectCollection($table);
            }

            public function findOne(array $filter, array $projection = []): mixed {
                return \Idae\Tests\TestCase::$db->selectCollection($this->table)->findOne($filter, $projection);
            }

            public function distinct(string $field, array $filter, int $limit = 0): \AppCommon\MongodbCursorWrapper {
                $cursor = \Idae\Tests\TestCase::$db->selectCollection($this->table)->distinct($field, $filter);
                return new \AppCommon\MongodbCursorWrapper($cursor);
            }
        }
    }
