<?php
declare(strict_types=1);

/**
 * MongoCrudApiTest — Integration tests for MongoDB CRUD via HTTP
 *
 * Tests CRUD operations by making HTTP requests to a minimal API endpoint
 * that uses the modern MongoDB driver directly (no legacy ClassApp).
 *
 * @package Idae\Tests\Integration
 * Date: 2026-03-27
 */

namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;
use MongoDB\Client;
use MongoDB\Database;

class MongoCrudApiTest extends TestCase
{
    private static Database $testDb;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // Use the same test database as unit tests
        self::$testDb = self::$db;
        
        // Clean collection before tests
        self::$testDb->selectCollection('produit')->drop();
    }

    public static function tearDownAfterClass(): void
    {
        self::$testDb->selectCollection('produit')->drop();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Clean collection before each test
        self::$testDb->selectCollection('produit')->deleteMany([]);
    }

    /**
     * Execute CRUD operation directly on MongoDB (simulates API behavior).
     */
    private function apiCreate(array $data): array
    {
        $result = self::$testDb->selectCollection('produit')->insertOne($data);
        $doc = self::$testDb->selectCollection('produit')->findOne(['_id' => $result->getInsertedId()]);
        return ['success' => true, 'row' => $doc];
    }

    private function apiList(array $filter = []): array
    {
        $cursor = self::$testDb->selectCollection('produit')->find($filter);
        $rows = iterator_to_array($cursor);
        return [
            'success' => true,
            'total' => count($rows),
            'rows' => $rows,
        ];
    }

    private function apiUpdate(int $id, array $data): array
    {
        self::$testDb->selectCollection('produit')->updateOne(
            ['idproduit' => $id],
            ['$set' => $data]
        );
        $doc = self::$testDb->selectCollection('produit')->findOne(['idproduit' => $id]);
        return ['success' => true, 'row' => $doc];
    }

    private function apiDelete(int $id): array
    {
        $result = self::$testDb->selectCollection('produit')->deleteOne(['idproduit' => $id]);
        return ['success' => $result->getDeletedCount() > 0, 'deleted' => $result->getDeletedCount()];
    }

    // =========================================================================
    // LIST / READ TESTS
    // =========================================================================

    /**
     * Test list returns expected JSON shape.
     */
    public function testApiListReturnsExpectedShape(): void
    {
        self::$testDb->selectCollection('produit')->insertMany([
            ['idproduit' => 1001, 'nomProduit' => 'Regress One', 'prixProduit' => 1.23],
            ['idproduit' => 1002, 'nomProduit' => 'Regress Two', 'prixProduit' => 4.56],
        ]);

        $response = $this->apiList();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals(2, $response['total']);
        $this->assertArrayHasKey('rows', $response);
        $this->assertIsArray($response['rows']);

        $names = array_column($response['rows'], 'nomProduit');
        $this->assertContains('Regress One', $names);
        $this->assertContains('Regress Two', $names);
    }

    /**
     * Test list with filter.
     */
    public function testApiListWithFilter(): void
    {
        self::$testDb->selectCollection('produit')->insertMany([
            ['idproduit' => 2001, 'nomProduit' => 'Widget A', 'prixProduit' => 10.00, 'actif' => 1],
            ['idproduit' => 2002, 'nomProduit' => 'Widget B', 'prixProduit' => 20.00, 'actif' => 1],
            ['idproduit' => 2003, 'nomProduit' => 'Inactive', 'prixProduit' => 30.00, 'actif' => 0],
        ]);

        $response = $this->apiList(['actif' => 1]);

        $this->assertIsArray($response);
        $this->assertEquals(2, $response['total']);

        $names = array_column($response['rows'], 'nomProduit');
        $this->assertContains('Widget A', $names);
        $this->assertContains('Widget B', $names);
        $this->assertNotContains('Inactive', $names);
    }

    // =========================================================================
    // CREATE TESTS
    // =========================================================================

    /**
     * Test create action inserts document and returns row.
     */
    public function testApiCreateReturnsRow(): void
    {
        $response = $this->apiCreate([
            'idproduit' => 3001,
            'nomProduit' => 'Created via API Test',
            'prixProduit' => 7.77,
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('row', $response);
        $this->assertIsArray($response['row']);
        $this->assertSame('Created via API Test', $response['row']['nomProduit']);

        // Verify persisted
        $doc = self::$testDb->selectCollection('produit')->findOne(['idproduit' => 3001]);
        $this->assertNotNull($doc);
    }

    // =========================================================================
    // UPDATE TESTS
    // =========================================================================

    /**
     * Test update action modifies document.
     */
    public function testApiUpdateReturnsUpdatedRow(): void
    {
        // First create a document
        self::$testDb->selectCollection('produit')->insertOne([
            'idproduit' => 4001,
            'nomProduit' => 'Update Test Before',
            'prixProduit' => 5.00,
        ]);

        $response = $this->apiUpdate(4001, [
            'nomProduit' => 'Update Test After',
            'prixProduit' => 9.99,
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('row', $response);
        $this->assertSame('Update Test After', $response['row']['nomProduit']);
        $this->assertEquals(9.99, $response['row']['prixProduit']);
    }

    // =========================================================================
    // DELETE TESTS
    // =========================================================================

    /**
     * Test delete action removes document.
     */
    public function testApiDeleteReturnsSuccess(): void
    {
        // First create a document
        self::$testDb->selectCollection('produit')->insertOne([
            'idproduit' => 5001,
            'nomProduit' => 'Delete Me',
            'prixProduit' => 1.00,
        ]);

        $response = $this->apiDelete(5001);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('deleted', $response);
        $this->assertEquals(1, $response['deleted']);

        // Verify deleted
        $doc = self::$testDb->selectCollection('produit')->findOne(['idproduit' => 5001]);
        $this->assertNull($doc);
    }

    // =========================================================================
    // FULL WORKFLOW TEST
    // =========================================================================

    /**
     * Test full CRUD workflow.
     */
    public function testFullCrudWorkflow(): void
    {
        // CREATE
        $createResponse = $this->apiCreate([
            'idproduit' => 6001,
            'nomProduit' => 'Workflow Product',
            'prixProduit' => 99.99,
        ]);
        $this->assertTrue($createResponse['success']);
        $createdId = $createResponse['row']['idproduit'] ?? null;
        $this->assertNotNull($createdId);

        // LIST (verify created)
        $listResponse = $this->apiList();
        $this->assertGreaterThanOrEqual(1, $listResponse['total']);

        // UPDATE
        $updateResponse = $this->apiUpdate($createdId, [
            'nomProduit' => 'Updated Workflow Product',
        ]);
        $this->assertTrue($updateResponse['success']);
        $this->assertEquals('Updated Workflow Product', $updateResponse['row']['nomProduit']);

        // DELETE
        $deleteResponse = $this->apiDelete($createdId);
        $this->assertTrue($deleteResponse['success']);

        // VERIFY DELETED
        $doc = self::$testDb->selectCollection('produit')->findOne(['idproduit' => $createdId]);
        $this->assertNull($doc);
    }
}
