<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();
$time = time(); 
$idproduit = (int)$_POST['idproduit'];
$arr = $APP->plug('sitebase_production','produit')->findOne(array('idproduit'=>$idproduit)) ;
 

		
?>
  <div class="applink applinkblock borderb">

    <a onclick="ajaxInMdl('app/app_custom/produit/produit','wh<?=$idproduit?>azou','idproduit=<?=$idproduit?>',{onglet:'pfrd <?=$idproduit?>'})">
    <i class="fa fa-cog"></i> <?=idioma('Edition complète')?>
    </a>
    <a onclick="<?=fonctionsJs::app_create('devis',['idproduit'=>$idproduit])?>">
    <i class="fa fa-cog"></i> <?=idioma('Faire un devis')?>
    </a>
</div>