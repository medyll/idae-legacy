<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('opportunite_ligne');
	$APPS = new App('opportunite_statut');

	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	$time = time();
	//
	$idopportunite_ligne = (int)$_POST['table_value'];
	$ARR = $APP->query_one(['idopportunite_ligne' => $idopportunite_ligne]);

?>
<div class="padding borderb flex_h"  >
		<div style="width: 40px;" class="aligncenter" >
			<?= $ARR['quantiteOpportunite_ligne'] ?> x &nbsp;
		</div >
		<div class="flex_main" >
			<div class="ellipsis"><?= $ARR['nomProduit'] ?></div>
		</div >
	<div style="width: 40px;" class="aligncenter" >
		<a onclick="<?=fonctionsJs::app_delete($table,$table_value) ?>"><i class="fa fa-times ms-fontColor-red"></i> </a>
	</div >
</div >

