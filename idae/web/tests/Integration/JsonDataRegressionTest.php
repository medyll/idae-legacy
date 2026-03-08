<?php
declare(strict_types=1);

namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;

/**
 * JsonDataRegressionTest
 * - Sends simulated requests to services/json_data.php and verifies JSON shape
 * - Safe for test DB (MONGO_ENV=test)
 */
class JsonDataRegressionTest extends TestCase
{
    protected array $collectionsToClean = ['produit'];

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure conf.inc.php is found when including services directly from tests
        $_SERVER['CONF_INC'] = realpath(__DIR__ . '/../../conf.inc.php');
        $_SERVER['HTTP_HOST'] = 'localhost';
        // Ensure request method present
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = 'POST';
        }
    }

    protected function tearDown(): void
    {
        // Clean up superglobals to avoid bleed between tests
        $_POST = [];
        parent::tearDown();
    }

    public function testJsonDataListReturnsExpectedShape(): void
    {
        // Seed a predictable produit document
        $this->insertMany('produit', [
            ['idproduit' => 1001, 'nomProduit' => 'Regress One', 'prixProduit' => 1.23, 'actif' => 1],
            ['idproduit' => 1002, 'nomProduit' => 'Regress Two', 'prixProduit' => 4.56, 'actif' => 1],
        ]);

        // Simulate POST to services/json_data.php by setting $_POST and buffering include
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'action' => 'list',
            'table' => 'produit',
            'start' => 0,
            'limit' => 10,
        ];

        ob_start();
        try {
            require __DIR__ . '/../../services/json_data.php';
        } finally {
            $output = (string) ob_get_clean();
        }

        $this->assertNotEmpty($output, 'json_data.php returned no output');
        $json = json_decode($output, true);
        $this->assertIsArray($json, 'json_data.php did not return JSON array/object');

        // Expect common keys in legacy response
        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('total', $json);
        $this->assertGreaterThanOrEqual(2, $json['total']);
        $this->assertArrayHasKey('rows', $json);
        $this->assertIsArray($json['rows']);

        // Verify at least one seeded document present
        $names = array_column($json['rows'], 'nomProduit');
        $this->assertContains('Regress One', $names);
        $this->assertContains('Regress Two', $names);
    }

    public function testJsonDataCreateUpdatesAndReturnsRow(): void
    {
        // Simulate create action
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'action' => 'create',
            'table' => 'produit',
            'nomProduit' => 'Created via Test',
            'prixProduit' => 7.77,
            'actif' => 1,
        ];

        ob_start();
        try {
            require __DIR__ . '/../../services/json_data.php';
        } finally {
            $output = (string) ob_get_clean();
        }

        $json = json_decode($output, true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('row', $json);
        $this->assertIsArray($json['row']);
        $this->assertSame('Created via Test', $json['row']['nomProduit']);
    }
}
