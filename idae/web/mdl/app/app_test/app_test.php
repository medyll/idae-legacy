<?
	include_once($_SERVER['CONF_INC']);
	$time = time();
	$i = 0;
	//
	$APP = new App();

	$dateStart = new DateTime();

	$DADATE = $dateStart->format('Y-m-d');
	$jourEnCours = $dateStart->format('d');
	$moisEnCours = $dateStart->format('m');
	$anneeEnCours = $dateStart->format('Y');
	$numeroSemaineEnCours = $dateStart->format('W');

	$ce_lundi = clone $dateStart->modify('monday this week');
	$fin_semaine = clone $dateStart->modify('last day next week');
	$prochain_lundi = clone $dateStart->modify('next monday this month');
	$dernier_jour_mois = clone $dateStart->modify('last day of this month');
	$premier_jour_prochain_mois = clone $dateStart->modify('first day of next month');
	$dernier_jour_prochain_mois = clone $dateStart->modify('last day of next month');

	// 	cette semaine
	// 	le reste du mois
	//	le mois prochain
	//  on prend les timestamp. puis boucle :
	$periode['Cette semaine'] = [ $ce_lundi , $fin_semaine ];
	$periode['Fin du mois'] = [ $prochain_lundi , $dernier_jour_mois ];
	$periode['Le mois prochain'] = [ $premier_jour_prochain_mois , $dernier_jour_prochain_mois ];

	// Les schemes avec dates :
	$rs_sc = $APP->get_schemes([ 'hasDateScheme' => 1 ]);
	$arr_sc = iterator_to_array($rs_sc);
	//
?>

<div style = "height:100%;overflow-x:auto;overflow-y: hidden;" class = "blanc">
	<div class = "flex_h flex_nowrap" style = "height:100%;overflow-y: hidden;">
		<? foreach ($periode as $key => $value) {
			$titre = $key . " " . $value[0]->format('d/m/Y') . ' au ' . $value[1]->format('d/m/Y');
			?>
			<div class = "flex_v">
				<div class = "titre_entete aligncenter">
					<?= $titre ?>

				</div>
				<div class = "flex_h flex_main flex_nowrap">
					<?  $begin = $value[0];
						$end = $value[1];

						for ($i = $begin; $begin <= $end; $i->modify('+1 day')) {
							$dadate = $i->format("Y-m-d");
							?>
							<div class = "flex_v" style = "min-width:200px;">
								<div class="padding ededed" ><?= fonctionsProduction::date_fr($dadate) ?></div>
								<div class = "flex_main " style = "overflow:auto;">
									<? foreach ($arr_sc as $key => $value) {
										$table      = $value['nomAppscheme'];
										$Table      = ucfirst($table);
										$APP_tmp    = new App($value['nomAppscheme']);
										$rs_tmp_deb = $APP_tmp->find([ 'dateDebut' . $Table => $dadate ]);
										$rs_tmp_fin = $APP_tmp->find([ 'dateFin' . $Table => $dadate ]);
										if ( $rs_tmp_deb->count() == 0 && $rs_tmp_fin->count() == 0 ) {
											continue;
										}
										?>
										<div class="retrait borderr">
											<div class = "margin padding border4 ededed"><?= $value['nomAppscheme'] ?></div>
											<div class="retrait">
												<? while ($arr_tmp_deb = $rs_tmp_deb->getNext()) {
													$value_id = (int)$arr_tmp_deb['id' . $table];
													?>
												<div    vars="table=<?= $table ?>&table_value=<?= $value_id ?>">
												-	<?=$arr_tmp_deb['nom' . ucfirst($table)];?>
												</div ><?
												} ?>
												<? while ($arr_tmp_fin = $rs_tmp_fin->getNext()) {
													$value_id = (int)$arr_tmp_fin['id' . $table];
													?>
												<div  vars="table=<?= $table ?>&table_value=<?= $value_id ?>">
												-	<?=$arr_tmp_fin['nom' . ucfirst($table)];?>
												</div ><?
												} ?>
											</div>
										</div>
									<? } ?>
								</div>
							</div>

						<? } ?>
				</div>
			</div>
		<? } ?>
	</div>
</div>