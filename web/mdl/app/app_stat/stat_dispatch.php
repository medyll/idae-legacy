<?
include_once($_SERVER['CONF_INC']);
$uniqid =  uniqid(); 
echo $mdl = $_POST['mdl_stat'].'/'.$_POST['mdl_stat'];
?>

<div class="fond_noir color_fond_noir titre_entete">
  <li class="fa fa-chevron-right"></li>&nbsp;<?=idioma('Statistiques').' '.$_POST['mdl_stat']?>
</div>
<div id="liste_<?=$uniqid?>" class="flowDown borderb" style="overflow:auto;">
  <?=skelMdl::cf_module('app/app_stat/'.$mdl.'_liste');?>
</div>
<div class="stayDown">
  <div class="bordert borderb flowDown" id="date_<?=$uniqid?>">
    <?=skelMdl::cf_module('app/app_stat/statistique_periode');?> 
  </div>
  <div class="stayDown" id="chart_<?=$uniqid?>" style="height: 400px; width: 100%;">
    <?=skelMdl::cf_module('app/app_stat/'.$mdl.'_stat',array('emptyModule'=>true));?>
  </div>
</div>
<script>
$('liste_<?=$uniqid?>').on('click','input[type=checkbox]',function(event,node){ 
	vars		= Form.serialize($('liste_<?=$uniqid?>'));
	varsDate	= Form.serialize($('date_<?=$uniqid?>'));
	$('chart_<?=$uniqid?>').loadModule('statistique/<?=$mdl?>_stat',vars+'&'+varsDate).show(); 
}.bind(this)) 	
$('date_<?=$uniqid?>').observe('dom:datechoosen',function(event){
	vars		= Form.serialize($('liste_<?=$uniqid?>'));
	varsDate	= Form.serialize($('date_<?=$uniqid?>'));
	$('chart_<?=$uniqid?>').loadModule('statistique/<?=$mdl?>_stat',vars+'&'+varsDate).show(); 
}.bind(this)) 	
</script> 
