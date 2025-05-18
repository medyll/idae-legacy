<?
	include_once($_SERVER['CONF_INC']);

	$path_to_devis = 'business/cruise/app/devis/';

	$APP      = new App('devis');
	$APP_CLI  = new App('client');
	$APP_TYPE = new App('devis_type');

	$APP->init_scheme('sitebase_devis', 'devis_prestation');
	$APP->init_scheme('sitebase_devis', 'devis_acompte');
	$APP->init_scheme('sitebase_devis', 'devis_passager');
	$APP->init_scheme('sitebase_devis', 'devis_annotation');
	$APP->init_scheme('sitebase_devis', 'devis_envie');
	$APP->init_scheme('sitebase_devis', 'devis_mail');

	$APP_PREST = new App('devis_prestation');

	$time = time();

	$iddevis  = (int)$_POST['iddevis'];
	$arr      = $APP->findOne(['iddevis' => (int)$iddevis]);
	$arr_type = $APP->findOne(['iddevis_type' => (int)$arr['iddevis_type']]);
	// vardump($arr);
	$table       = 'devis';
	$Table       = ucfirst($table);
	$table_value = $iddevis;
	//
	$idproduit             = $arr['idproduit'];
	$idproduit_tarif       = (int)$arr['idproduit_tarif'];
	$idproduit_tarif_gamme = (int)$arr['idproduit_tarif_gamme'];
	$idclient              = $arr['idclient'];
	$idagent               = (int)$arr['idagent'];
	//
	$arr_client = $APP_CLI->findOne(['idclient' => (int)$idclient]);
	// spy_code
	$round_numerator      = 60 * 5;
	$rounded_time         = (round(time() / $round_numerator) * $round_numerator);
	$upd                  = [];
	$upd['codeActivite']  = 'OUVERTURE_DEVIS_UPDATE';
	$upd['timeActivite']  = (int)$rounded_time;
	$upd['dateActivite']  = date('Y-m-d', $rounded_time);
	$upd['heureActivite'] = date('H:i:s', $rounded_time);
	$upd['idagent']       = (int)$_SESSION['idagent'];
	$upd['iddevis']       = (int)$iddevis;

	//
	$TEST_PREST = $APP_PREST->find(['iddevis' => $iddevis]);
	if ($TEST_PREST->count() == 0) {
		// 5 prestations
		skelMdl::runModule('app/actions', ['F_action' => 'app_multi_create', 'occurence' => 5, 'table' => 'devis_prestation', 'vars' => ['iddevis' => $iddevis, 'quantiteDevis_prestation' => 1, 'prixDevis_prestation' => 0]]);
	}

	$uniqkey = $table . $table_value;

	/*$rscorrect = $APP_CLI->find();
	while ($arrcorrect = $rscorrect->getNext()) {
		$email = '';
		if (is_array($arrcorrect['emailClient'])) {
			if (!empty($arrcorrect['emailClient']['emailClient'])) {
				$email = $arrcorrect['emailClient']['emailClient'];
			} else if (!empty($arrcorrect['emailClient']['emailDevis'])) {
				$email = $arrcorrect['emailClient']['emailDevis'];
			}
			if (!empty($email)) {
				$APP_CLI->update(['idclient' => (int)$arrcorrect['idclient']], ['emailClient' => strtolower($email)]);
			}
		}
	}*/
?>
<? if ($arr_type['codeDevis_type'] != 'STE'): ?>
	<script>
		load_table_in_zone ('table=devis_acompte&vars[iddevis]=<?=$iddevis?>', 'zone_a<?=$iddevis?>');
		load_table_in_zone ('table=devis_passager&vars[iddevis]=<?=$iddevis?>', 'zone_pas<?=$iddevis?>');
	</script>
