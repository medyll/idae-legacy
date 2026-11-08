<?php
declare(strict_types=1);

/**
 * Measure grouping reply payloads on generated data in the isolated test sidecar.
 * @package Idae\Audit
 * @date 2026-11-08
 */
if (getenv('MONGO_ENV') !== 'test'
    || parse_url(getenv('MONGO_TEST_DSN') ?: 'mongodb://mongo-test:27017', PHP_URL_HOST) !== 'mongo-test') {
    throw new RuntimeException('This benchmark requires the isolated mongo-test sidecar.');
}
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/Unit/ClassAppAggTest.php';

$monitor = new class implements \MongoDB\Driver\Monitoring\CommandSubscriber {
    public int $jsonReplyBytes = 0;
    public int $replyDocuments = 0;
    public int $commands = 0;
    public function commandStarted(\MongoDB\Driver\Monitoring\CommandStartedEvent $event): void {}
    public function commandFailed(\MongoDB\Driver\Monitoring\CommandFailedEvent $event): void {}
    public function commandSucceeded(\MongoDB\Driver\Monitoring\CommandSucceededEvent $event): void
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 30) as $frame) {
            if (in_array($frame['class'] ?? '', ['App', 'AppCommon\\ClassAppAgg'], true)
                && ($frame['function'] ?? '') === 'distinct_rs') {
                $reply = $event->getReply();
                if (($reply->cursor->ns ?? '') === 'sitebase_pref.produit') {
                    $batch = $reply->cursor->firstBatch ?? $reply->cursor->nextBatch ?? [];
                    $this->jsonReplyBytes += strlen(json_encode($batch, JSON_THROW_ON_ERROR));
                    $this->replyDocuments += count($batch);
                    $this->commands++;
                }
                break;
            }
        }
    }
};

\Idae\Tests\Unit\ClassAppAggTest::setUpBeforeClass();
$client = new \MongoDB\Client(getenv('MONGO_TEST_DSN') ?: 'mongodb://mongo-test:27017');
try {
    $client->selectDatabase('sitebase_app')->selectCollection('appscheme')->insertOne([
        'codeAppscheme' => 'categorie', 'codeAppscheme_base' => 'sitebase_pref', 'nomAppscheme' => 'Categorie',
    ]);
    $documents = [];
    for ($i = 0; $i < 200; $i++) {
        $documents[] = ['idproduit' => 1000 + $i, 'nomProduit' => 'Fixture ' . $i,
            'idcategorie' => $i % 2 === 0 ? 10 : 20, 'descriptionProduit' => str_repeat('x', 16384)];
    }
    $client->selectDatabase('sitebase_pref')->selectCollection('produit')->insertMany($documents);
    $app = new \App('produit');
    \MongoDB\Driver\Monitoring\addSubscriber($monitor);
    $rows = $app->distinct_rs(['groupBy_table' => 'categorie', 'limit' => 50]);
    \MongoDB\Driver\Monitoring\removeSubscriber($monitor);
    $summary = ['rows' => count($rows), 'counts' => array_column($rows, 'count'),
        'result_sha256' => hash('sha256', json_encode($rows, JSON_THROW_ON_ERROR)),
        'source_reply_commands' => $monitor->commands, 'source_reply_documents' => $monitor->replyDocuments,
        'source_reply_json_bytes' => $monitor->jsonReplyBytes, 'peak_memory_bytes' => memory_get_peak_usage(true)];
    echo json_encode($summary, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), PHP_EOL;
} finally {
    $client->selectDatabase('sitebase_app')->selectCollection('appscheme')->deleteOne(['codeAppscheme' => 'categorie']);
    \Idae\Tests\Unit\ClassAppAggTest::tearDownAfterClass();
}
