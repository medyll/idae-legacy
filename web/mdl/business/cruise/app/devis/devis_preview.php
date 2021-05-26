<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('devis');

	$iddevis = (int)$_POST['iddevis'];
	$arrF = array();

	$arrDevis = $APP->query_one(array( 'iddevis' => $iddevis ));
	$iddevis = $arrF['iddevis'] = $_POST['iddevis'] = (int)$arrDevis['iddevis'];

	//
	$grid = $APP->plug_fs('sitebase_devis');
	$testpdf = $grid->findOne(array( 'md5Devis' => $md5Devis ));
	$lastPdfBuild = ($testpdf->file['date']->sec);
	$lastDevisModif = $arrDevis['timeModificationDevis'];
	//

?>
<div class = "flex_v" style = "height:100%;" >
	<div   style = "overflow:auto;" >
		AQUI
		<?= skelMdl::cf_module('business/cruise/app/devis/devis_preview_inner' , array( 'scope' => 'iddevis' , 'iddevis' => $iddevis , 'md5Devis' => $md5Devis ) , $iddevis) ?>
	</div >
</div>