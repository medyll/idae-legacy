<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 20/06/14
	 * Time: 22:28
	 */
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	$name_form = $uniqid;
	$tbody = 'tbody' . $uniqid;
	$table = 'table' . $uniqid;
	$tfoot = 'tfoot' . $uniqid;
	$ttitre = 'ttitre' . $uniqid;
	//
	$iddossier_devis = (int)$_POST['iddossier_devis'];

	$arr = skelMongo::plug('sitebase_devis', 'dossier_devis')->findOne(array('iddossier_devis' => (int)$iddossier_devis));
	$idfacture = (int)$arr['idfacture'];
	$idclient = (int)$arr['idclient'];
	$iddevis = (int)$arr['iddevis'];
	$numeroDossierDevis = (int)$arr['numeroDossierDevis'];
	$arrC = skelMongo::connect('client', 'sitebase_devis')->findOne(array('idclient' => $idclient));
	$arrD = skelMongo::connect('devis', 'sitebase_devis')->findOne(array('iddevis' => $iddevis));
	$arrDateFact = skelMongo::connect('facture', 'sitebase_devis')->find(array('numeroDossierDevis' => $numeroDossierDevis))->sort(array('dateCreationFacture' => 1))->getNext();

	$arrAcompte = $arrD['grilleAcompteDevis'];
	$arrAllAcompte = array();
	$rsDevis = skelMongo::connect('devis', 'sitebase_devis')->find(array('numeroDossierDevis' => $numeroDossierDevis))->sort(array('iddevis' => -1));
	while ($arrDevis = $rsDevis->getNext()):
		$arrAllAcompte = array_merge($arrAllAcompte, $arrDevis['grilleAcompteDevis']);
	endwhile;

	// vardump($arrAllAcompte);
	//$arrAcompte = $arrAllAcompte;
	foreach ($arrAllAcompte as $key => $value):
		$tmp = (!empty($value[0])) ? $value[0] : $value;

		$totalAcpte += $tmp['sommeDevisAcompte'];
	endforeach;
	usort($arrAllAcompte, "custom_sort_acompte");
	//
	//	usort($outETAPE, "custom_sort_acompte");
	//	$grille1 = $grille2 = $arrClean['grilleEtapeProduit'] = $outETAPE;

	$arrE1 = array_pop($arrAllAcompte);

?>
<div app_gui_flowdown class="relative blanc" style="margin:0.1em;overflow:auto;">
<div class="page_header"></div>
<div class="margin alignright applink">
	<div class="inline padding ededed border4"><a class="autoToggle"
	                                              onclick="ajaxMdl('print/print','Impression demandée','mdlPrint=facture/facture_view_comptable&vars[idfacture]=<?= $idfacture ?>')"><img
				src="<?= ICONPATH ?>print16.png"/></a></div>
	<div class="inline padding ededed border4"><a
			onclick="popopen('proxyIndex.php?mdl=facture/facture_view_comptable&<?= http_build_query($_POST) ?>')"><img
				src="<?= ICONPATH ?>max16.png"/></a></div>
</div>
<div class="autoNext ededed"> Produit</div>
<div class="autoBlock">
	<table class="table_info" width="100%" cellspacing="0">
		<tr>
			<td class="uppercase bold" >Intitulé</td>
			<td><?= $arrD['produit']['nomProduit'] ?></td>
		</tr>
		<tr>
			<td class="uppercase bold">Cie</td>
			<td><?= $arrD['produit']['nomFournisseur'] ?></td>
		</tr>
	</table>
	<br/><br/>
	<?
		$db = skelMongo::connectBase('sitebase_devis');
		$rs = $db->command(array("distinct" => "devis_marge_type", "key" => 'groupe_ligne', "query" => array()));
		$tot = 0;
	?>
	<table class="table_info" width="100%" cellspacing="0">
		<tr>
			<td ></td>
			<td style="width:120px;" class="uppercase">Prestataire</td>
			<td class="uppercase">Dénomination</td>
			<td style="width:120px;" class="uppercase">Prix achat</td>
		</tr>
		<?
			foreach ($rs['values'] as $key => $value):
				$rsD = skelMongo::connect('devis', 'sitebase_devis')->find(array('numeroDossierDevis' => $numeroDossierDevis, 'est_signe' => 1))->sort(array('iddevis' => -1));
				?>
				<?
				while ($arrD = $rsD->getNext()):
					$iddevis = (int)$arrD['iddevis'];
					$rs      = skelMongo::connect('devis_marge', 'sitebase_devis')->find(array('iddevis' => (int)$iddevis, 'codeMarge' => $value))->sort(array('ordreMarge' => 1));
					if ($rs->count() != 0):
						?>
						<?
						while ($arr = $rs->getNext()):
							$idmarge = (int)$arr['idmarge'];
							$tot += $arr['prixAchatMarge'];
							?>
							<tr>
								<td><?= $value ?></td>
								<td class="uppercase bold"><?= $arr['nomPrestataire'] ?></td>
								<td class=""><?= $arr['nomMarge'] ?></td>
								<td class="bold"><?= maskNbre($arr['prixAchatMarge']) . '&nbsp;€' ?></td>
							</tr>
						<? endwhile; ?>
					<?
					endif;
				endwhile; ?>
			<?
			endforeach;  ?>
		<tr>
			<td></td>
			<td class="uppercase bold"></td>
			<td class="alignright">Total&nbsp;</td>
			<td class="bold"><?= maskNbre($tot) . '&nbsp;€' ?></td>
		</tr>


	</table>
