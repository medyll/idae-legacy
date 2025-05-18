<?
	include_once($_SERVER['CONF_INC']);
	// vardump($_POST);
// return '';
	$path_to_devis = 'business/cruise/app/devis/';
	ini_set('display_errors', 55);
	$uniqid        = uniqid();
	$iddevis_marge = (int)$_POST['table_value'];
	//
	$APP_DEVIS       = new App('devis');
	$APP_DEVIS_MARGE = new App('devis_marge');
	//
	$arr     = $APP_DEVIS_MARGE->findOne(array('iddevis_marge' => (int)$iddevis_marge));
	//vardump($arr);
	$iddevis = (int)$arr['iddevis'];
	//
	$code = '';
	//
?>
<table class="tabletop blanc" cellpadding="0" cellspacing="0" border="0" style="width:100%;border:none;">

	<tr idmarge="<?= $iddevis_marge ?>" iddevis="<?= $iddevis ?>" class="<?= $arr['codeDevis_marge'] ?>">
		<input type="hidden" class="idmarge" name="idmarge" value="<?= $iddevis_marge ?>"/>
		<input type="hidden" class="iddevis" name="iddevis" value="<?= $iddevis ?>"/>
		<input type="hidden" class="codeDevis_marge" name="codeDevis_marge" value="<?= $arr['codeDevis_marge'] ?>"/>
		<input type="hidden" class="comDevis_marge" name="comDevis_marge" value="10"/>
		<td style="border:none;border-right:1px solid #ccc;width:160px;" class="borderr">
			<input name="idprestataire" type="hidden" class="toPick inputFree alignright noborder <?= $arr['codeDevis_marge'] ?>" value="<?= $arr['idprestataire'] ?>"/>
			<input name="nomPrestataire" type="text" class="inputFree  noborder select <?= $arr['codeDevis_marge'] ?>" value="<?= $arr['nomPrestataire'] ?>" select="prestataire/prestataire_select"
			       paramName="nomPrestataire" populate='false'/>
		</td>
		<td style="border:none;border-right:1px solid #ccc;">
			<input id="nomDevis_marge_<?= $iddevis_marge ?>" type="text" name="nomDevis_marge" class="nomDevis_marge inputFree noborder" value="<?= $arr['nomDevis_marge'] ?>">
		</td>
		<td style="border:none;width:90px;">
			<input name="prixAchatDevis_marge" type="text" class="prixAchatDevis_marge inputFree alignright noborder <?= $arr['codeDevis_marge'] ?>" value="<?= maskNbre($arr['prixAchatDevis_marge']) ?>"/>
		</td>
		<td style="width:20px;border:none;" class="ededed aligncenter">
			<a onclick="ajaxMdl('devis/devis_prestataire/devis_prestataire_delete','Supprimer','iddevis=<?= $iddevis ?>&idmarge=<?= $iddevis_marge ?>')">
				<img src="<?= ICONPATH ?>/trash16.png"/>
			</a>
		</td>
	</tr>
</table>
