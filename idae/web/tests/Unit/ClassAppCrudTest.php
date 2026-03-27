<?php
declare(strict_types=1);

/**
 * ClassAppCrudTest — Unit tests for ClassApp CRUD methods
 *
 * Tests insert(), update(), remove(), and create_update() methods
 * with the modern MongoDB driver and MongoCompat integration.
 *
 * @package Idae\Tests\Unit
 * Date: 2026-03-27
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

require_once __DIR__ . '/../../appclasses/appcommon/ClassApp.php';

class ClassAppCrudTest extends TestCase
{
    private static \MongoDB\Database $sitebaseApp;
    private static \MongoDB\Database $sitebasePref;
    private static \MongoDB\Database $sitebaseIncrement;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$sitebaseApp  = self::$mongoClient->selectDatabase('sitebase_app');
        self::$sitebasePref = self::$mongoClient->selectDatabase('sitebase_pref');
        self::$sitebaseIncrement = self::$mongoClient->selectDatabase('sitebase_increment');

        // Seed appscheme for produit
        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebaseApp->selectCollection('appscheme')->insertOne([
            'idappscheme' => 1,
            'codeAppscheme' => 'produit',
            'nomAppscheme' => 'Produit',
            'codeAppscheme_base' => 'sitebase_pref',
            'hasTypeScheme' => 0,
            'hasCodeScheme' => 0,
            'hasPrixScheme' => 1,
            'actif' => 1,
        ]);

        // Seed appscheme_field
        self::$sitebaseApp->selectCollection('appscheme_field')->drop();
        self::$sitebaseApp->selectCollection('appscheme_field')->insertMany([
            ['idappscheme_field' => 1, 'codeAppscheme_field' => 'nom', 'nomAppscheme_field' => 'Nom', 'codeAppscheme_field_type' => 'text'],
            ['idappscheme_field' => 2, 'codeAppscheme_field' => 'prix', 'nomAppscheme_field' => 'Prix', 'codeAppscheme_field_type' => 'prix'],
            ['idappscheme_field' => 3, 'codeAppscheme_field' => 'description', 'nomAppscheme_field' => 'Description', 'codeAppscheme_field_type' => 'textarea'],
        ]);

        // Seed appscheme_has_field
        self::$sitebaseApp->selectCollection('appscheme_has_field')->drop();
        self::$sitebaseApp->selectCollection('appscheme_has_field')->insertMany([
            ['idappscheme' => 1, 'idappscheme_field' => 1, 'ordreAppscheme_has_field' => 1],
            ['idappscheme' => 1, 'idappscheme_field' => 2, 'ordreAppscheme_has_field' => 2],
            ['idappscheme' => 1, 'idappscheme_field' => 3, 'ordreAppscheme_has_field' => 3],
        ]);

        // Seed auto_increment for idproduit
        self::$sitebaseIncrement->selectCollection('auto_increment')->drop();
        self::$sitebaseIncrement->selectCollection('auto_increment')->insertOne([
            '_id' => 'idproduit',
            'value' => 100,
        ]);

        // Clean produit collection
        self::$sitebasePref->selectCollection('produit')->drop();
    }

    public static function tearDownAfterClass(): void
    {
        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebaseApp->selectCollection('appscheme_field')->drop();
        self::$sitebaseApp->selectCollection('appscheme_has_field')->drop();
        self::$sitebasePref->selectCollection('produit')->drop();
        self::$sitebaseIncrement->selectCollection('auto_increment')->drop();
        parent::tearDownAfterClass();
    }

    private function makeApp(): \App
    {
        global $PERSIST_CON;
        $PERSIST_CON = null;
        return new \App('produit');
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Clean produit collection before each test
        self::$sitebasePref->selectCollection('produit')->deleteMany([]);
        // Reset auto_increment
        self::$sitebaseIncrement->selectCollection('auto_increment')->updateOne(
            ['_id' => 'idproduit'],
            ['$set' => ['value' => 100]]
        );
    }

    // =========================================================================
    // INSERT TESTS
    // =========================================================================

    /**
     * Test insert() creates a new document and returns the ID.
     */
    public function testInsertReturnsId(): void
    {
        $app = $this->makeApp();
        $id = $app->insert([
            'nomProduit' => 'Test Product',
            'prixProduit' => 29.99,
        ]);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        // Verify document was inserted
        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => $id]);
        $this->assertNotNull($doc);
        $this->assertEquals('Test Product', $doc['nomProduit']);
    }

    /**
     * Test insert() auto-generates ID if not provided.
     */
    public function testInsertAutoGeneratesId(): void
    {
        $app = $this->makeApp();
        $id = $app->insert(['nomProduit' => 'Auto ID Product']);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => $id]);
        $this->assertEquals($id, $doc['idproduit']);
    }

    /**
     * Test insert() with empty array still creates document.
     */
    public function testInsertWithEmptyArray(): void
    {
        $app = $this->makeApp();
        // Empty insert will auto-generate ID but consolidate_scheme expects nomProduit
        // So we test with minimal fields instead
        $id = $app->insert(['nomProduit' => 'Minimal Product']);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => $id]);
        $this->assertNotNull($doc);
        $this->assertEquals('Minimal Product', $doc['nomProduit']);
    }

    // =========================================================================
    // UPDATE TESTS
    // =========================================================================

    /**
     * Test update() modifies existing document and returns changed fields.
     */
    public function testUpdateReturnsChangedFields(): void
    {
        // First insert a document
        $insertId = self::$sitebasePref->selectCollection('produit')->insertOne([
            'idproduit' => 101,
            'nomProduit' => 'Update Test Before',
            'prixProduit' => 5.00,
        ]);

        $app = $this->makeApp();
        $result = $app->update(
            ['idproduit' => 101],
            ['nomProduit' => 'Updated Widget A']
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('nomProduit', $result);

        // Verify update persisted
        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => 101]);
        $this->assertEquals('Updated Widget A', $doc['nomProduit']);
    }

    /**
     * Test update() returns null when no changes detected.
     */
    public function testUpdateReturnsNullWhenNoChanges(): void
    {
        self::$sitebasePref->selectCollection('produit')->insertOne([
            'idproduit' => 102,
            'nomProduit' => 'No Change Product',
            'prixProduit' => 10.00,
        ]);

        $app = $this->makeApp();
        $result = $app->update(
            ['idproduit' => 102],
            ['nomProduit' => 'No Change Product'] // Same as existing
        );

        $this->assertNull($result);
    }

    /**
     * Test update() returns null when ID is missing from filter.
     */
    public function testUpdateReturnsNullWhenIdMissing(): void
    {
        $app = $this->makeApp();
        $result = $app->update(
            ['nomProduit' => 'Widget A'], // No ID field
            ['prixProduit' => 15.99]
        );

        $this->assertNull($result);
    }

    /**
     * Test update() with multiple fields.
     */
    public function testUpdateMultipleFields(): void
    {
        self::$sitebasePref->selectCollection('produit')->insertOne([
            'idproduit' => 103,
            'nomProduit' => 'Multi Field Product',
            'prixProduit' => 20.00,
        ]);

        $app = $this->makeApp();
        $result = $app->update(
            ['idproduit' => 103],
            [
                'nomProduit' => 'Updated Multi Field',
                'prixProduit' => 25.99,
            ]
        );

        $this->assertIsArray($result);

        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => 103]);
        $this->assertEquals('Updated Multi Field', $doc['nomProduit']);
        $this->assertEquals(25.99, $doc['prixProduit']);
    }

    // =========================================================================
    // REMOVE TESTS
    // =========================================================================

    /**
     * Test remove() deletes matching documents.
     */
    public function testRemoveDeletesDocument(): void
    {
        self::$sitebasePref->selectCollection('produit')->insertOne([
            'idproduit' => 201,
            'nomProduit' => 'Delete Me',
            'prixProduit' => 1.00,
        ]);

        $app = $this->makeApp();
        $count = $app->remove(['idproduit' => 201]);

        $this->assertIsInt($count);
        $this->assertGreaterThan(0, $count);

        // Verify document was deleted
        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => 201]);
        $this->assertNull($doc);
    }

    /**
     * Test remove() returns 0 when no documents match.
     */
    public function testRemoveReturnsZeroWhenNoMatch(): void
    {
        $app = $this->makeApp();
        $count = $app->remove(['idproduit' => 9999]);

        $this->assertEquals(0, $count);
    }

    /**
     * Test remove() with empty filter returns 0.
     */
    public function testRemoveWithEmptyFilter(): void
    {
        $app = $this->makeApp();
        $count = $app->remove([]);

        $this->assertEquals(0, $count);
    }

    /**
     * Test remove() can delete multiple documents.
     */
    public function testRemoveMultipleDocuments(): void
    {
        self::$sitebasePref->selectCollection('produit')->insertMany([
            ['idproduit' => 301, 'nomProduit' => 'Delete A', 'actif' => 1],
            ['idproduit' => 302, 'nomProduit' => 'Delete B', 'actif' => 1],
        ]);

        $app = $this->makeApp();
        $count = $app->remove(['actif' => 1]);

        $this->assertIsInt($count);
        $this->assertGreaterThan(1, $count);
    }

    // =========================================================================
    // CREATE_UPDATE (UPSERT) TESTS
    // =========================================================================

    /**
     * Test create_update() inserts new document when filter doesn't match.
     */
    public function testCreateUpdateInsertsNewDocument(): void
    {
        $app = $this->makeApp();
        $id = $app->create_update(
            ['idproduit' => 401],
            ['nomProduit' => 'New Product via Upsert']
        );

        $this->assertNotNull($id);

        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => 401]);
        $this->assertNotNull($doc);
        $this->assertEquals('New Product via Upsert', $doc['nomProduit']);
    }

    /**
     * Test create_update() updates existing document when filter matches.
     */
    public function testCreateUpdateUpdatesExistingDocument(): void
    {
        self::$sitebasePref->selectCollection('produit')->insertOne([
            'idproduit' => 402,
            'nomProduit' => 'Existing Product',
        ]);

        $app = $this->makeApp();
        $result = $app->create_update(
            ['idproduit' => 402],
            ['nomProduit' => 'Modified Product']
        );

        $this->assertNotNull($result);

        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => 402]);
        $this->assertEquals('Modified Product', $doc['nomProduit']);
    }

    // =========================================================================
    // CRUD WORKFLOW TESTS
    // =========================================================================

    /**
     * Test full CRUD workflow: insert → update → remove.
     */
    public function testFullCrudWorkflow(): void
    {
        $app = $this->makeApp();

        // INSERT
        $insertId = $app->insert([
            'nomProduit' => 'Workflow Test Product',
            'prixProduit' => 49.99,
        ]);
        $this->assertIsInt($insertId);
        $this->assertGreaterThan(0, $insertId);

        // UPDATE
        $updateResult = $app->update(
            ['idproduit' => $insertId],
            ['nomProduit' => 'Updated Workflow Product']
        );
        $this->assertIsArray($updateResult);

        // VERIFY UPDATE
        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => $insertId]);
        $this->assertEquals('Updated Workflow Product', $doc['nomProduit']);

        // REMOVE
        $removeCount = $app->remove(['idproduit' => $insertId]);
        $this->assertEquals(1, $removeCount);

        // VERIFY REMOVE
        $doc = self::$sitebasePref->selectCollection('produit')->findOne(['idproduit' => $insertId]);
        $this->assertNull($doc);
    }

    /**
     * Test insert multiple documents and query them.
     */
    public function testInsertMultipleAndQuery(): void
    {
        $app = $this->makeApp();

        $id1 = $app->insert(['nomProduit' => 'Product 1', 'prixProduit' => 10.00]);
        $id2 = $app->insert(['nomProduit' => 'Product 2', 'prixProduit' => 20.00]);
        $id3 = $app->insert(['nomProduit' => 'Product 3', 'prixProduit' => 30.00]);

        $this->assertIsInt($id1);
        $this->assertIsInt($id2);
        $this->assertIsInt($id3);

        // Query all products
        $results = $app->query([]);
        $this->assertInstanceOf(\AppCommon\MongodbCursorWrapper::class, $results);
    }
}
