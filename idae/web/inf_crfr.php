<?

	include_once($_SERVER['CONF_INC']);
	require_once __DIR__ . '/appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;

	$APP->init_scheme('sitebase_devis', 'devis_prestation');
	$APP->init_scheme('sitebase_devis', 'devis_acompte');
	$APP->init_scheme('sitebase_devis', 'devis_passager');
	$APP->init_scheme('sitebase_devis', 'devis_annotation');
	$APP->init_scheme('sitebase_devis', 'devis_envie');
	$APP->init_scheme('sitebase_devis', 'devis_marge_type');
	$APP->init_scheme('sitebase_production', 'fournisseur_clause');
	$APP->init_scheme('sitebase_production', 'fournisseur_clause_type');

	$APP              = new App('devis');
	$APP_MARGE        = new App('devis_marge');
	$APP_MARGE_TYPE   = new App('devis_marge_type');
	$APP_PREST        = new App('devis_prestation');
	$APP_AC           = new App('devis_acompte');
	$APP_ACT          = new App('devis_acompte_type');
	$APP_ENVIE        = new App('devis_envie');
	$APP_ANNOT        = new App('devis_annotation');
	$APP_STATUT       = new App('devis_statut');
	$APP_DOSSIER      = new App('dossier_devis');
	$APP_FACTURE      = new App('facture');
	$APP_FACTURE_TYPE = new App('facture_type');
	$APP_F            = new App('fournisseur');
	$APP_FC           = new App('fournisseur_clause');
	$APP_FCT          = new App('fournisseur_clause_type');

	// fournisseur
	$rs = $APP_F->find();
?>
	<progress min="0" max="<?= $rs->count() ?>" id="progress_F"></progress>
<?
	echo "<br>";
	flush();
	buffer_flush();
	$i = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$out = [];
		if (!empty($arr['clauseFournisseur'])) {
			foreach ($arr['clauseFournisseur'] as $key => $value) {
				// print_r($value);
				if (is_array($value)) {
					$export                                      = [];
					$export['idfournisseur']                     = (int)$arr['idfournisseur'];
					$export['ordreFournisseur_clause']           = (int)$value['ordreFournisseur_clause_type'];
					$export['descriptionFournisseur_clause']     = $value['descriptionFournisseur_clause'];
					$export['descriptionHTMLFournisseur_clause'] = $value['descriptionFournisseur_clause'];
					$export['nomFournisseur_clause']             = $value['nomFournisseur_clause_type'];

					if (!empty($value['codeFournisseur_clause_type'])) {
						$export['idfournisseur_clause_type'] = (int)$APP_FCT->create_update(['codeFournisseur_clause_type' => $key], ['nomFournisseur_clause_type' => $value['nomFournisseur_clause_type']]);
					}
					$APP_FC->create_update(['idfournisseur' => (int)$arr['idfournisseur'], 'idfournisseur_clause_type' => $export['idfournisseur_clause_type']], $export);

				}
			}
		}

		?>
		<script>
			document.getElementById('progress_F').value = '<?=$i?>';
		</script>
		<?
		flush();
		buffer_flush();
	}

	// devis_ marge
	$rs = $APP_MARGE->find();
?>
	<progress min="0" max="<?= $rs->count() ?>" id="progress_marge"></progress>
<?
	echo "<br>";
	flush();
	buffer_flush();
	$i = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$out = [];
		if (!empty($arr['idprestataire']) && empty($arr['iddevis_prestataire'])) {
			$out['iddevis_prestataire'] = (int)$arr['idprestataire'];
		}
		if (!empty($arr['idmarge']) && empty($arr['iddevis_marge'])) {
			$out['iddevis_marge'] = (int)$arr['idmarge'];
		}
		if (!empty($arr['nomMarge']) && empty($arr['nomDevis_marge'])) {
			$out['nomDevis_marge'] = $arr['nomMarge'];
		}
		if (!empty($arr['comMarge']) && empty($arr['comissionDevis_marge'])) {
			$out['comissionDevis_marge'] = $arr['comMarge'];
		}
		if (!empty($arr['ordreMarge']) && empty($arr['ordreDevis_marge'])) {
			$out['ordreDevis_marge'] = $arr['ordreMarge'];
		}
		if (!empty($arr['codeMarge']) && empty($arr['iddevis_marge_type'])) {
			$out['iddevis_marge_type'] = (int)$APP_STATUT->create_update(['codeDevis_marge_type' => $arr['codeMarge']], ['nomDevis_marge_type' => strtolower($arr['codeMarge'])]);
		}
		if (!empty($arr['codeMarge']) && empty($arr['codeDevis_marge'])) {
			$out['codeDevis_marge'] = $arr['codeMarge'];
		}
		if (!empty($arr['prixAchatMarge']) && empty($arr['prixAchatDevis_marge'])) {
			$out['prixAchatDevis_marge'] = $arr['prixAchatMarge'];
		}

		if (sizeof($out) != 0) {
			$APP_MARGE->update(['_id' => MongoCompat::toObjectId($arr['_id'])], $out);
		}

		?>
		<script>
			document.getElementById('progress_marge').value = '<?=$i?>';
		</script>
		<?
		flush();
		buffer_flush();
	}

	// facture
	$rs = $APP_FACTURE->find();
