<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$ONLINE_KEY = $_POST['ONLINE_KEY'];
$time=time();
ini_set('display_errors',55);
$baseLive  = skelMongo::connect('onlive','sitebase_base');
$baseCheck = skelMongo::connect('lastCheck','sitebase_sockets');
$baseAgent = skelMongo::connect('agent','sitebase_base');

/*while($arr=$rsT->getNext()):  
		$rsT = skelMongo::connect('agent_note','sitebase_base')->find()->sort(array('dateCreationNote'=>1,'heureCreationNote'=>1,'codeNote'=>1));
		$last='';
		unset($arr['_id']);
		$arr = fonctionsProduction::cleanPostMongo($arr);
		$insert = array();
		$key = array($arr['idagent'],$arr['idagent_writer']);
		sort($key);
		$ONLINE_KEY = 'oKy_'.implode('_',$key);
		 
		if($arr['idagent']!=$arr['idagent_writer']):
			$out = array('texte'=>$arr['texteNote'],'idagent'=>$arr['idagent_writer'],'dateCreation'=>$arr['dateCreationNote'],'heureCreation'=>$arr['heureCreationNote']);
			$out['time']= (int)strtotime($arr['dateCreationNote'].' '.$arr['heureCreationNote']);
			
			$insert['$set'] = array('onlive_thread.'.$out['time']=>$out);
			$last = $arr['codeNote'];
			$baseLive->update(array('ONLINE_KEY'=>$ONLINE_KEY),$insert ,array('upsert'=>true));
		endif; 
endwhile; */
 
$rs = $baseLive->find(array('ONLINE_KEY'=>$ONLINE_KEY));
$arrA = $baseAgent->findOne(array('idagent'=>(int)$_POST['idagent']));
?>

<div class="titleFor padding ededed">
  <h3 class="retrait">
    <?=$arrA['prenomAgent']?>
  </h3>
</div>
<div id="onlive_slider_<?=$ONLINE_KEY?>"   style="width:350px;overflow:hidden">
  <div   style="height:350px;overflow:auto;background-color:#fff;" id='onlive_holder_<?=$ONLINE_KEY?>'>
    <?
while($arr=$rs->getNext()): 
$arr['onlive_thread'] = array_reverse($arr['onlive_thread']);
foreach($arr['onlive_thread'] as $key=>$value):
$arrA = $baseAgent->findOne(array('idagent'=>(int)$value['idagent'])); 
$value['texte'] = UrlToShortLink($value['texte']);
$value['texte'] = preg_replace('!<a href="(https?://[-a-z0-9+&@#/%?=_()|,;.]+\.(?:png|jpe?g|gif))" target="_blank">(.+?)</a>!si', '<img src="$1" />', $value['texte']);
$css = ($value['idagent'] != $_SESSION['idagent'] )? 'ededed border4' : 'blanc' ;
?>
    <div style="margin:10px 5px;" class="padding <?=$css?>" id="p_<?=$value['time']?>">
      <div class="table" style="width:100%;">
        <div class="cell"><strong>
          <?=$arrA['prenomAgent']?>
          </strong>
        </div>
        <div class="cell alignright"> 
          <?=($value["dateCreation"]!=date('Y-m-d'))? date('d m y') : maskHeure($value["heureCreation"]) ;?>
        </div>
      </div>
      <?=stripslashes(nl2br($value['texte']))?>
    </div>
    <?
endforeach;
endwhile;

?>
  </div>
  <div class="blanc" >
    <?=skelMdl::cf_module('liveidle/writer',array('ONLINE_KEY'=>$ONLINE_KEY),$_SESSION['idagent']);?>
  </div>
  <div class="none">
    <?=skelMdl::cf_module('liveidle/spy',array('emptyModule'=>true,'ONLINE_KEY'=>$ONLINE_KEY),$ONLINE_KEY);?>
  </div>
</div>
<script> 
$('onlive_slider_<?=$ONLINE_KEY?>').scrollTop = 10000
</script>
