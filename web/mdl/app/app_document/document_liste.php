<?
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
        <div class="barre_entete applink disinput" > <a onClick="ajaxMdl('app_document/app_document_update_multi','<?=idioma('Supprimer')?>',Form.serialize($('tfile<?=$uniqid?>'))+'&F_action=suppr');">
          <i class="fa fa-times"></i>
          &nbsp;
          <?=idioma('supprimer')?>
          </a> <a onClick="ajaxMdl('app_document/app_document_update_multi','<?=idioma('Rapprocher')?>',Form.serialize($('tfile<?=$uniqid?>'))+'&F_action=setmetadata');"> &nbsp;
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
multiDoc =function(event,node){
    filename    = $(node).readAttribute('deleteFile'); 
    base        = $(node).readAttribute('base'); 
    collection  = $(node).readAttribute('collection'); 
    ajaxMdl('app_document/app_document_delete','','base='+base+'&collection='+collection+'&_id='+filename)
    } ;
$('tfile<?=$uniqid?>').on('click','a[deleteFile]',function(event,node){
    filename    = $(node).readAttribute('deleteFile'); 
    base        = $(node).readAttribute('base'); 
    collection  = $(node).readAttribute('collection'); 
    ajaxMdl('app_document/app_document_delete','','base='+base+'&collection='+collection+'&_id='+filename)
    });  
$('tfile<?=$uniqid?>').on('click','[mdl=trfilename]',function(event,node){  
   
    uid    = $(node).readAttribute('value'); 
    base        = $(node).readAttribute('base'); 
    collection  = $(node).readAttribute('collection');   
    //   $('act_file_viewer').loadModule('app_document/app_document'_detail','base='+base+'&collection='+collection+'&uid='+uid);
   
}.bind(this))
</script> 
<script> 

pleaseTag=function(tag){
    vars = Form.serialize($('tfile<?=$uniqid?>'));
    // ajaxValidation('tagDocument','mdl/document/','<?=http_build_query($_POST)?>&'+vars+'&tag='+tag);
    }
inverseTag=function(){
    unch    =   $('tfile<?=$uniqid?>').select('[type=checkbox]:not([bugchk])');
    ch  =   $('tfile<?=$uniqid?>').select('[type=checkbox][bugchk]');
    unch.invoke('fire','dom:click');
    ch.invoke('fire','dom:click');
    }
</script> 
