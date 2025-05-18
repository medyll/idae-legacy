<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php');
if(file_exists('../../../conf.inc.php')) include_once('../../../conf.inc.php');
$uniqid = uniqid(); 
// 7 derniers jours
$startTime	= date('d/m/Y',mktime() - 7*3600*24);
$endTime	= date('d/m/Y',mktime());
?>
<div id="sel<?=$uniqid?>" class="padding relative">
<form id="form<?=$uniqid?>">
<strong>Choix de la période</strong> &nbsp;
du <input type="text" value="<?=$startTime?>" class='validate-date-au' name="dateDebut">
au <input type="text" value="<?=$endTime?>" class='validate-date-au' name="dateFin">
&nbsp;
<input type="text" class="select" select="statistique/statistique_date_select" />
</form>
</div>
<script>
$('form<?=$uniqid?>').observe('dom:datechoosen',function(event){ 
	$('form<?=$uniqid?>').dateDebut.value = event.memo.dateDebut
	$('form<?=$uniqid?>').dateFin.value = event.memo.dateFin
	})
</script>