<?
	include_once($_SERVER['CONF_INC']);

	if (!droit('DEV')) return;
	$path_to_devis = 'business/cruise/app/devis/';

	ini_set('display_errors', 55);
	$APP_DEV  = new App('devis');
	$APP_AC   = new App('devis_acompte');
	$APP_CLI  = new App('client');
	$APP_DEVM = new App('devis_mail');

	$time = time();
	//
	$iddevis = (int)$_POST['iddevis'];

	$arr       = $APP_DEV->query_one(array('iddevis' => (int)$iddevis));
	$idclient  = (int)$arr['idclient'];
	$arrClient = $APP_CLI->query_one(array('idclient' => (int)$arr['idclient']));
	// MAILS
	$rsNbMail = $APP_DEVM->query(array('iddevis' => (int)$iddevis))->sort(array('mongoDateDevis_mail' => -1));
	$nbMail   = $rsNbMail->count();
	// docs
	/*$baseDoc = skelMongo::connectBase('sitebase_ged')->getGridFs('ged_client');
	$nbDocs = $baseDoc->find(array( 'metadata.iddevis' => $iddevis ))->count();*/

	// verification :
	$dist_ac = $APP_AC->distinct_all('prixDevis_acompte', array('iddevis' => (int)$iddevis));

	$tot_ac  = array_sum($dist_ac);
	$tot_dev = $arr['prixDevis'];
?>
<div class="padding">
	<? if ($tot_ac != $tot_dev) { ?>
		<div class="padding flex_h ededed border4 margin">
			<div><i class="fa fa-warning"></i></div>
			<div>Vérifier total devis / total acompte
				<br>
			     Echéancier, manque <?= maskNbre($tot_dev - $tot_ac, 2) . ' €' ?>
			</div>
		</div>
	<? } else { ?>
		<? 	} ?>
	<div class="padding margin border4">
		<div class="padding borderb bold uppercase aligncenter"><?= $arr['nomDevis_statut'] ?></div>
		<div>
			<? if ( $arr['codeDevis_statut'] == 'WAIT') { ?>
				<a onclick="act_chrome_gui('app/app_update_field','mode=fk&table=devis&table_value=<?= $iddevis ?>&field_name_raw=devis_statut')" class="cursor">
					<img src="<?= ICONPATH ?>signature16.png"/>
					&nbsp;Mettre en cours de traitement
				</a>
			<? } ?>
			<? if ($arr['codeDevis_statut'] != 'END' && $arr['codeDevis_statut'] != 'WAITCONFIRM' && $arr['codeDevis_statut'] != 'WAIT') { ?>
				<a onclick="act_chrome_gui('app/app_update_field','mode=fk&table=devis&table_value=<?= $iddevis ?>&field_name_raw=devis_statut')" class="cursor">
					<img src="<?= ICONPATH ?>signature16.png"/>
					&nbsp;Mettre en attente signature
				</a>
			<? } ?>
			<? if ( $arr['codeDevis_statut'] == 'WAITCONFIRM' && $arr['codeDevis_statut'] != 'WAIT') { ?>
				<a onclick="act_chrome_gui('app/app_update_field','mode=fk&table=devis&table_value=<?= $iddevis ?>&field_name_raw=devis_statut')" class="cursor">
					<img src="<?= ICONPATH ?>signature16.png"/>
					&nbsp;Marquer comme signé
				</a>
			<? } ?>
		</div>

	</div>
	<a onclick="ajaxMdl('devis/devis_document','Documents de <?= addslashes($arrClient['nomClient']) ?>','iddevis=<?= $iddevis ?>')">
		<img src="<?= ICONPATH ?>document16.png"/>
		&nbsp;Documents
	</a>
	<a onclick="ajaxMdl('<?= $path_to_devis ?>devis_prestataire','Prestataires','iddevis=<?= $iddevis ?>')">
		<img src="<?= ICONPATH ?>cash16.png"/>
		&nbsp;Prestataires
	</a>
	<a onclick="ajaxMdl('devis/devis_marge/devis_marge','Calcul et relevé des profits','iddevis=<?= $iddevis ?>')">
		<img src="<?= ICONPATH ?>cash16.png"/>
		&nbsp;Relevé des profits
	</a>
	<? if (!empty($arr['attente_signature']) || !empty($arr['est_signe'])) { ?>
		<a onclick="<? //= fonctionsJs::facture_create(array( 'iddevis' => $arr['iddevis'] , 'idclient' => $arr['idclient'] )) ?>">
			<img src="<?= ICONPATH ?>euro16.png"/>
			&nbsp;Faire une facture
		</a>
	<? } ?>
</div>
