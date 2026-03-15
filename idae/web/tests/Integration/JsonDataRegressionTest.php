<?php
declare(strict_types=1);

namespace {
    // Lightweight stubs used by tests when including services/json_data.php directly.
    // Each test resets $_POST['table'] before including the service; the App stub
    // reads that value so query() targets the right collection.

    if (!class_exists('fonctionsProduction')) {
        class fonctionsProduction {
            public static function cleanPostMongo($vars, $a = 1) { return $vars; }
        }
    }

    if (!class_exists('Act')) {
        class Act {
            public static function decodeVars($arr) { return $arr; }
            public static function imageSite($table, $id, $size, ...$rest) { return ''; }
        }
    }

    if (!class_exists('App')) {
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
}

namespace Idae\Tests\Integration {

use Idae\Tests\TestCase;

/**
 * JsonDataRegressionTest
 *
 * Validates that services/json_data.php returns structurally correct JSON for
 * multiple tables. Covers three tables — produit, agent, appscheme — across the
 * two most-used PIECE modes: 'data' (default) and 'query'.
 *
 * Prerequisites:
 *  - MONGO_ENV=test and mongo-test sidecar reachable (see BUG-03 / status.yaml)
 *  - json_data.php must use `return` not `exit` to be require-safe (done 2026-03-15)
 */
class JsonDataRegressionTest extends TestCase
{
    protected array $collectionsToClean = ['produit', 'agent', 'appscheme'];

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['CONF_INC']      = realpath(__DIR__ . '/conf_test.php');
        $_SERVER['HTTP_HOST']     = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Include json_data.php with the given POST vars, capture and decode output.
     *
     * @param  array<string,mixed>  $post
     * @return array<mixed>
     */
    private function callJsonData(array $post): array
    {
        $_POST = $post;
        ob_start();
        try {
            require __DIR__ . '/../../services/json_data.php';
        } finally {
            $output = (string) ob_get_clean();
        }
        $this->assertNotEmpty($output, 'json_data.php returned no output for table=' . ($post['table'] ?? '?'));
        $decoded = json_decode($output, true);
        $this->assertIsArray($decoded, 'json_data.php did not return a JSON array/object');
        return $decoded;
    }

    // ------------------------------------------------------------------
    // Table: produit — PIECE=data (default)
    // ------------------------------------------------------------------

    public function testProduitDataShapeAndRows(): void
    {
        $this->insertMany('produit', [
            ['idproduit' => 2001, 'nomProduit' => 'Shape Alpha', 'prixProduit' => 1.11, 'actif' => 1],
            ['idproduit' => 2002, 'nomProduit' => 'Shape Beta',  'prixProduit' => 2.22, 'actif' => 1],
        ]);

        $rows = $this->callJsonData(['table' => 'produit', 'piece' => 'data']);

        // PIECE=data returns a flat array of row objects
        $this->assertIsArray($rows);
        $this->assertGreaterThanOrEqual(2, count($rows));

        $first = $rows[0];
        // Legacy row shape keys
        foreach (['_id', 'idproduit', 'nom', 'nomProduit', 'prod'] as $key) {
            $this->assertArrayHasKey($key, $first, "Row missing key '$key'");
        }
        // Image link keys always present (empty string in test stub)
        foreach (['linkSrc_mini', 'linkSrc_small', 'linkSrc_large'] as $key) {
            $this->assertArrayHasKey($key, $first, "Row missing link key '$key'");
        }

        $names = array_column($rows, 'nomProduit');
        $this->assertContains('Shape Alpha', $names);
        $this->assertContains('Shape Beta', $names);
    }

    // ------------------------------------------------------------------
    // Table: produit — PIECE=query (returns {count, maxcount, rs})
    // ------------------------------------------------------------------

    public function testProduitQueryShapeHasCountAndRs(): void
    {
        $this->insertMany('produit', [
            ['idproduit' => 2003, 'nomProduit' => 'Query Gamma', 'actif' => 1],
        ]);

        $result = $this->callJsonData(['table' => 'produit', 'piece' => 'query']);

        $this->assertArrayHasKey('count',    $result, 'query piece missing key "count"');
        $this->assertArrayHasKey('maxcount', $result, 'query piece missing key "maxcount"');
        $this->assertArrayHasKey('rs',       $result, 'query piece missing key "rs"');
        $this->assertIsArray($result['rs']);
        $this->assertGreaterThanOrEqual(1, $result['count']);
    }

    // ------------------------------------------------------------------
    // Table: agent — PIECE=data
    // ------------------------------------------------------------------

    public function testAgentDataShapeAndRows(): void
    {
        // agent records already present from seed; add extras for isolation
        $this->insertMany('agent', [
            ['idagent' => 3001, 'nomAgent' => 'Regress Agent', 'loginAgent' => 'r_agent', 'actif' => 1],
        ]);

        $rows = $this->callJsonData(['table' => 'agent', 'piece' => 'data']);

        $this->assertIsArray($rows);
        $this->assertGreaterThanOrEqual(1, count($rows));

        $first = $rows[0];
        foreach (['_id', 'idagent', 'nom', 'nomAgent', 'prod'] as $key) {
            $this->assertArrayHasKey($key, $first, "agent row missing key '$key'");
        }
        foreach (['linkSrc_mini', 'linkSrc_small', 'linkSrc_large'] as $key) {
            $this->assertArrayHasKey($key, $first, "agent row missing link key '$key'");
        }

        $names = array_column($rows, 'nomAgent');
        $this->assertContains('Regress Agent', $names);
    }

    // ------------------------------------------------------------------
    // Table: appscheme — PIECE=data
    // ------------------------------------------------------------------

    public function testAppschemeDataShapeAndRows(): void
    {
        // appscheme already seeded with produit + agent entries; verify shape
        $rows = $this->callJsonData(['table' => 'appscheme', 'piece' => 'data']);

        $this->assertIsArray($rows);
        $this->assertGreaterThanOrEqual(1, count($rows), 'appscheme collection returned no rows');

        $first = $rows[0];
        foreach (['_id', 'idappscheme', 'nom', 'nomAppscheme', 'prod'] as $key) {
            $this->assertArrayHasKey($key, $first, "appscheme row missing key '$key'");
        }
    }

    // ------------------------------------------------------------------
    // Table: appscheme — PIECE=query (count/maxcount/rs contract)
    // ------------------------------------------------------------------

    public function testAppschemeQueryShapeHasCountAndRs(): void
    {
        $result = $this->callJsonData(['table' => 'appscheme', 'piece' => 'query']);

        $this->assertArrayHasKey('count',    $result);
        $this->assertArrayHasKey('maxcount', $result);
        $this->assertArrayHasKey('rs',       $result);
        $this->assertIsArray($result['rs']);
    }
}
}
