<?
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/cruise/app/devis/';
	$time          = time();

	$iddevis = (int)$_POST['iddevis'];

	$APP_DEVIS       = new App('devis');
	$APP_DEVIS_MARGE = new App('devis_marge');
	//
	$arr = $APP_DEVIS->findOne(array('iddevis' => (int)$iddevis));
	$rs  = $APP_DEVIS_MARGE->find(array('iddevis' => (int)$iddevis))->sort(array('ordreMarge' => 1));
	if ($rs->count() == 0):

	endif;
?>
<div style="width:650px;position:relative">
	<div class="titre_entete">
		<?= idioma('Edition feuille prestataires') ?>
	</div>
	<div data-dsp_liste="true" data-dsp="mdl" data-dsp-mdl="<?=$path_to_devis?>devis_prestataire_ligne" data-vars="table=devis_marge&vars[iddevis]=<?=$iddevis?>" class=" relative margin">
	</div>
	<div class="bordert" style="max-height:450px;overflow:auto;position:relative;">
		<?= skelMdl::cf_module('devis/devis_prestataire/devis_prestataire_inner', array('iddevis' => $iddevis), $iddevis) ?>
	</div>
	<div class="buttonZone">
		<input type="button" class="cancelClose" value="<?= idioma('Fermer') ?>"/>
	</div>
</div>
