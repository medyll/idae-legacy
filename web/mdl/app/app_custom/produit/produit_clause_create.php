<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('produit');
	$idproduit = (int)$_POST['vars']['idproduit'];
	$arr = $APP->query_one(array('idproduit' => $idproduit));

?>
<div style="width:750px;">
	<div class="titre_entete fond_noir color_fond_noir">
		clause produit
	</div>
	<form action="<?= ACTIONMDL ?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >

	<input type="hidden" name="F_action" value="app_update"/>
	<input type="hidden" name="table" value="produit"/>
	<input type="hidden" name="table_value" value="<?= $idproduit ?>">
	<textarea name="vars[<?=$_POST['clause']?>Produit]" style="width:750px;height:250px;" ><?=$arr[$_POST['clause'].'Produit']?></textarea>

	<div class="buttonZone">
		<input type="submit" value="Valider">
		<input type="reset" value="Annuler" class="cancelClose">
	</div>
	</form>
</div>
