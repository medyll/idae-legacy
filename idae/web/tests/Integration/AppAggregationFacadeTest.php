<?php
declare(strict_types=1);

/**
 * Characterize aggregation contracts used by the legacy App facade.
 * @package Idae\Tests\Integration
 * @date 2026-11-08
 */
namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;
use Idae\Tests\Unit\ClassAppAggTest;

class AppAggregationFacadeTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ClassAppAggTest::setUpBeforeClass();
        $meta = self::$mongoClient->selectDatabase('sitebase_app');
        $meta->selectCollection('appscheme')->insertOne([
            'codeAppscheme' => 'categorie', 'codeAppscheme_base' => 'sitebase_pref',
            'nomAppscheme' => 'Categorie', 'benchmarkAppscheme' => 'aggregation-facade',
        ]);
        foreach (['nomProduit', 'scoreProduit'] as $field) {
            $meta->selectCollection('appscheme_has_field')->insertOne([
                'idappscheme' => 1, 'codeAppscheme_field' => $field,
                'benchmarkAppscheme_has_field' => 'aggregation-facade',
            ]);
        }
    }

    public static function tearDownAfterClass(): void
    {
        $meta = self::$mongoClient->selectDatabase('sitebase_app');
        $meta->selectCollection('appscheme')->deleteMany(['benchmarkAppscheme' => 'aggregation-facade']);
        $meta->selectCollection('appscheme_has_field')->deleteMany([
            'benchmarkAppscheme_has_field' => 'aggregation-facade',
        ]);
        ClassAppAggTest::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $data = self::$mongoClient->selectDatabase('sitebase_pref');
        $data->selectCollection('categorie')->updateMany([], ['$unset' => ['count' => '']]);
        foreach ([1 => 30, 2 => 20, 3 => 10] as $id => $score) {
            $data->selectCollection('produit')->updateOne(['idproduit' => $id], [
                '$set' => ['countProduit' => 0, 'scoreProduit' => $score], '$unset' => ['count' => ''],
            ]);
        }
    }

    public function testRawDistinctAppliesFiltersAndDistinctAllKeepsItsUnusedArguments(): void
    {
        $app = new \App('produit');
        $this->assertSame([20], $app->distinct('categorie', ['idproduit' => 3], '200', 'raw'));
        $this->assertEqualsCanonicalizing([10, 20], $app->distinct_all('idcategorie', [], null, null));
    }

    public function testFullDistinctResolvesSortedRecordsWithNumericStringLimit(): void
    {
        $cursor = (new \App('produit'))->distinct('categorie', [], '1', 'full', '', ['nom', -1]);
        $rows = iterator_to_array($cursor);
        $this->assertCount(1, $rows);
        $this->assertSame(20, reset($rows)['idcategorie']);
    }

    public function testGroupingSortsByCountAndPreservesLegacyLimit(): void
    {
        $rows = (new \App('produit'))->distinct_rs([
            'groupBy_table' => 'categorie', 'limit' => 2, 'sort_field' => ['count', -1],
        ]);
        // Legacy callers receive limit - 1 rows. Changing pagination is outside this extraction.
        $this->assertSame([10 => 2], array_column($rows, 'count', 'idcategorie'));
    }

    public function testGroupingRestoresLegacyDefaultsForEmptyOptions(): void
    {
        $rows = (new \App('produit'))->distinct_rs([
            'groupBy_table' => 'categorie', 'limit' => 0, 'sort_field' => [], 'field' => '', 'vars' => [],
        ]);
        $this->assertSame([10 => 2, 20 => 1], array_column($rows, 'count', 'idcategorie'));
    }

    public function testComputedCountOverridesAFieldNamedCountOnRelatedRecords(): void
    {
        self::$mongoClient->selectDatabase('sitebase_pref')->selectCollection('categorie')
            ->updateMany([], ['$set' => ['count' => 999]]);
        $rows = (new \App('produit'))->distinct_rs([
            'groupBy_table' => 'categorie', 'limit' => 50, 'sort_field' => ['count', -1],
        ]);
        $this->assertSame([10 => 2, 20 => 1], array_column($rows, 'count', 'idcategorie'));
    }

    public function testGroupingKeepsTheExistingNumericFieldOrdering(): void
    {
        $rows = (new \App('produit'))->distinct_rs([
            'groupBy_table' => 'categorie', 'limit' => '50', 'sort_field' => ['score', -1],
        ]);
        $this->assertSame([20, 10], array_column($rows, 'idcategorie'));
        $this->assertSame([10, 30], array_column($rows, 'scoreProduit'));
    }
}
