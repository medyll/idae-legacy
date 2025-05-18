<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
ini_set('display_errors',0);
$time = time(); 
$baseLive = skelMongo::connect('onLine','sitebase_sockets');
$baseCheck = skelMongo::connect('lastCheck','sitebase_sockets');
$baseAgent =skelMongo::connect('agent','sitebase_base');
$rsLive = $baseLive->find();
$rsA =skelMongo::connect('agent','sitebase_base')->find()->sort(array('onlineAgent'=>1,'prenomAgent'=>1)); 
$id_onlive = 'discuss'.$time;
?>

<div class="applink applinkblock" >
  <div class="titreGadget">onLive</div>
  <div class="in_gadget">
    <div class="gdt_1">
      <?
while($arr=$rsA->getNext()):
	$arrLive  = $baseLive->findOne(array('idagent'=>(int)$arr['idagent']));
	$arrCheck = $baseCheck->findOne(array('idagent'=>(int)$arr['idagent']));
	$first =(int)( time()-$arrLive['firstConnect'] );
	$idle =(int)( time()-$arrCheck['lastConnect'] );
	$ONLINE_KEY = array($arr['idagent'],$_SESSION['idagent']);
	sort($ONLINE_KEY);
	$ONLINE_KEY = 'oKy_'.implode('_',$ONLINE_KEY);
//	if($idle < 60 ):
	
?>
      <div class="applink applinkblock">
        <a style="text-shadow:0 0 5px #ffffff" class="" onclick="ajaxMdl('liveidle/discussion','appLive','start=1&ONLINE_KEY=<?=$ONLINE_KEY?>&idagent=<?=$arr['idagent']?>',{value: '<?=$ONLINE_KEY?>',ident: '<?=$ONLINE_KEY?>'})">
        <img src="<?=ICONPATH?>support16.png" />
        <?=$arr['prenomAgent']?>
        </a>
      </div>
      <div class="retrait none" >
        <? if(!empty($arrLive['firstConnect'])): ?>
        depuis
        <?=maskTime($first)?>
        <br>
        <? endif; ?>
        <? if(!empty($arrCheck['lastConnect'])): ?>
        idle
        <?=maskTime($idle)?>
        <? endif; ?>
      </div>
      <?
///	endif;
endwhile;
?>
    </div>
  </div>
</div>