<? endif; ?>
<div class="blanc flex_h" style="overflow:hidden;width:100%;height:100%;" id="mainDevis">
	<div class="frmCol1  applink applinkblock">
		<div class="alignright padding borderb flex_h">
			<a onclick="<?= fonctionsJs::app_fiche('devis', $table_value) ?>"><i class="fa fa-file"></i></a>
			<a onclick="<?= fonctionsJs::app_fiche_maxi('devis', $table_value) ?>"><i class="fa fa-file"></i></a>
			<a onclick="reloadModule('<?= $_POST['module'] ?>','*')"><i class="fa fa-refresh"></i></a>
		</div>
		<div class="applink applinkblock applinkbig borderb">
			<a onclick="$('build<?= $uniqkey ?>').toggleContent()"><i class="fa fa-info-circle"></i> <?= idioma('Détails') ?> </a>
			<a onclick="$('preview<?= $uniqkey ?>').toggleContent()"><i class="fa fa-eye"></i> <?= idioma('Visualiser version client') ?> </a>
		</div>
		<? //  if ($arr_type['codeDevis_type'] != 'STE'): ?>
			<br>
			<a onclick="act_chrome_gui('app/app_custom/mail/mail_send','iddevis=<?= $arr['iddevis'] ?>')">
				<i class="fa fa-at"></i>&nbsp;Envoyer un mail
			</a>
			<br>
		<a onclick="act_chrome_gui('app/app_custom/mail/mail_send','iddevis=<?= $arr['iddevis'] ?>&confirm=true')">
			<i class="fa fa-at"></i>&nbsp;Envoyer un mail de confirmation
		</a>
			<div class="applink applinkblock">
				<?= skelMdl::cf_module($path_to_devis . 'devis_make_nav', ['scope' => 'iddevis', 'iddevis' => $iddevis], $iddevis) ?>
			</div>
			<br>
			<div class="margin border4 padding">
				<div class="padding ededed border4"><i class="fa fa-wrench"></i>&nbsp;<?= idioma('Ajouter') ?></div>
				<div class="retrait">
					<a act_chrome_gui="app/app/app_create" vars="table=devis_prestation&vars[iddevis]=<?= $iddevis ?>" options="{scope:'devis_prestation'}">
						<?= idioma('Prestation') ?>
					</a>
					<a act_chrome_gui="app/app/app_create" vars="table=devis_acompte&vars[iddevis]=<?= $iddevis ?>" options="{scope:'devis_acompte'}">
						<?= idioma('Acompte') ?>
					</a>
					<a act_chrome_gui="app/app/app_create" vars="table=devis_passager&vars[iddevis]=<?= $iddevis ?>" options="{scope:'devis_passager'}">
						<?= idioma('Passager') ?>
					</a>
					<a act_chrome_gui="app/app/app_create" vars="table=devis_annotation&vars[iddevis]=<?= $iddevis ?>&vars[idagent]=<?= $idagent ?>">
						<?= idioma('Annotation') ?>
					</a>
				</div>
			</div>
		<? // endif; ?>
		<div>
			<div class="padding margin">
				<div style="position:relative;width:100px;" id="drag_perso">
					<a class="cursor inline relative" style="overflow:hidden;width:140px">
						<i class="fa fa-upload"></i> <?= idioma('document') ?>
						<input name="file" id="file" class="cursor inline" type="file" style="opacity:0;position:absolute;left:0;top:0;z-index:0;"/>
					</a>
				</div>
			</div>
			<form novalidate id="form_upload_<?= $table ?>" action="mdl/app/app_document/actions.php" onsubmit="ajaxFormValidation(this);return false">
				<input type="hidden" name="F_action" value="addDoc"/>
				<input type="hidden" name="base" value="sitebase_ged"/>
				<input type="hidden" name="collection" value="ged_bin"/>
				<input type="hidden" name="multiple" value="1"/>
				<input type="hidden" name="vars[idagent_owner]" value="<?= $_SESSION['idagent'] ?>"/>
				<input type="hidden" name="vars[table]" value="<?= $table ?>"/>
				<input type="hidden" name="vars[table_value]" value="<?= $table_value ?>"/>
				<input type="hidden" name="vars[<?= $name_id ?>]" value="<?= $table_value ?>"/>
				<input type="hidden" name="table" value="<?= $table ?>"/>
				<input type="hidden" name="table_value" value="<?= $table_value ?>"/>
				<input type="hidden" name="reloadModule[app/app/app_fiche_rfk]" value="<?= $table_value ?>"/>
			</form>
			<div id="pref_preview_<?= $table ?>" class="aligncenter ededed" style="overflow:auto"></div>
			<style>
				#pref_preview img {
					max-width : 150px;
				}
			</style>
			<script>
				// new myddeAttach($('fiche_table_<?=$table?>_<?=$table_value?>'), {form: 'form_upload_<?= $table ?>', autoSubmit: true});
			</script>
		</div>
	</div>
	<div class="flex_main flex_v relative" style="overflow:hidden;" id='maindevisresie<?= $iddevis ?>'>
		<div class="uppercase bold ededed   titre_entete" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
			Devis n°<?= $iddevis ?> <?= $APP->draw_field(['field_name_raw' => 'nomClient', 'table' => 'devis', 'field_value' => $arr['nomClient']]) ?> <?= $APP->draw_field(['field_name_raw' => 'nomFournisseur', 'table' => 'devis', 'field_value' => $arr['nomFournisseur']]) ?> | Prix
			<?= $APP->draw_field(['field_name_raw' => 'prix', 'table' => 'devis', 'field_value' => $arr['prixDevis']]) ?>
		</div>
		<div class="barre_entete">
			<?= idioma('agent') ?> <?= $APP->draw_field(['field_name' => 'nomAgent', 'table' => 'devis', 'field_value' => $arr['nomAgent']]) ?> <?= $APP->draw_field(['field_name_raw' => 'dateCreation', 'table' => 'devis', 'field_value' => $arr['dateCreationDevis']]) ?>
		</div>
		<div class="relative flex_main flex_h flex_wrap flex_align_top" style="overflow:auto;">
			<div class="relative margin    inline" style="width:720px;">
				<div class="border4 margin_moreb">
					<div class="titre_entete bold"><i class="fa fa-euro"></i><?= idioma('Commentaires au client') ?></div>
					<div class=" " act_defer mdl="<?= $path_to_devis ?>devis_service_update" vars="field=texte&table=devis&table_value=<?= $iddevis ?>">
					</div>
				</div>
				<div class="border4 margin_moreb">
					<div class="titre_entete bold"><i class="fa fa-euro"></i><?= idioma('Devis prestations') ?></div>
					<div class="blanc" app_gui_explorer>
						<div class="padding borderb">
							<table class="table table-bordered bold" style="width:100%;">
								<tr>
									<td>
										<a onclick="ajaxValidation('app_multi_create','mdl/app/','occurence=1&table=devis_prestation&vars[iddevis]=<?= $iddevis ?>')"><i class="fa fa-plus"></i></a>
									</td>
									<td style="width:40px;">Qte</td>
									<td style="width:80px;">Prix</td>
									<td style="width:80px;">Total</td>
									<td style="width:40px;"><i class="fa fa-times textgris"></i></td>
								</tr>
							</table>
						</div>
						<div data-dsp_liste="true" data-vars="table=devis_prestation&vars[iddevis]=<?= $table_value ?>" data-dsp="mdl" data-dsp-mdl="<?= $path_to_devis ?>devis_prestation_update" data-sort="true" class=" relative margin">
						</div>
						<div class="padding" data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
							<table class="table table-bordered" style="width:100%;">
								<tr>
									<td class="alignright">Total :
									</td>
									<td style="width:80px;" class="bold"><?= $APP->draw_field(['field_name_raw' => 'prix', 'table' => 'devis', 'field_value' => $arr['prixDevis']]) ?></td>
									<td style="width:40px;"></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div>
					<div class="relative flex_main flex_h flex_wrap flex_align_top" style="overflow:auto;">
						<div class="relative margin flex_main border4">
							<div class="titre_entete bold"><i class="fa fa-euro"></i><?= idioma('Inclus') ?></div>
							<div class="padding" act_defer mdl="<?= $path_to_devis ?>devis_service_update" vars="field=inclus&table=devis&table_value=<?= $iddevis ?>">
							</div>
						</div>
						<div class="relative margin flex_main border4">
							<div class="titre_entete bold"><i class="fa fa-euro"></i><?= idioma('Non inclus') ?></div>
							<div class="padding" act_defer mdl="<?= $path_to_devis ?>devis_service_update" vars="field=nonInclus&table=devis&table_value=<?= $iddevis ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="relative flex_inline flex_wrap flex_h" style="width:720px;">
				<div class="relative margin   inline border4" style="width:720px;">
					<div class="titre_entete bold"><i class="fa fa-calendar-o"></i><?= idioma('Echéancier des paiements') ?></div>
					<div class=" retraitblanc" app_gui_explorer>
						<div class="padding borderb">
							<table class="table table-bordered" style="width:100%;">
								<tr>
									<td style="width:120px;">Date
										<a onclick="ajaxValidation('app_multi_create','mdl/app/','occurence=2&table=devis_acompte&vars[iddevis]=<?= $iddevis ?>')"><i class="fa fa-plus"></i></a>
									</td>
									<td style="width:120px;">Montant <i class="fa fa-euro"></i></td>
									<td style="width:120px;">Type</td>
									<td class="alignright"><i class="fa fa-times textgris"></i></td>
								</tr>
							</table>
						</div>
						<div id="zone_a<?= $iddevis ?>" data-dsp="mdl" data-dsp-mdl="<?= $path_to_devis ?>devis_acompte_update" class="relative margin">
						</div>
					</div>
				</div>
				<div class="relative margin   inline border4" style="width:720px;">
					<div class="titre_entete bold"><i class="fa fa-users"></i><?= idioma('Liste des passagers') ?></div>
					<div class="retrait blanc">
						<div class="padding borderb">
							<table class="table table-bordered" style="width:100%;">
								<tr>
									<td style="width:120px;">Nom
										<a onclick="ajaxValidation('app_multi_create','mdl/app/','occurence=2&table=devis_passager&vars[iddevis]=<?= $iddevis ?>')"><i class="fa fa-plus"></i></a>
									</td>
									<td style="width:120px;">Prénom</td>
									<td style="width:120px;">Email</td>
									<td class="alignright"><i class="fa fa-times textgris"></i></td>
								</tr>
							</table>
						</div>
						<div id="zone_pas<?= $iddevis ?>" data-dsp="mdl" data-dsp-mdl="<?= $path_to_devis ?>devis_passager_update" class=" relative margin">
						</div>
					</div>
				</div>
			</div>
			<div class="relative margin   inline" style="width:720px;">
				<div data-dsp_liste="true" data-vars="table=devis_marge&vars[iddevis]=<?= $table_value ?>" data-dsp="mdl" data-dsp-mdl="<?= $path_to_devis ?>devis_prestataire_ligne" class="ededed relative margin">
				</div>
			</div>
			<div data-dsp_liste="true" data-vars="table=devis_annotation&vars[iddevis]=<?= $table_value ?>" data-dsp="table_line" class="relative margin"></div>
		</div>
	</div>
	<div class="borderl boxshadow padding_more ededed">
		<div id="build<?= $uniqkey ?>" class="padding_more flex_main  " data-table="<?= $table ?>" data-table_value="<?= $table_value ?>">
			<br>
			<div class="blanc border4 padding_more boxshadow">
				<div class="alignright padding borderb flex_h">
					<div class="bold"><?= idioma('Client') ?></div>
					<div class="flex_main">
						<a onclick="<?= fonctionsJs::app_update('client', $idclient) ?>"><i class="fa fa-clone"></i> <?= idioma('mettre à jour') ?></a>
					</div>
				</div>
				<div class="retrait" data-table="client"  data-table_value="<?=$idclient?>">
					<?= $APP->draw_field(['field_name_raw' => 'prenomClient', 'table' => 'client', 'field_value' => $arr_client['prenomClient']]) ?>
					<?= $APP->draw_field(['field_name_raw' => 'nomClient', 'table' => 'client', 'field_value' => $arr_client['nomClient']]) ?>
					<br>
					<?= $APP->draw_field(['field_name_raw' => 'nomClient', 'table' => 'client', 'field_value' => $arr_client['telephoneClient']]) ?>
					<?= $APP->draw_field(['field_name_raw' => 'nomClient', 'table' => 'client', 'field_value' => $arr_client['emailClient']]) ?>
				</div>
				<br>
				<br>
				<div class="alignright padding borderb flex_h">
					<div class="bold"><?= idioma('Croisière') ?> <?= $arr['idproduit'] ?></div>
					<div class="flex_main">
						<a onclick="ajaxMdl('<?= $path_to_devis ?>devis_make_update','Modification devis <?= $iddevis ?>','iddevis=<?= $iddevis ?>');"><i class="fa fa-clone"></i> <?= idioma('modifier') ?></a>
					</div>
				</div>
				<div class="retrait">
					<?= $APP->draw_field(['field_name_raw' => 'nomProduit', 'table' => 'devis', 'field_value' => $arr['nomProduit']]) ?>
				</div>
				<div class="retrait">
					<div class="bold"><?= idioma('Fournisseur') ?></div>
					<?= $APP->draw_field(['field_name_raw' => 'nomFournisseur', 'table' => 'devis', 'field_value' => $arr['nomFournisseur']]) ?>
					<br>
					<?= $APP->draw_field(['field_name_raw' => 'nomTransport', 'table' => 'devis', 'field_value' => $arr['nomTransport']]) ?>
				</div>
				<br>
				<br>
				<div class="alignright padding borderb flex_h">
					<div class="bold">Date de départ</div>
					<div class="flex_main">
						<a onclick="ajaxMdl('<?= $path_to_devis ?>devis_make_update','Modification devis <?= $iddevis ?>','iddevis=<?= $iddevis ?>');"><i class="fa fa-clone"></i> <?= idioma('modifier') ?></a>
					</div>
				</div>
				<div class="retrait">
					<?= $APP->draw_field(['field_name_raw' => 'dateDebut', 'table' => 'devis', 'field_value' => $arr['dateDebutDevis']]) ?></div>
				<div class="retrait">
					<div class="bold">Passagers</div>
					<?= $APP->draw_field(['field_name_raw' => 'nbreAdulte', 'table' => 'devis', 'field_value' => $arr['nbreAdulteDevis']]) ?> adultes,
					<?= $APP->draw_field(['field_name_raw' => 'nbreEnfant', 'table' => 'devis', 'field_value' => $arr['nbreEnfantDevis']]) ?> enfants
				</div>
				<div class="retrait">
					<div class="bold">Cabine</div>
					<?= $APP->draw_field(['field_name_raw' => 'cabine', 'table' => 'devis', 'field_value' => $arr['cabineDevis']]) ?>
				</div>
			</div>
		</div>
		<div id="preview<?= $uniqkey ?>" class="flex_v relative border4 boxshadow" style="display:none;height:100%;overflow:hidden;">
			<div class="padding applink  borderb">
				<div class="inline padding ededed">
					<a onclick="$('preview<?= $uniqkey ?>').unToggleContent()"><i class="fa fa-times textrouge"></i> <?= idioma('Fermer') ?></a>
					<a onclick="runModule('mdl/dyn/dyn_devis_html','iddevis=<?= $iddevis ?>')"><i class="fa fa-cogs textbleu"></i> Reconstruire</a>
				</div>
			</div>
			<div class="flex_main" style="width:100%;overflow:auto;" act_defer mdl="<?= $path_to_devis ?>devis_preview_inner" vars="iddevis=<?= $iddevis ?>" value="<?= $iddevis ?>"></div>
		</div>
	</div>
</div>
<script>
	// var redt = new resizeGui($('maindevisresie<?=$iddevis?>'));
</script>
