<?php
include_once($_SERVER['CONF_INC']);

ini_set('display_errors',0);
$uniqid =   $_POST['uniqid'];
$test 	= 	skelMongo::connect('email','sitebase_email')->findOne(array('uniqid'=>$uniqid));  
  

$grid = skelMongo::connectBase('sitebase_email')->getGridFs();
$ct = $grid->find(array('metadata.mail_uniqid'=>$uniqid));
if($ct->count()==0 ){}else{
?>

<div class="  padding borderb">
  <table width="100%" class="tabletop">
    <tr>
      <td style="width:110px"><label>Fichier(s) joint(s)</label></td>
      <td class="applink"><? 
		while($arr = $ct->getNext()){   
		?>
        <a class="left margin inline padding blanc ellipsis alignright" style="max-width:30%" onclick="openDoc('<?=$uniqid?>','<?=addslashes($arr->file['filename'])?>')">
        <?=$arr->file['filename']?>
        </a>
        <? } ?>
        <div class="spacer"></div></td>
    </tr>
  </table>
</div>
<? }?>
