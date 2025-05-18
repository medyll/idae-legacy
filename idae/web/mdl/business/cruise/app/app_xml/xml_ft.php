<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	ini_set("default_socket_timeout", 360);
	//
	$query_vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	$page = empty($_POST['page']) ? 0 : $_POST['page'];
	//
	$APP = new App('feed_header');

	$col_F     = $APP->plug('sitebase_production', 'fournisseur');
	$col_debug = $APP->plug('sitebase_xml', 'xml_cruise_debug');
	//
	$col_XD = $APP->plug('sitebase_xml', 'xml_destination');
	$col_XV = $APP->plug('sitebase_xml', 'xml_ville');

	// DROP
	$col_debug->drop();
	//

	$rsApp = $APP->query($query_vars + array('estActifFeed_header' => 1))->sort(array('nomFeed_header' => 1));
	//
?>
<div class="flex_v">
	<div class="titre_entete applink">
		<a onclick="runModule('mdl/business/<?= BUSINESS ?>/app/app_xml/xml_ftp')"><i class="fa fa-cloud-download"></i> <?= idioma('Tout télécharger') ?></a>
	</div>
	<br>
	<br>
	<div class="flex_h flex_align_top flex_main" style="overflow:auto;">
		<br>
		<div class="retrait">
			<?
				while ($arrApp = $rsApp->getNext()):
					$CODE_FOURNISSEUR = $arrApp['codeFeed_header'];
					$idfournisseur    = (int)$arrApp['idfournisseur'];
					$CRUISELINE       = $arrApp['codeFeed_header'];
					$arr_F            = $col_F->findOne(array('idfournisseur' => $idfournisseur));
					$nomFournisseur   = $arr_F['nomFournisseur'];
					$rs_XD            = $col_XD->find(array('idfournisseur' => $idfournisseur))->sort(['nomXml_destination']);
					?>
					<div class="padding ededed bold border4" style="position: sticky;top:0em;z-index:100">
						<?= $CRUISELINE ?> <?= $APP->draw_field(['field_name_raw' => 'dateRun', 'table' => 'feed_header', 'field_value' => date_fr($arrApp['dateRunFeed_header'])]) ?>
					</div>
					<div style="max-height:450px;overflow:auto;">
						<progress style="display: none;" id="auto_iti_<?= $CRUISELINE ?>"></progress>
					</div>
					<div class="flex_h">
						<div style="width:100px;" class="padding aligncenter applink" table="feed_header" table_value="<?= $arrApp['idfeed_header'] ?>">
							<a onclick="runModule('mdl/business/<?= BUSINESS ?>/app/app_xml/xml_ftp','session_id=<?= session_id(); ?>&vars[idfournisseur]=<?= $idfournisseur ?>')"><i class="fa fa-cloud-download"></i>

								<br><?= $CRUISELINE ?>
								<br><?= idioma('Lancer') ?></a>
							<? //=Act::imgApp('fournisseur',$idfournisseur,'tiny')
							?>
						</div>
						<div class="flex_main">
							<div class="flex_h flex_wrap">
								<?
									// pour chaque destination de fournisseur GeoCode
									$i = 0;
									while ($arr_XD = $rs_XD->getNext()):
										//
										$i++;
										$XD            = $arr_XD['codeXml_destination'];
										$PROGRESS_NAME = str_replace(' ', '', $CRUISELINE . $arr_XD['codeXml_destination']);
										$PROGRESS_NAME = str_replace('-', '_', $PROGRESS_NAME);
										?>
										<div class="relative flex_main" table="xml_destination" table_value="<?= $arr_XD['idxml_destination'] ?>" style="min-width:33%;max-width:33%;width:33%;overflow:hidden;max-height:350px;">
											<div style="z-index:1;" class="relative flex_h flex_align_middle flex_main">
												<div class=" padding bold" style="width:50px;overflow:hidden">
													<?= $XD ?>
												</div>
												<div class=" padding" style="width:200px;overflow:hidden">
													<div class="ellipsis"><?= strtolower($arr_XD['nomXml_destination']) ?></div>
												</div>
												<div class="flex_h flex_align_middle">
													<div class="flex_h flex_align_middle">
														<div class="none" style="width:100px;">
															<?= $APP->draw_field(['field_name_raw' => 'dateRun', 'field_name' => 'dateRunXml_destination', 'table' => 'xml_destination', 'field_value' => $arr_XD['dateRunXml_destination']]) ?>
														</div>
														<div class="none" style="width:100px;">
															<?= $APP->draw_field(['field_name_raw' => 'heureRun', 'table' => 'xml_destination', 'field_value' => $arr_XD['heureRunXml_destination']]) ?>
														</div>
														<a onclick="runModule('mdl/business/<?= BUSINESS ?>/app/app_xml/xml_ftp_in','session_id=<?= session_id(); ?>&vars[CruiseLine]=<?= $CRUISELINE ?>&vars[GeoCode]=<?= $XD ?>&PROGRESS_NAME=<?= $PROGRESS_NAME ?>')">Lancer</a>
													</div>
												</div>
											</div>
											<div style="position:relative;top:0;left: 0;width:100%;z-index: 0;">
												<progress style="display: none;color:red" id="auto_<?= $PROGRESS_NAME ?>"></progress>
											</div>
										</div>
									<? endwhile; ?>
							</div>
						</div>
					</div>
				<? endwhile; ?>
		</div>
		<div style="overflow:hidden;position:sticky;">
			<div class="borderl ededed" style="position:sticky;height:100%;overflow:auto;width:350px;">
				<?
					$rsApp->reset();
					while ($arrApp = $rsApp->getNext()):
						$CODE_FOURNISSEUR = $arrApp['codeFeed_header'];
						$idfournisseur    = (int)$arrApp['idfournisseur'];
						$CRUISELINE       = $arrApp['codeFeed_header'];
						$arr_F            = $col_F->findOne(array('idfournisseur' => $idfournisseur));
						$nomFournisseur   = $arr_F['nomFournisseur'];
						$rs_XD            = $col_XD->find(array('idfournisseur' => $idfournisseur))->sort(['nomXml_destination']);
						?>
						<div class="margin padding bordert">
							<div class="padding borderb bold" style="position: sticky;top:0em;z-index:100">
								<?= $CRUISELINE ?> <?= $APP->draw_field(['field_name_raw' => 'dateRun', 'table' => 'feed_header', 'field_value' => date_fr($arrApp['dateRunFeed_header'])]) ?>
							</div>
							<?
								// pour chaque destination de fournisseur GeoCode
								$i = 0;
								while ($arr_XD = $rs_XD->getNext()):
									//
									$i++;
									$XD            = $arr_XD['codeXml_destination'];
									$PROGRESS_NAME = str_replace(' ', '', $CRUISELINE . $arr_XD['codeXml_destination']);
									$PROGRESS_NAME = str_replace('-', '_', $PROGRESS_NAME);
									?>
									<div class="retrait">
										<div style="position:relative;z-index: 0;">
											<progress style="display: none;color:red" id="auto_<?= $PROGRESS_NAME ?>_panel"></progress>
										</div>
									</div>
									<?

								endwhile;
							?>
						</div>
						<?
					endwhile; ?></div>
		</div>
	</div>
</div>
