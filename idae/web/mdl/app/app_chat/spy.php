<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$ONLINE_KEY = $_POST['ONLINE_KEY']; 
ini_set('display_errors',55);
$baseLive  = skelMongo::connect('onlive','sitebase_base');
$baseAgent = skelMongo::connect('agent','sitebase_base');
$arr = $baseLive->findOne(array('ONLINE_KEY'=>$ONLINE_KEY));
 
foreach($arr['onlive_thread'] as $key=>$value):
	$arrA = $baseAgent->findOne(array('idagent'=>(int)$value['idagent'])); 
	$value['texte'] = UrlToShortLink($value['texte']);
	$value['texte'] = preg_replace('!<a href="(https?://[-a-z0-9+&@#/%?=_()|,;.]+\.(?:png|jpe?g|gif))" target="_blank">(.+?)</a>!si', '<img src="$1" />', $value['texte']);
	ob_start(); 
?>
<div id="temp_<?=$value['time']?>">
  <div class="padding" id="temp_2_<?=$value['time']?>">
    <label class="textgris" title="<?=date_fr($value['dateCreation'])?>">
      <?=$arrA['prenomAgent']?>
    </label>
    <div class="retrait">
     <?=($value["dateCreation"]!=date('Y-m-d'))? '' : maskHeure($value["heureCreation"]) ;?> <?=stripslashes(nl2br($value['texte']))?>
    </div>
    <br />
  </div>
</div>
<?
$final = ob_get_contents();
ob_end_clean();   
$pattern = '/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/';
$final = preg_replace($pattern,"", $final ); 
?><?=trim($final)?>
<script>
if(!$('p_<?=$value['time']?>')){ 
	$('onlive_holder_<?=$ONLINE_KEY?>').insert({top:$('temp_<?=$value['time']?>').innerHTML});  
	$('temp_<?=$value['time']?>').update();
	$('temp_2_<?=$value['time']?>').id = 'p_<?=$value['time']?>' ;
	$('onlive_slider_<?=$ONLINE_KEY?>').scrollTop = 10000
	}
</script>
<?
endforeach; 
?>
</div>
<script> 
$('onlive_slider_<?=$ONLINE_KEY?>').scrollTop = 10000
</script>
