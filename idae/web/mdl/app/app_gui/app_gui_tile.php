<?

include_once($_SERVER['CONF_INC']);
$APP = new App();

$arr = $APP->plug('sitebase_base','agent')->findOne(array('idagent' => (int)$_SESSION['idagent']));
$APP = new App();
$ARR_SCH = $APP->get_schemes();

foreach ($ARR_SCH as $key => $arr_sh):
    $arr_sh['codeAppscheme'];

endforeach;
$POST = empty($MDLPOST) ? $_POST : $MDLPOST;
$time = time();

$COL = array();
foreach ($arr['tile'] as $key => $sch):
    $table = $sch['table'];
	$table_value = $sch['table_value'];
	$arr_sch = $APP->get_table_scheme($table);
    $OU[$arr_sch['mainscope_app']][] = $arr['tile'][$key];
endforeach;
foreach ($OU as $key_gr => $arr_gr):
    foreach ($arr_gr as $key => $sch):
        $table = $sch['table'];
        $table_value = $sch['table_value'];
        $arr_sch = $APP->get_table_scheme($table);
	    if(empty($arr_sch['codeAppscheme_base'] ) || empty($arr_sch['codeAppscheme'] ))continue;
        $arrT = $APP->plug($arr_sch['base'], $arr_sch['codeAppscheme'])->findOne(['id' . $table => (int)$table_value]);
	    if(empty($arrT['id' . $table] ))continue;
        ?>
        <div class="left tile" style="margin:0.5em;"
             data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>">
            <div class="aligncenter mastershow" style="vertical-align:top;padding:0.25em;">
                <div class="inline relative padding" style="width: 50px;overflow:hidden;">
                    <i class="fa fa-file-o fa-2x textgris"></i><br>
                    <div class="inline absolute"  style="top:10px">
	                    <i class="fa fa-<?= $arr_sch['icon'] ?> fa-2x"></i>
                    </div>
                </div>
	            <div class="tile_text aligncenter"> <span class="borderb"> <?= $table ?> <?= $table_value ?></span></div>
	            <div class="tile_text aligncenter"> <?= $arrT['nom' . ucfirst($table)] ?></div>
                <div class="absolute slaveshow ededed" style="bottom:0;margin-bottom:-25px;">
                    <?= skelMdl::cf_module('app/app_gui/app_gui_tile_click', array('table' => $table, 'table_value' => $table_value, 'moduleTag' => 'span'), $table_value); ?></div>

            </div>
        </div>
    <?
    endforeach;
?><div class="spacer"></div><?
endforeach;
?>
<div class="spacer"></div>
<style>

</style>