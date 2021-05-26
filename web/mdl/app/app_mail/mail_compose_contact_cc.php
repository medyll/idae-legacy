<?
include_once($_SERVER['CONF_INC']);  
$uniqid = uniqid();
$mail_tmp	= $_POST['mail_tmp']; 
$arr		= skelMongo::connect('email_compose_tmp','sitebase_email')->findOne(array('mail_tmp'=>$mail_tmp));
?>
<? if(!empty($arr['grilleContactCC'])){ 
  foreach($arr['grilleContactCC'] as $BR):
  ?>

<div class="inline applink" style="vertical-align:middle;line-height:18px;">
  <a>
  <?=coupeChaineMilieu($BR['email'],12)?>
  </a>
   <a email="<?=$BR['email']?>">
  <li class="fa fa-times"></li>
  </a>
</div>
<? endforeach;?> 
<? }?>
