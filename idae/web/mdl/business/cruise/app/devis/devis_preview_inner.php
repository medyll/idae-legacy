<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('devis');

	$iddevis = (int)$_POST['iddevis'];
	$arrF    = array();

	$arrDevis = $APP->query_one(array('iddevis' => $iddevis));

	$content = $arrDevis['htmlDevis'];

	//
	$grid           = $APP->plug_fs('sitebase_devis');
	$testpdf        = $grid->findOne(array('iddevis' => $iddevis));
	$lastPdfBuild   = ($testpdf->file['date']->sec);
	$lastDevisModif = $arrDevis['timeModificationDevis'];

?>
<div style="height:100%;overflow:auto;background-color:#fff;" id="preview<?= $uniqid ?>">
	<?= $content ?>
</div>
<style>
	.pagebreaker {
		page-break-before: always;
		background-color: #666;
		height: 40px;
		box-shadow: 0 0 inset 5px #000;
		z-index: 10;
		margin-bottom: 10px;
	}
</style>
