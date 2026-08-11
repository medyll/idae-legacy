<?php
/**
 * Modified: 2026-08-11 — Effect.Appear -> appearElement (engine/methods.js);
 *                        the Scriptaculous shim was deleted on 2026-08-09 and
 *                        this call had been throwing "Effect is not defined"
 */
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
<?php
for($m=$annee; $m<=$annee+8;$m++){ 
$lien = gmmktime(12,0,0,$mois,$jour,$m); 
($lien==$sd)? $class = 'goodChoice' : $class = '';
if($i!=0 && $i%3==0) echo '</tr><tr>';
$i++;
?>
<td class="listMois <?=$class?>">
<a onclick="reloadModule('app/app_calendrier/mdlCalendrier','<?=$calendarId?>','<?=sendPost("sd=$lien",$_POST)?>');" ><?=$m?></a>
</td>
<?php } ?></table>
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
 /*
  * Modified: 2026-08-11 — migrated the remaining PrototypeJS shim calls
  * ($, .cleanWhitespace) to native DOM. The Effect.Appear on the next line was
  * already replaced (ed8b761); this finishes the file.
  */
 function cly_el(ref) {
 	return typeof ref === 'string' ? document.getElementById(ref) : ref;
 }

 /**
  * Prototype's Element#cleanWhitespace: drop the whitespace-only text nodes
  * between children, which is what made its inline-block grid lay out without
  * stray gaps.
  */
 function cly_cleanWhitespace(node) {
 	if (!node) return node;
 	var child = node.firstChild, next;
 	while (child) {
 		next = child.nextSibling;
 		if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
 		child = next;
 	}
 	return node;
 }

 cly_cleanWhitespace(cly_el('dynlistYear'))
 appearElement(cly_el('dynlistYear'))
// new tableGui(cly_el('dynlistYear'),{numRow: 3, numCol: 3   })
closeFrmListYear=function(file,div,links){
	ajaxInMdl('file','div','link');
	if( cly_el('mouseDiv')) {cly_el('mouseDiv').close();}
}

</script>