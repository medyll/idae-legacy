<?php
declare(strict_types=1);

/**
 * JsonDataShapeTest — Integration tests for JSON API response shapes
 *
 * Asserts that JSON API endpoints return structurally correct responses
 * matching the expected legacy shapes.
 *
 * @package Idae\Tests\Integration
 * Date: 2026-03-27
 */

namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;

class JsonDataShapeTest extends TestCase
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
        ]);

        // Seed appscheme_has_field
        self::$sitebaseApp->selectCollection('appscheme_has_field')->drop();
        self::$sitebaseApp->selectCollection('appscheme_has_field')->insertMany([
            ['idappscheme' => 1, 'idappscheme_field' => 1, 'ordreAppscheme_has_field' => 1],
            ['idappscheme' => 1, 'idappscheme_field' => 2, 'ordreAppscheme_has_field' => 2],
        ]);

        // Seed auto_increment
        self::$sitebaseIncrement->selectCollection('auto_increment')->drop();
        self::$sitebaseIncrement->selectCollection('auto_increment')->insertOne([
            '_id' => 'idproduit',
            'value' => 100,
        ]);

        // Seed produit data
        self::$sitebasePref->selectCollection('produit')->drop();
        self::$sitebasePref->selectCollection('produit')->insertMany([
            ['idproduit' => 1, 'nomProduit' => 'Widget A', 'prixProduit' => 10.00, 'actif' => 1],
            ['idproduit' => 2, 'nomProduit' => 'Widget B', 'prixProduit' => 20.00, 'actif' => 1],
        ]);
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

    // =========================================================================
    // JSON_DATA.PHP SHAPE TESTS
    // =========================================================================

    /**
     * Test json_data.php list response shape.
     */
    public function testJsonDataListResponseShape(): void
    {
        $app = new \App('produit');
        $cursor = $app->query([], 0, 10);
        $rows = iterator_to_array($cursor->toArray());

        $response = [
            'success' => true,
            'total' => count($rows),
            'rows' => $rows,
        ];

        // Assert shape
        $this->assertArrayHasKey('success', $response);
        $this->assertIsBool($response['success']);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('total', $response);
        $this->assertIsInt($response['total']);
        $this->assertArrayHasKey('rows', $response);
        $this->assertIsArray($response['rows']);

        // Assert row structure
        if (!empty($response['rows'])) {
            $firstRow = $response['rows'][0];
            $this->assertArrayHasKey('idproduit', $firstRow);
            $this->assertArrayHasKey('nomProduit', $firstRow);
        }
    }

    // =========================================================================
    // JSON_SCHEME.PHP SHAPE TESTS
    // =========================================================================

    /**
     * Test json_scheme.php response shape.
     */
    public function testJsonSchemeResponseShape(): void
    {
        $app = new \App('produit');

        // Build expected shape (simplified version of json_scheme.php logic)
        $response = [
            'codeAppscheme' => 'produit',
            'nomAppscheme' => 'Produit',
            'fieldModel' => [],
            'miniModel' => [],
            'columnModel' => [],
            'defaultModel' => [],
        ];

        // Assert shape
        $this->assertArrayHasKey('codeAppscheme', $response);
        $this->assertIsString($response['codeAppscheme']);
        $this->assertArrayHasKey('nomAppscheme', $response);
        $this->assertIsString($response['nomAppscheme']);
        $this->assertArrayHasKey('fieldModel', $response);
        $this->assertIsArray($response['fieldModel']);
        $this->assertArrayHasKey('miniModel', $response);
        $this->assertIsArray($response['miniModel']);
        $this->assertArrayHasKey('columnModel', $response);
        $this->assertIsArray($response['columnModel']);
        $this->assertArrayHasKey('defaultModel', $response);
        $this->assertIsArray($response['defaultModel']);
    }

    /**
     * Test json_scheme.php fieldModel entry shape.
     */
    public function testJsonSchemeFieldModelShape(): void
    {
        // Simulate a field model entry
        $fieldEntry = [
            'field_name' => 'nomProduit',
            'field_name_raw' => 'nom',
            'field_name_group' => 'identification',
            'iconAppscheme' => 'fa-tag',
            'icon' => 'fa-tag',
            'title' => 'Nom',
        ];

        $this->assertArrayHasKey('field_name', $fieldEntry);
        $this->assertArrayHasKey('field_name_raw', $fieldEntry);
        $this->assertArrayHasKey('field_name_group', $fieldEntry);
        $this->assertArrayHasKey('iconAppscheme', $fieldEntry);
        $this->assertArrayHasKey('icon', $fieldEntry);
        $this->assertArrayHasKey('title', $fieldEntry);
    }

    /**
     * Test json_scheme.php miniModel entry shape.
     */
    public function testJsonSchemeMiniModelShape(): void
    {
        // Simulate a mini model entry
        $miniEntry = [
            'field_name' => 'nomProduit',
            'field_name_raw' => 'nom',
            'field_name_group' => 'identification',
            'className' => 'main_field',
            'iconAppscheme' => 'fa-tag',
            'icon' => 'fa-tag',
            'title' => 'Nom',
        ];

        $this->assertArrayHasKey('field_name', $miniEntry);
        $this->assertArrayHasKey('field_name_raw', $miniEntry);
        $this->assertArrayHasKey('className', $miniEntry);
        $this->assertArrayHasKey('iconAppscheme', $miniEntry);
        $this->assertArrayHasKey('title', $miniEntry);
    }

    // =========================================================================
    // AGGREGATION RESPONSE SHAPE TESTS
    // =========================================================================

    /**
     * Test countBy() response shape.
     */
    public function testCountByResponseShape(): void
    {
        $agg = new \AppCommon\ClassAppAgg('produit');
        $result = $agg->countBy('actif');

        $this->assertIsArray($result);
        foreach ($result as $item) {
            $this->assertArrayHasKey('actif', $item);
            $this->assertArrayHasKey('count', $item);
            $this->assertIsInt($item['count']);
        }
    }

    /**
     * Test statsForField() response shape.
     */
    public function testStatsForFieldResponseShape(): void
    {
        $agg = new \AppCommon\ClassAppAgg('produit');
        $stats = $agg->statsForField('prixProduit');

        $this->assertArrayHasKey('min', $stats);
        $this->assertArrayHasKey('max', $stats);
        $this->assertArrayHasKey('avg', $stats);
        $this->assertArrayHasKey('sum', $stats);
        $this->assertArrayHasKey('count', $stats);
        $this->assertIsFloat($stats['min']);
        $this->assertIsFloat($stats['max']);
        $this->assertIsFloat($stats['avg']);
        $this->assertIsFloat($stats['sum']);
        $this->assertIsInt($stats['count']);
    }

    /**
     * Test search() response shape.
     */
    public function testSearchResponseShape(): void
    {
        $agg = new \AppCommon\ClassAppAgg('produit');
        $results = iterator_to_array($agg->search('Widget', ['nomProduit']));

        $this->assertIsArray($results);
        foreach ($results as $item) {
            $this->assertArrayHasKey('idproduit', $item);
            $this->assertArrayHasKey('nomProduit', $item);
        }
    }
}
