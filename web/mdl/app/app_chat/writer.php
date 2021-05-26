<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$time = time(); 
$ONLINE_KEY=$_POST['ONLINE_KEY'];
?>
<div style="min-width:180px" class="bordert" >
  <form action="mdl/liveidle/actions.php" onsubmit="ajaxFormValidation(this);return false;" />
  <input type="hidden" name="F_action" id="F_action" value="write" /> 
  <input type="hidden"  name="dateCreation" value="<?=date_fr(date('Y-m-d'))?>">
  <input type="hidden" name="heureCreation" value="<?=date('H:i:s')?>"> 
  <input type="hidden" name="reloadModule[liveidle/spy]" value="<?=$ONLINE_KEY?>" /> 
  <input type="hidden" name="reloadModule[liveidle/writer]" value="<?=$_SESSION['idagent']?>" /> 
  <input type="hidden" name="ONLINE_KEY" value="<?=$ONLINE_KEY?>">
  <input type="hidden" name="TIME_KEY" value="<?=$time?>">
  <div class="relative margin blanc" >
    <input type="text" id="writer_text_<?=$ONLINE_KEY?>" name="texte" class="required" width="100%" style="width:100%;height:40px" > 
    <span class="absolute padding inline aligncenter" style="right:0"><img src="<?=ICONPATH?>info16.png" /></span>
  </div>
  </form>
</div>
