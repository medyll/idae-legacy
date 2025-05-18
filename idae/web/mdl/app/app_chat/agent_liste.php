<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
ini_set('display_errors',55);
$time=time();
$baseLive = skelMongo::connect('onLine','sitebase_sockets');
$baseCheck = skelMongo::connect('lastCheck','sitebase_sockets');
$baseAgent =skelMongo::connect('agent','sitebase_base');
$rsLive = $baseLive->find();
$rsA =skelMongo::connect('agent','sitebase_base')->find()->sort(array('onlineAgent'=>1,'prenomAgent'=>1)); 
$id_onlive = 'discuss'.$time;
?>

<div class="" style="height:450px;">
  <div class="padding">
    <a onclick="ajaxMdl('liveidle/contact_liste','<?=idioma('onLive')?>','time=<?=$time?>',{className:'widget noSize',inTask: true })">Contacts</a>
  </div> 
  <div class="relative flowDown">
    <div class="cell flowDown" style="overflow:hidden;width:350px">
      <div class="flowDown" id="<?=$id_onlive?>" style="overflow:hidden">
      </div>
    </div>
  </div>
</div>
