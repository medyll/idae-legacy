<? 
include_once('../conf.inc.php');
$time = time();
$mat=array();
$ClassStockMateriel = new StockMateriel();
$ClassMateriel = new Materiel();
$ClassProduit = new Produit(); 
$ClassLigneLocationClient = new LigneLocationClient();  
$rs = $ClassStockMateriel->getOneStockMateriel(array('orderBy'=>'nomStock_materiel'));
	while($arr = $rs->fetchRow()){
				$mat[] = $arr['idstock_materiel'];	
			}
if(!empty($_POST['nomProduit'])){
	$rsMateriel = $ClassMateriel->searchOneMateriel(array('estActifMateriel'=>1,'nomProduit'=>$_POST['nomProduit'],'groupBy'=>'produit_idproduit','nostock_materiel_idstock_materiel'=>$mat));
}
if(!empty($_POST['idproduit'])){ 
	$rsMateriel = $ClassMateriel->getOneMateriel(array('estActifMateriel'=>1,'idproduit'=>$_POST['idproduit'],'nostock_materiel_idstock_materiel'=>$mat));
}
if(!empty($_POST['idproduit']) && !empty($_POST['numeroSerieInterneMateriel']) ){ 
	$rsMateriel = $ClassMateriel->searchOneMateriel(array('estActifMateriel'=>1,'numeroSerieInterneMateriel'=>$_POST['numeroSerieInterneMateriel'],'idproduit'=>$_POST['idproduit'],'nostock_materiel_idstock_materiel'=>$mat));
}
?>
<ul>
<? while ($arr = $rsMateriel->FetchRow()){  ?>
  <li id="auto<?=$arr['idmateriel']?>">
  	<span class="informal"><img src="<?=ICONPATH?>fiche16.png" />&nbsp;</span>
    <?
	if(!empty($_POST['nomProduit'])){ ?>
        <span class="informal nomProduit">
        <?=$arr['nomProduit']?> 
        </span>
        <span class="informal idproduit none">
        <?=$arr['idproduit']?> 
        </span> 
    <? } ?>
    <?
	if(!empty($_POST['idproduit'])){ ?>
        <span class="informal numeroSerieInterneMateriel">
        <?=$arr['numeroSerieInterneMateriel']?> 
        </span>
        <span class="informal idmateriel none">
        <?=$arr['idmateriel']?> 
        </span> 
    <? } ?>
    </li>
  <? } ?>
</ul>
