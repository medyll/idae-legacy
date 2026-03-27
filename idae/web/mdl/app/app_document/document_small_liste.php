<?php
include_once($_SERVER['CONF_INC']);   
$uniqid 	= uniqid();
$_POST['vars'] = empty($_POST['vars'])? array() : $_POST['vars'];
$_POST 		= fonctionsProduction::cleanPostMongo($_POST,1);
$base 		= $_POST['base']; 
$collection = $_POST['collection']; 
$baseM 	 	= skelMongo::connectBase($base); 
$fs 	 	= $baseM->getGridFs($collection);
$rs 	 	= $fs->find($_POST['vars'])->sort(array('uploadDate'=>-1)); 
$typeListe  = empty($_POST['typeListe'])? 'small' : $_POST['typeListe'];
?>

<table id="drag<?=$uniqid?>" class="tableverticale" width="100%" cellpadding="0" cellspacing="0">
  <?php if($typeListe=='big'): ?>
  <thead>
    <tr class="entete">
      <td style="width:40px"></td>
      <td>Fichier</td>
      <td style="width:120px">Date</td>
      <td style="width:40px"></td>
    </tr>
  </thead>
  <?php endif; ?>
  <tbody class="toggler" id="tfile<?=$uniqid?>">
    <?php
while($file=$rs->getNext()){   
	$arr = $file->file;
?>
    <tr class="autoToggle applink" mdl='trfilename' value="<?=$arr['_id']?>">
      <?php if($typeListe=='big'): ?>
      <td class="aligncenter"><input type="checkbox" value="<?=$arr['_id']?>" name="_id[]" /></td>
      <?php endif; ?>
      <td class="" title="<?=$arr['filename']?>"ondblclick="openDoc('<?=$arr['_id']?>','ged_client','sitebase_ged')"><a >
        <?=$arr['filename']?>
        </a></td>
      <td ><a >
        <?=strtolower(implode(' ',$arr['metatag']));?>
        </a></td>
      <td class="textgris" style="width:100px"><?=date_fr($arr['metadata']['date']).' '.maskHeure($arr['metadata']['heure'])?></td>
      <?php if($typeListe=='big'): ?>
      <td class="aligncenter"><a deleteFile="<?=$arr['_id']?>">
        <li class="fa fa-times"></li>
        </a></td>
      <?php endif; ?>
    </tr>
    <?php }?>
  </tbody>
</table>
<script>
$('tfile<?=$uniqid?>').on('click','a[deleteFile]',function(event,node){
	filename = $(node).readAttribute('deleteFile');
	ajaxValidation('deleteDoc','mdl/document/','deleteModule[trfilename]='+filename+'&base=<?=$base?>&collection=<?=$collection?>&_id='+filename);
	});
</script>