?>
	<progress min="0" max="<?= $rs->count() ?>" id="progress_2"></progress>
<?
	echo "<br>";
	flush();
	buffer_flush();
	$i = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$out = [];
		if (!empty($arr['numeroFactureDevis']) && empty($arr['codeFacture'])) {
			$out['codeFacture'] = $arr['numeroFactureDevis'];
		}

		if (!empty($arr['typeFacture']) && empty($arr['idfacture_type'])) {
			$out['idfacture_type'] = (int)$APP_FACTURE_TYPE->create_update(['codeFacture_type' => strtoupper($arr['typeFacture'])], ['nomFacture_type' => strtolower($arr['typeFacture'])]);
		}

		if (sizeof($out) != 0) {
			$APP_FACTURE->update(['idfacture' => (int)$arr['idfacture']], $out);
		}

		$test = $APP->findOne(['iddevis' => (int)$arr['iddevis']]);
		if (empty($test['iddevis'])) print_r($arr['iddevis']);

		?>
		<script>
			document.getElementById('progress_2').value = '<?=$i?>';
		</script>
		<?
		flush();
		buffer_flush();
	}

	// dossiers
	$rs = $APP_DOSSIER->find();
?>
	<progress min="0" max="<?= $rs->count() ?>" id="progress_1"></progress>
<?
	echo "<br>";
	flush();
	buffer_flush();
	$i = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$out = [];
		if (!empty($arr['numeroDossier_devis']) && empty($arr['codeDossier_devis'])) {
			$out['codeDossier_devis'] = $arr['numeroDossier_devis'];
		}
		if (sizeof($out) != 0) {
			$APP_DOSSIER->update(['iddossier_devis' => (int)$arr['iddossier_devis']], $out);
		}
		?>
		<script>
			document.getElementById('progress_1').value = '<?=$i?>';
		</script>
		<?
		flush();
		buffer_flush();
	}
	/*$APP_PREST->resetNext('iddevis_prestation');
	$APP_PREST->resetNext('iddevis_acompte');
	$APP_PREST->resetNext('iddevis_acompte_type');*/

	$rs = $APP->find();

?>
	<progress min="0" max="<?= $rs->count() ?>" id="progress"></progress>
