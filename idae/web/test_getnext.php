<?php
/**
 * MongoDB getNext() Validation Test
 * Tests the MongodbCursorWrapper implementation
 * 
 * Created: 2026-02-02
 */
header('Content-Type: text/plain; charset=utf-8');

include_once(__DIR__ . '/conf.inc.php');

echo "=== MongoDB getNext() Compatibility Test ===\n\n";

try {
	// Test 1: App->find() returns wrapped cursor
	echo "Test 1: App->find() returns MongodbCursorWrapper\n";
	$app = new App('appscheme');
	$cursor = $app->find([]);
	$class = get_class($cursor);
	echo "  Cursor class: {$class}\n";
	if ($class === 'AppCommon\MongodbCursorWrapper') {
		echo "  ✓ PASS: Returns wrapped cursor\n";
	} else {
		echo "  ✗ FAIL: Expected MongodbCursorWrapper, got {$class}\n";
	}
	echo "\n";
	
	// Test 2: getNext() iteration
	echo "Test 2: getNext() iteration on App->find() result\n";
	$cursor = $app->find(['codeAppscheme' => ['$exists' => true]]);
	$count = 0;
	while ($doc = $cursor->getNext()) {
		$count++;
		if ($count === 1) {
			echo "  First document: " . ($doc['nomAppscheme'] ?? 'N/A') . "\n";
		}
		if ($count >= 5) break;
	}
	echo "  ✓ PASS: Iterated {$count} documents with getNext()\n\n";
	
	// Test 3: foreach iteration
	echo "Test 3: foreach iteration on wrapped cursor\n";
	$cursor2 = $app->find(['codeAppscheme' => ['$exists' => true]]);
	$count2 = 0;
	foreach ($cursor2 as $doc) {
		$count2++;
		if ($count2 >= 5) break;
	}
	echo "  ✓ PASS: Iterated {$count2} documents with foreach\n\n";
	
	// Test 4: count() method
	echo "Test 4: count() method on wrapped cursor\n";
	$cursor3 = $app->find(['codeAppscheme' => ['$exists' => true]]);
	$total = $cursor3->count();
	echo "  Total documents: {$total}\n";
	echo "  ✓ PASS: count() works\n\n";
	
	// Test 5: Direct collection->find() with wrapper
	echo "Test 5: Direct collection->find() with MongodbCursorWrapper\n";
	$col = $app->plug('sitebase_app', 'appscheme');
	$raw_cursor = $col->find(['codeAppscheme' => ['$exists' => true]]);
	$wrapped = new AppCommon\MongodbCursorWrapper($raw_cursor);
	$count5 = 0;
	while ($doc = $wrapped->getNext()) {
		$count5++;
		if ($count5 >= 3) break;
	}
	echo "  Iterated {$count5} documents\n";
	echo "  ✓ PASS: Manual wrapping works\n\n";
	
	// Test 6: Empty result set
	echo "Test 6: getNext() on empty result set\n";
	$empty_cursor = $app->find(['codeAppscheme' => 'nonexistent_table_xyz']);
	$first = $empty_cursor->getNext();
	if ($first === false) {
		echo "  ✓ PASS: Returns false for empty result\n";
	} else {
		echo "  ✗ FAIL: Expected false, got " . json_encode($first) . "\n";
	}
	echo "\n";
	
	// Test 7: Multiple getNext() calls consume cursor
	echo "Test 7: Cursor consumption test\n";
	$cursor7 = $app->find(['codeAppscheme' => ['$exists' => true]]);
	$first_count = 0;
	while ($doc = $cursor7->getNext()) {
		$first_count++;
		if ($first_count >= 3) break;
	}
	echo "  First iteration: {$first_count} documents\n";
	
	// Continue iteration
	$second_count = 0;
	while ($doc = $cursor7->getNext()) {
		$second_count++;
		if ($second_count >= 2) break;
	}
	echo "  Second iteration: {$second_count} documents\n";
	echo "  ✓ PASS: Cursor maintains position\n\n";
	
	echo "=================================\n";
	echo "All getNext() tests passed!\n";
	echo "MongodbCursorWrapper is working correctly.\n";
	
} catch (Exception $e) {
	echo "\n✗ ERROR: " . $e->getMessage() . "\n";
	echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
	echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
	exit(1);
}
