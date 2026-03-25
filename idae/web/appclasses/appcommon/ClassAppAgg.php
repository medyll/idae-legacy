<?php
declare(strict_types=1);

/**
 * ClassAppAgg.php — Aggregation helper extraction for ClassApp
 *
 * Provides distinct() and groupBy aggregation helpers as static methods
 * so they can be tested and maintained independently of ClassApp.
 *
 * Date: 2026-03-11
 */

namespace AppCommon;

use AppCommon\MongoCompat;

class ClassAppAgg
{
    /**
     * Return distinct values for a field, optionally resolved to full records.
     *
     * Executes a MongoDB distinct() on the current table's collection, then
     * optionally fetches complete records from the groupBy table sorted by
     * $sort_field.
     *
     * @param \App                 $app        App instance (caller)
     * @param string               $groupBy    Table name used to resolve full records
     * @param array<string, mixed> $vars       Optional filter applied to the distinct query
     * @param int                  $limit      Maximum number of records returned (mode=full)
     * @param string               $mode       'full' returns resolved records; any other value returns raw IDs
     * @param string               $field      Override field name (defaults to 'id'.$groupBy)
     * @param array<int|string>    $sort_field Two-element array [field, direction] e.g. ['nom', 1]
     * @return \AppCommon\MongodbCursorWrapper|array<mixed>
     */
    public static function distinct(
        \App $app,
        string $groupBy,
        array $vars = [],
        int $limit = 200,
        string $mode = 'full',
        string $field = '',
        array $sort_field = ['nom', 1]
    ) {
        if (empty($field)) {
            $field = 'id' . $groupBy;
        }

        $dist = $app->plug(
            $app->app_table_one['codeAppscheme_base'],
            $app->app_table_one['codeAppscheme']
        );

        $vars = MongoCompat::convertFilter($vars);

        if (!empty($vars)) {
            $first_arr_dist = $dist->distinct($field, $vars);
        } else {
            $first_arr_dist = $dist->distinct($field);
        }

        if (empty($first_arr_dist)) {
            $first_arr_dist = [];
        }

        $base = $app->get_base_from_table($groupBy);
        if (empty($base)) {
            $base = 'sitebase_base';
        }

        if ($mode === 'full') {
            $sortKey = $sort_field[0] . ucfirst($groupBy);
            $rs = $app->plug($base, $groupBy)->find(
                ['id' . $groupBy => ['$in' => $first_arr_dist]],
                ['sort' => [$sortKey => (int)$sort_field[1]], 'limit' => $limit]
            );

            return $rs;
        }

        return $first_arr_dist;
    }

