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

        $db = self::$mongoClient->selectDatabase('idae_test');

        // Seed produit collection for distinct tests
        $db->selectCollection('produit')->insertMany([
            ['idproduit' => 1, 'nomProduit' => 'Prod A', 'idcategorie' => 10],
            ['idproduit' => 2, 'nomProduit' => 'Prod B', 'idcategorie' => 10],
            ['idproduit' => 3, 'nomProduit' => 'Prod C', 'idcategorie' => 20],
        ]);

        // Seed categorie collection so distinct() full-mode can resolve records
        $db->selectCollection('categorie')->insertMany([
            ['idcategorie' => 10, 'nomCategorie' => 'Cat Alpha'],
            ['idcategorie' => 20, 'nomCategorie' => 'Cat Beta'],
        ]);

        // Appscheme entry for produit (needed by App constructor)
        $existing = $db->selectCollection('appscheme')->findOne(['codeAppscheme' => 'produit']);
        if (empty($existing)) {
            $db->selectCollection('appscheme')->insertOne([
                'idappscheme'        => 1,
                'codeAppscheme'      => 'produit',
                'codeAppscheme_base' => 'idae_test',
                'nomAppscheme'       => 'Produit',
            ]);
        }
    }

    public static function tearDownAfterClass(): void
    {
        $db = self::$mongoClient->selectDatabase('idae_test');
        $db->selectCollection('produit')->drop();
        $db->selectCollection('categorie')->drop();
        $db->selectCollection('appscheme')->deleteOne(['codeAppscheme' => 'produit']);
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
