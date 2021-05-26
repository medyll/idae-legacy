<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$time = time();
	$i    = 0;
	//
	$APP  = new App();
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$dateStart = new DateTime();

	$default = empty($_POST['periode_default']) ? 'semaine' : $_POST['periode_default'];

	$DADATE               = $dateStart->format('Y-m-d');
	$jourEnCours          = $dateStart->format('d');
	$moisEnCours          = $dateStart->format('m');
	$anneeEnCours         = $dateStart->format('Y');
	$numeroSemaineEnCours = $dateStart->format('W');

	$ce_lundi = clone $dateStart;
	$ce_lundi->modify('monday this week');
	$fin_semaine = clone $dateStart;
	$fin_semaine->modify('last day next week');
	$fin_semaine_prochaine = new DateTime();
	$fin_semaine_prochaine->modify('sunday next week');
	$prochain_lundi = clone $dateStart;
	$prochain_lundi->modify('monday next week');
	$dernier_jour_mois = clone $dateStart;
	$dernier_jour_mois->modify('last day of this month');
	$premier_jour_prochain_mois = clone $dateStart;
	$premier_jour_prochain_mois->modify('first day of next month');
	$dernier_jour_prochain_mois = clone $dateStart;
	$dernier_jour_prochain_mois->modify('last day of next month');

	// 	cette semaine
	// 	le reste du mois
	//	le mois prochain
	//  on prend les timestamp. puis boucle :
	$periode['semaine']           = ['titre' => 'Cette semaine', 'dates' => [$ce_lundi, $fin_semaine]];
	$periode['semaine_prochaine'] = ['titre' => 'Semaine prochaine', 'dates' => [$prochain_lundi, $fin_semaine_prochaine]];

	$periode['mois_fin']      = ['titre' => 'Fin du mois', 'dates' => [$prochain_lundi, $dernier_jour_mois]];
	$periode['mois_prochain'] = ['titre' => 'Le mois prochain', 'dates' => [$premier_jour_prochain_mois, $dernier_jour_prochain_mois]];

	// Les schemes avec dates :
	$rs_sc = $APP->get_schemes();//['hasDateScheme' => 1]
	// $rs_sc = $APP->get_schemes();
	$arr_sc = iterator_to_array($rs_sc);
	//
	$BIG_ARR = [];
	$arrdate = $periode[$default]['dates'];
	$titre   = $periode[$default]['titre'] . " du " . $arrdate[0]->format('d/m/Y') . ' au ' . $arrdate[1]->format('d/m/Y');
?>
<div class="padding   alignright textgrisfonce"><span class="inline bordert"><i class="fa fa-caret-down textgris"></i> <?= idioma('Echéancier') ?></span>s</div>
<div class="flex_h flex_align_top">
	<div class="padding">
		<i class="fa fa-calendar-o textbleu"></i>
	</div>
	<div class="blanc" main_auto_tree>
		<div class="padding margin flex_h flex_align_middle sticky">
			<div class="flex_main"><?= $titre ?></div>
		</div>
		<div class="titre_entete_menu none">
			<a class="ededed padding" data-menu="data-menu"><i class="fa fa-navicon"></i></a>
			<div class="contextmenu applink applinkblock">
				<? foreach ($periode as $key => $value): ?>
					<a><?= $value['titre'] ?></a>
				<? endforeach; ?>
			</div>
		</div>
		<div class=" ">
			<div class="flex_v">
				<div class="">
					<? $begin = $arrdate[0];
						$end  = $arrdate[1];

						for ($i = $begin; $begin <= $end; $i->modify('+1 day')) {
							$dadate = $i->format("Y-m-d");
							$datime =strtotime($dadate);
							$count_empty = 0;
							?>
							<div id="eche_empty<?=$datime?>" class=" ">
								<div auto_tree class=" ">
									<div class="padding"><i class="fa fa-calendar-o"></i> <?= fonctionsProduction::jourMoisDate_fr($dadate) ?></div>
								</div>
								<div class="">
									<? foreach ($arr_sc as $key => $value) {
										$dsp = '';
										// vardump($value);
										$table      = $value['codeAppscheme'];
										$Table      = ucfirst($table);
										$APP_tmp    = new App($table);
										$GRILLE_FK  = $APP_tmp->get_grille_fk();
										$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
										$vars_tmp   = $vars;
										if (!empty($TEST_AGENT)): unset ($vars_tmp['idagent']); endif;
										$rs_tmp_deb = $APP_tmp->find($vars_tmp + ['dateDebut' . $Table => $dadate]);
										$rs_tmp_fin = $APP_tmp->find($vars_tmp + ['dateFin' . $Table => $dadate]);
										if ($rs_tmp_deb->count() == 0 && $rs_tmp_fin->count() == 0) {

											$count_empty++;
											continue;
										}
										if ($rs_tmp_deb->count() > 5 && $rs_tmp_fin->count() > 5) {
											$dsp = 'none';
										}
										$BIG_ARR[$key]['table'][$key][$dadate] = $table;
										?>
										<div class="retrait">
											<div auto_tree class="margin padding "><div><i class="fa fa-<?= $value['icon'] ?> textbleu"></i> <?= ucfirst($value['nomAppscheme']) ?></div></div>
											<div class="retrait" style="display:<?= $dsp ?>;">
												<? while ($arr_tmp_deb = $rs_tmp_deb->getNext()) {
													$value_id = (int)$arr_tmp_deb['id' . $table];
													?>
												<a class="hide_gui_pane ellipsis autoToggle" data-contextual="table=<?= $table ?>&table_value=<?= $value_id ?>" act_chrome_gui="app/app/app_fiche"
												   data-vars="table=<?= $table ?>&table_value=<?= $value_id ?>">
													<i class="fa fa-plus-circle textvert"></i>    <?= $arr_tmp_deb['nom' . ucfirst($table)]; ?>
													</a><?
												} ?>
												<? while ($arr_tmp_fin = $rs_tmp_fin->getNext()) {
													$value_id = (int)$arr_tmp_fin['id' . $table];
													?>
												<a class="hide_gui_pane ellipsis  autoToggle" data-contextual="table=<?= $table ?>&table_value=<?= $value_id ?>" act_chrome_gui="app/app/app_fiche"
												   data-vars="table=<?= $table ?>&table_value=<?= $value_id ?>">
													<i class="fa fa-minus-circle textrouge"></i>    <?= $arr_tmp_fin['nom' . ucfirst($table)]; ?>
													</a><?
												} ?>
											</div>
										</div>
										<?
										// $dsp='none';
									}

										if($count_empty == sizeof($arr_sc)){
											?><style>#eche_empty<?=$datime?> {display:none}</style><?=$datime?><?
										} ;
									?>
								</div>
							</div>
						<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>