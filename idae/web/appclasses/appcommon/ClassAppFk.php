<?php
/**
 * ClassAppFk.php — FK helper extraction for ClassApp
 *
 * Provides a centralized location for FK (grilleFK) helper logic so it can be
 * tested and maintained separately from ClassApp.
 *
 * Modified: 2026-03-07
 */

namespace AppCommon;

use AppCommon\MongoCompat;

class ClassAppFk {

    /**
     * Get reverse FK grid (apps that reference $table)
     *
     * @param \App $app App instance (caller)
     * @param string|null $table Table name
     * @param int|string $table_value Optional table value
     * @param array $add Optional additional filter
     * @return array
     */
    public static function get_grille_rfk($app, $table = null, $table_value = '', $add = []) {
        if (empty($table) || empty($app->table)) return [];
        $table = empty($table) ? $app->table : $table;
        $id    = 'id' . $table;
        $vars  = $out = [];
        if (!empty($table_value)) {
            $vars[$id] = (int)$table_value;
        }

        // Convert legacy filters to modern driver types
        $vars = MongoCompat::convertFilter($vars);

        if (empty($add)) {
            $rs = $app->app_conn->find(['grilleFK.table' => $table]);
        } else {
            $rs = $app->app_conn->find($add + ['grilleFK.table' => $table]);
        }

        $arr_ty = $app->appscheme->distinct('idappscheme_type', ['grilleFK.table' => $table]);
        $rs_ty  = $app->appscheme_type->find(['idappscheme_type' => ['$in' => $arr_ty]])->sort(['nomAppscheme_type' => 1]);

        $arr_final = [];
        while ($arr_ty_item = $rs_ty->getNext()) {
            $arr_tmp = $arr_out = [];
            $vars_type = ['idappscheme_type' => (int)$arr_ty_item['idappscheme_type']];

            if (empty($add)) {
                $rs_det = $app->appscheme->find($vars_type + ['grilleFK.table' => $table]);
            } else {
                $rs_det = $app->appscheme->find($vars_type + $add + ['grilleFK.table' => $table]);
            }

            while ($arr_det = $rs_det->getNext()) {
                if (empty($table_value)) {
                    if (str_find('_ligne', $arr_det['codeAppscheme_base'])) continue;
                    $rs_fk = $app->plug($arr_det['codeAppscheme_base'], $arr_det['codeAppscheme'])->find();
                } else {
                    $rs_fk = $app->plug($arr_det['codeAppscheme_base'], $arr_det['codeAppscheme'])->find([$id => (int)$table_value]);
                }

                if ($rs_fk->count() == 0) continue;
                $arr_det['count']                                = $rs_fk->count();
                $arr_det['table']                                = $arr_det['codeAppscheme'];
                $arr_tmp['appscheme'][$arr_det['codeAppscheme']] = $arr_det;
            }

            if (!empty($arr_tmp['appscheme'])) {
                $arr_out     = array_merge($arr_ty_item, $arr_tmp);
                $arr_final[] = $arr_out;
            }
        }

        return $arr_final;
    }
}
