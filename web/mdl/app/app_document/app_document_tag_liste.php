<?    
include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55);
    $APP = new App();

$uniqid		= uniqid(); 
$_POST		= fonctionsProduction::cleanPostMongo($_POST);
$vars 		= empty($_POST['vars'])? array() : $_POST['vars'] ;
$base 		= empty($_POST['base'])? 'sitebase_ged' : $_POST['base']; 
$collection = empty($_POST['collection'])? 'ged_client' : $_POST['collection']; 
$idagent	= (int)$_SESSION['idagent'];  
$arrGed['Bin'] 	= array('titre'=>'Collecte','base'=>'sitebase_ged','collection'=>'ged_bin');
$arrGed['Cli'] 	= array('titre'=>'Client','base'=>'sitebase_ged','collection'=>'ged_client');
$arrGed['Soc'] 	= array('titre'=>'Destinationsreve','base'=>'sitebase_ged','collection'=>'ged_societe');
$arrGed['User']	= array('titre'=>'Utilisateur','base'=>'sitebase_ged','collection'=>'ged_user');
$arrGed['Img']  = array('titre'=>'Image','base'=>'sitebase_image','collection'=>'source');

$arrTagBin		= array('GEDMAIL'=>'Par mail','DIV'=>'Divers');
$arrTagCli		= array('FACTFOURN'=>'facture fournisseur','FACTPREST'=>'facture prestataire','CNICLI'=>'piece client','SIGN'=>'devis signés', 'CONFIRMF'=>'confirm fournisseur','DOC'=>'document');
$arrTagSoc		= array('FACTPREST'=>'facture prestataire','CONTRF'=>'Contrats fournisseurs','MODS'=>'Modeles');
$arrTagUser		= array('DOC'=>'documents','CNIUSER'=>'pièce');
$arrTagImg      = array('GENE'=>'Général','Istock'=>'Istock');
?>
<div class="padding applink applinkblock toggler" id="loader<?=$uniqid?>">13
<?
$rs 	 	= $APP->plug('sitebase_ged','ged_tag')->find()->sort(array('titre'=>1)); // skelMongo::connect('ged_tag','sitebase_ged')->find()->sort(array('titre'=>1));
while($arr=$rs->getNext()){
   $collection 	=	$arr['collection'];
   $base	= 	$arr['base'];
   $trashTarget =  	'target[tag]=TRASH&target[collection]='.$collection.'&target[base]='.$base; 
   $trashNotag  =  	'target[tag]=notag&target[collection]='.$collection.'&target[base]='.$base;
    // skelMongo::connect('ged_tag','sitebase_ged')->update(array('ged'=>$KEY),array('$set'=>$TYPE + array('grilleTag'=>$$namevar)),array('upsert'=>true));
   ?>
   <div class="autoNext active"><?=$arr['titre']?></div>
   <div class="autoBlock" >
      <a dropzone="move" tag="notag" base="<?=$base?>" collection="<?=$collection?>" class="autoToggle" target="<?=$trashNotag?>">
      <i class="fa fa-tag"></i>
      &nbsp;
      Sans tag
      <span count class="bold"> <?=$total?></span>
      </a>
   <?
   foreach($arr['grilleTag'] as $TAG=>$value_text): 
      $ct	= 	skelMongo::connectBase($base)->getGridFs($collection)->find(array('metatag'=>array('$in'=>array($TAG))))->count();
      $target 	=  	'target[tag]='.$TAG.'&target[collection]='.$collection.'&target[base]='.$base; 
      $total=($ct==0)? '' : $ct ;
      ?>
      <a dropzone="move" tag="<?=$TAG?>" base="<?=$base?>" collection="<?=$collection?>" class="autoToggle" target="<?=$target?>">
      <i class="fa fa-tag"></i>
      &nbsp;
      <?=$value_text?>
      <span count class="bold"> <?=$total?></span>
      </a>
      <?
       
   endforeach;
   ?>
   <a  dropzone="move" tag="TRASH" base="<?=$base?>" collection="<?=$collection?>" class="autoToggle" target="<?=$trashTarget?>">
      <i class="fa fa-trash-o"></i>
      corbeille<span count></span></a>  
   </div>
   <?
}
?>
<?
$arrGed = array();
foreach($arrGed as $KEY=>$TYPE): 
$namevar = 'arrTag'.$KEY;
$trashTarget =  'target[tag]=TRASH&target[collection]='.$TYPE['collection'].'&target[base]='.$TYPE['base']; 
$trashNotag  =  'target[tag]=notag&target[collection]='.$TYPE['collection'].'&target[base]='.$TYPE['base'];
skelMongo::connect('ged_tag','sitebase_ged')->update(array('ged'=>$KEY),array('$set'=>$TYPE + array('grilleTag'=>$$namevar)),array('upsert'=>true));
?> 
  <div class="autoNext active"><?=$TYPE['titre']?></div>
  <div class="autoBlock" >
      <a dropzone="move" tag="notag" base="<?=$TYPE['base']?>" collection="<?=$TYPE['collection']?>" class="autoToggle" target="<?=$trashNotag?>">
      <i class="fa fa-star-o"></i>
      Sans tags<span count></span></a>  
    <? foreach($$namevar as $key=>$tag):
      $target =  'target[tag]='.$key.'&target[collection]='.$TYPE['collection'].'&target[base]='.$TYPE['base']; 
    ?>
    <a dropzone="move" tag="<?=$key?>" base="<?=$TYPE['base']?>" collection="<?=$TYPE['collection']?>" class="autoToggle" target="<?=$target?>">
    <i class="fa fa-tag"></i>
    &nbsp;
    <?=$tag?>
    <span count class="bold"></span>
    </a>
    <? endforeach; ?>
     <a  dropzone="move" tag="TRASH" base="<?=$TYPE['base']?>" collection="<?=$TYPE['collection']?>" class="autoToggle" target="<?=$trashTarget?>">
      <i class="fa fa-trash-o"></i>
      corbeille<span count></span></a>  
  </div> 
<? endforeach; ?>
</div>
<style>
[draggable=true], [dropzone]{
    transition:
        box-shadow linear 0.2s,
        background-color linear 0.2s;
}
</style>
<script>
//
$('loader<?=$uniqid?>').on('click','a[tag]',function(event,node){
	tag 		= $(node).readAttribute('tag');
	base 		= $(node).readAttribute('base');
	collection 	= $(node).readAttribute('collection');
	reloadScope('document','<?=$idagent?>','tag='+tag+'&base='+base+'&collection='+collection); 
	});
</script> 