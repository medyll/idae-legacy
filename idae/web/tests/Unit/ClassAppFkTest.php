<?php
declare(strict_types=1);

/**
 * ClassAppFkTest.php — Unit tests for ClassAppFk helpers
 * Date: 2026-03-07
 */

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

require_once __DIR__ . '/../../appclasses/appcommon/ClassAppFk.php';
require_once __DIR__ . '/../../appclasses/appcommon/ClassApp.php';

class ClassAppFkTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Use test database to avoid touching production data
        $db = self::$mongoClient->selectDatabase('idae_test');

        // Minimal appscheme entry that references 'produit' via grilleFK
        $db->selectCollection('appscheme')->insertOne([
            'idappscheme' => 9001,
            'codeAppscheme' => 'order',
            'codeAppscheme_base' => 'idae_test',
            'nomAppscheme' => 'Order',
            'grilleFK' => [['table' => 'produit']]
        ]);

        // Create collections and fixture documents
        $db->selectCollection('order')->insertOne(['idorder' => 1, 'idproduit' => 1]);
        $db->selectCollection('produit')->insertOne(['idproduit' => 1, 'nomProduit' => 'Prod A']);
    }

    public static function tearDownAfterClass(): void
    {
        $db = self::$mongoClient->selectDatabase('idae_test');
        $db->selectCollection('appscheme')->deleteOne(['idappscheme' => 9001]);
        $db->selectCollection('order')->drop();
        $db->selectCollection('produit')->drop();
        parent::tearDownAfterClass();
    }

    public function testGetGrilleRfkReturnsArray(): void
    {
        $app = new \App('produit');
        $out = \AppCommon\ClassAppFk::get_grille_rfk($app, 'produit', 1);
        $this->assertIsArray($out);
    }
}