</div>
<div class="autoNext ededed">
	<?= idioma('Echéancier client') ?>
</div>
<div class="autoBlock">
	<?
		$rsDevis = skelMongo::connect('devis', 'sitebase_devis')->find(array('numeroDossierDevis' => (int)$numeroDossierDevis, 'est_signe' => 1));
		while ($arrDevis = $rsDevis->getNext()):
			$arrAcompte = $arrDevis['grilleAcompteDevis'];
			usort($arrAcompte, "custom_sort_acompte");
			$totalAcpte = 0;
			foreach ($arrAcompte as $key => $value):
				$tmp = (!empty($value[0])) ? $value[0] : $value;
				$totalAcpte += $tmp['sommeDevisAcompte'];
			endforeach;
			?>
			<table class="table_info   " style="width:100%" cellspacing="0">
				<tr>
					<td class="borderb"></td>
					<? foreach ($arrAcompte as $key => $value):
						$tmp = (!empty($value[0])) ? $value[0] : $value;
						?>
						<td style="width:120px;" ><?= $tmp['typeDevisAcompte'] ?></td>
					<? endforeach; ?>
					<td></td>
					<td style="width:120px;"  ><?= idioma('Total') ?></td>
				</tr>
				<tr>
					<td class=""><?= idioma('date') ?>
						&nbsp;</td>
					<? foreach ($arrAcompte as $key => $value):
						$tmp = (!empty($value[0])) ? $value[0] : $value;
						?>
						<td class="td_cb"><?= date_fr($tmp['dateDevisAcompte']) ?></td>
					<? endforeach; ?>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class=""><?= idioma('Somme') ?>
						&nbsp;</td>
					<? foreach ($arrAcompte as $key => $value):
						$tmp = (!empty($value[0])) ? $value[0] : $value;
						?>
						<td class="td_cb bold"><strong>
								<?= maskNbre($tmp['sommeDevisAcompte']) . ' €' ?>
							</strong></td>
					<? endforeach; ?>
					<td></td>
					<td class="bold"><?= maskNbre($totalAcpte) . ' €' ?></td>
				</tr>
			</table><br/>
		<? endwhile; ?>
</div>
<div class="autoNext ededed"><span>
    <?= idioma('Comptabilité client') ?>
    </span></div>
<div class="autoBlock" id="inner<?= $uniqid ?>">
	<?= skelMdl::cf_module('devis_paiement/devis_paiement_liste', array('moduleTag' => 'none', 'vars' => array('numeroDossierDevis' => $numeroDossierDevis), 'scope' => 'numeroDossierDevis'), $numeroDossierDevis) ?>
</div>
<div class="autoNext ededed">Comptabilité Fournisseur</div>
<div class="autoBlock">
	<?
		$rsD = skelMongo::connect('devis', 'sitebase_devis')->find(array('numeroDossierDevis' => $numeroDossierDevis))->sort(array('iddevis' => -1));
	?> <?
		$tot = 0;
		$rsD->reset();
		while ($arrD = $rsD->getNext()):
			$iddevis = (int)$arrD['iddevis'];
			$rs      = skelMongo::connect('paiement_prestataire', 'sitebase_devis')->find(array('iddevis' => (int)$iddevis))->sort(array('codeMarge' => 1, 'nomPrestataire' => 1));
			if ($rs->count() != 0):
				?>
				<table class="table_info" width="100%" cellspacing="0">
					<tr>
						<td style="width:60px;" class="uppercase">type</td>
						<td style="width:120px;" class="uppercase">Prestataire</td>
						<td style="width:60px;" class="uppercase">mode</td>
						<td class="uppercase"><?= idioma('Date') ?></td>
						<td style="width:120px;" class="uppercase"><?= idioma('Montant') ?></td>
					</tr>
					<?
						while ($arr = $rs->getNext()):
							$idmarge = (int)$arr['idmarge'];
							$arrM    = skelMongo::connect('devis_marge', 'sitebase_devis')->findOne(array('idmarge' => (int)$idmarge));
							$tot += $arr['montantPaiement'];
							?>
							<tr>
								<td><?= $arr['typePaiement'] ?></td>
								<td class="uppercase bold"><?= $arrM['nomPrestataire'] ?></td>
								<td><?= $arr['modePaiement'] ?></td>
								<td><?= date_fr($arr['datePaiement']) ?></td>
								<td class="bold"><?= maskNbre($arr['montantPaiement']) . ' €' ?></td>
							</tr>
						<? endwhile; ?>
					<tr>
						<td></td>
						<td class="uppercase bold"></td>
						<td></td>
						<td class="alignright">Total&nbsp;</td>
						<td class="bold"><?= maskNbre($tot) . ' €' ?></td>
					</tr>
				</table>
			<?
			endif;
		endwhile; ?>
</div>
<br/>