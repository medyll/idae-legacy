<?php
declare(strict_types=1);

/**
 * Characterize reverse-relation lookup results and its database round trips.
 * @package Idae\Tests\Integration
 * @date 2026-11-09
 */
namespace Idae\Tests\Integration;

use Idae\Tests\TestCase;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

class AppReverseRelationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanFixtures();
        $meta = self::$mongoClient->selectDatabase('sitebase_app');
        $meta->selectCollection('appscheme_type')->insertOne([
            'idappscheme_type' => 9201, 'nomAppscheme_type' => 'Test invoices',
        ]);
        $meta->selectCollection('appscheme')->insertOne([
            'idappscheme' => 9201, 'idappscheme_type' => 9201, 'codeAppscheme' => 'invoice_test',
            'codeAppscheme_base' => 'idae_test_reverse_fk', 'nomAppscheme' => 'Test invoice',
            'grilleFK' => [['table' => 'produit']],
        ]);
        self::$mongoClient->selectDatabase('idae_test_reverse_fk')->selectCollection('invoice_test')->insertMany([
            ['idinvoice_test' => 1, 'idproduit' => 1],
            ['idinvoice_test' => 2, 'idproduit' => 1],
            ['idinvoice_test' => 3, 'idproduit' => 2],
        ]);
    }

    protected function tearDown(): void
    {
        $this->cleanFixtures();
        parent::tearDown();
    }

    private function cleanFixtures(): void
    {
        $meta = self::$mongoClient->selectDatabase('sitebase_app');
        $meta->selectCollection('appscheme')->deleteMany(['idappscheme' => 9201]);
        $meta->selectCollection('appscheme_type')->deleteMany(['idappscheme_type' => 9201]);
        self::$mongoClient->selectDatabase('idae_test_reverse_fk')->selectCollection('invoice_test')->drop();
    }

    public function testReverseRelationsCountOnlyRowsLinkedToTheRequestedEntity(): void
    {
        $rows = (new \App('produit'))->get_grille_rfk('produit', 1);
        $this->assertCount(1, $rows);
        $this->assertSame(9201, $rows[0]['idappscheme_type']);
        $this->assertSame(2, $rows[0]['appscheme']['invoice_test']['count']);
        $this->assertSame('invoice_test', $rows[0]['appscheme']['invoice_test']['table']);
    }

    public function testAdditionalSchemeFilterPreservesTheAllowedRelation(): void
    {
        $rows = (new \App('produit'))->get_grille_rfk('produit', 1, ['codeAppscheme' => 'invoice_test']);
        $this->assertCount(1, $rows);
        $this->assertSame(2, $rows[0]['appscheme']['invoice_test']['count']);
    }

    public function testAdditionalSchemeFilterCanExcludeTheRelation(): void
    {
        $this->assertSame([], (new \App('produit'))->get_grille_rfk('produit', 1, ['codeAppscheme' => 'missing']));
    }

    public function testMissingLinkedEntityReturnsNoGroups(): void
    {
        $this->assertSame([], (new \App('produit'))->get_grille_rfk('produit', 404));
    }

    public function testEmptyTableKeepsTheLegacyEarlyReturn(): void
    {
        $this->assertSame([], (new \App('produit'))->get_grille_rfk(null, 1));
    }

    public function testLookupDoesNotFetchAnUnusedSchemeCursor(): void
    {
        $app = new \App('produit');
        $monitor = new class implements CommandSubscriber {
            public int $schemeFinds = 0;
            public int $commands = 0;
            public function commandStarted(CommandStartedEvent $event): void
            {
                $this->commands++;
                if ($event->getDatabaseName() === 'sitebase_app'
                    && $event->getCommandName() === 'find'
                    && ($event->getCommand()->find ?? '') === 'appscheme') {
                    $this->schemeFinds++;
                }
            }
            public function commandSucceeded(CommandSucceededEvent $event): void {}
            public function commandFailed(CommandFailedEvent $event): void {}
        };
        \MongoDB\Driver\Monitoring\addSubscriber($monitor);
        try {
            $rows = $app->get_grille_rfk('produit', 1);
        } finally {
            \MongoDB\Driver\Monitoring\removeSubscriber($monitor);
        }
        $this->assertCount(1, $rows);
        $this->assertLessThanOrEqual(1, $monitor->schemeFinds, 'Unused scheme reads must not add a round trip.');
        $this->assertLessThanOrEqual(4, $monitor->commands);
    }
}
