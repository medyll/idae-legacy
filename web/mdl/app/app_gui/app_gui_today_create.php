<?
include_once($_SERVER['CONF_INC']);
$APP = new App('appscheme');
$arr_tbl = ['client', 'prospect', 'contact', 'tache', 'affaire', 'financement', 'contrat', 'opportunite', 'intervention', 'materiel'];
$RSSCHEME = $APP->get_schemes(['codeAppscheme_base' => 'sitebase_base'], '', 370);
$RSSCHEME = $APP->find()->sort(['nomAppscheme' => 1]); // ['codeAppscheme_base' => 'sitebase_base']

?>
<div class="padding">
    <div class="padding   alignright textgrisfonce"><span class="inline bordert"><i class="fa fa-caret-down textgrisfonce"></i> <?= idioma('Créer un nouvel élément') ?></span></div>
    <div class="flex_h">
        <div class="  flex_v " style="height:100%;width: 100%;">
            <div class="padding  flex_h flex_align_top  ">
                <div class="padding flex_v">
                    <div class="flex_main">

                    </div>
                    <div class="borderr"><?= skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['code' => 'app_menu_create']) ?></div>
                </div>
                <div class=" toggler applink applinkblock flex_h flex_wrap" style="overflow: hidden;">
                    <?
                    foreach ($RSSCHEME as $arr_dist):
                        //
                        $table = $arr_dist['codeAppscheme'];
                        $table_name = $arr_dist['nomAppscheme'];
                        if ($APP->get_settings($_SESSION['idagent'], 'app_menu_create_' . $table) != 'true') continue;
                        if (!droit_table($_SESSION['idagent'], 'C', $table)) continue;
                        $APP_TMP = new App($table);
                        $bgcolor = $APP_TMP->colorAppscheme;
                        $color = color_inverse($bgcolor);
                        ?>
                        <div class=" demi flex_main" style="">
                            <a class="flex_h flex autoToggle" onclick="<?= fonctionsJs::app_create($table, ['vars' => ['idagent' => $_SESSION['idagent']]]); ?>">
                                <span style="width:30px;color:<?= $bgcolor ?>" class="aligncenter"><i class="fa fa-<?= $APP_TMP->iconAppscheme ?>  "></i></span>
                                <span class="flex_main"><div class="ellipsis"><?= ucfirst(idioma($table_name)) ?></div> </span>
                            </a>
                        </div>
                        <?
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>