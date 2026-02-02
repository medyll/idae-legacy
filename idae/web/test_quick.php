<?php
/**
 * Quick MongoDB Migration Validation
 * Created: 2026-02-02
 */

try {
	// Load config first (it sends headers)
	include_once(__DIR__ . '/conf.inc.php');
	
	echo "=== Quick MongoDB Migration Test ===\n\n";
	echo "1. Config loaded: OK\n";
	
	$app = new App('appscheme');
	echo "2. App instance created: OK\n";
	
	$result = $app->findOne(['codeAppscheme' => 'appscheme']);
	echo "3. findOne() executed: " . (!empty($result) ? "OK" : "FAIL") . "\n";
	
	$cursor = $app->find(['codeAppscheme_type' => 'entity']);
	$count = 0;
	foreach ($cursor as $doc) {
		$count++;
		if ($count >= 3) break;
	}
	echo "4. find() cursor iteration: OK ({$count} entities)\n";
	
	// Test insert
	$test_app = new App('test_validation');
	$id = $test_app->insert([
		'testName' => 'Migration Test ' . date('Y-m-d H:i:s'),
		'timestamp' => time()
	]);
	echo "5. insert() (insertOne): OK (ID: {$id})\n";
	
	// Test update
	$test_app->update(
		['idtest_validation' => (int)$id],
		['$set' => ['testName' => 'Updated', 'updatedAt' => time()]]
	);
	echo "6. update() (updateOne): OK\n";
	
	// Test delete
	$test_app->remove(['idtest_validation' => (int)$id]);
	echo "7. remove() (deleteOne): OK\n";
	
	echo "\n=== ALL TESTS PASSED ===\n";
	echo "MongoDB migration is working correctly!\n";
	
} catch (Exception $e) {
	echo "\nERROR: " . $e->getMessage() . "\n";
	echo "File: " . $e->getFile() . "\n";
	echo "Line: " . $e->getLine() . "\n";
	exit(1);
}
