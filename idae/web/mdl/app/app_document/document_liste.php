<?php
include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55);
    $APP = new App();
$uniqid     = uniqid();
$_POST      = fonctionsProduction::cleanPostMongo($_POST,1);
$vars       = empty($_POST['vars'])? array() : $_POST['vars'] ;
$base       = empty($_POST['base'])? 'sitebase_ged' : $_POST['base']; 
$collection     = empty($_POST['collection'])? 'ged_client' : $_POST['collection'];

$baseF       = $APP->plug_base($base);// skelMongo::connectBase($base);
$fs         = $baseF->getGridFs($collection);
$rs         = $fs->find($vars)->sort(array('uploadDate'=>-1));  
?>

<div   class="relative">
  <div class="barre_entete relative borderb">
    <div class="table">
      <div class="cell">
        <div class="barre_entete applink" > <a>Tout sélectionner</a> <a expl_view_button="expl_view_button" >
          <i class="fa fa-eye"></i>
          &nbsp;
          <?=idioma('Visualiser')?>
          </a> </div>
      </div>
      <div class="cell">
        <div class="barre_entete applink disinput" > <a onClick="ajaxMdl('app_document/app_document_update_multi','<?=idioma('Supprimer')?>',serializeFields(dl_el('tfile<?=$uniqid?>'))+'&F_action=suppr');">
          <i class="fa fa-times"></i>
          &nbsp;
          <?=idioma('supprimer')?>
          </a> <a onClick="ajaxMdl('app_document/app_document_update_multi','<?=idioma('Rapprocher')?>',serializeFields(dl_el('tfile<?=$uniqid?>'))+'&F_action=setmetadata');"> &nbsp;
          <?=idioma('rapprocher')?>
          </a> <a>
          <i class="fa fa-exchange"></i>
          &nbsp;
          <?=idioma('Renommer')?>
          </a> <a onClick="inverseTag();">
          <i class="fa fa-random"></i>
          &nbsp;
          <?=idioma('Inverser')?>
          </a> <a onClick="inverseTag();">
          <i class="fa fa-random"></i>
          &nbsp;
          <?=idioma('Inverser')?>
          </a> </div>
      </div>
    </div>
  </div>
  <div act_drag_selection_zone id="select<?=$uniqid?>" style="overflow:hidden;">
    <div class="flowDown" style="overflow:hidden;">
      <div class="table" style="width:100%;">
        <div class="cell" style="width:60%;">
          <div  style="height:100%;overflow:auto;" class="relative" id="tfile<?=$uniqid?>" expl_drag_selection_zone="expl_drag_selection_zone">
            <table class="act_sort explorer" width="100%" cellpadding="0" cellspacing="0">
              <thead>
                <tr >
                  <td style="width:40px"></td>
                  <td>Nom</td>
                  <td style="width:120px;">client</td>
                  <td style="width:80px;">devis</td>
                  <td style="width:120px;">prestataire</td>
                  <td style="width:120px">Date</td>
                  <td style="width:50px">Type</td>
                  <td style="width:60px">Taille</td>
                  <td style="width:40px" class="avoid">&nbsp;</td>
                </tr>
              </thead> 
              <?=skelMdl::cf_module('app_document/app_document_liste_tbody',array('emptyModule'=>true,'className'=>'toggler','moduleTag'=>'tbody','scope'=>'document','document'=>$_SESSION['idagent']),$_SESSION['idagent'],'t_body_file="t_body_file"')?>
            </table>
          </div>
        </div>
        <div class="cell fond_noir" expl_preview_zone="expl_preview_zone" style="display:none;">
          <div id="act_file_viewer" style="height:100%;position:relative;"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="stayDown relative blanc ededed bordert" >
    <?=skelMdl::cf_module('app_document/app_document_liste_drop',array('moduleTag'=>'div','scope'=>'document','document'=>$_SESSION['idagent']),$_SESSION['idagent'])?>
  </div>
</div>


<script>
/*
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * ($, .on, .select, .readAttribute, .invoke, .fire) to native DOM, with
 * file-local `dl_` helpers and the native serializeFields helper.
 */
function dl_el(ref) {
    return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

/** Prototype's Element#select, as a real Array. */
function dl_select(ref, selector) {
    var node = dl_el(ref);
    if (!node) return [];
    return Array.prototype.slice.call(node.querySelectorAll(selector));
}

/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
function dl_fire(node, eventName, memo) {
    if (!node) return null;
    var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
    event.memo = memo || {};
    node.dispatchEvent(event);
    return event;
}

/**
 * Prototype's Element#on. With a selector it delegates, calling the handler
 * as (event, matchedElement); without one it is a plain listener.
 */
function dl_on(root, eventName, selectorOrHandler, maybeHandler) {
    if (!root) return;
    if (maybeHandler === undefined) {
        root.addEventListener(eventName, selectorOrHandler);
        return;
    }
    var selector = selectorOrHandler, handler = maybeHandler;
    root.addEventListener(eventName, function (event) {
        var target = event.target;
        while (target && target !== root) {
            if (target.nodeType === 1 && target.matches(selector)) {
                return handler(event, target);
            }
            target = target.parentNode;
        }
    }, false);
}

multiDoc =function(event,node){
    filename    = node.getAttribute('deleteFile');
    base        = node.getAttribute('base');
    collection  = node.getAttribute('collection');
    ajaxMdl('app_document/app_document_delete','','base='+base+'&collection='+collection+'&_id='+filename)
    } ;
dl_on(dl_el('tfile<?=$uniqid?>'),'click','a[deleteFile]',function(event,node){
    filename    = node.getAttribute('deleteFile');
    base        = node.getAttribute('base');
    collection  = node.getAttribute('collection');
    ajaxMdl('app_document/app_document_delete','','base='+base+'&collection='+collection+'&_id='+filename)
    });
dl_on(dl_el('tfile<?=$uniqid?>'),'click','[mdl=trfilename]',function(event,node){

    uid    = node.getAttribute('value');
    base        = node.getAttribute('base');
    collection  = node.getAttribute('collection');
    //   dl_el('act_file_viewer').loadModule('app_document/app_document'_detail','base='+base+'&collection='+collection+'&uid='+uid);

})
</script>
<script>

pleaseTag=function(tag){
    vars = serializeFields(dl_el('tfile<?=$uniqid?>'));
    // ajaxValidation('tagDocument','mdl/document/','<?=http_build_query($_POST)?>&'+vars+'&tag='+tag);
    }
inverseTag=function(){
    unch    =   dl_select('tfile<?=$uniqid?>', '[type=checkbox]:not([bugchk])');
    ch  =   dl_select('tfile<?=$uniqid?>', '[type=checkbox][bugchk]');
    unch.forEach(function (node) { dl_fire(node, 'dom:click'); });
    ch.forEach(function (node) { dl_fire(node, 'dom:click'); });
    }
</script> 
