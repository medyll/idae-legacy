<?
	include_once($_SERVER['CONF_INC']);
	$APP          = new App('produit');
	$APP_PREST    = new App('devis_prestation');
	$APP_PASS     = new App('devis_passager');
	$APP_DEVIS    = new App('devis');
	$APP_DEVIS_AC = new App('devis_acompte');
	$APP_TG       = new App('produit_tarif_gamme');
	$APP_DATE     = new App('produit_tarif');
	$APP_TPG      = new App('transport_gamme');
	$APP_CL       = new App('client');
	ini_set('display_errors', 0);
	$uniqid    = uniqid();
	$idproduit = (int)$_POST['vars']['idproduit'];
	$idclient  = (int)$_POST['vars']['idclient'];

	if (empty($_POST['vars']['idproduit']) || empty($_POST['vars']['idproduit_tarif']) || empty($_POST['vars']['idproduit_tarif_gamme']) || empty($_POST['vars']['idclient'])) {

		?>
		<div class="titre_entete">
			<i class="fa fa-warning fa-2x"></i>
		</div>
		<? if (empty($_POST['vars']['idproduit'])) { ?>
			<div class="titre_entete ">
				<?= idioma('Produit manquant') ?>
			</div>
		<? } ?>
		<? if (empty($_POST['vars']['idproduit_tarif'])) { ?>
			<div class="titre_entete ">
				<?= idioma('Date de départ manquante') ?>
			</div>
		<? } ?>
		<? if (empty($_POST['vars']['idproduit_tarif_gamme'])) { ?>
			<div class="titre_entete ">
				<?= idioma('Cabine manquante') ?>
			</div>
		<? } ?>
		<? if (empty($_POST['vars']['idclient'])) { ?>
			<div class="titre_entete ">
				<?= idioma('Client manquant') ?>
			</div>
		<? } ?>
		<div class="titre_entete">
			<button onclick="$('div_devis_create_wait').hide()">Retour</button>
		</div>
		<?
		return;
	}

	$arr = $APP->query_one(array('idproduit' => $idproduit));

	if (!empty($_POST['vars']['idproduit_tarif_gamme']) && empty($_POST['vars']['idtransport_gamme'])):
		$z                                  = $APP_TG->query_one(array('idproduit_tarif_gamme' => (int)$_POST['vars']['idproduit_tarif_gamme']));
		$_POST['vars']['idtransport_gamme'] = (int)$z['idtransport_gamme'];
		$_POST['vars']['cabineDevis']       = $z['nomProduit_tarif_gamme'];
	endif;
	$_POST['vars']['nomProduit']     = $arr['nomProduit'];
	$_POST['vars']['nomFournisseur'] = $arr['nomFournisseur'];
	$_POST['vars']['nomTransport']   = $arr['nomTransport'];

	$arrdate                            = $APP_DATE->query_one(array('idproduit_tarif' => (int)$_POST['vars']['idproduit_tarif']));
	$_POST['vars']['dateDebutDevis']    = $arrdate['dateDebutProduit_tarif'];
	$_POST['vars']['dateCreationDevis'] = date('Y-m-d');

	if (!empty($idclient)):
		$z                          = $APP_CL->query_one(array('idclient' => (int)$idclient));
		$_POST['vars']['nomClient'] = $z['nomClient'] . ' ' . $z['prenomClient'];
	endif;
	$_POST['vars']['codeDevis'] = $arr['codeProduit_type'] . $idclient . $arr['codeFournisseur'];
	$_POST['vars']['nomDevis']  = $z['nomClient'] . ' ' . $z['prenomClient'] . ' ' . $arr['nomFournisseur'] . ' ' . $arr['nomDestination'] . ' ' . $arr['nomTransport'];

	$vars = fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$iddevis = $APP_DEVIS->insert($vars);
	//
	$TEST_PREST = $APP_PREST->find(['iddevis' => $iddevis]);
	if ($TEST_PREST->count() == 0) {
		skelMdl::runModule('app/actions', ['F_action' => 'app_multi_create', 'occurence' => 5, 'table' => 'devis_prestation', 'vars' => ['iddevis' => $iddevis, 'quantiteDevis_prestation' => 1, 'prixDevis_prestation' => 0]]);
	}
	//
	$TEST_PASS = $APP_PASS->find(['iddevis' => $iddevis]);

	if ($TEST_PASS->count() == 0) {
		$tot = (int)$vars['nbreAdulteDevis'] + (int)$vars['nbreEnfantDevis'];
		skelMdl::runModule('app/actions', ['F_action' => 'app_multi_create', 'occurence' => $tot, 'table' => 'devis_passager', 'vars' => ['iddevis' => $iddevis]]);
	}

	skelMdl::runModule('app/actions', ['F_action' => 'app_multi_create', 'occurence' => 2, 'table' => 'devis_acompte', 'vars' => ['iddevis' => $iddevis]]);

	$link = "ajaxInMdl('business/" . BUSINESS . "/app/devis/devis_make','div_dev_upd_" . $iddevis . "','iddevis=$iddevis',{value: $iddevis ,ident:'dev_upd_$iddevis',onglet:'Devis " . $iddevis . ' ' . niceUrl($arrClient['prenomClient'] . ' ' . $arrClient['nomClient']) . "'});";

?>
<div class="titre_entete">
	<?= idioma('Génération du devis') . ' ' . $iddevis ?>
</div>
<br>
<div class="padding  aligncenter margin " id="laucher<?= $uniqid ?>">
	<a onClick="<?= $link ?>">
		&nbsp;
		<?= idioma('Devis disponible') ?>
		<br>
		<?= $iddevis ?>
	</a>
</div>
<div>
	<div class="loading  aligncenter  margin   padding">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
</div>
<script>
	<?
	if(empty($_POST['vars']['idproduit']) || empty($_POST['vars']['idproduit_tarif']) || empty($_POST['vars']['idproduit_tarif_gamme']) ||   empty($_POST['vars']['idclient'])){
			 ?>

	<? }else{ ?>
	setTimeout(function () {
		//	ajaxValidation('app_create', 'mdl/app/', 'table=devis&<?=$APP->translate_vars($_POST['vars'])?>');
	}.bind(this), 2500);
	<? } ?>
</script>