<?
	include_once($_SERVER['CONF_INC']);
	$APP      = new App('promo_zone');
	$APP_ITEM = new App('promo_zone_item');

	$uniqid        = uniqid();
	$nomPromo_zone = '';
	$idpromo_zone  = (int)$_POST['idpromo_zone'];
	$arr           = $APP->query_one(['idpromo_zone' => (int)$idpromo_zone]);
	$nomPromo_zone = $arr['nomPromo_zone'];

	unset($_SESSION['blockid']);
	// $idpromo_zone = (!empty($idpromo_zone))? $idpromo_zone : rand(1,1000).time() ;
	$action = (!empty($idpromo_zone)) ? 'updatePromo_zones' : 'createPromo_zones';
	$titre  = (!empty($idpromo_zone)) ? 'Mise a jour' : 'Cr&eacute;ation';
	//

	$arrTag = ['idproduit' => 'mdl_idproduit', 'titre' => 'mdl_titre', 'Atout' => 'mdl_sstitre', 'Description' => 'mdl_description', 'prix' => 'mdl_prix', 'url' => 'mdl_url'];
	//
	$formSearch = 'u' . uniqid();
?>
<div class="applink applinkblock" style="overflow:auto;">
	<div class="uppercase bold titre_entete">
		<?= idioma('Liste des modules') ?>
	</div>
	<a onclick="ajaxValidation('create_block_promo_zone','mdl/app/app_promo_zone/','idpromo_zone=<?= $idpromo_zone ?>')"><i class="fa fa-plus"></i>&nbsp;Ajouter un module</a>
	<div class="relative">
		<div  data-dsp_liste="dsp_list" data-vars="table=promo_zone_item&vars[idpromo_zone]=<?= $idpromo_zone ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_thumb"
		     style="max-height:100%;">
		</div>
		<div class="toggler" id="<?= $formSearch ?>">
			<? if (!empty($idpromo_zone)) {

				$rs_item = $APP_ITEM->find(['idpromo_zone' => (int)$idpromo_zone]);
				while ($arr_item = $rs_item->getNext()):
					$idpromo_zone_item  = (int)$arr_item['idpromo_zone_item'];
					$uid_grille_block   = $arr_item['uid_grille_block'];
					$nomPromo_zone_item = $arr_item['nomPromo_zone_item'];
					$type               = $arr_item['type'];
					?>
					<div class="relative table tablemiddle" style="width:100%;" sort_zone="sort_zone">
						<div class="cell">
							<div class="relative" style="width: 160px;">
								<div class="autoNext nude ellipsis" id="element_<?= $id ?>" value="<?= $id ?>" act_spy='<?= $uid_grille_mdl ?>'>
									<?= $nomPromo_zone_item ?>..
								</div>
								<div class="autoBlock" style="display:none;">
									<? foreach ($arrTag as $key_tag => $key_mdl): ?>
										<a onclick="ajaxMdl('app/app_promo_zone/app_promo_zone_build_module_update','Mise à jour vignette <?= $uid_grille_mdl ?>','idpromo_zone=<?= $idpromo_zone ?>&idpromo_zone_item=<?= $idpromo_zone_item ?>&uid_grille_mdl=<?= $uid_grille_mdl ?>&uid_grille_block=<?= $uid_grille_block ?>&key_tag=<?= $key_tag ?>&key_mdl=<?= $key_mdl ?>');">
											<? if (!empty($value2[$key_mdl])) { ?>
												<li class="fa fa-check"></li><? } ?>
											&nbsp; <span act_spy='<?= $uid_grille_mdl ?><?= $key_mdl ?>'>
                  <?= $key_tag ?>
                  </span> &nbsp; <span act_spy='<?= $uid_grille_mdl ?><?= $key_mdl ?>ouinon'>
                  <?= ouiNon(!empty($value2[$key_mdl])); ?>
                  </span></a>
									<? endforeach; ?>
								</div>
							</div>
						</div>
						<div class="cell alignright" style="width:40px;vertical-align:top;">
							<div class="padding">
								<i class="fa fa-chevron-up sortprevious"></i>
								<i class="fa fa-chevron-down sortnext"></i>
							</div>
						</div>
					</div>
					<?
				endwhile;

				$arr         = $APP->query_one(['idpromo_zone' => $idpromo_zone]);
				$content     = unserialize($arr['content']);
				$has_footer  = false;
				$grilleBlock = empty($arr['grilleBlock']) ? [] : $arr['grilleBlock'];

				foreach ($grilleBlock as $key => $value):
					$uid_grille_block   = $value['uid_grille_block'];
					$nomPromo_zone_item = $value['nomPromo_zone_item'];
					$type               = $value['type'];
					?>
					<div draggable dropzone="move" sortable="sortable" class="sort_uidblock" mdl="uidblock" value='<?= $uid_grille_block ?>'>
						<div class="autoNext nude active ellipsis uppercase" act_spy='<?= $uid_grille_block ?>'>
							<?= $nomPromo_zone_item ?>
							<?= $type ?>
						</div>
						<div class="autoBlock">
							<? foreach ($value['vignette'] as $key2 => $value2):
								$uid_grille_mdl = $value2['uid_grille_mdl'];
								$md_titre       = empty($value2['mdl_titre']) ? 'Sans titre' : $value2['mdl_titre'];
								?>
								<div class="relative table tablemiddle" style="width:100%;" sort_zone="sort_zone">
									<div class="cell">
										<div class="relative" style="width: 160px;">
											<div class="autoNext nude ellipsis" id="element_<?= $id ?>" value="<?= $id ?>" act_spy='<?= $uid_grille_mdl ?>'>
												<?= $md_titre ?>
											</div>
											<div class="autoBlock" style="display:none;">
												<? foreach ($arrTag as $key_tag => $key_mdl): ?>
													<a onclick="ajaxMdl('app/app_promo_zone/app_promo_zone_build_module_update','Mise à jour vignette <?= $uid_grille_mdl ?>','idpromo_zone=<?= $idpromo_zone ?>&uid_grille_mdl=<?= $uid_grille_mdl ?>&uid_grille_block=<?= $uid_grille_block ?>&key_tag=<?= $key_tag ?>&key_mdl=<?= $key_mdl ?>');">
														<? if (!empty($value2[$key_mdl])) { ?>
															<li class="fa fa-check"></li><? } ?>
														&nbsp; <span act_spy='<?= $uid_grille_mdl ?><?= $key_mdl ?>'>
                  <?= $key_tag ?>
                  </span> &nbsp; <span act_spy='<?= $uid_grille_mdl ?><?= $key_mdl ?>ouinon'>
                  <?= ouiNon(!empty($value2[$key_mdl])); ?>
                  </span></a>
												<? endforeach; ?>
											</div>
										</div>
									</div>
									<div class="cell alignright" style="width:40px;vertical-align:top;">
										<div class="padding">
											<li class="fa fa-chevron-up sortprevious"></li>
											<li class="fa fa-chevron-down sortnext"></li>
										</div>
									</div>
								</div>
							<? endforeach; ?>
						</div>
					</div>
				<? endforeach;
			} ?>
		</div>
	</div>
</div>
