<?php
declare(strict_types=1);

/**
 * TestCase.php — PHPUnit base class for Idae Legacy tests
 *
 * Connects exclusively to the mongo-test sidecar (MONGO_ENV=test).
 * Aborts immediately if MONGO_ENV is not "test" to prevent any
 * accidental writes to the production database.
 *
 * Usage: extend this class in all Idae PHPUnit test cases.
 *
 * @package Idae\Tests
 * Date: 2026-03-02
 */

namespace Idae\Tests;

use MongoDB\Client;
use MongoDB\Database;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase
{
    protected static Client   $mongoClient;
    public static Database $db;

    /** Collections wiped between tests — override in subclass to add more. */
    protected array $collectionsToClean = [];

    // -------------------------------------------------------------------------
    // Bootstrap
    // -------------------------------------------------------------------------

    /**
     * Called once per test class. Validates MONGO_ENV and opens the connection.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $env = getenv('MONGO_ENV') ?: 'dev';
        if ($env !== 'test') {
            self::fail(
                'MONGO_ENV must be "test" to run PHPUnit. ' .
                'Current value: "' . $env . '". ' .
                'Set MONGO_ENV=test in phpunit.xml or your shell before running tests.'
            );
        }

        $dsn = getenv('MONGO_TEST_DSN') ?: 'mongodb://mongo-test:27017';

        self::$mongoClient = new Client($dsn, [], [
            'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
            'connectTimeoutMS'         => 5000,
            'serverSelectionTimeoutMS' => 5000,
            'socketTimeoutMS'          => 30000,
        ]);

        // Use a dedicated test database — never the production sitebase_app
        self::$db = self::$mongoClient->selectDatabase('idae_test');
    }

    // -------------------------------------------------------------------------
    // Per-test isolation
    // -------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanCollections($this->collectionsToClean);
    }

    protected function tearDown(): void
    {
        $this->cleanCollections($this->collectionsToClean);
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Drop and recreate a list of collections (fast wipe).
     *
     * @param string[] $collections
     */
    protected function cleanCollections(array $collections): void
    {
        foreach ($collections as $name) {
            self::$db->selectCollection($name)->drop();
        }
    }

    /**
     * Insert one document and return its inserted id as string.
     */
    protected function insertOne(string $collection, array $document): string
    {
        $result = self::$db->selectCollection($collection)->insertOne($document);
        return (string) $result->getInsertedId();
    }

    /**
     * Insert many documents and return the count.
     */
    protected function insertMany(string $collection, array $documents): int
    {
        if (empty($documents)) {
            return 0;
        }
        $result = self::$db->selectCollection($collection)->insertMany($documents);
        return $result->getInsertedCount();
    }

    /**
     * Find all documents in a collection (no filter).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function findAll(string $collection): array
    {
        return iterator_to_array(
            self::$db->selectCollection($collection)->find([]),
            false
        );
    }

    /**
     * Return the raw MongoDB\Collection for advanced queries in a test.
     */
    protected function collection(string $name): \MongoDB\Collection
    {
        return self::$db->selectCollection($name);
    }

    /**
     * Seed the test database from the standard fixture set.
     * Call this in setUp() when a test needs pre-populated data.
     */
    protected function seedFixtures(): void
    {
        require_once __DIR__ . '/fixtures/seed.php';
        \Idae\Tests\Fixtures\seed(self::$db);
    }
}
