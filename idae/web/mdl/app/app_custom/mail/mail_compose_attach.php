<?
include_once($_SERVER['CONF_INC']);
	$APP = new App();
$uniqid = uniqid();
$ARRPOST = fonctionsProduction::cleanPostMongo($_POST,1);
// vardump($ARRPOST);
$mail_tmp = $ARRPOST['mail_tmp'];
	$COL_TMP = $APP->plug('sitebase_email','email_compose_tmp');
$arr = $COL_TMP->findOne(array('mail_tmp'=>$mail_tmp));
if(empty($arr['mail_tmp'])){
	$crud 	= $COL_TMP->update(array('mail_tmp'=>$mail_tmp),array('$set'=>array('timestamp'=>time(),'idagent'=>(int)$_SESSION['idagent'])),array('upsert'=>true));
	//
	$arr 	= $COL_TMP->findOne(array('mail_tmp'=>$mail_tmp));
}
if(!empty($ARRPOST['md5Brochure'])){ 
	$testB = $COL_TMP->find(array('mail_tmp'=>$mail_tmp,'grilleBrochure.md5Brochure'=>$ARRPOST['md5Brochure']))->count();
	if(empty($testB)):
		$ctbr = skelMongo::connect('brochure','sitebase_base')->findOne(array('md5Brochure'=>$ARRPOST['md5Brochure']));
		$ins = array('md5Brochure'=>$ARRPOST['md5Brochure'],'filename'=>$ctbr['titreBrochure'].'.html');
		$COL_TMP->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleBrochure'=>$ins)),array('upsert'=>true));
		//
		$arr 	= $COL_TMP->findOne(array('mail_tmp'=>$mail_tmp));
	endif;
}
if(!empty($ARRPOST['iddevis'])){ 
	$ctbr = $APP->plug('sitebase_devis','devis')->findOne(array('iddevis'=>(int)$ARRPOST['iddevis']));
	$test = $COL_TMP->find(array('mail_tmp'=>$mail_tmp,'grilleDevis.iddevis'=>(int)$ARRPOST['iddevis']));
	if($test->count()==0):
		$ins = array('iddevis'=>(int)$ARRPOST['iddevis'],'filename'=>$ctbr['nomDevis'].'.html');
		$COL_TMP->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleDevis'=>$ins)),array('upsert'=>true));
	endif;
	//
	$arr 	= $COL_TMP->findOne(array('mail_tmp'=>$mail_tmp));
}
if(!empty($ARRPOST['mailTo'])){ 
}
if(!empty($ARRPOST['idfacture'])){ 
	$ctbr = skelMongo::connect('facture','sitebase_devis')->findOne(array('idfacture'=>(int)$ARRPOST['idfacture']));
	$ins = array('idfacture'=>(int)$ARRPOST['idfacture'],'filename'=>'facture-'.$ctbr['numeroFactureDevis'].'.html');
	$COL_TMP->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleFacture'=>$ins)),array('upsert'=>true));
	//
	$arr 	= $COL_TMP->findOne(array('mail_tmp'=>$mail_tmp));
}
?>

<div class="applink" id="<?=$uniqid?>">
  <? if(!empty($arr['grilleBrochure'])){ 
  foreach($arr['grilleBrochure'] as $BR):
  ?>
  <div class="inline ededed" title="<?=$BR['filename']?>">
    <a>
    &nbsp;
    <?=coupeChaineMilieu($BR['filename'])?>
    </a>
    <a filename="<?=$BR['filename']?>">
    <li class="fa fa-times"></li>
    </a>
  </div>
  <? endforeach;?>
  <? }?>
  <? if(!empty($arr['grilleDevis'])){ 
  foreach($arr['grilleDevis'] as $BR):
  ?>
  <div class="inline ededed">
    <li class="fa fa-chevron-right">
      <?=$BR['filename']?>
    </li>
  </div>
  <? endforeach;?>
  <? }?>
  <? if(!empty($arr['grilleFacture'])){ 
  foreach($arr['grilleFacture'] as $BR):
  ?>
  <div class="inline ededed">
    <li class="fa fa-chevron-right">
      <?=$BR['filename']?>
    </li>
  </div>
  <? endforeach;?>
  <? }?>
  <? if(!empty($arr['grilleFichier'])){ 
  foreach($arr['grilleFichier'] as $BR):
  ?>
  <div class="inline ededed">
    <a deleteFichier="<?=$BR['filename']?>">
       <?=coupeChaineMilieu($BR['filename'])?> <li class="fa fa-times"></li> </a>
  </div>
  <? endforeach;?>
  <? }?>
</div>  