    /**
     * Group documents by a foreign-key field and return enriched rows with counts.
     *
     * Reads the main collection, groups rows by $vars_dist['groupBy_table'],
     * enriches each group with the resolved FK document, and returns an array
     * sorted either by a named field or by count.
     *
     * @param \App                 $app       App instance (caller)
     * @param array<string, mixed> $vars_dist Configuration array with keys:
     *   - 'groupBy_table' (string) required
     *   - 'vars'          (array)  optional filter
     *   - 'limit'         (int)    optional, default 250
     *   - 'field'         (string) optional field to distinct on
     *   - 'sort_field'    (array)  optional [field, dir], default ['nom', 1]
     * @return array<int, array<string, mixed>>
     */
    public static function distinct_rs(\App $app, array $vars_dist): array
    {
        $groupBy_table = (string)($vars_dist['groupBy_table'] ?? '');
        $vars          = (array)($vars_dist['vars'] ?? []);
        $limit         = (int)($vars_dist['limit'] ?? 250);
        $field         = (string)($vars_dist['field'] ?? '');
        $sort_field    = (array)($vars_dist['sort_field'] ?? ['nom', 1]);

        if (empty($field)) {
            $field = 'id' . $groupBy_table;
        }

        $vars = MongoCompat::convertFilter($vars);

        $idgroupBy_table   = 'id' . $groupBy_table;
        $arr_collect_rs    = [];
        $arr_collect       = [];
        $arr_collect_field = [];

        // Determine sort field on the main table
        if ($app->has_field($sort_field[0] . ucfirst($app->table))) {
            $sort_on = $sort_field[0] . ucfirst($app->table);
        } else {
            $sort_on = (string)$sort_field[0];
        }

        $base_rs  = $app->plug(
            $app->app_table_one['codeAppscheme_base'],
            $app->app_table_one['codeAppscheme']
        );
        $basedist = new \App($groupBy_table);

        $findOpts = array_merge(['_id' => 0], ['limit' => 3000]);
        if ($sort_field[0] !== 'count') {
            $findOpts['sort'] = [$sort_on => (int)$sort_field[1]];
        } else {
            $findOpts['sort'] = ['nom' . ucfirst($app->table) => 1];
        }

        $rs_basedist = new \AppCommon\MongodbCursorWrapper($base_rs->find($vars, $findOpts));

        while ($arr_basedist = $rs_basedist->getNext()) {
            $arr_collect_field[$arr_basedist[$field]][] = $arr_basedist[$app->app_field_name_id];

            if (empty($arr_basedist[$idgroupBy_table])) {
                continue;
            }
            if (array_key_exists($arr_basedist[$field], $arr_collect)) {
                continue;
            }

            $arr_dist = $basedist->findOne(
                [$idgroupBy_table => (int)$arr_basedist[$idgroupBy_table]],
                ['_id' => 0]
            );

            $arr_collect[$arr_basedist[$field]] = $arr_basedist[$field];

            $count_cursor = new \AppCommon\MongodbCursorWrapper(
                $base_rs->find($vars + [$idgroupBy_table => (int)$arr_basedist[$idgroupBy_table]], ['_id' => 0])
            );
            $row_count = $count_cursor->count();

            $arr_collect_rs[$arr_basedist[$field]] = ($arr_dist ?? []) + $arr_basedist + [
                'nomAppscheme'      => $groupBy_table,
                'count'             => $row_count,
                'groupBy'           => $groupBy_table,
                $field              => $arr_basedist[$field],
                $app->app_field_name_id => (int)$arr_basedist[$app->app_field_name_id],
                $sort_field[0]      => $arr_basedist[$sort_field[0] . ucfirst($app->table)] ?? null,
            ];

            if ($sort_field[0] !== 'count' && count($arr_collect_rs) === $limit) {
                break;
            }
        }

        if ($sort_field[0] === 'count') {
            usort($arr_collect_rs, static function (array $a, array $b): int {
                return (int)$b['count'] - (int)$a['count'];
            });
            $arr_collect_rs = array_slice($arr_collect_rs, 0, $limit - 1);
        } else {
            $sort_on_copy = $sort_on;
            usort($arr_collect_rs, static function (array $a, array $b) use ($sort_on_copy): int {
                return (int)($a[$sort_on_copy] ?? 0) <=> (int)($b[$sort_on_copy] ?? 0);
            });
            $arr_collect_rs = array_slice($arr_collect_rs, 0, $limit - 1);
        }

        return array_values($arr_collect_rs);
    }

    /**
     * Return raw distinct values for a field without record resolution.
     *
     * Simpler variant of distinct() that always returns the raw array of
     * distinct field values with no join to related collections.
     *
     * @param \App                 $app     App instance (caller)
     * @param string               $groupBy Field or conceptual grouping name
     * @param array<string, mixed> $vars    Optional filter
     * @param int                  $limit   Unused (reserved for future use)
     * @param string               $mode    Unused (reserved for future use)
     * @return array<mixed>
     */
    public static function distinct_all(
        \App $app,
        string $groupBy,
        array $vars = [],
        int $limit = 200,
        string $mode = 'full'
    ): array {
        $dist = $app->plug(
            $app->app_table_one['codeAppscheme_base'],
            $app->app_table_one['codeAppscheme']
        );

        $vars = MongoCompat::convertFilter($vars);

        if (!empty($vars)) {
            return $dist->distinct($groupBy, $vars) ?? [];
        }

        return $dist->distinct($groupBy) ?? [];
    }
}
