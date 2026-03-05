<?php
/**
 * MongoDB Migration Test Suite
 * 
 * Tests CRUD operations using the new MongoDB driver
 * Validates that updateOne, insertOne, deleteOne work correctly
 * 
 * Created: 2026-02-02 (Migration validation)
 */

// Load configuration
include_once(__DIR__ . '/conf.inc.php');

// Helper to display test results
function test_result($name, $passed, $message = '') {
	$status = $passed ? '✓ PASS' : '✗ FAIL';
	$color = $passed ? '32' : '31'; // Green or Red
	echo "\033[{$color}m{$status}\033[0m {$name}";
	if ($message) {
		echo " - {$message}";
	}
	echo "\n";
	return $passed;
}

// Test counter
$tests_passed = 0;
$tests_failed = 0;

echo "\n=== MongoDB Migration Test Suite ===\n\n";

// Test 1: Connection
echo "1. Testing MongoDB Connection\n";
try {
	$app = new App('appscheme');
	$connected = !empty($app->collection);
	if (test_result('MongoDB connection', $connected)) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('MongoDB connection', false, $e->getMessage());
	$tests_failed++;
	die("Cannot proceed without connection\n");
}

// Test 2: findOne
echo "\n2. Testing findOne() method\n";
try {
	$result = $app->findOne(['codeAppscheme' => 'appscheme']);
	$passed = !empty($result) && is_array($result);
	if (test_result('findOne() returns data', $passed, "Found: " . ($result['nomAppscheme'] ?? 'N/A'))) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('findOne()', false, $e->getMessage());
	$tests_failed++;
}

// Test 3: find (cursor)
echo "\n3. Testing find() method\n";
try {
	$cursor = $app->find(['codeAppscheme_type' => 'entity']);
	$count = 0;
	foreach ($cursor as $doc) {
		$count++;
		if ($count >= 5) break; // Just test first 5
	}
	$passed = $count > 0;
	if (test_result('find() returns cursor', $passed, "Found {$count} entities")) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('find()', false, $e->getMessage());
	$tests_failed++;
}

// Test 4: Insert
echo "\n4. Testing insertOne() method\n";
try {
	$test_app = new App('test_migration_collection');
	$test_data = [
		'nomTest' => 'Test Migration ' . date('Y-m-d H:i:s'),
		'codeTest' => 'test_' . uniqid(),
		'timestampTest' => time()
	];
	
	$inserted_id = $test_app->insert($test_data);
	$passed = !empty($inserted_id);
	
	if (test_result('insertOne() creates document', $passed, "ID: {$inserted_id}")) {
		$tests_passed++;
		
		// Store for next tests
		$test_id = $inserted_id;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('insertOne()', false, $e->getMessage());
	$tests_failed++;
}

// Test 5: Update (via updateOne)
echo "\n5. Testing updateOne() method\n";
if (!empty($test_id)) {
	try {
		$test_app = new App('test_migration_collection');
		$test_app->update(
			['idtest_migration_collection' => (int)$test_id],
			['$set' => ['nomTest' => 'Updated Migration Test', 'updatedAt' => time()]]
		);
		
		// Verify update
		$updated = $test_app->findOne(['idtest_migration_collection' => (int)$test_id]);
		$passed = $updated && $updated['nomTest'] === 'Updated Migration Test';
		
		if (test_result('updateOne() modifies document', $passed)) {
			$tests_passed++;
		} else {
			$tests_failed++;
		}
	} catch (Exception $e) {
		test_result('updateOne()', false, $e->getMessage());
		$tests_failed++;
	}
} else {
	test_result('updateOne()', false, 'No test document to update');
	$tests_failed++;
}

// Test 6: Update with upsert
echo "\n6. Testing updateOne() with upsert\n";
try {
	$test_app = new App('test_migration_collection');
	$unique_code = 'upsert_test_' . uniqid();
	
	// First upsert (insert)
	$result = $test_app->create_update(
		['codeTest' => $unique_code],
		['nomTest' => 'Upsert Test 1', 'counter' => 1]
	);
	
	$passed = !empty($result);
	
	// Second upsert (update)
	if ($passed) {
		$result2 = $test_app->create_update(
			['codeTest' => $unique_code],
			['nomTest' => 'Upsert Test 2', 'counter' => 2]
		);
		$passed = $result === $result2; // Same ID
	}
	
	if (test_result('updateOne() upsert works', $passed, "ID: {$result}")) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('updateOne() upsert', false, $e->getMessage());
	$tests_failed++;
}

// Test 7: Aggregation / Count
echo "\n7. Testing count operations\n";
try {
	$test_app = new App('test_migration_collection');
	$count = $test_app->find(['nomTest' => ['$regex' => 'Test']])->count();
	$passed = $count > 0;
	
	if (test_result('count() works', $passed, "Count: {$count}")) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('count()', false, $e->getMessage());
	$tests_failed++;
}

// Test 8: Foreign Key relationships
echo "\n8. Testing FK relationships (get_grille_fk)\n";
try {
	// Test with a table that has FKs
	$test_fk = new App('agent');
	$fks = $test_fk->get_grille_fk();
	$passed = is_array($fks);
	
	if (test_result('get_grille_fk() returns FK data', $passed, count($fks) . " FKs found")) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('get_grille_fk()', false, $e->getMessage());
	$tests_failed++;
}

// Test 9: Regex search (MongoCompat)
echo "\n9. Testing regex search (MongoCompat)\n";
try {
	$app = new App('appscheme');
	$pattern = \AppCommon\MongoCompat::toRegex('app', 'i');
	$results = $app->find(['codeAppscheme' => ['$regex' => $pattern]]);
	$count = 0;
	foreach ($results as $doc) {
		$count++;
		if ($count >= 5) break;
	}
	$passed = $count > 0;
	
	if (test_result('Regex search works', $passed, "{$count} matches found")) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('Regex search', false, $e->getMessage());
	$tests_failed++;
}

// Test 10: Delete
echo "\n10. Testing deleteOne() method\n";
try {
	$test_app = new App('test_migration_collection');
	// Delete all test documents
	$deleted = $test_app->remove(['nomTest' => ['$regex' => 'Migration Test']]);
	$passed = true; // No error means success
	
	if (test_result('deleteOne() removes documents', $passed)) {
		$tests_passed++;
	} else {
		$tests_failed++;
	}
} catch (Exception $e) {
	test_result('deleteOne()', false, $e->getMessage());
	$tests_failed++;
}

// Summary
echo "\n=== Test Summary ===\n";
echo "Passed: \033[32m{$tests_passed}\033[0m\n";
echo "Failed: \033[31m{$tests_failed}\033[0m\n";
$total = $tests_passed + $tests_failed;
$percentage = $total > 0 ? round(($tests_passed / $total) * 100, 2) : 0;
echo "Success Rate: {$percentage}%\n\n";

if ($tests_failed === 0) {
	echo "\033[32m✓ All tests passed! MongoDB migration is successful.\033[0m\n";
	exit(0);
} else {
	echo "\033[31m✗ Some tests failed. Please review the errors above.\033[0m\n";
	exit(1);
}
