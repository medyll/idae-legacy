<?php
include_once($_SERVER['CONF_INC']);

$uniqid = $_POST['uniqid'];
$msg = 	skelMongo::connect('email','sitebase_email')->findOne(array('uniqid'=>$uniqid));  
$time = time(); 
 
$arrClient = skelMongo::connect('client','sitebase_devis')->findOne(array('emailClient'=>trim($msg['from'])));
?>

<td class="aligncenter"><? if (!empty($msg['attachement'])){ ?>
    <a onclick="ajaxMdl('app/app_mail/app_mail_attach','<?=idioma('Piece jointe')?>','uniqid=<?=$uniqid?>');return false;"><img src="<?=ICONPATH?>task16.png" /></a>
  <? }?></td>
<td class="" ><?=$msg['subject']?></td>
<td class="aligncenter"><? if(!empty($arrClient['nomClient'])){ ?>
  <a onclick="<?=fonctionsJs::client_fiche($arrClient['idclient']);?>;return false;"><img src="<?=ICONPATH?>vcard16.png" /></a>
  <? } ?></td>
<td class="ellipsis"><span class="" title="<?=$arrClient['emailClient']?>">
  <?=$msg['from']?>
  </span></td>
<td class="aligncenter"><?=($msg['date']==date('Y-m-d'))? ' ' : $msg['heure'] ?></td>
<td class="alignright"><?=date_fr($msg['date'])?></td>

