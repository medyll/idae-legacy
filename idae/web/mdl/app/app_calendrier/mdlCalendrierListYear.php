<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$sd = $_POST['sd'];
$calendarId = $_POST['calendarId']; 
$jour  = date("d", $sd);
$mois  = date("m", $sd);
$annee = date("Y", $sd);
$i = 0; 
$lienP = gmmktime(12,0,0,$mois,$jour,$annee-8); 
$lienS = gmmktime(12,0,0,$mois,$jour,$annee+6);  
?>
<div class="padding applink aligncenter">
	<a onClick="ajaxInMdl('app/app_calendrier/mdlCalendrierListYear','<?=$_POST['yearCal']?>','<?=sendPost("sd=$lienP")?>');" ><<</a>
    <span><?=$annee?></span>
	<a onClick="ajaxInMdl('app/app_calendrier/mdlCalendrierListYear','<?=$_POST['yearCal']?>','<?=sendPost("sd=$lienS")?>');" >>></a>
</div><div style="text-align:left;padding:0px;min-height: 120px;width:100%;overflow:hidden;"  id="dynlistYear">
<table style="width:100%;height:100%" class="applink">
<?
for($m=$annee; $m<=$annee+8;$m++){ 
$lien = gmmktime(12,0,0,$mois,$jour,$m); 
($lien==$sd)? $class = 'goodChoice' : $class = '';
if($i!=0 && $i%3==0) echo '</tr><tr>';
$i++;
?>
<td class="listMois <?=$class?>">
<a onclick="reloadModule('app/app_calendrier/mdlCalendrier','<?=$calendarId?>','<?=sendPost("sd=$lien",$_POST)?>');" ><?=$m?></a>
</td>
<? } ?></table>
</div>
<style> 
.listYear:hover {
	display:table;
	width:100%;
	background-image: url(images/calHover.png);
	background-position:center center;
	background-repeat:no-repeat;
	font-style:bold;
	text-decoration:none;
} 
</style>
<script>
 $('dynlistYear').cleanWhitespace()
 new Effect.Appear($('dynlistYear'))
// new tableGui($('dynlistYear'),{numRow: 3, numCol: 3   })
closeFrmListYear=function(file,div,links){
	ajaxInMdl('file','div','link');
	if( $('mouseDiv')) {$('mouseDiv').close();}
}

</script>