<?php
include_once($_SERVER['CONF_INC']);

ini_set('display_errors',0);
$uniqid = $_POST['uniqid'];
$test = 	skelMongo::connect('email','sitebase_email')->findOne(array('uniqid'=>$uniqid));   
$arrA = 	skelMongo::connect('agent','sitebase_base')->findOne(array('idagent'=>(int)$test['idagent']));  
//
//$mbox=imap_open( "{".SMTPHOST.":143/imap/novalidate-cert}" , $arrA['emailAgent'],$arrA['mailPasswordAgent']);
//$status = imap_setflag_full($mbox,$test['uid'], "\\Seen \\Flagged",ST_UID);
//
//skelMongo::connect('email','sitebase_email')->update(array('uniqid'=>$uniqid),array('$set'=>array('SEEN'=>1)),array('upsert'=>true));
?>
<div style="overflow:hidden;height:100%;" class="blanc flowDown">
  <div class="padding">
    <div class="entete borderb">
      <span class="titre">
      <?=$test['subject']?>
      </span><br />
      <?=$test['from']?>
    </div>
  </div>
  <div class="padding borderb">
  <input type="button" onclick="ajaxMdl('app/app_mail/app_mail_send','Répondre <?=$test['from_name'].' '.$test['from']?>','uniqid=<?=$uniqid?>&email=<?=$test['from']?>&subject=RE : ')" value="Repondre" />
  <input type="button" onclick="ajaxMdl('app/app_mail/app_mail_send','Transférer <?=$test['from_name'].' '.$test['from']?>','uniqid=<?=$uniqid?>&subject=TR : ')" value="Transférer" />
  </div>
  <div>
    <?=skelMdl::cf_module('app/app_mail/app_mail_attach',array('uniqid'=>$uniqid))?>
  </div>
  <div class="flowDown relative" style="overflow:hidden;width:100%;">
    <?=skelMdl::cf_module('app/app_mail/app_mail_preview',array('defer'=>true,'uniqid'=>$uniqid))?>
  </div>
</div>
<script>
reloadModule('app/app_mail/app_mail_liste_tr','<?=$uniqid?>');
$$('[mdl="app/app_mail/app_mail_liste_tr"][value="<?=$uniqid?>"]').invoke( 'removeClassName','bold')
</script>
