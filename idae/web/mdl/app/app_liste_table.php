<?
include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

if (empty($_POST['table']))
    return;
$uniqid = uniqid();
ini_set('display_errors', 55);
//
$table = $_POST['table'];
//
$APP = new App($table);
//
$settings_groupBy = $APP->get_settings($_SESSION['idagent'], 'groupBy', $table);
$settings_sortBy = $APP->get_settings($_SESSION['idagent'], 'sortBy', $table);
$settings_sortDir = $APP->get_settings($_SESSION['idagent'], 'sortDir', $table);
$settings_nbRows = $APP->get_settings($_SESSION['idagent'], 'nbRows', $table);

$id = 'id' . $table;
$nom = 'nom' . ucfirst($table);
$id_type = 'id' . $table . '_type';
$nom_type = 'nom' . ucfirst($table) . '_type';
$top = 'estTop' . ucfirst($table);
$actif = 'estActif' . ucfirst($table);
$visible = 'estVisible' . ucfirst($table);
//
$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
$groupBy = !isset($_POST['groupBy']) ? !isset($settings_groupBy) ? '' : $settings_groupBy : $_POST['groupBy'];
$sortBy = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
$page = (empty($_POST['page'])) ? 0 : $_POST['page'] - 1;
$nbRows = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
//  vars_date
if (!empty($_POST['vars_date'])):
    $key_date = $_POST['vars_date'] ['name_key'];
    $vars[$key_date] = $_POST['vars_date'][$key_date];
endif;
// SETTINGS       ne pas faire ici mais par javascript ....
$APP->set_settings($_SESSION['idagent'], ['sortBy_' . $table => $sortBy, 'sortDir_' . $table => $sortDir, 'groupBy_' . $table => $groupBy, 'nbRows_' . $table => $nbRows]);
//
$APP_TABLE = $APP->app_table_one;
$GRILLE_FK = $APP->get_grille_fk();
$APP_DATE_FIELDS = $APP->get_date_fields($table);
$arrFieldsBool = $APP->get_array_field_bool();
$APP_FIELD_BOOL = $APP->get_bool_fields($table);
//
$FIELDS = array_merge(array_keys($APP_DATE_FIELDS), array_values($APP_FIELD_BOOL));
//
$where = array();
if (!empty($_POST['search'])) {
    $regexp = MongoCompat::toRegex(".*" . MongoCompat::escapeRegex($_POST['search']) . "*.", 'i');
    $where['$or'][] = array($nom => $regexp);
    $where['$or'][] = array($id => (int)$_POST['search']);
    // tourne ds fk
    if (sizeof($GRILLE_FK) != 0) {
        foreach ($GRILLE_FK as $field):
            $nom_fk = 'nom' . ucfirst($field['table_fk']);
            $regexp = MongoCompat::toRegex("." . MongoCompat::escapeRegex($nom_fk) . "*.", 'i');
            $where['$or'][] = array($nom_fk => $regexp);
        endforeach;
    }
}
//
$rs = $APP->query($vars + $where)->sort(array($sortBy => $sortDir));
//
if (!empty($groupBy)):
    $rs_dist = $APP->distinct($groupBy, $vars + $where);
    $dist_count = $rs_dist->count();
    // echo "=>".(ceil($nbRows/$dist_count));
    $nbRows = (ceil($nbRows / $dist_count));
    $rs_dist = $rs_dist; // pagination handled upstream; keep cursor as-is

endif;
//
$rs = $APP->query($vars + $where, (int)$page, (int)$nbRows);
//
?>
<table
    class="explorer act_sort"
    style="max-width: 100%;table-layout: fixed;"
    cellspacing="0">
    <thead>
    <tr>
        <td class="avoid aligncenter"><input type="checkbox" onclick="doClickto(this);"></td>
        <td class="alignright">id</td>
        <? foreach ($arrFieldsBool as $bool => $arr_ico): ?>
            <td class="aligncenter" style="width:40px">
                <li class="fa fa-<?= $arr_ico[0] ?>"></li>
            </td>
        <? endforeach; ?>
        <td class="aligncenter">
            <li class="fa fa-cubes"></li>
        </td>
        <? if (!empty($key_date)): ?>
            <td><?= $key_date ?></td>
        <? endif; ?>
        <td><?= $table ?></td>
        <? foreach ($GRILLE_FK as $fk):
            ?>
            <td>
                <?= $fk['table_fk'] ?>
            </td>
        <? endforeach; ?>
        <? if (!empty($APP_TABLE['hasPrixScheme'])): ?>
            <td class="alignright"><?= idioma('Prix') ?></td>
        <? endif; ?>

    </tr>
    </thead>
    <?
    if (!empty($groupBy)):
        ?>
        <? foreach ($rs_dist as $arr_dist):
        $vars['id' . $groupBy] = (int)$arr_dist['id' . $groupBy];
        $rs = $APP->query($vars + $where)->sort(array($sortBy => $sortDir));
        //
        $vars_rfk['vars'] = ['id' . $table => $table_value];
        $vars_rfk['table'] = $arr_fk['table'];
        $vars_rfk['table_value'] = $arr_fk['table_value'];

        $vars_rfk['vars'] = ['id' . $groupBy => $arr_dist['id' . $groupBy]];

        $vars_rfk['table'] = $table;
        $vars_rfk['table_value'] = $arr_dist['id' . $groupBy];
        $vars_rfk['groupBy'] = '';

        ?>
        <tbody class="toggler">
        <tr>
            <td style="height:40px;"
                class="textvert  uppercase"
                colspan="<?= sizeof($GRILLE_FK) + 7 + (empty($APP_TABLE['hasPrixScheme']) ? 0 : 1) ?>">
                <a act_chrome_gui="app/app_liste/app_liste_gui" vars="<?= http_build_query($vars_rfk); ?>">
                    <li class="fa fa-sign-in"></li> <?= $arr_dist['nom' . ucfirst($groupBy)] ?>
                    (<?= $rs->count() ?>)
                </a>

            </td>
        </tr>
        <?
        while ($arr = $rs->getNext()) {
            // variables pour le mdl_tr
            $trvars['id' . $table] = $arr[$id];
            $trvars['table'] = $table;
            $trvars['table_value'] = $arr[$id];
            $trvars['sortBy'] = $sortBy;
            $trvars['key_date'] = $key_date;
            echo skelMdl::cf_module('app/app_liste_tr', array('className' => 'autoToggle', 'scope' => 'id' . $table, 'moduleTag' => 'tr') + $trvars, $arr[$id],
                'data-contextual="table=' . $table . '&table_value=' . $arr[$id] . '" act_preview_mdl = "app/app_liste/app_liste_preview" draggable="draggable"');
            ?>

        <? } ?>

        </tbody>
    <? endforeach; ?>
    <? else: ?>
        <tbody class="toggler" id="tb_<?=$uniqid?>">
        <?
        while ($arr = $rs->getNext()) {
            // variables pour le mdl_tr
            $trvars['id' . $table] = $arr[$id];
            $trvars['table'] = $table;
            $trvars['table_value'] = $arr[$id];
            $trvars['sortBy'] = $sortBy;
            $trvars['key_date'] = $key_date;
            echo skelMdl::cf_module('app/app_liste_tr', array('className' => 'autoToggle', 'scope' => 'id' . $table, 'moduleTag' => 'tr') + $trvars, $arr[$id],
                'data-contextual="table=' . $table . '&table_value=' . $arr[$id] . '" act_preview_mdl = "app/app_liste/app_liste_preview" draggable="draggable" ');
        }
            ?>
        </tbody>
    <? endif; ?>

</table>