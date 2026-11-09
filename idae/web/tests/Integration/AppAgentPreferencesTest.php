<?php
declare(strict_types=1);

/**
 * Exercise agent preferences through the legacy App entry points.
 * @package Idae\Tests\Integration
 * @date 2026-11-09
 */
namespace Idae\Tests\Integration;

use AppCommon\MongoCollection;
use AppCommon\MongoDB;
use Idae\Tests\TestCase;

class AppAgentPreferencesTest extends TestCase
{
    private const AGENT = 913001;
    private const OTHER_AGENT = 913002;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanPreferences();
    }

    protected function tearDown(): void
    {
        $this->cleanPreferences();
        parent::tearDown();
    }

    private function preferences(): MongoCollection
    {
        return (new MongoDB(self::$mongoClient, 'sitebase_pref'))->selectCollection('agent_pref');
    }

    private function cleanPreferences(): void
    {
        $this->preferences()->deleteMany(['idagent' => ['$in' => [self::AGENT, self::OTHER_AGENT]]]);
    }

    public function testTableSpecificPreferencesAreScopedToTheAgent(): void
    {
        $app = new \App();
        $app->set_settings((string)self::AGENT, ['sort' => 'date', 'sort_produit' => 'name']);
        $app->set_settings(self::OTHER_AGENT, ['sort_produit' => 'price']);

        $this->assertSame('date', $app->get_settings(self::AGENT, 'sort'));
        $this->assertSame('name', $app->get_settings(self::AGENT, 'sort', 'produit'));
        $this->assertTrue($app->del_settings(self::AGENT, 'sort', 'produit'));
        $this->assertSame('price', $app->get_settings(self::OTHER_AGENT, 'sort', 'produit'));
        $this->assertSame('date', $app->get_settings(self::AGENT, 'sort'));
        $this->assertNull($app->get_settings(self::AGENT, 'sort', 'produit'));
    }

    public function testCreatingThenUpdatingInOneBatchKeepsTheExistingId(): void
    {
        $existing = ['idagent' => self::AGENT, 'codeAgent_pref' => 'existing'];
        $this->preferences()->insertOne($existing + ['idagent_pref' => 987654321, 'valeurAgent_pref' => 'old']);

        $app = new \App();
        $app->set_settings(self::AGENT, ['new' => false, 'existing' => 'updated']);

        $this->assertSame(false, $app->get_settings(self::AGENT, 'new'));
        $created = $this->preferences()->findOne(['idagent' => self::AGENT, 'codeAgent_pref' => 'new']);
        $this->assertGreaterThan(0, $created['idagent_pref']);
        $this->assertSame('updated', $app->get_settings(self::AGENT, 'existing'));
        $this->assertSame(987654321, $this->preferences()->findOne($existing)['idagent_pref']);
    }
}
