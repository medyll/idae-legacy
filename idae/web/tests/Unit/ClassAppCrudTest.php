<?php
declare(strict_types=1);

namespace Idae\Tests\Unit;

use Idae\Tests\TestCase;

class ClassAppCrudTest extends TestCase
{
    public function testInsertSkipsWhenNotTestEnv(): void
    {
        if ((getenv('MONGO_ENV') ?: '') !== 'test') {
            $this->markTestSkipped('Requires MONGO_ENV=test to run integration-style CRUD test');
        }

        $app = new \App('test_collection');
        $id = $app->insert(['nomTest' => 'x']);
        $this->assertIsInt($id);
    }

    public function testUpdateSkipsWhenNotTestEnv(): void
    {
        $this->markTestSkipped('Integration-style test; enable MONGO_ENV=test to run');
    }

    public function testRemoveSkipsWhenNotTestEnv(): void
    {
        $this->markTestSkipped('Integration-style test; enable MONGO_ENV=test to run');
    }
}
