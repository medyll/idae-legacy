<?
include_once($_SERVER['CONF_INC']);  
$uniqid = uniqid();
$idclient = (int)$_POST['idclient']; 
$base 	= skelMongo::connectBase('sitebase_ged'); 
$fs 	= $base->getGridFs('ged_client');
$rs 	= $fs->find(array('metadata.idclient'=>$idclient))->sort(array('uploadDate'=>-1)); 
//  
?>

<div class="padding blanc relative alignright">
  <li class="fa fa-search"></li>
  &nbsp;
  <input type="text" class="borderl" onkeyup="quickFind(this.value,'tclient_doc','tr')" />
  &nbsp; </div>
<div class="flowDown"  style="width:100%;overflow:auto;position:relative;" id="drag<?=$uniqid?>">
  <table class="sortable tableexcell" width="100%" cellpadding="0" cellspacing="0">
    <thead>
      <tr class="entete">
        <td>Fichier</td>
        <td style="width:120px">Date</td>
        <td style="width:80px"></td>
      </tr>
    </thead>
    <tbody class="toggler" id="tclient_doc">
      <?
while($file=$rs->getNext()){   
	$arr = $file->file;
?>
      <tr class="autoToggle applink">
        <td title="<?=$arr['filename']?>" class="" ondblclick="openDoc('<?=$arr['_id']?>','ged_client','sitebase_ged')"><a >
          <?=$arr['filename']?>
          </a></td>
        <td><?=date_fr($arr['metadata']['date']).' '.maskHeure($arr['metadata']['heure'])?></td>
        <td class="aligncenter"><a deleteFile="<?=$arr['filename']?>">
          <li class="fa fa-times"></li>
          </a></td>
      </tr>
      <? }?>
    </tbody>
  </table>
</div>
