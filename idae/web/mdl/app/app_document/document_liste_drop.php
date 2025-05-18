<?
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
    var cooldrop = new myddeAttach($('formdrop<?=$uniqid ?>'),{form:'formdrag<?=$uniqid ?>',autoSubmit:true});
 </script>
 
 
<script>
    if(window.timer_file_watcher) clearInterval(window.timer_file_watcher);
    if($$('#file_watcher<?=$uniqid?>').size()!=0){
	    $('file_watcher<?=$uniqid?>').loadModule('app_document/app_document'_liste_spy','<?=http_build_query($_POST);?>')
    }
    window.timer_file_watcher = setInterval(function(){
        if($$('#file_watcher<?=$uniqid?>').size()!=0){
            $('file_watcher<?=$uniqid?>').loadModule('app_document/app_document'_liste_spy','<?=http_build_query($_POST);?>')
        }else{
            clearInterval(window.timer_file_watcher);
        }
       
    },30000)
 </script>
