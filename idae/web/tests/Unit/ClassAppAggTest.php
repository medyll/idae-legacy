<?php
declare(strict_types=1);

/**
 * ClassAppAggTest.php — Unit tests for ClassAppAgg helpers
 * Date: 2026-03-11
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

require_once __DIR__ . '/../../appclasses/appcommon/ClassAppAgg.php';
require_once __DIR__ . '/../../appclasses/appcommon/ClassApp.php';

class ClassAppAggTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // The real App constructor reads appscheme from sitebase_app, so seed there
        $prefix = defined('MDB_PREFIX') ? MDB_PREFIX : '';
        $appDb = self::$mongoClient->selectDatabase($prefix . 'sitebase_app');

        // Appscheme entry for produit (needed by App constructor to resolve codeAppscheme_base)
        $existing = $appDb->selectCollection('appscheme')->findOne(['codeAppscheme' => 'produit']);
        if (empty($existing)) {
            $appDb->selectCollection('appscheme')->insertOne([
                'idappscheme'        => 1,
                'codeAppscheme'      => 'produit',
                'codeAppscheme_base' => $prefix . 'sitebase_pref',
                'nomAppscheme'       => 'Produit',
            ]);
        }

        // Seed produit + categorie into sitebase_pref (where plug() resolves to)
        $dataDb = self::$mongoClient->selectDatabase($prefix . 'sitebase_pref');

        $dataDb->selectCollection('produit')->insertMany([
            ['idproduit' => 1, 'nomProduit' => 'Prod A', 'idcategorie' => 10],
            ['idproduit' => 2, 'nomProduit' => 'Prod B', 'idcategorie' => 10],
            ['idproduit' => 3, 'nomProduit' => 'Prod C', 'idcategorie' => 20],
        ]);

        $dataDb->selectCollection('categorie')->insertMany([
            ['idcategorie' => 10, 'nomCategorie' => 'Cat Alpha'],
            ['idcategorie' => 20, 'nomCategorie' => 'Cat Beta'],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        $prefix = defined('MDB_PREFIX') ? MDB_PREFIX : '';

        $appDb = self::$mongoClient->selectDatabase($prefix . 'sitebase_app');
        $appDb->selectCollection('appscheme')->deleteOne(['codeAppscheme' => 'produit']);

        $dataDb = self::$mongoClient->selectDatabase($prefix . 'sitebase_pref');
        $dataDb->selectCollection('produit')->drop();
        $dataDb->selectCollection('categorie')->drop();

        parent::tearDownAfterClass();
    }

    // -------------------------------------------------------------------------
    // distinct_all
    // -------------------------------------------------------------------------

    public function testDistinctAllReturnsArray(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_all($app, 'idcategorie');
        $this->assertIsArray($result);
    }

    public function testDistinctAllReturnsUniqueValues(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_all($app, 'idcategorie');
        // Two distinct category IDs in fixture data (10 and 20)
        $this->assertCount(2, $result);
        $this->assertContains(10, $result);
        $this->assertContains(20, $result);
    }

    public function testDistinctAllWithFilterNarrowsResult(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_all($app, 'idcategorie', ['idcategorie' => 10]);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame(10, $result[0]);
    }

    public function testDistinctAllEmptyFilterReturnsAll(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_all($app, 'nomProduit', []);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(3, count($result));
    }

    // -------------------------------------------------------------------------
    // distinct (raw mode)
    // -------------------------------------------------------------------------

    public function testDistinctRawModeReturnsIdArray(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct(
            $app,
            'categorie',
            [],
            200,
            'raw',
            'idcategorie'
        );
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    // -------------------------------------------------------------------------
    // distinct_rs
    // -------------------------------------------------------------------------

    public function testDistinctRsReturnsArray(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_rs($app, [
            'groupBy_table' => 'categorie',
            'field'         => 'idcategorie',
            'limit'         => 50,
        ]);
        $this->assertIsArray($result);
    }

    public function testDistinctRsRowsHaveCountKey(): void
    {
        $app = new \App('produit');
        $result = \AppCommon\ClassAppAgg::distinct_rs($app, [
            'groupBy_table' => 'categorie',
            'field'         => 'idcategorie',
            'limit'         => 50,
        ]);
        foreach ($result as $row) {
            $this->assertArrayHasKey('count', $row, 'Each row must have a count key');
        }
    }
}
