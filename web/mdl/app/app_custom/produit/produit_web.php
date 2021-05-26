<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('produit');
	$idproduit = (int)$_POST['idproduit'];
	$arr = $APP->query_one(array('idproduit' => $idproduit));
?>
<div class="flex_h" style="width:100%;">
	<div class="frmCol1 applink applinkblock aligncenter">
		<div class="padding borderb"><i class="fa fa-recycle fa-2x"></i></div>
		<a onclick="loadModule('dyn/dyn_cache_vide','run=1&vars[idproduit]=<?=$idproduit?>')"><i class="fa fa-rebel"></i> Vider le cache</a>
	</div>
	<div class="flex_main">
		<iframe class="blanc" src="<?=Act::lienProduit($idproduit)?>" style="width:100%;height:100%;overflow:auto;" frameborder="0" ></iframe>
	</div>
</div>

