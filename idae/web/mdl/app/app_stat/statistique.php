<? 
include_once($_SERVER['CONF_INC']);
$uniqid = uniqid();
?>

<div class="flex_h blanc" style="height:100%;">
  <div class="frmCol1">
    <div class="applink applinkblock padding toggler">
      <a class="autoToggle" onclick="$('<?=$uniqid?>').loadModule('app/app_stat/app_stat_dispatch','mdl_stat=produit');">Produits</a>
    </div>
  </div>
  <div class="frmCol2flex_1" id="<?=$uniqid?>" > 
  </div>
</div>
<style>
.canvasjs-chart-credit{display:none;} 
</style> 