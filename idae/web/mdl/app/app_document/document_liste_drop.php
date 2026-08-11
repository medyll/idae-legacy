<?php
include_once ($_SERVER['CONF_INC']);
$uniqid = uniqid();

$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
$tag = empty($_POST['tag']) ? 'notag' : $_POST['tag'];
$base = empty($_POST['base']) ? 'sitebase_ged' : $_POST['base'];
$collection = empty($_POST['collection']) ? 'ged_bin' : $_POST['collection'];
?>
<div style="position:relative;" class="applink ededed" id="formdrop<?=$uniqid ?>">

    <div class="table tablemiddle" style="width:100%;">
        <div class="cell" style="width:150px;">
            <a class="cursor inline relative aligncenter borderr" style="overflow:hidden;width:140px"> <li class="fa fa-cloud-upload fa-3x"></li> 
            <?=idioma('Charger un fichier ') ?>
            <input name="file" id="file" class="cursor inline" type="file"  style="opacity:0.5;position:absolute;left:0;top:10px;z-index:0;height:30px;" />
            </a>
        </div>
<div class="cell" id="file_watcher<?=$uniqid?>"> 
</div>
        <div class="cell"><?=$base.' '.$collection.' '.$tag;?></div>
        <div class="cell aligncenter" title="<?=idioma('Glisser déposer ici')?>">
            <li class="fa fa-mail-reply-all fa-3x textgris"></li>
            &nbsp;
        </div>
    </div>

</div>
<form novalidate id="formdrag<?=$uniqid ?>" action="mdl/document/actions.php" onsubmit="ajaxFormValidation(this);return false"  >
    <input type="hidden" name="F_action" value="addDoc" />
    <input type="hidden" name="tag" value="<?=$tag ?>" />
    <input type="hidden" name="base" value="<?=$base ?>" />
    <input type="hidden" name="collection" value="<?=$collection ?>" /> 
</form>

<script>
    var cooldrop = new myddeAttach(document.getElementById('formdrop<?=$uniqid ?>'),{form:'formdrag<?=$uniqid ?>',autoSubmit:true});
 </script>
 
 
<script>
    /*
     * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
     * ($, $$, .size) to native DOM.
     *
     * Both loadModule calls read
     *     loadModule('app_document/app_document'_liste_spy', ...)
     * which is not valid JavaScript: a string literal immediately followed by
     * an identifier. This whole <script> block therefore failed to parse, so
     * neither the initial refresh nor the 30s poll below has ever run. The
     * intended module path is plainly 'app_document/app_document_liste_spy';
     * the misplaced quote is closed. Predates the idae-be migration.
     */
    if(window.timer_file_watcher) clearInterval(window.timer_file_watcher);
    if(document.getElementById('file_watcher<?=$uniqid?>')){
	    document.getElementById('file_watcher<?=$uniqid?>').loadModule('app_document/app_document_liste_spy','<?=http_build_query($_POST);?>')
    }
    window.timer_file_watcher = setInterval(function(){
        if(document.getElementById('file_watcher<?=$uniqid?>')){
            document.getElementById('file_watcher<?=$uniqid?>').loadModule('app_document/app_document_liste_spy','<?=http_build_query($_POST);?>')
        }else{
            clearInterval(window.timer_file_watcher);
        }
       
    },30000)
 </script>
