<?php
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
    /*
     * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility
     * shims ($$, .size, .first) to native DOM, and fixed a syntax error that
     * made this block unparseable: the module path below read
     * 'app_document/app_document'_liste_tr — a string literal immediately
     * followed by an identifier. Nothing in this block has ever run.
     *
     * This is the module document_liste_drop.php polls, and that file carried
     * the identical broken quote. Both ends of the document-list live refresh
     * were dead, for the same reason. Predates the idae-be migration.
     *
     * The attribute value is quoted now: an _id starting with a digit makes
     * [value=...] invalid CSS, and native querySelectorAll throws where the
     * shim's $$ retried with quotes added.
     */
    if(document.querySelectorAll('[value="<?=$arr['_id']?>"]').length==0){  
    var spy_tbody = document.querySelector('[t_body_file]');
    if(spy_tbody) spy_tbody.socketModule('app_document/app_document_liste_tr','uid=<?=$arr['_id']?>',{insertion:true});
    }
</script> 
<?php }?> 