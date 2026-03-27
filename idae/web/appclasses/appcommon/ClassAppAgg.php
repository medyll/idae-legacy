<?php
declare(strict_types=1);

/**
 * ClassAppAgg.php — MongoDB Aggregation Helpers for ClassApp
 *
 * Extends ClassApp with aggregation pipeline methods for analytics,
 * grouping, counting, and summing operations.
 *
 * @package AppCommon
 * Date: 2026-03-27
 */

namespace AppCommon;

require_once __DIR__ . '/ClassApp.php';
require_once __DIR__ . '/MongoCompat.php';

use MongoDB\BSON\Regex;

// App class is not namespaced
class ClassAppAgg extends \App
{
    /**
     * Execute an aggregation pipeline on the current collection.
     *
     * @param array<int, array<string, mixed>> $pipeline Aggregation pipeline stages
     * @param array<string, mixed> $options Additional options (typeMap, maxTimeMS, etc.)
     * @return iterable<int, array<string, mixed>> Aggregation results
     * @throws \MongoDB\Driver\Exception\RuntimeException on connection error
     */
    public function aggregate(array $pipeline, array $options = []): iterable
    {
        if (empty($this->table)) {
            error_log('[ClassAppAgg::aggregate] No table specified');
            return [];
        }

        $collection = $this->plug(
            $this->app_table_one['codeAppscheme_base'] ?? 'sitebase_app',
            $this->table
        )->getCollection();

        $defaultOptions = [
            'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
        ];

        try {
            $cursor = $collection->aggregate($pipeline, array_merge($defaultOptions, $options));
            return $cursor;
        } catch (\Throwable $e) {
            error_log('[ClassAppAgg::aggregate] Aggregation failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count documents grouped by a specific field.
     *
     * Example result: [
     *   ['_id' => 'active', 'count' => 15],
     *   ['_id' => 'inactive', 'count' => 5]
     * ]
     *
     * @param string $groupByField Field to group by
     * @param array<string, mixed> $matchFilter Optional filter to apply before grouping
     * @return array<int, array<string, mixed>> Grouped counts
     */
    public function countBy(string $groupByField, array $matchFilter = []): array
    {
        $pipeline = [];

        // Add match stage if filter provided
        if (!empty($matchFilter)) {
            $pipeline[] = ['$match' => MongoCompat::convertFilter($matchFilter)];
        }

        // Group and count
        $pipeline[] = [
            '$group' => [
                '_id' => '$' . $groupByField,
                'count' => ['$sum' => 1],
            ],
        ];

        // Sort by count descending
        $pipeline[] = ['$sort' => ['count' => -1]];

        $results = iterator_to_array($this->aggregate($pipeline));

        return array_map(function ($row) use ($groupByField) {
            return [
                $groupByField => $row['_id'],
                'count' => $row['count'],
            ];
        }, $results);
    }

    /**
     * Sum a numeric field grouped by another field.
     *
     * Example result: [
     *   ['_id' => 'category_a', 'total' => 1500.50],
     *   ['_id' => 'category_b', 'total' => 2300.75]
     * ]
     *
     * @param string $sumField Field to sum (must be numeric)
     * @param string $groupByField Field to group by
     * @param array<string, mixed> $matchFilter Optional filter to apply before grouping
     * @return array<int, array<string, mixed>> Grouped sums
     */
    public function sumBy(string $sumField, string $groupByField, array $matchFilter = []): array
    {
        $pipeline = [];

        // Add match stage if filter provided
        if (!empty($matchFilter)) {
            $pipeline[] = ['$match' => MongoCompat::convertFilter($matchFilter)];
        }

        // Group and sum
        $pipeline[] = [
            '$group' => [
                '_id' => '$' . $groupByField,
                'total' => ['$sum' => '$' . $sumField],
            ],
        ];

        // Sort by total descending
        $pipeline[] = ['$sort' => ['total' => -1]];

        $results = iterator_to_array($this->aggregate($pipeline));

        return array_map(function ($row) use ($groupByField) {
            return [
                $groupByField => $row['_id'],
                'total' => (float)($row['total'] ?? 0),
            ];
        }, $results);
    }

    /**
     * Get distinct values for a field with optional filtering.
     *
     * @param string $field Field to get distinct values for
     * @param array<string, mixed> $filter Optional filter
     * @return array<int, mixed> Distinct values
     */
    public function distinctValues(string $field, array $filter = []): array
    {
        $pipeline = [];

        // Add match stage if filter provided
        if (!empty($filter)) {
            $pipeline[] = ['$match' => MongoCompat::convertFilter($filter)];
        }

        // Group by field
        $pipeline[] = [
            '$group' => [
                '_id' => '$' . $field,
            ],
        ];

        // Sort
        $pipeline[] = ['$sort' => ['_id' => 1]];

        $results = iterator_to_array($this->aggregate($pipeline));

        return array_map(fn($row) => $row['_id'], $results);
    }

    /**
     * Get top N items by a field, grouped by another field.
     *
     * Example: Get top 5 products by price in each category
     *
     * @param string $groupByField Field to group by (e.g., 'categorie')
     * @param string $sortField Field to sort by (e.g., 'prix')
     * @param int $limit Number of top items per group
     * @param array<string, mixed> $filter Optional filter
     * @return array<int, array<string, mixed>> Top N items per group
     */
    public function topNByGroup(
        string $groupByField,
        string $sortField,
        int $limit = 5,
        array $filter = []
    ): array {
        $pipeline = [];

        // Add match stage if filter provided
        if (!empty($filter)) {
            $pipeline[] = ['$match' => MongoCompat::convertFilter($filter)];
        }

        // Sort by the specified field
        $pipeline[] = ['$sort' => [$sortField => -1]];

        // Group and push top N
        $pipeline[] = [
            '$group' => [
                '_id' => '$' . $groupByField,
                'items' => [
                    '$push' => [
                        'id' => '$id' . ($this->table ?? 'item'),
                        $sortField => '$' . $sortField,
                        'nom' => '$nom' . ($this->table ?? 'item'),
                    ],
                ],
            ],
        ];

        // Limit items per group
        $pipeline[] = [
            '$project' => [
                '_id' => 0,
                $groupByField => '$_id',
                'topItems' => ['$slice' => ['$items', $limit]],
            ],
        ];

        $results = iterator_to_array($this->aggregate($pipeline));

        return array_values($results);
    }

    /**
     * Calculate statistics (min, max, avg, sum, count) for a numeric field.
     *
     * @param string $field Numeric field to analyze
     * @param array<string, mixed> $filter Optional filter
     * @return array<string, float|int> Statistics
     */
    public function statsForField(string $field, array $filter = []): array
    {
        $pipeline = [];

        // Add match stage if filter provided
        if (!empty($filter)) {
            $pipeline[] = ['$match' => MongoCompat::convertFilter($filter)];
        }

        // Calculate statistics
        $pipeline[] = [
            '$group' => [
                '_id' => null,
                'min' => ['$min' => '$' . $field],
                'max' => ['$max' => '$' . $field],
                'avg' => ['$avg' => '$' . $field],
                'sum' => ['$sum' => '$' . $field],
                'count' => ['$sum' => 1],
            ],
        ];

        $results = iterator_to_array($this->aggregate($pipeline));

        if (empty($results)) {
            return [
                'min' => 0,
                'max' => 0,
                'avg' => 0,
                'sum' => 0,
                'count' => 0,
            ];
        }

        $stats = $results[0];
        return [
            'min' => (float)($stats['min'] ?? 0),
            'max' => (float)($stats['max'] ?? 0),
            'avg' => (float)($stats['avg'] ?? 0),
            'sum' => (float)($stats['sum'] ?? 0),
            'count' => (int)($stats['count'] ?? 0),
        ];
    }

    /**
     * Search with regex across multiple fields.
     *
     * @param string $searchTerm Search term
     * @param array<string> $fields Fields to search in
     * @param array<string, mixed> $additionalFilter Additional filters
     * @param int $limit Max results
     * @return iterable<int, array<string, mixed>> Search results
     */
    public function search(
        string $searchTerm,
        array $fields,
        array $additionalFilter = [],
        int $limit = 100
    ): iterable {
        $orConditions = [];

        foreach ($fields as $field) {
            $orConditions[] = [
                $field => MongoCompat::toRegex(MongoCompat::escapeRegex($searchTerm), 'i'),
            ];
        }

        $matchFilter = array_merge($additionalFilter, ['$or' => $orConditions]);

        $pipeline = [
            ['$match' => MongoCompat::convertFilter($matchFilter)],
            ['$limit' => $limit],
        ];

        return $this->aggregate($pipeline);
    }
}
