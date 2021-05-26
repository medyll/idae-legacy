<? 
include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55);
$time 	= time();   
$base 	= skelMongo::connectBase('sitebase_ged')->getGridFs('ged_client'); 
$rs 	= $base->find()->sort(array('metadata.date'=>-1));
?>
<div class="padding blanc">
  <div class="padding blanc inline"> <img src="<?=ICONPATH?>search16.png" class=" " />&nbsp;Recherche&nbsp;
    <input type="text" class="inputLarge borderl" onkeyup="quickFind(this.value,'tbody_doc','tr')" />
  </div>
</div>
<div class="flowDown blanc" style="overflow:auto">
  <table class="sortable tablegrille" width="100%" cellpadding="0" cellspacing="0">
    <thead>
      <tr class="entete">
        <td>Fichier</td>
        <td style="width:80px">Date</td>
      </tr>
    </thead>
    <tbody class="toggler" id="tbody_doc">
<?
while($do=$rs->getNext()){ 
	$arr = $do->file;
?>
      <tr class="autoToggle">
        <td class="applink applinkblock" ondblclick="openDoc('<?=$arr['_id']?>','ged_client','sitebase_ged')"><a >
          <?=$arr['filename']?>
          </a></td>
        <td><?=date_fr($arr['metadata']['date'])?></td>
      </tr>
      <? }?>
    </tbody>
  </table>
</div>
