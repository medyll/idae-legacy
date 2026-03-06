<?php
declare(strict_types=1);

/**
 * ClassAppTest.php -- S3-01: unit tests for ClassApp::query() / findOne()
 * Date: 2026-03-03
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;
use AppCommon\MongodbCursorWrapper;

require_once __DIR__ . '/../../appclasses/appcommon/ClassApp.php';

class ClassAppTest extends TestCase
{
    private static \MongoDB\Database $sitebaseApp;
    private static \MongoDB\Database $sitebasePref;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$sitebaseApp  = self::$mongoClient->selectDatabase('sitebase_app');
        self::$sitebasePref = self::$mongoClient->selectDatabase('sitebase_pref');

        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebaseApp->selectCollection('appscheme')->insertMany([
            [
                'idappscheme'        => 1,
                'codeAppscheme'      => 'produit',
                'codeAppscheme_base' => 'sitebase_pref',
                'nomAppscheme'       => 'Produit',
                'hasTypeScheme'      => 0,
                'actif'              => 1,
            ],
        ]);
        foreach (['appscheme_type', 'appscheme_base', 'appscheme_field',
                  'appscheme_field_type', 'appscheme_field_group',
                  'appscheme_has_field', 'appscheme_has_table_field'] as $coll) {
            self::$sitebaseApp->selectCollection($coll)->drop();
        }
        self::$sitebasePref->selectCollection('produit')->drop();
        self::$sitebasePref->selectCollection('produit')->insertMany([
            ['idproduit' => 1, 'nomProduit' => 'Widget Alpha',
             'prixProduit' => 9.99, 'estTopProduit' => 0, 'actif' => 1],
            ['idproduit' => 2, 'nomProduit' => 'Widget Beta',
             'prixProduit' => 19.99, 'estTopProduit' => 1, 'actif' => 1],
            ['idproduit' => 3, 'nomProduit' => 'Widget Gamma (inactive)',
             'prixProduit' => 0.0, 'estTopProduit' => 0, 'actif' => 0],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$sitebaseApp->selectCollection('appscheme')->drop();
        self::$sitebasePref->selectCollection('produit')->drop();
        parent::tearDownAfterClass();
    }

    private function makeApp(): \App
    {
        global $PERSIST_CON;
        $PERSIST_CON = null;
        return new \App('produit');
    }

    // ----------------------------------------------------------------
    // query() tests
    // ----------------------------------------------------------------

    public function testQueryReturnsWrapper(): void
    {
        $this->assertInstanceOf(MongodbCursorWrapper::class, $this->makeApp()->query([]));
    }

    public function testQueryNoFilterReturnsAllDocs(): void
    {
        $this->assertCount(3, $this->makeApp()->query([], 0, 100)->toArray());
    }

    public function testQueryCountMatchesResults(): void
    {
        $rs = $this->makeApp()->query(['actif' => 1]);
        $this->assertSame(2, $rs->count());
        $this->assertSame(2, count($rs->toArray()));
    }

    public function testQuerySimpleFilter(): void
    {
        $docs = $this->makeApp()->query(['idproduit' => 2])->toArray();
        $this->assertCount(1, $docs);
        $this->assertSame(2, $docs[0]['idproduit']);
        $this->assertSame('Widget Beta', $docs[0]['nomProduit']);
    }

    public function testQueryLimitPagination(): void
    {
        $this->assertCount(1, $this->makeApp()->query([], 0, 1)->toArray());
    }

    public function testQuerySkipPagination(): void
    {
        $all  = $this->makeApp()->query([], 0, 100)->toArray();
        $page = $this->makeApp()->query([], 1, 1)->toArray();
        $this->assertCount(1, $page);
        $this->assertNotSame($all[0]['idproduit'], $page[0]['idproduit']);
    }

    public function testQueryResultsAreArrays(): void
    {
        foreach ($this->makeApp()->query([], 0, 10)->toArray() as $doc) {
            $this->assertIsArray($doc);
        }
    }

    public function testQueryProjectionFiltersFields(): void
    {
        $docs  = $this->makeApp()->query([], 0, 1, ['idproduit' => 1, 'nomProduit' => 1])->toArray();
        $first = $docs[0];
        $this->assertArrayHasKey('idproduit', $first);
        $this->assertArrayHasKey('nomProduit', $first);
        $this->assertArrayNotHasKey('prixProduit', $first);
    }

    public function testQueryComplexFilter(): void
    {
        $docs = $this->makeApp()->query(['prixProduit' => ['$gt' => 10.0]])->toArray();
        $this->assertCount(1, $docs);
        $this->assertSame(2, $docs[0]['idproduit']);
    }

    public function testQueryEmptyResultForMissingId(): void
    {
        $this->assertCount(0, $this->makeApp()->query(['idproduit' => 9999])->toArray());
    }

    public function testQueryChainedSortCompatibility(): void
    {
        $rs = $this->makeApp()->query([])->sort(['nomProduit' => 1]);
        $this->assertInstanceOf(MongodbCursorWrapper::class, $rs);
    }

    // ----------------------------------------------------------------
    // findOne() tests
    // ----------------------------------------------------------------

    public function testFindOneExistingReturnsArray(): void
    {
        $doc = $this->makeApp()->findOne(['idproduit' => 1]);
        $this->assertIsArray($doc);
        $this->assertSame(1, $doc['idproduit']);
        $this->assertSame('Widget Alpha', $doc['nomProduit']);
    }

    public function testFindOneNonExistentReturnsNull(): void
    {
        $this->assertNull($this->makeApp()->findOne(['idproduit' => 9999]));
    }

    public function testFindOneReturnsNullNotEmptyArray(): void
    {
        $doc = $this->makeApp()->findOne(['idproduit' => 99999]);
        $this->assertNull($doc);
        $this->assertNotSame([], $doc);
    }

    public function testFindOneWithProjection(): void
    {
        $doc = $this->makeApp()->findOne(['idproduit' => 2], ['idproduit' => 1, 'nomProduit' => 1]);
        $this->assertArrayHasKey('idproduit', $doc);
        $this->assertArrayHasKey('nomProduit', $doc);
        $this->assertArrayNotHasKey('prixProduit', $doc);
    }

    public function testFindOneStructureMatchesInserted(): void
    {
        $doc = $this->makeApp()->findOne(['idproduit' => 3]);
        $this->assertSame(0, $doc['actif']);
        $this->assertSame(0.0, $doc['prixProduit']);
        $this->assertSame('Widget Gamma (inactive)', $doc['nomProduit']);
    }

    public function testFindOneComplexFilter(): void
    {
        $doc = $this->makeApp()->findOne(['actif' => 1, 'estTopProduit' => 1]);
        $this->assertNotNull($doc);
        $this->assertSame(2, $doc['idproduit']);
    }

    public function testInsertCreatesDocument(): void
    {
        $app = $this->makeApp();
        $id = $app->insert(['nomProduit' => 'Widget Delta', 'prixProduit' => 5.55, 'estTopProduit' => 0, 'actif' => 1]);
        $this->assertIsInt($id);
        $doc = $app->findOne(['idproduit' => $id]);
        $this->assertNotNull($doc);
        $this->assertSame('Widget Delta', $doc['nomProduit']);
    }

    public function testCreateUpdateUpsertsAndUpdates(): void
    {
        $app = $this->makeApp();
        $id = (int)$app->create_update(['nomProduit' => 'Widget Epsilon'], ['prixProduit' => 7.77, 'actif' => 1]);
        $this->assertIsInt($id);
        $doc = $app->findOne(['idproduit' => $id]);
        $this->assertNotNull($doc);
        $this->assertSame('Widget Epsilon', $doc['nomProduit']);

        // Update same
        $app->create_update(['idproduit' => $id], ['prixProduit' => 9.99]);
        $doc2 = $app->findOne(['idproduit' => $id]);
        $this->assertSame(9.99, $doc2['prixProduit']);
    }

    public function testUpdateModifiesDocument(): void
    {
        $app = $this->makeApp();
        $id = (int)$app->insert(['nomProduit' => 'Widget Zeta', 'prixProduit' => 1.23, 'actif'=>1]);
        $app->update(['idproduit' => $id], ['prixProduit' => 2.34]);
        $doc = $app->findOne(['idproduit' => $id]);
        $this->assertSame(2.34, $doc['prixProduit']);
    }

    public function testRemoveDeletesDocument(): void
    {
        $app = $this->makeApp();
        $id = (int)$app->insert(['nomProduit' => 'Widget Theta', 'prixProduit' => 3.21, 'actif'=>1]);
        $app->remove(['idproduit' => $id]);
        $this->assertNull($app->findOne(['idproduit' => $id]));
    }
}