<?
	echo "<br>";
	flush();
	buffer_flush();
	$i = 0;
	while ($arr = $rs->getNext()) {
		$i++;
		$out = [];
		if (!empty($arr['envieDevis']) && empty($arr['iddevis_envie'])) {
			$out['iddevis_envie'] = (int)$APP_ENVIE->create_update(['codeDevis_envie' => strtoupper($arr['envieDevis'])], ['nomDevis_envie' => strtolower($arr['envieDevis'])]);
		}
		if (!empty($arr['devisCommentaire']) && empty($arr['commentaireDevis'])) {
			$out['commentaireDevis'] = $arr['devisCommentaire'];
		}
		if (!empty($arr['dateDepartDevis']) && empty($arr['dateDebutDevis'])) {
			$out['dateDebutDevis'] = $arr['dateDepartDevis'];
		}
		if (!empty($arr['dateRetourDevis']) && empty($arr['dateFinDevis'])) {
			$out['dateFinDevis'] = $arr['dateRetourDevis'];
		}
		if (!empty($arr['numeroDevis']) && empty($arr['codeDevis'])) {
			$out['codeDevis'] = $arr['numeroDevis'];
		}
		if (!empty($arr['nombreEnfantDevis']) && empty($arr['nbreEnfantDevis'])) {
			$out['nbreEnfantDevis'] = (int)$arr['nombreEnfantDevis'];
		}
		if (!empty($arr['nombreAdulteDevis']) && empty($arr['nbreAdulteDevis'])) {
			$out['nbreAdulteDevis'] = (int)$arr['nombreAdulteDevis'];
		}
		if (empty($arr['attente_signature']) && empty($arr['est_signe']) && empty($arr['iddevis_statut'])) {
			$out['iddevis_statut'] = (int)$APP_STATUT->create_update(['codeDevis_statut' => 'WAIT'], ['nomDevis_statut' => 'en attente traitement']);
		}
		if (!empty($arr['attente_signature']) && empty($arr['iddevis_statut'])) {
			$out['iddevis_statut'] = (int)$APP_STATUT->create_update(['codeDevis_statut' => 'WAITCONFIRM'], ['nomDevis_statut' => 'en attente signature']);
		}
		if (!empty($arr['est_signe']) && empty($arr['iddevis_statut'])) {
			$out['iddevis_statut'] = (int)$APP_STATUT->create_update(['codeDevis_statut' => 'END'], ['nomDevis_statut' => 'devis signé']);
		}
		if (sizeof($out) != 0) {
			$APP->update(['iddevis' => (int)$arr['iddevis']], $out);
		}
		/*if (!empty($arr['arrAnnotationDevis'])) {
			foreach ($arr['arrAnnotationDevis'] as $key => $value) {
				echo $key . '<br>______________________________<br>';
				// print_r($value);
				if (is_array($value)) {
					if (!empty($value['idagent'])) {
						echo (int)$value['idagent'] . '<br>';
						echo $value['annotationDevis'] . '<br>';
						$export                          = [];
						$export['iddevis']               = (int)$arr['iddevis'];
						$export['idagent']               = (int)$value['idagent'];
						$export['nomDevis_annotation']   = $value['annotationDevis'];
						$export['dateDevis_prestation']  = $value['dateAnnotationDevis'];
						$export['heureDevis_prestation'] = $value['heureAnnotationDevis'];

						$APP_ANNOT->create_update(['codeDevis_annotation' => $arr['iddevis'] . $value['idagent'] . $value['heureAnnotationDevis']], $export);
					}

				}
			}
		}*/

		/*if (!empty($arr['prestation'])) {
			foreach ($arr['prestation'] as $key => $value) {
				$export                                = [];
				$export['iddevis']                     = (int)$arr['iddevis'];
				$export['ordreDevis_prestation']       = $value['ordrePrestation'];
				$export['nomDevis_prestation']         = $value['nomPrestation'];
				$export['descriptionDevis_prestation'] = $value['nomPrestation'];
				$export['totalDevis_prestation']       = $value['totalPrestation'];
				$export['prixDevis_prestation']        = $value['prixUnitairePrestation'];
				$export['quantiteDevis_prestation']    = $value['quantitePrestation'];

				unset($export['idprestation']);
				$APP_PREST->create_update(['iddevis' => (int)$arr['iddevis'], 'codeDevis_prestation' => $value['idprestation']], $export);
			}
		}*/
		/*if (!empty($arr['grilleAcompteDevis'])) {
			foreach ($arr['grilleAcompteDevis'] as $key => $value) {
				if((int)$value['sommeDevisAcompte']==0) continue;
				$export                         = [];
				$export['iddevis']              = (int)$arr['iddevis'];
				$export['dateDevis_acompte']    = $value['dateDevisAcompte'];
				$export['prixDevis_acompte']    = $value['sommeDevisAcompte'];
				$export['iddevis_acompte_type'] = (int)$APP_ACT->create_update(['codeDevis_acompte_type' => $value['typeDevisAcompte']], ['nomDevis_acompte_type' => strtolower($value['typeDevisAcompte'])]);

				$APP_AC->create_update(['codeDevis_acompte' => $export['iddevis'] . $export['dateDevis_acompte'] . $export['prixDevis_acompte'] . $export['iddevis_acompte_type']], $export);
			}
		}*/
		?>
		<script>
			document.getElementById('progress').value = '<?=$i?>';
		</script>
		<?
		flush();
		buffer_flush();
	}