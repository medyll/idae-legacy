<?
include_once($_SERVER['CONF_INC']);  
$uniqid 	= uniqid();
$_POST 		= fonctionsProduction::cleanPostMongo($_POST,1);
$tag 		= empty($_POST['tag'])? 'notag' : $_POST['tag'] ;
$vars 		= empty($_POST['vars'])? array() : $_POST['vars'] ;
$baseF 		= empty($_POST['base'])? 'sitebase_ged' : $_POST['base']; 
$collection 	= empty($_POST['collection'])? 'ged_client' : $_POST['collection']; 
$baseFS 	 	= skelMongo::connectBase($baseF); 
$fs 	 	 = $baseFS->getGridFs($collection); 

$vars['metatag']=   array('$in'=>array($tag));
$rs         = $fs->find($vars)->sort(array('uploadDate'=>-1)); 
 
while($file=$rs->getNext()){   
	$arr = $file->file;
	//$dragvars =  'drop[_id]='.$arr['_id'].'&drop[collection]='.$collection.'&drop[base]='.$base;
?> 
<script> 
    if($$('[value=<?=$arr['_id']?>]').size()==0){  
    $$('[t_body_file]').first().socketModule('app_document/app_document'_liste_tr','uid=<?=$arr['_id']?>',{insertion:true});
    }
</script> 
<? }?> 