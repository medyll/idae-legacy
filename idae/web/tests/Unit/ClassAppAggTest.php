<?php
declare(strict_types=1);

/**
 * ClassAppAggTest — Unit tests for ClassAppAgg aggregation methods
 *
 * @package Idae\Tests\Unit
 * Date: 2026-03-27
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

require_once __DIR__ . '/../../appclasses/appcommon/ClassAppAgg.php';

class ClassAppAggTest extends TestCase
{
    private static \MongoDB\Database $sitebaseApp;
    private static \MongoDB\Database $sitebasePref;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$sitebaseApp  = self::$mongoClient->selectDatabase('sitebase_app');
        self::$sitebasePref = self::$mongoClient->selectDatabase('sitebase_pref');

        // Seed appscheme
        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebaseApp->selectCollection('appscheme')->insertOne([
            'idappscheme' => 1,
            'codeAppscheme' => 'produit',
            'nomAppscheme' => 'Produit',
            'codeAppscheme_base' => 'sitebase_pref',
            'hasTypeScheme' => 0,
            'actif' => 1,
        ]);

        // Clean produit collection
        self::$sitebasePref->selectCollection('produit')->drop();
    }

    public static function tearDownAfterClass(): void
    {
        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebasePref->selectCollection('produit')->drop();
        parent::tearDownAfterClass();
    }

    private function makeAgg(): \AppCommon\ClassAppAgg
    {
        global $PERSIST_CON;
        $PERSIST_CON = null;
        return new \AppCommon\ClassAppAgg('produit');
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Seed test data
        self::$sitebasePref->selectCollection('produit')->deleteMany([]);
        self::$sitebasePref->selectCollection('produit')->insertMany([
            ['idproduit' => 1, 'nomProduit' => 'Widget A', 'categorieProduit' => 'Electronics', 'prixProduit' => 100.00, 'actif' => 1],
            ['idproduit' => 2, 'nomProduit' => 'Widget B', 'categorieProduit' => 'Electronics', 'prixProduit' => 200.00, 'actif' => 1],
            ['idproduit' => 3, 'nomProduit' => 'Gadget C', 'categorieProduit' => 'Accessories', 'prixProduit' => 50.00, 'actif' => 1],
            ['idproduit' => 4, 'nomProduit' => 'Gadget D', 'categorieProduit' => 'Accessories', 'prixProduit' => 75.00, 'actif' => 0],
            ['idproduit' => 5, 'nomProduit' => 'Tool E', 'categorieProduit' => 'Tools', 'prixProduit' => 150.00, 'actif' => 1],
        ]);
    }

    // =========================================================================
    // COUNT BY TESTS
    // =========================================================================

    /**
     * Test countBy() groups and counts correctly.
     */
    public function testCountByReturnsGroupedCounts(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->countBy('categorieProduit');

        $this->assertIsArray($result);
        $this->assertCount(3, $result); // Electronics, Accessories, Tools

        // Find Electronics count
        $electronics = array_filter($result, fn($r) => $r['categorieProduit'] === 'Electronics');
        $this->assertCount(1, $electronics);
        $electronics = array_values($electronics)[0];
        $this->assertEquals(2, $electronics['count']);
    }

    /**
     * Test countBy() with filter.
     */
    public function testCountByWithFilter(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->countBy('categorieProduit', ['actif' => 1]);

        $this->assertIsArray($result);

        // Only active products: Electronics=2, Accessories=1, Tools=1
        $electronics = array_filter($result, fn($r) => $r['categorieProduit'] === 'Electronics');
        $electronics = array_values($electronics)[0];
        $this->assertEquals(2, $electronics['count']);

        $accessories = array_filter($result, fn($r) => $r['categorieProduit'] === 'Accessories');
        $accessories = array_values($accessories)[0];
        $this->assertEquals(1, $accessories['count']);
    }

    // =========================================================================
    // SUM BY TESTS
    // =========================================================================

    /**
     * Test sumBy() calculates correct totals.
     */
    public function testSumByReturnsGroupedSums(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->sumBy('prixProduit', 'categorieProduit');

        $this->assertIsArray($result);

        // Electronics: 100 + 200 = 300
        $electronics = array_filter($result, fn($r) => $r['categorieProduit'] === 'Electronics');
        $electronics = array_values($electronics)[0];
        $this->assertEquals(300.0, $electronics['total']);

        // Accessories: 50 + 75 = 125
        $accessories = array_filter($result, fn($r) => $r['categorieProduit'] === 'Accessories');
        $accessories = array_values($accessories)[0];
        $this->assertEquals(125.0, $accessories['total']);
    }

    // =========================================================================
    // DISTINCT VALUES TESTS
    // =========================================================================

    /**
     * Test distinctValues() returns unique values.
     */
    public function testDistinctValuesReturnsUniqueValues(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->distinctValues('categorieProduit');

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertContains('Electronics', $result);
        $this->assertContains('Accessories', $result);
        $this->assertContains('Tools', $result);
    }

    /**
     * Test distinctValues() with filter.
     */
    public function testDistinctValuesWithFilter(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->distinctValues('categorieProduit', ['actif' => 1]);

        $this->assertIsArray($result);
        // Only active: Electronics, Accessories, Tools (all 3 have active products)
        $this->assertCount(3, $result);
    }

    // =========================================================================
    // STATS FOR FIELD TESTS
    // =========================================================================

    /**
     * Test statsForField() calculates correct statistics.
     */
    public function testStatsForFieldReturnsStatistics(): void
    {
        $agg = $this->makeAgg();
        $stats = $agg->statsForField('prixProduit');

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('min', $stats);
        $this->assertArrayHasKey('max', $stats);
        $this->assertArrayHasKey('avg', $stats);
        $this->assertArrayHasKey('sum', $stats);
        $this->assertArrayHasKey('count', $stats);

        // Min: 50, Max: 200, Avg: 115, Sum: 575, Count: 5
        $this->assertEquals(50.0, $stats['min']);
        $this->assertEquals(200.0, $stats['max']);
        $this->assertEquals(115.0, $stats['avg']);
        $this->assertEquals(575.0, $stats['sum']);
        $this->assertEquals(5, $stats['count']);
    }

    /**
     * Test statsForField() with filter.
     */
    public function testStatsForFieldWithFilter(): void
    {
        $agg = $this->makeAgg();
        $stats = $agg->statsForField('prixProduit', ['actif' => 1]);

        // Active products: 100, 200, 50, 150 (4 items, excluding inactive 75)
        $this->assertEquals(4, $stats['count']);
        $this->assertEquals(50.0, $stats['min']);
        $this->assertEquals(200.0, $stats['max']);
        $this->assertEquals(125.0, $stats['avg']);
        $this->assertEquals(500.0, $stats['sum']);
    }

    // =========================================================================
    // SEARCH TESTS
    // =========================================================================

    /**
     * Test search() finds matching documents.
     */
    public function testSearchReturnsMatchingResults(): void
    {
        $agg = $this->makeAgg();
        $results = iterator_to_array($agg->search('Widget', ['nomProduit']));

        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        $names = array_column($results, 'nomProduit');
        $this->assertContains('Widget A', $names);
        $this->assertContains('Widget B', $names);
    }

    /**
     * Test search() is case-insensitive.
     */
    public function testSearchIsCaseInsensitive(): void
    {
        $agg = $this->makeAgg();
        $results = iterator_to_array($agg->search('widget', ['nomProduit']));

        $this->assertCount(2, $results);
    }

    /**
     * Test search() with multiple fields.
     */
    public function testSearchWithMultipleFields(): void
    {
        $agg = $this->makeAgg();
        $results = iterator_to_array($agg->search('Gadget', ['nomProduit', 'categorieProduit']));

        $this->assertCount(2, $results);
        $names = array_column($results, 'nomProduit');
        $this->assertContains('Gadget C', $names);
        $this->assertContains('Gadget D', $names);
    }

    /**
     * Test search() respects limit.
     */
    public function testSearchRespectsLimit(): void
    {
        $agg = $this->makeAgg();
        $results = iterator_to_array($agg->search('Widget', ['nomProduit'], [], 1));

        $this->assertCount(1, $results);
    }

    // =========================================================================
    // TOP N BY GROUP TESTS
    // =========================================================================

    /**
     * Test topNByGroup() returns top items per group.
     */
    public function testTopNByGroupReturnsTopItems(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->topNByGroup('categorieProduit', 'prixProduit', 2);

        $this->assertIsArray($result);

        // Should have 3 groups
        $this->assertCount(3, $result);

        // Find Electronics group
        $electronics = array_filter($result, fn($r) => $r['categorieProduit'] === 'Electronics');
        $electronics = array_values($electronics)[0];
        $this->assertArrayHasKey('topItems', $electronics);
        $this->assertCount(2, $electronics['topItems']); // Top 2

        // Top item should be Widget B (200.00)
        $this->assertEquals(200.00, $electronics['topItems'][0]['prixProduit']);
    }

    /**
     * Test topNByGroup() with limit 1.
     */
    public function testTopNByGroupWithLimitOne(): void
    {
        $agg = $this->makeAgg();
        $result = $agg->topNByGroup('categorieProduit', 'prixProduit', 1);

        $this->assertIsArray($result);

        // Each group should have exactly 1 item
        foreach ($result as $group) {
            $this->assertCount(1, $group['topItems']);
        }
    }

    // =========================================================================
    // AGGREGATE (RAW PIPELINE) TESTS
    // =========================================================================

    /**
     * Test aggregate() executes raw pipeline.
     */
    public function testAggregateExecutesPipeline(): void
    {
        $agg = $this->makeAgg();
        $pipeline = [
            ['$match' => ['actif' => 1]],
            ['$group' => ['_id' => '$categorieProduit', 'count' => ['$sum' => 1]]],
        ];

        $results = iterator_to_array($agg->aggregate($pipeline));

        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));

        // Each result should have _id and count
        foreach ($results as $result) {
            $this->assertArrayHasKey('_id', $result);
            $this->assertArrayHasKey('count', $result);
        }
    }
}
