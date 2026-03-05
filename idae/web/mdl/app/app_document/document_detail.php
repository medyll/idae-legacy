<?
include_once($_SERVER['CONF_INC']);  
ini_set('display_errors',55);

echo $_id        =   $_POST['uid'];
$collection =   $_POST['collection'];
$base       =   $_POST['base'];
$grid       = skelMongo::connectBase($base)->getGridFs($collection);
$ct = $grid->findOne(array('_id'=>MongoCompat::toObjectId($_id)));
$file_extension = strtolower(substr(strrchr($ct->file['filename'],'.'),1)); 
?>

<div class="flowDown"> 
    <div class="titre_entete fond_noir color_fond_noir relative"> 
      <?=$ct->file['filename']?> 
    </div>  
    <div class="titre_entete fond_bleu color_fond_bleu relative"> 
     <?=implode('  ',$ct->file['metadata'])?>
    </div>
	<div>  <a
		onclick = "openDoc('<?= $arr['_id'] ?>','<?= $collection ?>','<?= $base ?>')" >Ouvrir </a></div>
  <div class="flowDown fond_bleu color_fond_bleu" style="width:100%;overflow:auto;">
  <?=skelMdl::cf_module('app_document/app_document_preview',$_POST);?>
  </div>
</div>
